<?php

// подключение файла start.php
include_once '../inc/start.php';

// проверка на пустоту переменной запроса "phpaction"
if (empty($_POST['phpaction'])) {
    exit('Ошибка: [Прямой вызов скрипта]');
}

// проверка если токен конфигурации не совпадает с токеном запроса
if ($config->token == 1 && ($_SESSION['token'] != check_js($_POST['token']))) {
    exit('Ошибка: [Неверный токен]');
}

if (isset($_POST['load_template'])) {
    $template = $_POST['template'];

    $module_file = $_POST['module_file'] ?? 'index';

    $data_array = $_POST['data_array'] ?? [];

    $data_template = require_once __DIR__ . '/../modules/' . $template . '/' . $module_file . '.php';

    exit(json_encode(json_decode($data_template)));
}

/* ЛОГИН ПОЛЬЗОВАТЕЛЯ И АДМИНА */
if (isset($_POST['user_login']) || isset($_POST['admin_login'])) {

    // очистка переменных
    $login = check_js($_POST['login_email']);
    $password = check_js($_POST['password']);

    // создание класса пользователя
    $user = new Users($pdo);

    // получить ip адрес пользователя
    $ip = get_ip();

    // проверка на пустоту поля "deleted"
    $db_response = $pdo->prepare("SELECT deleted FROM users WHERE login = :auth OR email = :auth LIMIT 1");
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    $db_response->execute([':auth' => $login]);

    if ($db_response->fetch()->deleted == 1) {
        exit(json_encode(['status' => 'error', 'message' => 'Your account has been deleted. Please contact the administrator.']));
    }

    // проверка на невалидныые авторизации
    $invalid_auths = $user_obj->check_to_invalid_auth($ip);

    // проверка на количество невалидных авторизаций
    if ($invalid_auths >= $config->invalid_auths && $config->invalid_auths != 0) {

        // вывод сообщения об блокировке аккаунта
        exit(json_encode(['status' => 'error', 'message' => 'You are blocked for 15 minutes. Try later.', 'banned' => true]));
    }

    // шифрование пароля
    $password = $user_obj->convert_password($password, $config->salt);

    // выполнение запроса на получение данных пользователя
    $db_response = $pdo->prepare("SELECT id, rights, active, password, login, protect FROM users WHERE password = :password AND (login = :auth OR email = :auth) LIMIT 1");

    // установить параметры выборки
    $db_response->setFetchMode(PDO::FETCH_OBJ);

    // привязка переменных к запросу и выполнение запроса
    $db_response->execute([':password' => $password, ':auth' => $login]);

    // получение данных пользователя из запроса
    $user_data = $db_response->fetch();

    // проверка на наличие пользователя
    if (empty($user_data->id)) {

        if ($config->invalid_auths > 0) {
            // добавить невалидную авторизацию
            $invalid_auths = $user_obj->up_invalid_auths($ip);

            exit(json_encode(['status' => 'error', 'message' => 'Attempt: ' . $invalid_auths . '/' . $config->invalid_auths . '. The entered data is incorrect.']));
        } else {
            exit(json_encode(['status' => 'error', 'message' => 'The entered data is incorrect.']));
        }
    } else {

        // удалить все невалидные авторизации
        if ($invalid_auths) {
            $user_obj->delete_invalid_auths($ip);
        }

        // авторизация пользователя
        $user_obj->auth_user($session_cookies, $user_data->protect, $user_data->password, $user_data->login, $user_data->id, $user_data->rights);

        // проверка флага пользователя
        if (is_worthy("z")) {

            // удалить массив сессии
            $session_cookies->unset_user_session();

            exit(json_encode(['status' => 'error', 'message' => 'You are blocked for 15 minutes. Try later.']));
        }

        // проверка флага пользователя
        if (is_worthy("x")) {

            // удалить куки
            $session_cookies->unset_user_session();

            // вставить ip адрес в базу данных
            $db_response = $pdo->prepare("INSERT INTO users__blocked (ip) VALUES (:ip)");

            // привязка переменных к запросу и выполнение запроса
            $db_response->execute([':ip' => $ip]);

            // установить куки
            $session_cookies->set_cookie("point", "1");

            // вывод сообщения о блокировке
            exit(json_encode(['status' => 'error', 'message' => 'You are blocked.']));
        }

        // установить куки по умолчанию
        $session_cookies->set_user_cookie();

        // вывод сообщения
        exit(json_encode(['status' => 'success', 'message' => 'You have successfully logged in!']));
    }
}

