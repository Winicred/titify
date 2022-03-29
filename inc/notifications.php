<?php
/*======================== Шаблоны писем ========================*/

/* Письмо при регистрации
=========================================*/
function reg_letter($site_name, $login): array
{
    $letter['subject'] = "Registration for " . $site_name;
    $letter['message'] = "Hello <b>$login</b>Thank you for registering on <b>$site_name</b><br><br>Your login <b>$login</b>.<br><br>Regards,<br>$site_name Administration";
    return $letter;
}

function reg_letter_with_key($site_name, $login, $link): array
{
    $letter['subject'] = "Registration for " . $site_name;
    $letter['message'] = "Hello <b>$login</b>Thank you for registering on $site_name <br><br>Your login: <b>$login</b><br>Please activate your account by clicking on the <a href='$link'>link</a>.<br><br>Regards,<br>$site_name Administration";
    return $letter;
}


/* Письмо восстановления пароля
=========================================*/
function recovery_check_letter($site_name, $login, $link): array
{
    $letter['subject'] = "Password recovery on" . $site_name;
    $letter['message'] = "Hello, <b>$login</b><br> We have generated a link for you, by clicking on which you can <a href='$link'>reset your password</a>.<br><br>Regards,<br>$site_name Administration";
    return $letter;
}

function recovery_letter($site_name, $login, $password): array
{
    $letter['subject'] = "Password has been changed";
    $letter['message'] = "Hello <b>$login</b><br>We have generated a new login password for you.<br><br>Login: <b>$login</b><br>Password: <b>$password</b><br><br>Regards,<br>$site_name Administration";
    return $letter;
}

/* Письмо при авторизации по ip которого нет в базе
=========================================*/
function invalid_ip_address_letter($site_name, $login, $link, $ip): array
{
    $user_os = get_OS();
    $user_browser = get_user_browser();
    $params = get_location($ip);

    $letter['subject'] = "Login Attempted from New IP address";
    $letter['message'] = "
            Hello, <b>$login</b>.
            
            Your account was logged in from a different IP address.
            
            IP address: <b>$ip</b>
            Location: <b>$params->geoplugin_continentName, $params->geoplugin_countryName, $params->geoplugin_city</b>
            Browser: <b>$user_browser</b>
            OS: <b>$user_os</b>
            
            To reset your password, follow the <a href='$link'>link</a>.
            
            Regards, 
            $site_name Administration.";
    return $letter;
}



/*======================== Шаблоны уведомлений ========================*/
function new_comment_notification(): string
{
    return "New comment has been added to the article";
}

