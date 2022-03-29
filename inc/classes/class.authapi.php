<?php

class AuthApi
{
    function auth_api_info($pdo)
    {
        $db_response = $pdo->query("SELECT fb_api, fb_id, fb_key FROM config");
        $db_response->setFetchMode(PDO::FETCH_OBJ);
        return $db_response->fetch();
    }


    function generate_login_str($pdo, $login)
    {
        $i = 0;
        $user = $login;
        do {
            if ($i != 0) {
                $user = $login . '(' . $i . ')';
            }
            $db_response = $pdo->prepare("SELECT id FROM users WHERE login=:login LIMIT 1");
            $db_response->setFetchMode(PDO::FETCH_OBJ);
            $db_response->execute(array(':login' => $user));
            $row = $db_response->fetch();
            if (isset($row->id)) {
                $temp = null;
            } else {
                $temp = 1;
            }
            $i++;
        } while (empty($temp));
        return $user;
    }

    function redirect_fix($type)
    {
        if (!isset($_SESSION['reg_session_' . $type])) {
            $_SESSION['reg_session_' . $type] = 1;
        }
        if ($_SESSION['reg_session_' . $type] == 3) {
            unset($_SESSION['reg_session_' . $type]);
            header('Location: index');
            exit();
        }
        $_SESSION['reg_session_' . $type]++;
    }

    function redirect_page($pdo): array
    {
        $pages = array();
        $db_response = $pdo->query("SELECT url, name FROM pages WHERE name = 'main' OR name = 'settings'");
        $db_response->setFetchMode(PDO::FETCH_OBJ);
        while ($row = $db_response->fetch()) {
            if (empty($row->url)) {
                $row->url = "index";
            }
            $pages[$row->name] = $row->url;
        }

        return $pages;
    }

    function setAttachCache($pdo)
    {
        $db_response = $pdo->prepare("SELECT password FROM users WHERE id=:id LIMIT 1");
        $db_response->setFetchMode(PDO::FETCH_OBJ);
        $db_response->execute(array(':id' => $_SESSION['id']));
        $row = $db_response->fetch();

        $_SESSION['attachCache'] = md5($_SESSION['id'] . $row->password);
    }

    function isAttachCacheCorrect($password)
    {
        if (isset($_SESSION['attachCache']) && ($_SESSION['attachCache'] == md5($_SESSION['id'] . $password))) {
            return true;
        } else {
            return false;
        }
    }
}