if (isset($_POST['registration'])) {

    // очистка переменных
    $login = check_js($_POST['login']);
    $password = check_js($_POST['password']);
    $password_repeat = check_js($_POST['password_repeat']);
    $display_name = check_js($_POST['display_name']);
    $email = check_js($_POST['email']);

    $redirect = $config->conf_us == 0;

    if (empty($login) || empty($password) || empty($password_repeat) || empty($display_name) || ($redirect == true && empty($email))) {
        exit(json_encode(['status' => 'error', 'message' => 'You must fill in all fields.']));
    }

    // проверка на занятый логин
    if (!$user_obj->check_login_busyness($login)) {
        exit(json_encode(['status' => 'error', 'message' => 'The entered login is already registered.']));
    }

    // проверка на длину логина
    if (!$user_obj->check_eng_login_length($login)) {
        exit(json_encode(['status' => 'error', 'message' => 'Login must be at least 5 characters and not more than 30.']));
    }

    // проверка на символы в логине
    if (!$user_obj->check_login_composition($login)) {
        exit(json_encode(['status' => 'error', 'message' => 'Only letters and numbers are allowed in the login.']));
    }

    // поверка на длину имени
    if (!$user_obj->check_display_name_length($display_name)) {
        exit(json_encode(['status' => 'error', 'message' => 'The name must be at least 2 characters and not more than 50.']));
    }

    // проверка на занятость имени
    if (!$user_obj->check_for_display_name_exist($display_name)) {
        exit(json_encode(['status' => 'error', 'message' => 'Display name is already exist.']));
    }

    // проверка на длину пароля
    if (!$user_obj->check_password_length($password)) {
        exit(json_encode(['status' => 'error', 'message' => 'The password must be at least 8 characters and no more than 25.']));
    }

    // сравнение паролей
    if ($password != $password_repeat) {
        exit(json_encode(['status' => 'error', 'message' => 'The entered passwords do not match.']));
    }

    if ($redirect == true) {

        // проверка на валидность почты
        if (!$user_obj->check_email($email)) {
            exit(json_encode(['status' => 'error', 'message' => 'E-mail entered incorrectly.']));
        }

        // проверка на занятость почты
        if (!$user_obj->check_email_busyness($email)) {
            exit(json_encode(['status' => 'error', 'message' => 'The E-mail you entered is already registered.']));
        }
    }

    // шифрование пароля
    $password = $user_obj->convert_password($password, $config->salt);

    // авторизация пользователя
    $user_data = $user_obj->entry_user($login, $password, $config->conf_us, $display_name, $email);

    // проверка на наличие пользователя
    if (!empty($user_data->id)) {

        // вызов функций после регистрации пользователя
        $answer = $user_obj->after_registration_actions($session_cookies, $config->salt, $config->name, $user_data->id, $full_site_host);

        // если метод "after_registration_actions" выдал ошибку
        if ($answer['message'] != 'error') {

            // вывод ошибки из метода "after_registration_actions"
            exit(json_encode(['status' => 'success', 'message' => $answer['message'], 'redirect' => !$redirect]));
        }

        exit(json_encode(['status' => 'error', 'message' => $answer['message']]));
    } else {

        // вывод сообщения о неудачной регистрации
        exit(json_encode(['status' => 'error', 'message' => 'Error! You are not registred']));
    }
}

if (isset($_POST['user_exit'])) {
    $session_cookies->unset_user_session();

    exit();
}

if (isset($_POST['auth_by_api'])) {

    $login = check_js($_POST['login']);
    $password = check_js($_POST['password']);
    $avatar_url = check_js($_POST['avatar']);
    $email = check_js($_POST['email']);
    $display_name = check_js($_POST['display_name']);
    $firstname = check_js($_POST['firstname']);
    $lastname = check_js($_POST['lastname']);

    $user_obj = new Users(pdo());

    $password = $user_obj->convert_password($password, $config->salt);

    if (!$user_obj->check_email_busyness($email)) {

        // выполнение запроса на получение данных пользователя
        $db_response = pdo()->prepare("SELECT id, rights, active, password, login, protect, deleted FROM users WHERE email = :auth");

        // установить параметры выборки
        $db_response->setFetchMode(PDO::FETCH_OBJ);

        // привязка переменных к запросу и выполнение запроса
        $db_response->execute([':auth' => $login]);

        // получение данных пользователя из запроса
        $user_data = $db_response->fetch();

        if ($user_data->deleted == 1) {
            exit(json_encode(['status' => 'error', 'message' => 'Your account has been deleted. Please contact the administrator.']));
        }

        $user_obj->auth_user($session_cookies, $user_data->protect, $user_data->password, $user_data->login, $user_data->id, $user_data->rights);

        // проверка флага пользователя
        if (is_worthy("z")) {

            // удалить массив сессии
            $session_cookies->unset_user_session();

            exit(json_encode(['status' => 'error', 'message' => 'You are blocked. Try later.']));
        }

        // проверка флага пользователя
        if (is_worthy("x")) {

            // удалить куки
            $session_cookies->unset_user_session();

            // вставить ip адрес в базу данных
            $db_response = $pdo->prepare("INSERT INTO users__blocked (ip) VALUES (:ip)");

            // привязка переменных к запросу и выполнение запроса
            $db_response->execute([':ip' => $ip]);

            // установить куки
            $session_cookies->set_cookie("point", "1");

            // вывод сообщения о блокировке
            exit(json_encode(['status' => 'error', 'message' => 'You are blocked.']));
        }

        $session_cookies->set_user_cookie();

        // вывод сообщения
        exit(json_encode(['status' => 'success', 'message' => 'You have successfully logged in!']));
    } else {

        // шифрование пароля

        $dir = __DIR__ . '/../files/avatars';
        $avatar = generation_name() . '.png';

        $avatar_url = str_replace('&amp;', '&', $avatar_url);

        copy($avatar_url, $dir . '/' . $avatar);

        // авторизация пользователя
        $user_data = $user_obj->entry_user($login, $password, $config->conf_us, $display_name, $email, $firstname, $lastname, 'files/avatars/' . $avatar);

        // проверка на наличие пользователя
        if (!empty($user_data->id)) {

            // вызов функций после регистрации пользователя
            $answer = $user_obj->after_registration_actions($session_cookies, $config->salt, $config->name, $user_data->id, $full_site_host);

            $session_cookies->set_user_cookie();

            // если метод "after_registration_actions" выдал ошибку
            if ($answer['message'] != 'error') {

                // вывод ошибки из метода "after_registration_actions"
                exit(json_encode(['status' => 'success', 'message' => $answer['message']]));
            }
        } else {

            // вывод сообщения о неудачной регистрации
            exit(json_encode(['status' => 'error', 'message' => 'Error! You are not registred']));
        }
    }

    exit();
}

