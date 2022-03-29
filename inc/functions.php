<?php

use PHPMailer\PHPMailer\PHPMailer;

function clean($variable, $param = null): array|float|string|null
{
    $variable = magic_quotes($variable);
    $variable = htmlspecialchars($variable, ENT_QUOTES);
    $variable = trim($variable);

    if ($param == "int") {
        $variable = preg_replace('/[^0-9]+/', '', $variable);
    }
    if ($param == "float") {
        $variable = str_replace(',', '.', $variable);
        $variable = preg_replace('/[^0-9.]/', '', $variable);
        $variable = (float)$variable;
        $variable = round($variable, 2);
    }
    return $variable;
}

function check($variable, $param): array|float|string|null
{
    if (isset($variable)) {
        $variable = clean($variable, $param);
        if ($variable == '') {
            unset($variable);
        }
    }
    return $variable ?? null;
}


function magic_quotes($data)
{
    $phpVersion = get_php_version();
    $phpVersion = $phpVersion[0] . $phpVersion[1] * 0.1;

    if ($phpVersion > 5 && $phpVersion < 8.1) {
        if (
            function_exists('get_magic_quotes_gpc')
            && get_magic_quotes_gpc()
        ) {
            $data = stripslashes($data);
        }
    }

    return $data;
}

function get_php_version(): array
{
    if (phpversion()) {
        $phpVersion = explode('.', phpversion());
    } else {
        $phpVersion = explode('.', PHP_VERSION);
    }

    return $phpVersion;
}

function send_noty($pdo, $message, $user_id, $status = 0)
{
    if ($user_id == 0) {
        $db_response = $pdo->query("SELECT admins_ids FROM config LIMIT 1");
        $db_response->setFetchMode(PDO::FETCH_OBJ);
        $row = $db_response->fetch();
        $user_ids = explode(",", $row->admins_ids);
        $ids_count = count($user_ids);
        for ($i = 0; $i < $ids_count; $i++) {
            $db_response = $pdo->prepare(
                "INSERT INTO notifications (message,date,user_id,status) values (:message, :date, :user_id, :status)"
            );
            $db_response->execute(
                [':message' => $message, ':date' => date("Y-m-d H:i:s"), ':user_id' => $user_ids[$i], ':status' => $status]
            );
        }
    } else {
        $db_response = $pdo->prepare(
            "INSERT INTO notifications (message,date,user_id,status) values (:message, :date, :user_id, :status)"
        );
        $db_response->execute([':message' => $message, ':date' => date("Y-m-d H:i:s"), ':user_id' => $user_id, ':status' => $status]);
    }
}

function get_ip(): float|array|string|null
{
    $serverVars = array("HTTP_X_FORWARDED_FOR",
        "HTTP_X_FORWARDED",
        "HTTP_FORWARDED_FOR",
        "HTTP_FORWARDED",
        "HTTP_VIA",
        "HTTP_X_COMING_FROM",
        "HTTP_COMING_FROM",
        "HTTP_CLIENT_IP",
        "HTTP_XROXY_CONNECTION",
        "HTTP_PROXY_CONNECTION",
        "HTTP_USERAGENT_VIA");
    foreach ($serverVars as $serverVar) {
        if (!empty($_SERVER) && !empty($_SERVER[$serverVar])) {
            $proxyIP = $_SERVER[$serverVar];
        } elseif (!empty($_ENV) && isset($_ENV[$serverVar])) {
            $proxyIP = $_ENV[$serverVar];
        } elseif (@getenv($serverVar)) {
            $proxyIP = getenv($serverVar);
        }
    }
    if (!empty($proxyIP)) {
        $isIP = preg_match('|^([0-9]{1,3}\.){3,3}[0-9]{1,3}|', $proxyIP, $regs);
        if (isset($regs[0])) {
            $long = ip2long($regs[0]);
            if ($isIP && (sizeof($regs) > 0) && $long != -1 && $long !== false) {
                if (filter_var($regs[0], FILTER_VALIDATE_IP)) {
                    return clean($regs[0], null);
                }
            }
        }
    }
    if (filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) {
        return clean($_SERVER['REMOTE_ADDR']);
    } else {
        return '127.0.0.1';
    }
}

