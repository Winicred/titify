<?php
if (!isset($protection)) {
    $protection = 1;
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/config_additional.php';

require_once __DIR__ . '/classes/class.authapi.php';
require_once __DIR__ . '/classes/class.phpmailer.php';
require_once __DIR__ . '/classes/class.phpmailer_exception.php';
require_once __DIR__ . '/classes/class.users.php';
require_once __DIR__ . '/classes/class.sessionscookies.php';
require_once __DIR__ . '/classes/class.template.php';
require_once __DIR__ . '/classes/class.getdata.php';

$database = new Database();
$pdo = $database->connect();
$config = $database->get_config_data();

$session_cookies = new SessionsCookies($config->salt, $host);
$token = $session_cookies->set_token();

$auth_api = new AuthApi();
$auth_api_info = $auth_api->auth_api_info($pdo);

// инициализация класса для получения данных
$get_data = new GetData($pdo);

$tpl = new Template();
$tpl->dir = '../templates/standart/tpl/';

if (empty($_SERVER['HTTP_USER_AGENT'])) {
    $_SERVER['HTTP_USER_AGENT'] = 'undefined';
}

if (isset($_COOKIE['cache'])) {
    $_SESSION['cache'] = clean($_COOKIE['cache']);
} else {
    $session_cookies->clean_user_session();
}

if (isset($_SESSION['admin']) && isset($_SESSION['admin_cache'])) {
    if ($config->ip_protect == 1) {
        $session_cookies->admin_ip = get_ip();
    }

    $_SESSION['dev_mode'] = $dev_mode;

    if ($_SESSION['admin_cache'] != $session_cookies->get_admin_cache($config->password)) {
        $session_cookies->clean_admin_session();
    }
} else {
    $session_cookies->clean_admin_session();
}

$users_groups = get_groups($pdo);

$user_obj = new Users($pdo);
if (is_auth()) {
    $user = Users::get_user_data($pdo, $_SESSION['id']);

    if ($user->protect == 1) {
        $session_cookies->ip = get_ip();
    }

    $_SESSION['rights'] = $user->rights;

    if (is_worthy("x")) {
        $ban = true;
        require_once 'modules/exit/index.php';
    }
}