if (isset($_POST['send_new_pass'])) {

    // очистка переменных
    $email = check($_POST['email'], null);

    // если пустой эмаил
    if (empty($email)) {
        exit('<p class="text-danger">Specify E-mail!</p>');
    }

    // создание нового экземпляра класса пользователя
    $user = new Users($pdo);

    // проверка почты на корректность
    if (!$user_obj->check_email($email)) {
        exit('<p class="text-danger">Email entered incorrectly!</p>');
    }

    // проверка на занятость почты
    if ($user_obj->check_email_busyness($email)) {
        exit('<p class="text-danger">The email you entered is not registered!</p>');
    }

    // получение данных пользователя из базы данных
    $db_response = $pdo->query("SELECT id, email, login, password FROM users WHERE email = '$email'");

    // установить режим выборки
    $db_response->setFetchMode(PDO::FETCH_OBJ);

    // выполнение запроса
    $row = $db_response->fetch();

    // выборка страницы с восстановлением из базы данных
    $db_response = $pdo->query("SELECT url FROM pages WHERE name = 'recovery'");

    // установить режим выборки
    $db_response->setFetchMode(PDO::FETCH_OBJ);

    // выполнение запроса
    $page_url = $db_response->fetch();

    // создание ссылки для восстановления пароля
    $link = $full_site_host . $page_url->url . '?a=' . $row->id . '&data=' . md5($row->id . $config->salt . $row->password . $row->email . date("Y-m-d"));

    // загрзука уведомлений
    inc_notifications();

    // получиться массив с уведомлением из словаря
    $letter = recovery_check_letter($config->name, $row->login, $link);

    // отправить сообщение с ссылкой пользователю на почту
    send_mail($row->email, $letter['subject'], $letter['message'], $pdo);

    // вывод сообщения
    exit('<p class="text-success">We have sent to your mail <b>' . $row->email . '</b> a link to reset your password, it will be valid for the current day.</p>');
}

if (isset($_POST['get_tracks'])) {

    // получить треки из базы данных
    $db_response = $pdo->query("SELECT tracks.id, tracks.path, tracks.title, tracks.cover, users.id AS author_id, users.name, users.lastname FROM tracks LEFT JOIN users ON tracks.author = users.id");

    // режим выборки
    $db_response->setFetchMode(PDO::FETCH_OBJ);

    // выборка треков
    while ($track = $db_response->fetch()) {

        // загрузка шаблона
        $tpl->load_template('elements/tracks/tracks_mini.tpl');

        // установка переменных
        $tpl->set('{track_id}', $track->id);
        $tpl->set('{title}', $track->title);
        $tpl->set('{cover}', $track->cover);
        $tpl->set('{author_id}', $track->author_id);
        $tpl->set('{author_name}', $track->name);
        $tpl->set('{author_lastname}', $track->lastname);

        // скомпилировать шаблон
        $tpl->compile('track');

        // очистка переменных
        $tpl->clear();
    }

    // вывести string-значение шаблона
    $tpl->show($tpl->result['track']);

    // глобальная очистка переменных шаблона
    $tpl->global_clear();

    exit();
}

if (isset($_POST['play_random_tracks'])) {

    $result_array = [];

    // выборка 20 случайных треков
    $db_response = $pdo->query("SELECT tracks.id, tracks.path, tracks.cover, tracks.title, users.id as author_id, users.name, users.lastname FROM tracks LEFT JOIN users ON tracks.author = users.id ORDER BY RAND() LIMIT 20");

    // режим выборки
    $db_response->setFetchMode(PDO::FETCH_OBJ);

    while ($track = $db_response->fetch()) {
        $result_array[] = $track->path;
    }

    if (!empty($result_array)) {
        exit(json_encode(['status' => 'success', 'path' => $result_array]));
    }

    exit(json_encode(['status' => 'error']));
}

if (isset($_POST['call_modal'])) {

    $modal = check_js($_POST['modal']);
    $data = $_POST['data_array'];

    $tpl->load_template('elements/modals/' . $modal . '.tpl');

    if (!empty($data)) {
        foreach ($data as $key => $value) {
            $tpl->set('{' . $key . '}', $value);
        }
    }

    $tpl->compile('modal');
    $tpl->clear();

    $tpl->show($tpl->result['modal']);
    $tpl->global_clear();

    exit();
}

if (isset($_POST['get_track_info'])) {

    $track = check_js($_POST['track']);

    $db_response = pdo()->query("SELECT tracks.id, tracks.cover, tracks.title, tracks.path, users.id as author_id, users.display_name FROM tracks LEFT JOIN users ON tracks.author = users.id WHERE tracks.path = '$track'");
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    $track_data = $db_response->fetch();

    if (is_auth()) {
        $db_response = pdo()->query("SELECT * FROM users__favorite_actions WHERE track_id = $track_data->id AND user_id = " . $_SESSION['id']);
    } else {
        $db_response = pdo()->query("SELECT * FROM users__favorite_actions WHERE track_id = $track_data->id AND ip = " . get_ip());
    }

    $db_response->setFetchMode(PDO::FETCH_OBJ);
    $favorite = $db_response->fetch();

    exit(json_encode(['image_id' => $track_data->id, 'image' => $track_data->cover, 'title' => $track_data->title, 'author' => $track_data->display_name, 'path' => $track_data->path, 'author_id' => $track_data->author_id, 'liked' => $favorite ? 1 : 0]));
}