function is_string_length_less($string, $length): bool
{
    if (mb_strlen($string, 'UTF-8') < $length) {
        return true;
    } else {
        return false;
    }
}

function is_string_length_more($string, $length): bool
{
    return !is_string_length_less($string, $length);
}

function clean_str($str): array|string|null
{
    return preg_replace('/[^a-zA-ZА-яёЁ0-9._ ]/ui', '', $str);
}

function clean_eng_str($str): array|string|null
{
    return preg_replace('/[^a-zA-Z0-9._ ]/ui', '', $str);
}

function create_pass($max, $type): ?string
{
    if ($type == 1) {
        $chars = "qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
    } elseif ($type == 2) {
        $chars = "1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
    }
    $size = StrLen($chars) - 1;
    $password = null;
    while ($max--)
        $password .= $chars[rand(0, $size)];

    return $password;
}

function is_worthy($access, $group = 0): bool
{
    global $users_groups;

    if ($group == 0 && array_key_exists('rights', $_SESSION)) {
        $group = $_SESSION['rights'];
    }

    if (strripos($users_groups[$group]['rights'], $access) !== false) {
        return true;
    }

    return false;
}

function get_groups($pdo): array
{
    $db_response = $pdo->query("SELECT * FROM users__groups");
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    while ($group = $db_response->fetch()) {
        $users_groups[$group->id]['name'] = $group->name;
        $users_groups[$group->id]['rights'] = $group->rights;
        $users_groups[$group->id]['id'] = $group->id;
    }

    return $users_groups;
}

function is_auth(): bool
{
    if (isset($_SESSION['id'])) {
        return true;
    } else {
        return false;
    }
}

function pdo()
{
    global $pdo;

    return empty($pdo) ? new stdClass() : $pdo;
}

function configs()
{
    global $config;

    return empty($config) ? new stdClass() : $config;
}

function config()
{
    global $config;

    return empty($config) ? new stdClass() : $config;
}

function tpl(): Template
{

    global $tpl;

    if (empty($tpl)) {
        $tpl = new Template;
    }

    return $tpl;
}

function page(): PagesInfo
{
    global $page_info;

    if (empty($page_info)) {
        include_once __DIR__ . '/classes/class.pagesinfo.php';

        $page_info = new PagesInfo(pdo());
    }

    return $page_info;
}

function users_groups(): array
{
    global $users_groups;

    if (empty($users_groups)) {
        $users_groups = get_groups(pdo());
    }

    return $users_groups;
}

function user()
{
    if (!is_auth()) {
        $user = null;
    } else {
        global $user;
    }

    return $user;
}

function file_get_contents_curl($url): bool|string
{
    $url = str_replace("&amp;", "&", $url);
    return @file_get_contents($url);
}

