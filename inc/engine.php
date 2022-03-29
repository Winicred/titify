<?php

// инициализация безопасности
if (!isset($protection)) {
    $protection = 1;
}

// инициализация вывода ошибок
if (isset($debug_display)) {
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
}

// подключение классов
require_once __DIR__ . "/db.php";
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/functions.php";

require_once __DIR__ . '/classes/class.phpmailer.php';
require_once __DIR__ . '/classes/class.phpmailer_exception.php';
require_once __DIR__ . '/classes/class.users.php';
require_once __DIR__ . '/classes/class.sessionscookies.php';
require_once __DIR__ . '/classes/class.authapi.php';
require_once __DIR__ . '/classes/class.pagesinfo.php';
require_once __DIR__ . '/classes/class.template.php';
require_once __DIR__ . '/classes/class.getdata.php';

// экземпляр класса базы данных
$database = new Database();

// получение подключения к базе данных
$pdo = $database->connect();

// получение конфигурации сайта
$config = $database->get_config_data();

// экземпляр класса класса для API
$auth_api = new AuthApi();

$auth_api_info = $auth_api->auth_api_info($pdo);

// экземпляр класса пользователей
$user = new Users($pdo);

// экземпляр класса сессий и куки
$session_cookies = new SessionsCookies($config->salt, $host);

// получение и установка токена
$token = $session_cookies->set_token();

// инициализация класса для получения данных
$get_data = new GetData($pdo);

// получение и установка идентификатора пользователя
if (empty($_SERVER["HTTP_USER_AGENT"])) {
    $_SERVER["HTTP_USER_AGENT"] = "undefined";
}

// получение ip адреса пользователя
$ip = get_ip();

// передача переменных из куки в сессию
if (isset($_COOKIE["cache"])) {

    // получение идентификатора пользователя и кеша из куки
    $_SESSION["cache"] = clean($_COOKIE["cache"]);
    $_SESSION['id'] = clean($_COOKIE["id"]);
} else {

    // если переменные не переданы в сессию, то очистить сессию
    $session_cookies->clean_user_session();
}

// получение идентификатора пользователя из таблицы забаненных пользователей
$db_response = $pdo->prepare("SELECT id FROM users__blocked WHERE ip = :ip AND date = '0000-00-00 00:00:00'");
$db_response->setFetchMode(PDO::FETCH_OBJ);

// задать параметры для запроса
$db_response->execute([":ip" => $ip]);

// выполнение запроса
$row = $db_response->fetch();

// проверка на сессию админа
if (isset($_SESSION["admin"]) && isset($_SESSION["admin_cache"])) {

    // защита ip адреса админа
    if ($config->ip_protect == 1) {
        $session_cookies->admin_ip = $ip;
    }

    // режим разработчика для админа
    $_SESSION["dev_mode"] = $dev_mode;

    // если кеш сессии отличается от md5 кеша сгенерированным классом SessionCookies
    if ($_SESSION["admin_cache"] != $session_cookies->get_admin_cache($config->password)) {

        // удалить сессию админа
        $session_cookies->clean_admin_session();
    }
} else {

    // очистить сессию админа, если пользователь - не админ
    $session_cookies->clean_admin_session();
}

// получение времени
$time = time();

// получение флагов доступа пользователей
$users_groups = get_groups($pdo);