if (isset($_POST['up_track_history'])) {

    $track = check_js($_POST['track']);

    $track_data = get_track_by_src($track);

    // check if row exist
    $db_response = $pdo->query("SELECT * FROM track__history WHERE track_id = $track_data->id AND user_id = " . $_SESSION['id']);
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    if ($db_response->fetch()) {
        exit();
    }

    if (is_auth()) {
        $db_response = $pdo->prepare("INSERT INTO track__history (user_id, track_id, ip) VALUES (:user_id, :track_id, :ip)");
        $db_response->execute([':user_id' => $_SESSION['id'], ':track_id' => $track_data->id, ':ip' => get_ip()]);
    } else {
        $db_response = $pdo->prepare("INSERT INTO track__history (track_id, ip) VALUES (:track_id, :ip)");
        $db_response->execute([':track_id' => $track_data->id, ':ip' => get_ip()]);
    }

    $db_response = $pdo->query("UPDATE tracks SET auditions = auditions + 1 WHERE id = $track_data->id");

    exit(json_encode(['status' => 'ok']));
}

if(isset($_POST['set_like_to_track'])) {
    $track = check_js($_POST['track']);

    $track_data = get_track_by_src($track);

    if (is_auth()) {
        $db_response = $pdo->query("SELECT * FROM users__favorite_actions WHERE track_id = $track_data->id AND user_id = " . $_SESSION['id']);
        $db_response->setFetchMode(PDO::FETCH_OBJ);
        if ($db_response->fetch()) {
            $db_response = $pdo->query("DELETE FROM users__favorite_actions WHERE track_id = $track_data->id AND user_id = " . $_SESSION['id']);

            $db_response = $pdo->query("UPDATE tracks SET likes = likes - 1 WHERE id = $track_data->id");
        } else {
            $db_response = $pdo->prepare("INSERT INTO users__favorite_actions (user_id, track_id, ip) VALUES (:user_id, :track_id, :ip)");
            $db_response->execute([':user_id' => $_SESSION['id'], ':track_id' => $track_data->id, ':ip' => get_ip()]);

            $db_response = $pdo->query("UPDATE tracks SET likes = likes + 1 WHERE id = $track_data->id");
        }

    } else {
        $db_response = $pdo->query("SELECT * FROM users__favorite_actions WHERE track_id = $track_data->id AND ip = " . get_ip());
        $db_response->setFetchMode(PDO::FETCH_OBJ);
        if ($db_response->fetch()) {
            $db_response = $pdo->query("DELETE FROM users__favorite_actions WHERE track_id = $track_data->id AND ip = " . get_ip());

            $db_response = $pdo->query("UPDATE tracks SET likes = likes - 1 WHERE id = $track_data->id");
        } else {
            $db_response = $pdo->prepare("INSERT INTO users__favorite_actions (track_id, ip) VALUES (:track_id, :ip)");
            $db_response->execute([':track_id' => $track_data->id, ':ip' => get_ip()]);

            $db_response = $pdo->query("UPDATE tracks SET likes = likes + 1 WHERE id = $track_data->id");
        }
    }

    exit(json_encode(['status' => 'success']));
}

if (isset($_POST['get_playlists'])) {

    $limit = 6;

    $playlists_array_template = ['Most Viewed', 'Likes', 'Top 6 Tracks'];
    $playlists_desc_array_template = ['Most Viewed Tracks On Titify', 'Most Liked Playlists By Users', 'Top 6 Tracks On Titify By Auditions'];

    $script = '';

    for ($i = 0; $i < count($playlists_array_template); $i++) {
        $tpl->result['playlist_item'] = '';

        if ($i == 0) {
            $db_response = $pdo->query("SELECT users__playlists.id, users__playlists.name, users__playlists.cover, users.id AS author_id, users.login FROM users__playlists LEFT JOIN users ON users__playlists.user_id = users.id LIMIT $limit");
        } elseif ($i == 1) {
            $db_response = $pdo->query("SELECT users__playlists.id, users__playlists.name, users__playlists.cover, users.id AS author_id, users.login FROM users__playlists LEFT JOIN users ON users__playlists.user_id = users.id ORDER BY users__playlists.likes DESC LIMIT $limit");
        } elseif ($i == 2) {
            $db_response = $pdo->query("SELECT tracks.id, tracks.title AS name, tracks.cover, users.id AS author_id, users.login FROM tracks LEFT JOIN users ON tracks.author = users.id ORDER BY tracks.auditions DESC LIMIT $limit");
        }

        $db_response->setFetchMode(PDO::FETCH_OBJ);

        while ($playlist = $db_response->fetch()) {
            $script = 'open_playlist(' . $playlist->id . ');';

            $tpl->load_template('elements/playlists/playlist_item.tpl');

            $tpl->set('{script}', $script);
            $tpl->set('{playlist_id}', $playlist->id);
            $tpl->set('{playlist_name}', $playlist->name);
            $tpl->set('{playlist_cover}', $playlist->cover ?? 'files/avatars/no_avatar.jpg');
            $tpl->set('{playlist_author_id}', $playlist->author_id);
            $tpl->set('{playlist_author_login}', $playlist->login);

            $tpl->compile('playlist_item');

            $tpl->clear();
        }

        $tpl->load_template('elements/playlists/playlist.tpl');
        $tpl->set('{title}', $playlists_array_template[$i]);
        $tpl->set('{description}', $playlists_desc_array_template[$i]);
        $tpl->set('{playlists}', $tpl->result['playlist_item']);

        $tpl->compile('content');

        $tpl->clear();
    }

    $tpl->show($tpl->result['content']);
    $tpl->global_clear();

    exit();
}

