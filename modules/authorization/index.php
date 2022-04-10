<?php

$pages_urls = $auth_api->redirect_page(pdo());

if (!is_auth()) {
    $user = new Users($pdo);
    $config_mess = '';
    if (isset($_GET['id']) and isset($_GET['key'])) {
        $id = clean($_GET['id'], "int");
        $code = clean($_GET['key']);

        $user = Users::get_user_data($pdo, $id);
        if (empty($user->id)) {
            $config_mess = '<p class="text-danger">Аккаунт не найден!</p>';
        } else {
            if ($user->active == 1) {
                $config_mess = '<p class="text-success">Аккаунт уже активирован!</p>';
            } elseif ($code == $user->convert_password($user->login, $config->salt)) {
                $db_response = $pdo->prepare("UPDATE users SET active=:active WHERE id='$id' LIMIT 1");
                $db_response->execute(['active' => '1']);

                $user->auth_user(
                    $session_cookies,
                    $user->protect,
                    $user->password,
                    $user->login,
                    $user->id,
                    $user->rights,
                    $user->multi_account
                );
                header('Location: ' . $pages_urls['main']);
                exit();
            } else {
                $config_mess = '<p class="text-danger">Произошла ошибка!</p>';
            }
        }
    }

    $reg = 0;
    $auth = 0;
//    $user_info['vk_api'] = 0;
    $user_info['fb_api'] = 0;
//
    $user_info['fb'] = 0;
//
//    if ($auth_api_info->vk_api == 1) {
//        if (isset($_GET['code']) && empty($_GET['fb_reg']) && empty($_GET['fb_auth'])) {
//            $auth_api->redirect_fix('vk');
//            $result = false;
//            $params = [
//                'client_id' => $auth_api_info->vk_id,
//                'client_secret' => $auth_api_info->vk_key,
//                'code' => $_GET['code'],
//                'redirect_uri' => $full_site_host . $pages_urls['main'],
//                'v' => configs()->vk_api_version
//            ];
//
//            $vk_token = json_decode(
//                file_get_contents_curl(
//                    str_replace(
//                        "&amp;",
//                        "&",
//                        'https://oauth.vk.com/access_token?' . urldecode(http_build_query($params))
//                    )
//                ),
//                true
//            );
//
//            if (isset($vk_token['access_token'])) {
//                $params = [
//                    'user_id' => $vk_token['user_id'],
//                    'fields' => 'id,first_name,last_name,photo_max,has_photo,bdate',
//                    'access_token' => $vk_token['access_token'],
//                    'v' => configs()->vk_api_version
//                ];
//
//                $user_info = json_decode(
//                    file_get_contents_curl(
//                        str_replace(
//                            "&amp;",
//                            "&",
//                            'https://api.vk.com/method/users.get?' . urldecode(http_build_query($params))
//                        )
//                    ),
//                    true
//                );
//                if (isset($user_info['response'][0]['id'])) {
//                    $user_info = $user_info['response'][0];
//                    $result = true;
//                }
//            }
//
//            if ($result) {
//                $user_info['vk_api'] = $user_info['id'];
//                if ($_GET['state'] == 'login') {
//                    $auth = 1;
//                } else {
//                    if (isset($_GET['state'])) {
//                        $user_info['email'] = check($_GET['state'], null);
//                    }
//
//                    if (empty($user_info['email'])) {
//                        $config_mess = '<p class="text-danger">Почта не может быть пустой!</p>';
//                    } elseif (!$user->check_email($user_info['email'])) {
//                        $config_mess = '<p class="text-danger">Невалидная почта!</p>';
//                    } else {
//                        $user_info['vk'] = "id" . $user_info['id'];
//                        $user_info['password'] = 'none';
//                        $user_info['login'] = clean($user_info['first_name'] . " " . $user_info['last_name']);
//                        if ($user_info['has_photo'] == 1) {
//                            $user_info['avatar'] = $user_info['photo_max'];
//                        } else {
//                            $user_info['avatar'] = "files/avatars/no_avatar.jpg";
//                        }
//
//                        $reg = 1;
//                    }
//                }
//            } else {
//                $config_mess = '<p class="text-danger">Произошла ошибка</p>';
//            }
//        }
//    }
    if ($auth_api_info->fb_api == 1) {
        if (isset($_GET['code']) && ((isset($_GET['fb_reg']) || isset($_GET['fb_auth'])))) {

//            $auth_api->redirect_fix('fb');
//            $result = false;
//            $params = [
//                'client_id' => $auth_api_info->fb_id,
//                'client_secret' => $auth_api_info->fb_key,
//                'code' => $_GET['code'],
//                'redirect_uri' => $full_site_host . $pages_urls['main'],
//            ];
//
//            $fb_token = json_decode(
//                file_get_contents_curl(
//                    str_replace(
//                        "&amp;",
//                        "&",
//                        'https://graph.facebook.com/v' . configs()->fb_api_version . '/oauth/access_token?' . urldecode(http_build_query($params))
//                    )
//                ),
//                true
//            );
            // facebook oauth authorize

            //oauth/authorize facebook url

            $auth_api->redirect_fix('fb');

            $params = [
                'client_id' => $auth_api_info->fb_id,
                'redirect_uri' => $full_site_host . $pages_urls['main'],
                'response_type' => 'code',
                'scope' => 'email'
            ];

            $fb_token = json_decode(
                file_get_contents_curl(
                    str_replace(
                        "&amp;",
                        "&",
                        'https://graph.facebook.com/oauth/authorize?' . urldecode(http_build_query($params))
                    )
                ),
                true
            );

            $result = false;

            if (isset($_GET['fb_reg'])) {
                $method = 'fb_reg';
            } else {
                $method = 'fb_auth';
            }

            $params = [
                'client_id' => $auth_api_info->fb_id,
                'client_secret' => $auth_api_info->fb_key,
                'redirect_uri' => $full_site_host . 'login?' . $method . '=1',
                'code' => $_GET['code'],
            ];

            $fb_token = json_decode(
                file_get_contents_curl(
                    str_replace(
                        "&amp;",
                        "&",
                        'https://graph.facebook.com/oauth/access_token?' . urldecode(http_build_query($params))
                    )
                ),
                true
            );

            if (isset($fb_token['access_token'])) {
                $params = [
                    'access_token' => $fb_token['access_token'],
                    'fields' => 'id,name,email,picture.width(150).height(150)'
                ];
                $user_info = json_decode(
                    file_get_contents_curl(
                        str_replace(
                            "&amp;",
                            "&",
                            'https://graph.facebook.com/me?' . urldecode(http_build_query($params))
                        )
                    ),
                    true
                );
                if (isset($user_info['id'])) {
                    $user_info = $user_info;
                    $result = true;
                }
            }

//            $params = [
//                'client_id' => $auth_api_info->fb_id,
//                'client_secret' => $auth_api_info->fb_key,
//                'code' => $_GET['code'],
//                'redirect_uri' => $full_site_host . "login?" . $method . "=1",
//            ];
//
//            echo var_dump($params);
//
//            $fb_token = json_decode(
//                file_get_contents_curl(
//                    str_replace(
//                        "&amp;",
//                        "&",
//                        'https://graph.facebook.com/oauth/access_token?' . urldecode(http_build_query($params))
//                    )
//                ),
//                true
//            );
//
//            echo var_dump($fb_token);
//
//            if (isset($fb_token['access_token'])) {
//                $user_info = json_decode(
//                    file_get_contents_curl(
//                        'https://graph.facebook.com/me?access_token=' . $fb_token['access_token'] . '&fields=id,name,email,first_name,last_name,picture.type(large)'
//                    ),
//                    true
//                );
//                if (isset($user_info['id'])) {
//                    $user_info = $user_info;
//                    $result = true;
//                }
//            }
//
//            echo var_dump($user_info);



//            $params = [
//                'client_id' => $auth_api_info->fb_id,
//                'redirect_uri' => $full_site_host . "login?" . $method . "=1",
//                'client_secret' => $auth_api_info->fb_key,
//                'code' => $_GET['code']
//            ];

//            $fb_token = json_decode(
//                file_get_contents_curl(
//                    str_replace(
//                        "&amp;",
//                        "&",
//                        'https://graph.facebook.com/oauth/access_token?' . urldecode(http_build_query($params))
//                    )
//                ),
//                true
//            );

//            if (isset($fb_token['access_token'])) {
//                $params = [
//                    'access_token' => $fb_token['access_token'],
//                    'fields' => 'id,name,email,first_name,last_name,picture.type(large)'
//                ];
//
//                $user_info = json_decode(
//                    file_get_contents_curl(
//                        'https://graph.facebook.com/me?' . urldecode(http_build_query($params))
//                    ),
//                    true
//                );
//
//                $result = true;
//            }

//            if (isset($fb_token['access_token'])) {
//                $params = ['access_token' => $fb_token['access_token']];
//                $user_info = json_decode(
//                    file_get_contents_curl(
//                        str_replace(
//                            "&amp;",
//                            "&",
//                            'https://graph.facebook.com/me?' . urldecode(http_build_query($params))
//                        )
//                    ),
//                    true
//                );
//
//                echo var_dump($user_info);
//
//                if (isset($user_info['id'])) {
//                    $result = true;
//                }
//            }

//            echo var_dump($fb_token);

            if ($result) {
                $user_info['fb_api'] = $user_info['id'];
                if (isset($_GET['fb_auth'])) {
                    $auth = 1;
                } else {
                    if (isset($_GET['state'])) {
                        $user_info['email'] = check($_GET['state'], null);
                    }

                    if (empty($user_info['email'])) {
                        $config_mess = '<p class="text-danger">Почта не может быть пустой!</p>';
                    } elseif (!$user->check_email($user_info['email'])) {
                        $config_mess = '<p class="text-danger">Невалидная почта!</p>';
                    } else {
                        $user_info['fb_api'] = $user_info['id'];
                        $user_info['password'] = 'none';
                        $user_info['login'] = clean($user_info['name'] . " " . $user_info['lastname']);
                        $user_info['avatar'] = 'https://graph.facebook.com/' . $user_info['id'] . '/picture?type=large';

                        $reg = 1;
                    }
                }
            } else {
                $config_mess = '<p class="text-danger">Произошла ошибка</p>';
            }
        }
    }

    if ($reg == 1) {
        if ($user_info['fb_api'] != 0) {
            $db_response = $pdo->prepare("SELECT id FROM users WHERE fb_api = :fb_api LIMIT 1");
            $db_response->setFetchMode(PDO::FETCH_OBJ);
            $db_response->execute([':fb_api' => $user_info['fb_api']]);
        }

        $row = $db_response->fetch();
        if (isset($row->id)) {
            $auth = 1;
        } else {
            $db_response = $pdo->prepare("SELECT id FROM users WHERE login=:login LIMIT 1");
            $db_response->setFetchMode(PDO::FETCH_OBJ);
            $db_response->execute([':login' => $user_info['login']]);
            $row = $db_response->fetch();
            if (isset($row->id)) {
                $user_info['login'] = $auth_api->generate_login_str($pdo, $user_info['login']);
            }

            if (!$user->check_email_busyness($user_info['email'])) {
                $config_mess = '<p class="text-danger">Данная почта уже зарегистрирована!</p>';
            } else {
                $user_info['regdate'] = date("Y-m-d H:i:s");
                if (
                    isset($user_info['bdate'])
                    && !empty($user_info['bdate'])
                    && preg_match(
                        "/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4}$/",
                        $user_info['bdate']
                    )
                ) {
                    $birth = explode('.', $user_info['bdate']);
                }
                if (isset($birth)) {
                    $birth[0] = (int)$birth[0];
                    $birth[1] = (int)$birth[1];

                    if ($birth[0] < 10) {
                        $birth[0] = '0' . $birth[0];
                    }
                    if ($birth[1] < 10) {
                        $birth[1] = '0' . $birth[1];
                    }
                    $user_info['birth'] = $birth[2] . '-' . $birth[1] . '-' . $birth[0];
                } else {
                    $user_info['birth'] = null;
                }

                if ($user_info['avatar'] == 'http://vk.com/images/camera_b.gif') {
                    $user_info['avatar'] = "files/avatars/no_avatar.jpg";
                } else {
                    $date = time();
                    $file = file_get_contents($user_info['avatar']);
                    file_put_contents('files/avatars/' . $date . '.jpg', $file);
                    $user_info['avatar'] = 'files/avatars/' . $date . '.jpg';
                }

                $user_data = $user->entry_user(
                    $user_info['login'],
                    $user_info['password'],
                    $user_info['email'],
                    $config->conf_us,
                    $user_info['name'],
                    $user_info['lastname'],
                    $user_info['fb'],
                    $user_info['fb_api'],
                );

                if (!empty($user_data->id)) {
                    $answer = $user->after_registration_actions(
                        $session_cookies,
                        $config->salt,
                        $config->name,
                        $user_data->id,
                        $full_site_host . $pages_urls['main']
                    );

                    if ($answer['message'] != 'error') {
                        $config_mess = '<p class="text-success">' . $answer['message'] . '</p>';
                        send_mail($user_info['email'], $answer['letter']['subject'], $answer['letter']['message'], $pdo);
                    } else {
                        $config_mess = '<p class="text-danger">Произошла ошибка!</p>';
                    }

                    if (isset($_SESSION['id'])) {
                        header('Location: ' . $pages_urls['main']);
                        exit();
                    }
                } else {
                    $config_mess = '<p class="text-danger">Вы не авторизированны!</p>';
                }
            }
        }
    }

    if ($auth == 1) {
        if ($user_info['fb_api'] != 0) {
            $db_response = $pdo->prepare("SELECT id,password,login,rights,active,protect,multi_account FROM users WHERE fb_api = :fb_api LIMIT 1");
            $db_response->setFetchMode(PDO::FETCH_OBJ);
            $db_response->execute([':fb_api' => $user_info['fb_api']]);
            $row = $db_response->fetch();
        }

        if (isset($row->id)) {
            if ($row->active != 1) {
                $config_mess = '<p class="text-danger">Пожалуйста, активируйте аккаунт, инструкция выслана на Ваш E-mail!</p>';
            } else {
                $result = $user->auth_user(
                    $session_cookies,
                    $row->protect,
                    $row->password,
                    $row->login,
                    $row->id,
                    $row->rights
                );

                if ($result['status']) {
                    header('Location: ' . $pages_urls['main']);
                    exit();
                } else {
                    $config_mess = '<p class="text-danger">' . $result['response'] . '</p>';
                }
            }
        } else {
            $config_mess = '<p class="text-danger">Account not registered, please register first!</p>';
        }
    }

    $tpl->load_template('elements/title/title.tpl');
    $tpl->set("{title}", $page->title);
    $tpl->set("{name}", $config->name);
    $tpl->compile('title');
    $tpl->clear();

    include_once "inc/not_authorized.php";

    $tpl->load_template('authorization/index.tpl');
    $tpl->set("{site_host}", $full_site_host);
    $tpl->set("{message}", $config_mess);

    $tpl->compile('content');
    $tpl->clear();
} else {
    header('Location: ../');
}
