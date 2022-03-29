<?PHP

/*
 * $safe_mode = 1 - безопасный режим
 * $safe_mode = 2 - небезопасный режим
 */

/*
 * $dev_mode = 1 - режим разработки
 * $dev_mode = 2 - режим продакшена
 */

// безопасность
$safe_mode = 2;
$dev_mode = $conf->developer_mode ?? 2;

//ini_set('display_errors', ($dev_mode == 1 && $safe_mode != 1) ? 1 : 0);
// отображение ошибок
ini_set('display_errors', E_ALL);
ini_set('display_startup_errors', E_ALL);
error_reporting(E_ALL);
date_default_timezone_set($conf->time_zone ?? 'Europe/Moscow');

// получение глобальной переменных
global $protection;

// проверка на предмет включения безопасного режима
if ($protection == 1) {

    // установка безопасного режима
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
}

// получение протокола сайта
if (empty($conf->protocol) || $conf->protocol == 1) {
    $protocol = $_SERVER['HTTP_SCHEME'] ?? (((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')|| 443 == $_SERVER['SERVER_PORT']) ? 'https' : 'http');
} elseif ($conf->protocol == 2) {
    $protocol = 'http';
} elseif ($conf->protocol == 3) {
    $protocol = 'https';
}

$inactive_time = 900;
$host = $_SERVER['SERVER_NAME'];
$site_host = '../';
$full_site_host = $protocol . '://' . $host . '/';