if (isset($_POST['add_favorite_track'])) {

    $id = check_js($_POST['id']);
    $type = check_js($_POST['type']);

    if ($type == 'track') {
        if (is_auth()) {
            $db_response = $pdo->prepare("SELECT * FROM users__favorite_actions WHERE (user_id = :user_id OR ip = :ip) AND track_id = :track_id");
            $db_response->execute([':user_id' => $_SESSION['id'], ':track_id' => $id, ':ip' => get_ip()]);
        } else {
            $db_response = $pdo->prepare("SELECT * FROM users__favorite_actions WHERE ip = :ip AND track_id = :track_id");
            $db_response->execute([':track_id' => $id, ':ip' => get_ip()]);
        }
    } elseif ($type == 'playlist') {
        if (is_auth()) {
            $db_response = $pdo->prepare("SELECT * FROM users__favorite_actions WHERE (user_id = :user_id OR ip = :ip) AND playlist_id = :playlist_id");
            $db_response->execute([':user_id' => $_SESSION['id'], ':playlist_id' => $id, ':ip' => get_ip()]);
        } else {
            $db_response = $pdo->prepare("SELECT * FROM users__favorite_actions WHERE ip = :ip AND playlist_id = :playlist_id");
            $db_response->execute([':playlist_id' => $id, ':ip' => get_ip()]);
        }
    } else {
        exit();
    }

    $is_like_set = (bool)$db_response->fetch();

    if ($type == 'track') {
        if (is_auth()) {
            if ($is_like_set == true) {
                $db_response = $pdo->prepare("DELETE FROM users__favorite_actions WHERE (user_id = :user_id OR ip = :ip) AND track_id = :track_id");
                $db_response->execute([':user_id' => $_SESSION['id'], ':track_id' => $id, ':ip' => get_ip()]);

                $db_response = $pdo->prepare("UPDATE tracks SET likes = likes - 1 WHERE id = :track_id");
                $db_response->execute([':track_id' => $id]);
            } else {
                $db_response = $pdo->prepare("INSERT INTO users__favorite_actions (user_id, track_id, ip) VALUES (:user_id, :track_id, :ip)");
                $db_response->execute([':user_id' => $_SESSION['id'], ':track_id' => $id, ':ip' => get_ip()]);

                $db_response = $pdo->prepare("UPDATE tracks SET likes = likes + 1 WHERE id = :track_id");
                $db_response->execute([':track_id' => $id]);
            }
        } else {
            if ($is_like_set == true) {
                $db_response = $pdo->prepare("DELETE FROM users__favorite_actions WHERE ip = :ip AND track_id = :track_id");
                $db_response->execute([':track_id' => $id, ':ip' => get_ip()]);

                $db_response = $pdo->prepare("UPDATE tracks SET likes = likes - 1 WHERE id = :track_id");
                $db_response->execute([':track_id' => $id]);
            } else {
                $db_response = $pdo->prepare("INSERT INTO users__favorite_actions (track_id, ip) VALUES (:track_id, :ip)");
                $db_response->execute([':track_id' => $id, ':ip' => get_ip()]);

                $db_response = $pdo->prepare("UPDATE tracks SET likes = likes + 1 WHERE id = :track_id");
                $db_response->execute([':track_id' => $id]);
            }
        }
    } elseif ($type == 'playlist') {
        if (is_auth()) {
            if ($is_like_set == true) {
                $db_response = $pdo->prepare("DELETE FROM users__favorite_actions WHERE (user_id = :user_id OR ip = :ip) AND playlist_id = :playlist_id");
                $db_response->execute([':user_id' => $_SESSION['id'], ':playlist_id' => $id, ':ip' => get_ip()]);

                $db_response = $pdo->prepare("UPDATE users__playlists SET likes = likes - 1 WHERE id = :playlist_id");
                $db_response->execute([':playlist_id' => $id]);
            } else {
                $db_response = $pdo->prepare("INSERT INTO users__favorite_actions (user_id, playlist_id, ip) VALUES (:user_id, :playlist_id, :ip)");
                $db_response->execute([':user_id' => $_SESSION['id'], ':playlist_id' => $id, ':ip' => get_ip()]);

                $db_response = $pdo->prepare("UPDATE users__playlists SET likes = likes + 1 WHERE id = :playlist_id");
                $db_response->execute([':playlist_id' => $id]);
            }
        } else {
            if ($is_like_set == true) {
                $db_response = $pdo->prepare("DELETE FROM users__favorite_actions WHERE ip = :ip AND playlist_id = :playlist_id");
                $db_response->execute([':playlist_id' => $id, ':ip' => get_ip()]);

                $db_response = $pdo->prepare("UPDATE users__playlists SET likes = likes - 1 WHERE id = :playlist_id");
                $db_response->execute([':playlist_id' => $id]);
            } else {
                $db_response = $pdo->prepare("INSERT INTO users__favorite_actions (playlist_id, ip) VALUES (:playlist_id, :ip)");
                $db_response->execute([':playlist_id' => $id, ':ip' => get_ip()]);

                $db_response = $pdo->prepare("UPDATE users__playlists SET likes = likes + 1 WHERE id = :playlist_id");
                $db_response->execute([':playlist_id' => $id]);
            }
        }
    }

    exit(json_encode(['status' => 'success', 'likes_status' => !$is_like_set]));
}

