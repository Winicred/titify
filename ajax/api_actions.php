<?php

// подключение файла start.php
include_once '../inc/start.php';

$auth_api = new AuthApi;
$auth_api_info = $auth_api->auth_api_info($pdo);

/* Facebook
=========================================*/
if (isset($_POST['get_fb_auth_link'])) {
    $url = '#';

    if ($auth_api_info->fb_api == 1) {
        $pages_urls = $auth_api->redirect_page($pdo);

        $params = [
            'client_id' => $auth_api_info->fb_id,
            'redirect_uri' => $full_site_host . "login?fb_auth=1",
            'response_type' => 'code'
        ];

        $url = str_replace(
            "&amp;",
            "&",
            'https://www.facebook.com/dialog/oauth?' . urldecode(http_build_query($params))
        );
    }

    exit(json_encode(['url' => $url]));
}

if (isset($_POST['attach_user_fb']) && is_auth()) {
    $url = '#';

    if ($auth_api_info->fb_api == 1) {
        $pages_urls = $auth_api->redirect_page($pdo);
        $auth_api->setAttachCache($pdo);

        $params = [
            'client_id' => $auth_api_info->fb_id,
            'redirect_uri' => $full_site_host . $pages_urls['settings'] . "?fb_attach=1&state=" . md5($session_cookies->set_token()),
            'response_type' => 'code'
        ];

        $url = str_replace(
            "&amp;",
            "&",
            'https://www.facebook.com/dialog/oauth?' . urldecode(http_build_query($params))
        );
    }

    exit(json_encode(['url' => $url]));
}

/* Регистрация пользователя через API
=========================================*/
if (isset($_POST['reg_by_api'])) {

    $email = check_js($_POST['email']);
    $type = check_js($_POST['type']);

    if (empty($email)) {
        exit(json_encode(['data' => '<p class="text-danger">Введите e-mail!</p>']));
    }

    $user = new Users($pdo);

    if (!$user->check_email($email)) {
        exit(json_encode(['data' => '<p class="text-danger">Неверно введен е-mail!</p>']));
    }
    if (!$user->check_email_busyness($email)) {
        exit(json_encode(['data' => '<p class="text-danger">Введеный Вами E-mail уже зарегистрирован!</p>']));
    }

    $pages_urls = $auth_api->redirect_page($pdo);

    if ($type == 'fb') {
        if ($auth_api_info->fb_api == 1) {

            $params = [
                'client_id' => $auth_api_info->fb_id,
                'redirect_uri' => $full_site_host . "login?fb_reg=1",
                'response_type' => 'code',
                'state' => $email
            ];

            $url = 'https://www.facebook.com/dialog/oauth?' . urldecode(http_build_query($params));
            $url = str_replace("&amp;", "&", $url);

            exit(
            json_encode(
                [
                    'data' => '<script>$("#api_reg_btn").fadeOut(0); document.location.href = "' . $url
                        . '";</script><p class="text-success">Если Вас не перенаправило на сайт Facebook автоматически, то нажмите на ссылку: <a href="'
                        . $url . '">перейти</a></p>'
                ]
            )
            );
        } else {
            exit(json_encode(['data' => '<p class="text-danger">Регистрация через Facebook недоступна!</p>']));
        }
    } else {
        exit(json_encode(['data' => '<p class="text-danger">Ошибка</p>']));
    }
}