function find_img_mp3($text, $id, $not_img = 0)
{
    $ok = 0;
    $length = mb_strlen($text, 'UTF-8');
    if ($length > 17) {
        $col = substr_count($text, ' ');
        if ($col == 0) {
            $http = substr($text, 0, 7);
            if ($http == 'sticker') {
                if (!str_starts_with(substr($text, 7), 'files/stickers/')) {
                    $text = check($text, null);
                } else {
                    $text = '<img class="g_sticker" src="' . substr($text, 7) . '">';
                }
                $ok = 1;
            }
        }
        if ($ok != 1) {
            if (preg_match('#(http://[^\s]+(?=\.(mp3|mp4)))#i', $text)) {
                //$val = mt_rand(0, 100);
                $text = preg_replace('#(http://[^\s]+(?=\.(mp3|mp4)))(\.(mp3|mp4))#i', '<audio src="$1.$2" controls="controls">Аудио файл: $1.$2</audio>', $text);
            }
            if ($not_img == 0) {
                if (preg_match('#((http|https)://[^\s]+(?=\.(jpe?g|png|gif|bmp)))#i', $text)) {
                    $text = preg_replace_callback('#((http|https)://[^\s]+(?=\.(jpe?g|png|gif|bmp)))(\.(jpe?g|png|gif))#i', "check_img", $text);
                }
                $text = preg_replace("/(^|[\n ])([\w]*?)((ht|f)tp(s)?:\/\/[\w]+[^ ,\"\n\r\t<]*)/is", "$1$2<span class=\"m-icon icon-link\"></span><a href=\"$3\" target=\"_blank\" title=\"Мы не несем ответственности за ресурс, на который направлена ссылка\">$3</a>", $text);
                $text = preg_replace("/(^|[\n ])([\w]*?)((www|ftp)\.[^ ,\"\t\n\r<]*)/is", "$1$2<span class=\"m-icon icon-link\"></span><a href=\"http://$3\" target=\"_blank\" title=\"Мы не несем ответственности за ресурс, на который направлена ссылка\">$3</a>", $text);
                if (preg_match("/(http|https):\/\/(www.youtube|youtube|youtu)\.(be|com)\/([^<\s]*)/", $text, $match)) {
                    if (preg_match('/youtube\.com\/watch\?v=([^&?\/]+)/', $text, $id)) {
                        $values = $id[1];
                    } else if (preg_match('/youtube\.com\/embed\/([^&?\/]+)/', $text, $id)) {
                        $values = $id[1];
                    } else if (preg_match('/youtube\.com\/v\/([^&?\/]+)/', $text, $id)) {
                        $values = $id[1];
                    } else if (preg_match('/youtu\.be\/([^&?\/]+)/', $text, $id)) {
                        $values = $id[1];
                    } else if (preg_match('/youtube\.com\/verify_age\?next_url=\/watch%3Fv%3D([^&?\/]+)/', $text, $id)) {
                        $values = $id[1];
                    }
                    $text = '<iframe width="400" height="200" src="https://www.youtube.com/embed/' . $values . '" frameborder="0" allowfullscreen></iframe>';
                }
            }
        }
    }
    if ($ok != 1) {
        $smiles_key = array();
        for ($i = 0; $i < 63; $i++) {
            $j = $i + 1;
            if ($j < 10) {
                $j = "0" . $j;
            }
            $smiles_key[$i] = ":smile" . $j . ":";
        }
        for ($i = 1; $i <= count($smiles_key); $i++) {
            $smiles_value[$i] = "<img class='g_smile' src='files/smiles/" . $i . ".png'>";
        }
        $text = str_replace($smiles_key, $smiles_value, $text);
    }
    return $text;
}

function user_by_id($user_id = null)
{
    $user_id = clean($user_id, "int");

    if (empty($user_id)) {
        return null;
    }

    $db_response = pdo()->query("SELECT * FROM users WHERE id = '$user_id' LIMIT 1");

    if (!$db_response->rowCount()) {
        return null;
    }

    return $db_response->fetch(PDO::FETCH_OBJ);
}

function playlists_by_user_id($user_id = null)
{
    $user_id = clean($user_id, "int");

    if (empty($user_id)) {
        return null;
    }

    $db_response = pdo()->query("SELECT * FROM users__playlists WHERE user_id = '$user_id'");

    if (!$db_response->rowCount()) {
        return null;
    }

    return $db_response->fetch(PDO::FETCH_OBJ);
}

function get_playlist_by_id(int $id) {
    $id = clean($id, "int");

    if (empty($id)) {
        return null;
    }

    $db_response = pdo()->query("SELECT * FROM users__playlists WHERE id = '$id'");

    if (!$db_response->rowCount()) {
        return null;
    }

    return $db_response->fetch(PDO::FETCH_OBJ);
}

function get_playlist_tracks(int $id) {
    $id = clean($id, "int");

    if (empty($id)) {
        return null;
    }

    $db_response = pdo()->query("SELECT up.* FROM users__playlists_tracks LEFT JOIN tracks up on users__playlists_tracks.track_id = up.id WHERE users__playlists_tracks.playlist_id = '$id'");

    if (!$db_response->rowCount()) {
        return null;
    }

    return $db_response->fetchAll();
}