if (isset($_POST['open_playlist'])) {

    $tpl->result['playlist_item'] = '';

    $playlist_id = $_POST['playlist_id'];

    $db_response = $pdo->prepare("SELECT users__playlists.id as playlist_id, users__playlists.name, users__playlists.cover, users.id, users.login FROM users__playlists LEFT JOIN users ON users__playlists.user_id = users.id WHERE users__playlists.id = :playlist_id");
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    $db_response->execute([':playlist_id' => $playlist_id]);
    $playlist = $db_response->fetch();

    if ($playlist) {
        $db_response = $pdo->prepare("SELECT tracks.id, tracks.path, tracks.title, tracks.cover, users.id AS author_id, users.login FROM users__playlists_tracks LEFT JOIN tracks on users__playlists_tracks.track_id = tracks.id LEFT JOIN users on tracks.author = users.id WHERE playlist_id = :playlist_id");
        $db_response->setFetchMode(PDO::FETCH_OBJ);
        $db_response->execute([':playlist_id' => $playlist->playlist_id]);

        while ($track = $db_response->fetch()) {
            if (isset($track->id)) {
                if (is_auth()) {
                    $is_liked = pdo()->query("SELECT id FROM users__favorite_actions WHERE track_id = $track->id AND ip = '".get_ip()."' AND user_id = {$_SESSION['id']}")->fetchColumn();
                } else {
                    $is_liked = pdo()->query("SELECT id FROM users__favorite_actions WHERE track_id = $track->id AND ip = '".get_ip() . "'")->fetchColumn();
                }

                $tpl->load_template('elements/playlists/list/get_playlist_item.tpl');
                $tpl->set('{track_id}', $track->id);
                $tpl->set('{track_path}', $track->path);
                $tpl->set('{track_title}', $track->title);
                $tpl->set('{track_cover}', $track->cover);
                $tpl->set('{track_author_id}', $track->author_id);
                $tpl->set('{track_author_login}', $track->login);
                $tpl->set('{is_liked}', $is_liked == true ? 'true' : 'false');

                $tpl->compile('playlist_item');

                $tpl->clear();
            }
        }

        $tpl->load_template('elements/check_playlist.tpl');

        $tpl->set('{playlist_title}', $playlist->name);
        $tpl->set('{playlist_items}', $tpl->result['playlist_item']);
    }

    if ($tpl->result['playlist_item'] != '') {
        $tpl->compile('content');

        $tpl->show($tpl->result['content']);
        $tpl->global_clear();
    }

    exit();
}

if (isset($_POST['find_tracks'])) {

    $search = check_js($_POST['search']);

    //поиск по названию, автору
    $db_response = $pdo->prepare("SELECT tracks.id, tracks.path, tracks.title, tracks.cover, tracks.author, users.id AS author_id, users.display_name, users.name, users.lastname, users.cover AS user_cover FROM tracks LEFT JOIN users ON tracks.author = users.id WHERE tracks.title LIKE :search OR users.name LIKE :search OR users.lastname LIKE :search");
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    $db_response->execute([':search' => '%' . $search . '%']);

    $tpl->result['search_item'] = '';

    while ($track = $db_response->fetch()) {
        $tpl->load_template('elements/search/get_search_item.tpl');

        $track_link = str_replace('files/tracks/', '', $track->path);
        $track_link = substr($track_link, 0, strrpos($track_link, '.'));

        $tpl->set('{track_id}', $track->id);
        $tpl->set('{track_path}', $track->path);
        $tpl->set('{track_title}', $track->title);
        $tpl->set('{track_cover}', $track->cover ?? $track->users_cover);
        $tpl->set('{track_author_login}', $track->display_name);
        $tpl->set('{track_author_id}', $track->author_id);
        $tpl->set('{track_link}', $track_link);

        $tpl->compile('search_item');

        $tpl->clear();
    }

    if ($tpl->result['search_item'] == '') {
        $tpl->load_template('elements/search/no_result.tpl');
        $tpl->set('{input_data}', $search);
        $tpl->compile('search_item');
        $tpl->clear();
    }

    $tpl->show($tpl->result['search_item']);
    $tpl->global_clear();

    exit();
}