// если пользователь авторизирован
if (is_auth()) {

    // получение данных о пользователе
    $user = Users::get_user_data($pdo, $_SESSION["id"]);

    // если данные каким то образом не получены
    if (empty($user->id)) {
        require_once __DIR__ . "/../modules/exit/index.php";
    }

    // запись ip адреса пользователя
    if ($user->protect == 1) {
        $session_cookies->ip = $ip;
    }

    // передача данных пользователя в сессию
    $_SESSION["login"] = $user->login;
    $_SESSION["rights"] = $user->rights;

    // сравнение кеша сессии
    if ($_SESSION["cache"] != $session_cookies->get_cache($user->password) || $user->deleted == 1 || is_worthy("z")) {

        // вызов модуля с ошибкой
        require_once __DIR__ . "/../modules/exit/index.php";
    }

    // проверка на бан пользователя
    if (is_worthy("x")) {
        $ban = true;

        // вызов модуля с ошибкой
        require_once __DIR__ . "/../modules/exit/index.php";
    }

    // получение md5 кеш идентификатора пользователя
    $browser = md5($_SERVER["HTTP_USER_AGENT"]);

    // изменение ip адреса при несовпадении и отправка сообщения на почту
    if ($user->ip != $ip) {

        // подключение уведомлений
        inc_notifications();

        // получить страницу с восстановлением пароля
        $db_response = $pdo->query("SELECT url FROM pages WHERE name = 'recovery'");

        // установить режим выборки
        $db_response->setFetchMode(PDO::FETCH_OBJ);

        // выполнение запроса
        $page_url = $db_response->fetch();

        // создание ссылки для восстановления пароля
        $link = $full_site_host . $page_url->url . '?a=' . $user->id . '&data=' . md5($user->id . $config->salt . $user->password . $user->email . date("Y-m-d"));

        // получить массив с сообщением
        $invalid_ip_letter = invalid_ip_address_letter($config->name, $user->login, $link, $ip);

        // отправить сообщение пользователю о неизвестном входе
        send_mail($user->email, $invalid_ip_letter['subject'], $invalid_ip_letter['message'], $pdo);

        $db_response = $pdo->prepare("UPDATE users SET ip = :ip WHERE id = :id");
        $db_response->execute([":ip" => $ip, ":id" => $_SESSION["id"]]);
    }

    // изменение кеша браузера при несовпадении
    if ($user->browser != $browser) {
        $db_response = $pdo->prepare("UPDATE users SET browser = :browser WHERE id = :id");
        $db_response->execute([":browser" => $browser, ":id" => $_SESSION["id"]]);
    }
}

// вставить не авторизированного пользователя в таблицу сессии
$db_response = $pdo->prepare("SELECT id FROM sessions WHERE ip = :ip");

// режим выборки
$db_response->setFetchMode(PDO::FETCH_OBJ);

// передача параметров id пользователя и времени
$db_response->execute([":ip" => $ip]);

// выборка
$user_session = $db_response->fetch();

if (!isset($user_session->id)) {

    // вставить не авторизированного пользователя в таблицу сессии
    $db_response = $pdo->prepare("INSERT INTO sessions (ip, volume, start_date) VALUES (:ip, DEFAULT, :start_date)");

    // передача параметров id пользователя и времени
    $db_response->execute([":ip" => $ip, ":start_date" => date('Y-m-d H:i:s')]);
}

$page_info = new PagesInfo($pdo);
$page_info->full_host = $full_site_host;
$page = $page_info->page_info();

$tpl = new Template();
$tpl->dir = "templates/standart/tpl/";

if (isset($modules_tpls)) {
    $tpl->modules_tpls = $modules_tpls;
    unset($modules_tpls);
} else {
    unset($tpl->modules_tpls);
}

$tpl->load_template('elements/title/title.tpl');
$tpl->set("{title}", $page->title);
$tpl->set("{name}", $config->name);
$tpl->compile('title');
$tpl->clear();

$tpl->load_template('header.tpl');
$tpl->set("{site_name}", $config->name);
$tpl->set("{title}", $tpl->result['title']);
$tpl->set("{token}", $token);
$tpl->set("{cache}", $config->cache);
$tpl->set("{site_host}", $site_host);
$tpl->compile('content');
$tpl->clear();

// запуск модулей.
require_once __DIR__ . '/../' . $page->file;

$tpl->load_template("footer.tpl");
$tpl->set("{site_host}", $full_site_host);
$tpl->set("{site_name}", $config->name);

$tpl->compile("content");
$tpl->clear();

$tpl->set("{content}", $tpl->result["content"]);
$tpl->load_template("main.tpl");

$tpl->compile("main");
eval(" ?>" . $tpl->result["main"] . "<?php ");
$tpl->global_clear();