function convert_avatar($user_id = null, $trading = true): string
{
    $user_data = user_by_id($user_id);

    if (empty($user_data)) {
        return "/files/avatars/no_avatar.jpg";
    }

    return user_by_id($user_id)->avatar;
}

function show_error_page($error_type = '404')
{
    global $messages;

    if ($error_type == '404') {
        $_SESSION['error_msg'] = 'Page Not Found';
        foreach ($GLOBALS as $key => $val) {
            global $$key;
        }

        $page = page()->page_info('error_page');
        include_once $_SERVER["DOCUMENT_ROOT"] . "/modules/error/index.php";
        exit();
    } elseif ($error_type == 'not_auth') {
        $_SESSION['error_msg'] = 'Log in to access this page.';
    } elseif ($error_type == 'not_adm') {
        $_SESSION['error_msg'] = 'You do not have administrator rights.';
    } elseif ($error_type == 'not_allowed') {
        $_SESSION['error_msg'] = "You don't have enough rights.";
    } elseif ($error_type == 'not_settings') {
        $_SESSION['error_msg'] = 'You have entered a page without parameters.';
    } elseif ($error_type == 'wrong_url') {
        $_SESSION['error_msg'] = 'Invalid URL.';
    }

    http_response_code(403);
    header('Location: ../error_page');
    exit();
}

function get_month($i, $type = 1): string
{
    if ($type == 1) {
        $months = array(1 => 'january',
            2 => 'february',
            3 => 'march',
            4 => 'april',
            5 => 'may',
            6 => 'june',
            7 => 'july',
            8 => 'august',
            9 => 'septebmer',
            10 => 'october',
            11 => 'november',
            12 => 'december');
    } else {
        $months = array(1 => '01', 2 => '02', 3 => '03', 4 => '04', 5 => '05', 6 => '06', 7 => '07', 8 => '08', 9 => '09', 10 => '10', 11 => '11', 12 => '12');
    }
    return $months[$i];
}

function expand_date($date, $type = 1)
{
    if (clean($date, "int") == $date) {
        $time = $date;
    } else {
        $time = strtotime($date);
    }

    $month = get_month(date('n', $time), 1);
    $day = date('j', $time);
    $year = date('Y', $time);
    $hour = date('H', $time);
    $min = date('i', $time);

    if ($type == 0) {
        return "$hour:$min";
    }
    if ($type == 1) {
        return "$day $month $year y, $hour:$min";
    }
    if ($type == 2) {
        return "$day $month $year y";
    }
    if ($type == 3) {
        return "$day $month $year";
    }
    if ($type == 4) {
        if ($day < 10) {
            $day = "0" . $day;
        }
        $month = get_month(date('n', $time), 3);
        $year = substr($year, 2);
        return "$day.$month.$year";
    }
    if ($type == 5) {
        $dtnew['day'] = date('j', $time);
        $dtnew['year'] = date('Y', $time);
        $dtnew['hour'] = date('G', $time);
        $dtnew['min'] = date('i', $time);
        $dtnew['month'] = get_month(date('n', $time), 1);
        $dtnew['month2'] = get_month(date('n', $time), 2);
        $dtnew['month3'] = get_month(date('n', $time), 3);
        return $dtnew;
    }
    if ($type == 6) {
        return "$day $month at $hour:$min";
    }

    $yesterday = strtotime('yesterday');

    if ($type == 7) {
        $dif = time() - $time;
        if ($dif < 59) {
            if ($dif < 15) {
                return "right now";
            } else {
                return $dif . " seconds ago";
            }
        } elseif ($dif / 60 > 1 and $dif / 60 < 59) {
            return round($dif / 60) . " minutes ago";
        } elseif ($dif / 3600 > 1 and $dif / 3600 < 23) {
            return round($dif / 3600) . " hours ago";
        } elseif ($time > $yesterday && $time < ($yesterday + 24 * 3600)) {
            return "day ago at $hour:$min";
        } elseif ($time > ($yesterday - 24 * 3600) && $time < $yesterday) {
            return "2 days ago at $hour:$min";
        } else {
            return "$day $month $year y, $hour:$min";
        }
    }
    if ($type == 8) {
        $dtnew['short'] = "$hour:$min";
        $dtnew['full'] = "$day $month $year y";
        return $dtnew;
    }

    return $date;
}