if (isset($_POST['site_search'])) {
    $search = $_POST['search'];

    // поиск песен
    $db_response = $pdo->prepare("SELECT tracks.id, tracks.path, tracks.title, tracks.cover, users.id AS author_id, users.display_name FROM tracks LEFT JOIN users ON tracks.author = users.id WHERE tracks.title LIKE :search OR tracks.author LIKE :search");
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    $db_response->execute([':search' => '%' . $search . '%']);

    $tpl->result['search_item'] = '';

    while ($track = $db_response->fetch()) {
        if (isset($track->id)) {
            $tpl->load_template('elements/search/site_search.tpl');

            // replace files/tracks/
            $track_path = str_replace('files/tracks/', '', $track->path);

            $tpl->set('{id}', $track->id);
            $tpl->set('{title}', "<a onclick='load_template(\"track\", {name: \"$track_path\"})' title=\"$track->title\">$track->title</a>");
            $tpl->set('{cover}', $track->cover);
            $tpl->set('{description}', "<a onclick='load_template(\"profile\", {id: $track->author_id})' title=\"$track->display_name's profile\">$track->display_name</a>");
            $tpl->set('{path}', $track->path);
            $tpl->set('{script}', 'onclick="load_template(\'track\', {name: \'' . $track->path . '\'})"');
            $tpl->set('{button_show}', 'true');
            $tpl->set('{block_title}', "Go to track page");

            $tpl->compile('search_item');

            $tpl->clear();
        }
    }

    // поиск авторов
    $db_response = $pdo->prepare("SELECT users.id, users.display_name, users.avatar FROM users WHERE users.display_name LIKE :search OR users.name LIKE :search OR users.lastname LIKE :search");
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    $db_response->execute([':search' => '%' . $search . '%']);

    while ($author = $db_response->fetch()) {
        if (isset($author->id)) {
            $tpl->load_template('elements/search/site_search.tpl');

            $tpl->set('{id}', $author->id);
            $tpl->set('{title}', "<a onclick='load_template(\"profile\", {id: $author->id})' title=\"$author->display_name's profile\">$author->display_name's profile</a>");
            $tpl->set('{cover}', $author->avatar);
            $tpl->set('{description}', '');
            $tpl->set('{button_show}', 'false');
            $tpl->set('{block_title}', 'Go to profile page');
            $tpl->set('{script}', '');

            $tpl->compile('search_item');

            $tpl->clear();
        }
    }

    // поиск плейлистов
    $db_response = $pdo->prepare("SELECT users__playlists.id, users__playlists.name, users__playlists.cover, users.id as author_id, users.display_name FROM users__playlists LEFT JOIN users on users__playlists.user_id = users.id WHERE users__playlists.name LIKE :search OR users.login LIKE :search OR users.name LIKE :search OR users.lastname LIKE :search");
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    $db_response->execute([':search' => '%' . $search . '%']);

    while ($playlist = $db_response->fetch()) {
        if (isset($playlist->id)) {
            $tpl->load_template('elements/search/site_search.tpl');

            $tpl->set('{id}', $playlist->id);
            $tpl->set('{title}', $playlist->name);
            $tpl->set('{cover}', $playlist->cover);
            $tpl->set('{description}', "<a onclick='load_template(\"profile\", {id: $playlist->author_id})' title=\"$playlist->display_name's profile\">$playlist->display_name's playlists</a>");
            $tpl->set('{button_show}', "false");
            $tpl->set('{block_title}', "Go to playlist page");
            $tpl->set('{script}', "");

            $tpl->compile('search_item');

            $tpl->clear();
        }
    }

    if ($tpl->result['search_item'] == '') {
        $tpl->load_template('elements/search/site_search_no_result.tpl');
        $tpl->compile('search_item');
        $tpl->clear();
    }

    if ($search == '') {
        $tpl->result['search_item'] = '';
        $tpl->load_template('elements/search/site_search_empty.tpl');
        $tpl->compile('search_item');
        $tpl->clear();
    }

    $tpl->show($tpl->result['search_item']);
    $tpl->global_clear();

    exit();
}

if (isset($_POST['load_track_comment'])) {
    $track_id = check_js($_POST['track_id']);

    tpl()->result['comments_data'] = '';

    $db_response = pdo()->prepare("SELECT users.id AS author_id, users.avatar, users.display_name, users__tracks_comments.id, users__tracks_comments.comment, users__tracks_comments.date FROM users__tracks_comments LEFT JOIN users ON users__tracks_comments.user_id = users.id WHERE users__tracks_comments.track_id = :track_id ORDER BY users__tracks_comments.date DESC");
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    $db_response->execute([':track_id' => $track_id]);
    while ($comment = $db_response->fetch()) {
        tpl()->load_template('elements/tracks/comments.tpl');
        tpl()->set('{is_auth}', isset($_SESSION['id']) ? 'true' : 'false');
        tpl()->set('{id}', $comment->id);
        tpl()->set('{avatar}', $comment->avatar);
        tpl()->set('{author}', $comment->display_name);
        tpl()->set('{author_id}', $comment->author_id);
        tpl()->set('{text}', $comment->comment);
        tpl()->set('{date}', expand_date($comment->date, 6));
        tpl()->compile('comments_data');
        tpl()->clear();
    }

    if (tpl()->result['comments_data'] == '') {
        tpl()->result['comments_data'] = '<div class="text-muted" style="margin: 10px">Nobody commented on the track. Be the first!</div>';
    }

    exit(json_encode(['status' => 'success', 'data' => tpl()->getShow(tpl()->result['comments_data']), 'count' => $db_response->rowCount()]));
}

if (isset($_POST['load_tracks_likes'])) {

    $id = check_js($_POST['id'], 'int');

    tpl()->result['likes_data'] = '';
    // получить аватарки пользователей, которые лайкнули трек
    $db_response = pdo()->prepare("SELECT users.id, users.display_name, users.avatar FROM users__favorite_actions LEFT JOIN users ON users.id = users__favorite_actions.user_id WHERE users__favorite_actions.track_id = :track_id AND users__favorite_actions.user_id IS NOT NULL GROUP BY users__favorite_actions.id DESC LIMIT 10");
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    $db_response->execute([':track_id' => $id]);

    // выборка данных из запроса
    while ($playlists = $db_response->fetch()) {

        // загрузка шаблона плейлиста
        tpl()->load_template('elements/tracks/likes.tpl');

        // передача переменных в шаблон
        tpl()->set("{id}", $playlists->id);
        tpl()->set("{cover}", $playlists->avatar);
        tpl()->set("{display_name}", $playlists->display_name);

        // компиляция шаблона
        tpl()->compile('likes_data');

        // очистка переменных
        tpl()->clear();
    }

    if (tpl()->result['likes_data'] == '') {
        tpl()->result['likes_data'] = '<div style="margin: 0 auto; color: var(--gray-color)">No one liked track.</div>';
    }

    exit(json_encode(['status' => 'success', 'data' => tpl()->getShow(tpl()->result['likes_data'])]));
}