function inc_notifications()
{
    include_once __DIR__ . '/notifications.php';
}

function send_mail($mail_to, $subject, $message, $pdo, $type = 0, $debug = 0)
{
    if ($type == 1 and $mail_to == 'none') {
        $db_response = $pdo->query("SELECT admins_ids FROM config LIMIT 1");
        $db_response->setFetchMode(PDO::FETCH_OBJ);
        $row = $db_response->fetch();
        $user_ids = explode(",", $row->admins_ids);
        $ids_count = count($user_ids);
        for ($i = 0; $i < $ids_count; $i++) {
            $db_response = $pdo->prepare("SELECT email, email_notice FROM users WHERE id=:id LIMIT 1");
            $db_response->setFetchMode(PDO::FETCH_OBJ);
            $db_response->execute(array(':id' => $user_ids[$i]));
            $row = $db_response->fetch();
            if ($row->email_notice == 1) {
                send_mail($row->email, $subject, $message, $pdo);
            }
        }
    } else {
        if (!str_starts_with($mail_to, 'vk_id_') && !empty($mail_to)) {
            $db_response = $pdo->query("SELECT * FROM config__email LIMIT 1");
            $db_response->setFetchMode(PDO::FETCH_OBJ);
            $email_conf = $db_response->fetch();
            $message = str_replace("\n", '<br>', $message);

            require_once 'classes/class.phpmailer.php';
            require_once 'classes/class.smtp.php';
            $mail = new PHPMailer(true);
            if ($email_conf->verify_peers == 2) {
                $mail->SMTPOptions = array('ssl' => array('verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true));
            }
            $mail->isSMTP();
            $mail->Host = gethostbyname($email_conf->host);
            $mail->Port = $email_conf->port;
            $mail->SMTPAuth = true;
            $mail->SMTPDebug = $debug;
            $mail->CharSet = $email_conf->charset;
            $mail->Username = $email_conf->username;
            $mail->Password = $email_conf->password;
            $mail->addReplyTo($email_conf->username, $email_conf->from_email);
            $mail->setFrom($email_conf->username, $email_conf->from_email);
            $mail->addAddress($mail_to);
            $mail->Subject = htmlspecialchars($subject);
            $mail->msgHTML($message);
            $mail->send();
        }
    }
}

function check_js($variable, $param = null): float|array|string|null
{
    if (isset($variable)) {
        $variable = clean($variable, $param);
        if (isset($variable) and $variable == '') {
            unset($variable);
        }
        if (isset($variable) and $variable == 'undefined') {
            unset($variable);
        }
    }

    return $variable ?? null;
}

function check_start($variable): float|array|int|string|null
{
    if (isset($variable)) {
        $variable = clean($variable, "int");
        if ($variable == '') {
            unset($variable);
        }
        if ($variable == "undefined") {
            $variable = 0;
        }
        return $variable;
    } else {
        return null;
    }
}

function get_user_status($id_user = null)
{
    if (empty($id_user)) {
        return null;
    }

    $db_response = pdo()->query("SELECT status_message FROM users WHERE id = $id_user");
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    $status = $db_response->fetch();

    return $status->status_message;
}

function generation_name($name = null): string
{
    if (empty($name)) {
        return md5(date("YmdHis") . time());
    }

    return (md5(date("YmdHis") . time() . $name) . '_' . $name);
}


function file_uploads($dir = null, $file = null): array|bool
{
    if (empty($dir) || empty($file)) {
        return ['alert' => 'error', 'message' => 'Не указаны параметры'];
    }

    if (0 < $file['error']) {
        return ['alert' => 'error', 'message' => 'Ошибка файла', 'code' => $file['error']];
    }

    $name = generation_name($file['name']);
    $full_dir = "$dir/$name";

    if (!move_uploaded_file($file['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/' . $full_dir)) {
        return false;
    }

    return ['alert' => 'success', 'name' => $name, 'full_dir' => $full_dir];
}

function get_user_avatar($uid = null): string
{
    $uid = clean($uid, "int");

    if (empty($uid)) {
        return "files/avatars/no_avatar.jpg";
    }

    return pdo()
        ->query("SELECT avatar FROM users WHERE id = '$uid'")
        ->fetch(PDO::FETCH_OBJ)
        ->avatar;
}

function get_user_cover($uid = null): string
{
    $uid = clean($uid, "int");

    if (empty($uid)) {
        return "files/covers/standart.jpg";
    }

    return pdo()
        ->query("SELECT cover FROM users WHERE id = '$uid'")
        ->fetch(PDO::FETCH_OBJ)
        ->cover;
}

function is_some_key_in_array_exists($keys, $array)
{
    if (!is_array($keys)) {
        $keys = [$keys];
    }

    foreach ($keys as $key) {
        if (array_key_exists($key, $array)) {
            return $key;
        }
    }

    return false;
}

function get_location($ip)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://www.geoplugin.net/json.gp?ip=" . $ip);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result);
}

function get_OS()
{
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    $os_platform = "Unknown OS Platform";

    $os_array = array(
        '/windows nt 10/i' => 'Windows 10',
        '/windows nt 6.3/i' => 'Windows 8.1',
        '/windows nt 6.2/i' => 'Windows 8',
        '/windows nt 6.1/i' => 'Windows 7',
        '/windows nt 6.0/i' => 'Windows Vista',
        '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
        '/windows nt 5.1/i' => 'Windows XP',
        '/windows xp/i' => 'Windows XP',
        '/windows nt 5.0/i' => 'Windows 2000',
        '/windows me/i' => 'Windows ME',
        '/win98/i' => 'Windows 98',
        '/win95/i' => 'Windows 95',
        '/win16/i' => 'Windows 3.11',
        '/macintosh|mac os x/i' => 'Mac OS X',
        '/mac_powerpc/i' => 'Mac OS 9',
        '/linux/i' => 'Linux',
        '/ubuntu/i' => 'Ubuntu',
        '/iphone/i' => 'iPhone',
        '/ipod/i' => 'iPod',
        '/ipad/i' => 'iPad',
        '/android/i' => 'Android',
        '/blackberry/i' => 'BlackBerry',
        '/webos/i' => 'Mobile'
    );

    foreach ($os_array as $regex => $value)
        if (preg_match($regex, $user_agent))
            $os_platform = $value;

    return $os_platform;
}

function get_user_browser()
{
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    $browser = "Unknown Browser";

    $browser_array = array(
        '/msie/i' => 'Internet Explorer',
        '/firefox/i' => 'Firefox',
        '/safari/i' => 'Safari',
        '/chrome/i' => 'Chrome',
        '/edge/i' => 'Edge',
        '/opera/i' => 'Opera',
        '/netscape/i' => 'Netscape',
        '/maxthon/i' => 'Maxthon',
        '/konqueror/i' => 'Konqueror',
        '/mobile/i' => 'Handheld Browser'
    );

    foreach ($browser_array as $regex => $value)
        if (preg_match($regex, $user_agent))
            $browser = $value;

    return $browser;
}

function strip_data($text): array|string|null
{
    $quotes = array("\x27", "\x22", "\x60", "\t", "\n", "\r", "%");
    $good_quotes = array("-", "+", "#");
    $rep_quotes = array("\-", "\+", "\#");
    $text = trim(strip_tags($text));
    $text = str_replace($quotes, '', $text);
    $text = str_replace($good_quotes, $rep_quotes, $text);
    return preg_replace("/ +/", " ", $text);
}

function get_welcome_message(): string
{
    $message = '';
    $time = date("H");

    if ($time >= 0 && $time < 6) {
        $message = 'Good night';
    } elseif ($time >= 6 && $time < 12) {
        $message = 'Good morning';
    } elseif ($time >= 12 && $time < 18) {
        $message = 'Good day';
    } elseif ($time >= 18 && $time < 24) {
        $message = 'Good evening';
    }

    return $message;
}

function get_track_by_src($src, $replace = false)
{
    if (empty($src)) {
        return ['alert' => 'error', 'message' => 'Не указаны параметры'];
    }

    if ($replace) {
        $src = str_replace('/files/tracks/', '', $src);
        $db_response = pdo()->query("SELECT * FROM tracks WHERE path = 'files/tracks/$src'");
    } else {
        $db_response = pdo()->query("SELECT * FROM tracks WHERE path = '$src'");
    }

    $db_response->setFetchMode(PDO::FETCH_OBJ);

    if ($db_response->rowCount() > 0) {
        return $db_response->fetch();
    } else {
        show_error_page();
    }

    return false;
}

function get_total_track_auditions_count_by_author($author_id)
{
    $author_id = clean($author_id, "int");

    if (empty($author_id)) {
        return ['alert' => 'error', 'message' => 'Parameter is empty'];
    }

    $auditions = 0;

// get total count of auditions
    $db_response = pdo()->query("SELECT auditions AS total FROM tracks WHERE author = '$author_id'");
    $db_response->setFetchMode(PDO::FETCH_OBJ);

    while ($total = $db_response->fetch()) {
        $auditions += $total->total;
    }

    return $auditions;
}

function get_total_track_count_by_author($author_id)
{
    $author_id = clean($author_id, "int");

    if (empty($author_id)) {
        return ['alert' => 'error', 'message' => 'Please specify author id'];
    }

    $db_response = pdo()->query("SELECT COUNT(*) AS total FROM tracks WHERE author = '$author_id'");
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    return $db_response->fetch()->total;
}

function get_author_by_track_id($track_id)
{
    $db_response = pdo()->query("SELECT users.* FROM tracks LEFT JOIN users ON users.id = tracks.author WHERE tracks.author = $track_id");
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    return $db_response->fetch();
}

function get_page_title($file_name)
{
    $path = explode('/', $file_name);

    $file_name = $path[count($path) - 3] . '/' . $path[count($path) - 2] . '/' . $path[count($path) - 1];

    $db_response = pdo()->query("SELECT title FROM pages WHERE file = '$file_name'");
    $db_response->setFetchMode(PDO::FETCH_OBJ);

    return $db_response->fetch()->title;
}

function track_by_id($id)
{
    $db_response = pdo()->query("SELECT * FROM tracks WHERE id = '$id'");
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    return $db_response->fetch();
}

function image_gradientrect($img, $x, $y, $x1, $y1, $start, $end): bool
{
    if ($x > $x1 || $y > $y1) {
        return false;
    }
    $s = array(
        hexdec(substr($start, 0, 2)),
        hexdec(substr($start, 2, 2)),
        hexdec(substr($start, 4, 2))
    );
    $e = array(
        hexdec(substr($end, 0, 2)),
        hexdec(substr($end, 2, 2)),
        hexdec(substr($end, 4, 2))
    );

    if (get_rand_number_result()) {
        $steps = $y1 - $y;
    } else {
        $steps = $x1 - $x;
    }

    for ($i = 0; $i < $steps; $i++) {
        $r = $s[0] - ((($s[0] - $e[0]) / $steps) * $i);
        $g = $s[1] - ((($s[1] - $e[1]) / $steps) * $i);
        $b = $s[2] - ((($s[2] - $e[2]) / $steps) * $i);
        $color = imagecolorallocate($img, intval($r), intval($g), intval($b));
        imagefilledrectangle($img, $x, $y + $i, $x1, $y + $i + 1, $color);
    }

    return true;
}

function rand_color($hash_need = false): string
{

    if ($hash_need) {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }

    return str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
}

function rand_int($min = 0, $max = 100): int
{
    return mt_rand($min, $max);
}

function get_rand_number_result(): bool
{
    $rand_number = rand_int();

    if ($rand_number > ($rand_number / 2)) {
        return true;
    } else {
        return false;
    }
}

function tracks_by_history(int $user_id) {
    if (empty($user_id)) {
        return ['alert' => 'error', 'message' => 'Please specify user id'];
    }

    $db_response = pdo()->prepare("SELECT id FROM track__history WHERE user_id = :user_id");
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    $db_response->execute([':user_id' => $user_id]);
    return $db_response->fetchAll();
}