if (isset($_POST['load_playlist_likes'])) {

    $id = check_js($_POST['id'], 'int');

    tpl()->result['likes_data'] = '';

    $db_response = pdo()->prepare("SELECT users.id, users.display_name, users.avatar FROM users__favorite_actions LEFT JOIN users ON users.id = users__favorite_actions.user_id WHERE users__favorite_actions.playlist_id = :playlist_id AND users__favorite_actions.user_id IS NOT NULL GROUP BY users__favorite_actions.id DESC LIMIT 10");
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    $db_response->execute([':playlist_id' => $id]);

    // выборка данных из запроса
    while ($playlists = $db_response->fetch()) {

        // загрузка шаблона плейлиста
        tpl()->load_template('elements/tracks/likes.tpl');

        // передача переменных в шаблон
        tpl()->set("{id}", $playlists->id);
        tpl()->set("{cover}", $playlists->avatar);
        tpl()->set("{display_name}", $playlists->display_name);

        // компиляция шаблона
        tpl()->compile('likes_data');

        // очистка переменных
        tpl()->clear();
    }

    if (tpl()->result['likes_data'] == '') {
        tpl()->result['likes_data'] = '<div style="margin: 0 auto; color: var(--gray-color)">No one liked playlist.</div>';
    }

    exit(json_encode(['status' => 'success', 'data' => tpl()->getShow(tpl()->result['likes_data'])]));
}

if (isset($_POST['load_playlist_reposts'])) {

    $id = check_js($_POST['id'], 'int');

    tpl()->result['likes_data'] = '';

    $db_response = pdo()->prepare("SELECT users.id, users.display_name, users.avatar FROM users__reposts_actions LEFT JOIN users ON users.id = users__reposts_actions.user_id WHERE users__reposts_actions.playlist_id = :playlist_id AND users__reposts_actions.user_id IS NOT NULL GROUP BY users__reposts_actions.id DESC LIMIT 10");
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    $db_response->execute([':playlist_id' => $id]);

    // выборка данных из запроса
    while ($playlists = $db_response->fetch()) {

        // загрузка шаблона плейлиста
        tpl()->load_template('elements/tracks/likes.tpl');

        // передача переменных в шаблон
        tpl()->set("{id}", $playlists->id);
        tpl()->set("{cover}", $playlists->avatar);
        tpl()->set("{display_name}", $playlists->display_name);

        // компиляция шаблона
        tpl()->compile('likes_data');

        // очистка переменных
        tpl()->clear();
    }

    if (tpl()->result['likes_data'] == '') {
        tpl()->result['likes_data'] = '<div style="text-align: center; color: var(--gray-color)">No one reposts playlist.</div>';
    }

    exit(json_encode(['status' => 'success', 'data' => tpl()->getShow(tpl()->result['likes_data'])]));
}

if (isset($_POST['load_other_users_playlists'])) {

    $user_id = check_js($_POST['user_id'], 'int');

    tpl()->result['other_playlists'] = '';

    $db_response = pdo()->prepare("SELECT users__playlists.id, users__playlists.cover, users__playlists.name, users__playlists.likes, users__playlists.reposts, users.id AS author_id, users.display_name FROM users__playlists LEFT JOIN users ON users.id = users__playlists.user_id WHERE user_id = :user_id ORDER BY users__playlists.date_add LIMIT 3");
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    $db_response->execute([':user_id' => $user_id]);

    // выборка данных из запроса
    while ($playlists = $db_response->fetch()) {

        // загрузка шаблона плейлиста
        tpl()->load_template('elements/playlists/list/other_users_playlist.tpl');

        // передача переменных в шаблон
        tpl()->set("{cover}", $playlists->cover);
        tpl()->set("{name}", $playlists->name);
        tpl()->set("{author_id}", $playlists->author_id);
        tpl()->set("{author}", $playlists->display_name);
        tpl()->set("{likes}", $playlists->likes);
        tpl()->set("{reposts}", $playlists->reposts);

        // компиляция шаблона
        tpl()->compile('other_playlists');

        // очистка переменных
        tpl()->clear();
    }

    if (tpl()->result['other_playlists'] == '') {
        tpl()->result['other_playlists'] = "<div style='text-align: center; color: var(--gray-color)'>The user hasn't created any playlists <yet class=''></yet></div>";
    }

    exit(json_encode(['status' => 'success', 'data' => tpl()->getShow(tpl()->result['other_playlists'])]));
}

if (isset($_POST['get_playlists_tracks'])) {
    $id = check_js($_POST['id'], 'int');

    $result_array = [];

    $db_response = pdo()->prepare('SELECT tracks.path FROM users__playlists_tracks LEFT JOIN tracks ON tracks.id = users__playlists_tracks.track_id WHERE users__playlists_tracks.playlist_id = :playlist_id');
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    $db_response->execute([':playlist_id' => $id]);

    if ($db_response->rowCount() > 0) {
        while ($tracks = $db_response->fetch()) {
            $result_array[] = $tracks->path;
        }

        exit(json_encode(['status' => 'success', 'data' => $result_array]));
    }

    exit(json_encode(['status' => 'error', 'data' => 'No tracks in this playlist']));
}