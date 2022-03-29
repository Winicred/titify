<?php

class Users
{
    private PDO $pdo;
    private int $ban_time = 900;
    private int $flood_time = 300;
    private $now = 300;

    function __construct(PDO $pdo = null)
    {
        $this->now = date("Y-m-d H:i:s");

        if (isset($pdo)) {
            $this->pdo = $pdo;
        }
    }

    public static function is_user_exist(PDO $pdo, int $user_id = 0)
    {
        $db_response = $pdo->prepare("SELECT id FROM users WHERE id = :id LIMIT 1");
        $db_response->setFetchMode(PDO::FETCH_OBJ);
        $db_response->execute([':id' => $user_id]);
        $row = $db_response->fetch();

        return !empty($row->id);
    }

    public static function get_id_by_route(PDO $pdo, string $route)
    {
        $db_response = $pdo->prepare("SELECT id FROM users WHERE route = :route LIMIT 1");
        $db_response->setFetchMode(PDO::FETCH_OBJ);
        $db_response->execute([':route' => $route]);
        $row = $db_response->fetch();

        return (empty($row->id)) ? false : $row;
    }

    public static function get_route_by_id(PDO $pdo, int $userId = 0)
    {
        $db_response = $pdo->prepare("SELECT route FROM users WHERE id = :id LIMIT 1");
        $db_response->setFetchMode(PDO::FETCH_OBJ);
        $db_response->execute([':id' => $userId]);
        $row = $db_response->fetch();

        return (empty($row->route)) ? false : $row;
    }

    public function check_to_invalid_auth(string $ip): int
    {
        $this->pdo->exec("DELETE FROM users__blocked WHERE (date != '0000-00-00 00:00:00') AND (UNIX_TIMESTAMP('$this->now') - UNIX_TIMESTAMP(date) > $this->ban_time)");

        $db_response = $this->pdo->prepare("SELECT col FROM users__blocked WHERE ip = :ip LIMIT 1");
        $db_response->setFetchMode(PDO::FETCH_OBJ);
        $db_response->execute([':ip' => $ip]);
        $row = $db_response->fetch();

        if (!isset($row->col)) {
            return 0;
        } else {
            return $row->col;
        }
    }

    public function up_invalid_auths(string $ip): int
    {
        $db_response = $this->pdo->prepare("SELECT ip, col FROM users__blocked WHERE ip = :ip LIMIT 1");
        $db_response->setFetchMode(PDO::FETCH_OBJ);
        $db_response->execute(array(':ip' => $ip));
        $tmp = $db_response->fetch();

        if (isset($tmp->ip)) {
            $invalid_auths = $tmp->col + 1;
            $db_response = $this->pdo->prepare("UPDATE users__blocked SET col = :col, date = :date, reason = :reason WHERE ip = :ip LIMIT 1");
            $db_response->execute(['col' => $invalid_auths, 'date' => date("Y-m-d H:i:s"), ':ip' => $ip, ':reason' => 'Превышено количество неудачных попыток авторизации']);
        } else {
            $invalid_auths = 1;
            $db_response = $this->pdo->prepare("INSERT INTO users__blocked (ip, date, col, reason) VALUES (:ip, :date, :col, :reason)");
            $db_response->execute(['ip' => $ip, 'date' => date("Y-m-d H:i:s"), 'col' => $invalid_auths, 'reason' => '']);
        }

        return $invalid_auths;
    }

    public function delete_invalid_auths(string $ip): bool
    {
        $this->pdo->exec("DELETE FROM users__blocked WHERE ip = '$ip' LIMIT 1");

        return true;
    }

    public function check_login_length(string $login): bool
    {
        if (is_string_length_less($login, 5) || is_string_length_more($login, 30)) {
            return false;
        } else {
            return true;
        }
    }

    public function check_route_length(string $route): bool
    {
        if (is_string_length_less($route, 1) || is_string_length_more($route, 32)) {
            return false;
        } else {
            return true;
        }
    }

    public function check_firstname_length(string $firstname): bool
    {
        if (is_string_length_less($firstname, 2) || is_string_length_more($firstname, 50)) {
            return false;
        } else {
            return true;
        }
    }

    public function check_display_name_length(string $display_name): bool
    {
        if (is_string_length_less($display_name, 3) || is_string_length_more($display_name, 50)) {
            return false;
        } else {
            return true;
        }
    }

    public function check_lastname_length(string $lastname): bool
    {
        if (is_string_length_less($lastname, 2) || is_string_length_more($lastname, 50)) {
            return false;
        } else {
            return true;
        }
    }

    public function check_login_composition(string $login): bool
    {
        if (clean_str($login) != $login) {
            return false;
        } else {
            return true;
        }
    }

    public function check_eng_login_length(string $login): bool
    {
        if (clean_eng_str($login) != $login) {
            return false;
        } else {
            return true;
        }
    }

    public function check_route_composition(string $login): bool
    {
        if (preg_replace('/[^a-zA-Z0-9_-]/ui', '', $login) != $login) {
            return false;
        } else {
            return true;
        }
    }

    public function check_login_busyness(string $login, int $id = 0): bool
    {
        $db_response = $this->pdo->prepare("SELECT * FROM users WHERE login = :login LIMIT 1");
        $db_response->setFetchMode(PDO::FETCH_OBJ);
        $db_response->execute([':login' => $login]);
        $row = $db_response->fetch();

        if ($id == 0) {
            if (empty($row->id)) {
                return true;
            } else {
                return false;
            }
        } else {
            if (!empty($row->id) && $id != $row->id) {
                return false;
            } else {
                return true;
            }
        }
    }

    public function check_route_busyness(string $route, int $id = 0): bool
    {
        $db_response = $this->pdo->prepare("SELECT * FROM users WHERE route = :route LIMIT 1");
        $db_response->setFetchMode(PDO::FETCH_OBJ);
        $db_response->execute([':route' => $route]);
        $row = $db_response->fetch();

        if ($id == 0) {
            if (empty($row->id)) {
                return true;
            } else {
                return false;
            }
        } else {
            if (!empty($row->id) && $id != $row->id) {
                return false;
            } else {
                return true;
            }
        }
    }

    public function check_for_display_name_exist(string $display_name)
    {
        $db_response = $this->pdo->prepare("SELECT display_name FROM users WHERE display_name = :display_name LIMIT 1");
        $db_response->setFetchMode(PDO::FETCH_OBJ);
        $db_response->execute([':display_name' => $display_name]);
        $row = $db_response->fetch();

        if (empty($row->display_name)) {
            return true;
        } else {
            return false;
        }
    }

    public function check_password_length(string $password): bool
    {
        if (mb_strlen($password, 'UTF-8') < 8 or mb_strlen($password, 'UTF-8') > 25) {
            return false;
        } else {
            return true;
        }
    }

    public function check_email(string $email): bool
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        return false;
    }

    public function check_email_busyness(string $email): bool
    {
        $db_response = $this->pdo->query("SELECT id FROM users WHERE email = '$email'");
        $db_response->setFetchMode(PDO::FETCH_OBJ);
        $row = $db_response->fetch();
        if (isset($row->id)) {
            return false;
        } else {
            return true;
        }
    }

    public function check_busyness(string $name, string $value, int $id = 0): bool
    {
        $db_response = $this->pdo->query("SELECT id FROM users WHERE $name = '$value'");
        $db_response->setFetchMode(PDO::FETCH_OBJ);
        $row = $db_response->fetch();
        if (!empty($row->id) && $row->id != $id) {
            return false;
        } else {
            return true;
        }
    }

    public function entry_user(string $login, string $password, int $active, string $display_name, string $email, string $name = null, string $lastname = null, string $avatar = null, string $birth = null)
    {
        $invited = $this->check_inviting();
        $regdate = date("Y-m-d H:i:s");

        $imgWidth = 200;
        $imgHeight = 200;
        $img = imagecreatetruecolor($imgWidth, $imgHeight);
        $img_name = generation_name() . '.png';

        $color_1 = rand_color();
        $color_2 = rand_color();

        image_gradientrect($img, 0, 0, $imgWidth, $imgHeight, $color_1, $color_2);

        header('Content-Type: image/png');
        imagepng($img, __DIR__ . '/../../files/avatars/' . $img_name);
        imagedestroy($img);

        if ($active == 2) {
            $active = 1;
        } elseif ($active == 1) {
            $active = 0;
        }

        if ($name == null) {
            $name = null;
        }

        if ($lastname == null) {
            $lastname = null;
        }

        if (empty($avatar)) {
            $avatar = '/files/avatars/' . $img_name;
        }

        if (empty($birth)) {
            $birth = null;
        }

        if ($password == "none") {
            $password .= "_" . create_pass(16, 1);
        }

        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            $_SERVER['HTTP_USER_AGENT'] = 'undefined';
        }

        $browser = md5($_SERVER['HTTP_USER_AGENT']);
        $ip = get_ip();

        $db_response = $this->pdo->query("SELECT stand_rights FROM config LIMIT 1");
        $db_response->setFetchMode(PDO::FETCH_OBJ);
        $row = $db_response->fetch();
        $rights = $row->stand_rights;

        $db_response = $this->pdo->prepare("INSERT INTO users (id,login,password,email,avatar,regdate,birth,rights,display_name,name,lastname,active,invited,ip,reg_ip,browser) VALUES (NULL, :login, :password, :email, :avatar, :regdate, :birth, :rights, :display_name, :name, :lastname, :active, :invited, :ip, :ip, :browser)");
        $db_response->execute(
            [
                ':login' => $login,
                ':password' => $password,
                ':email' => $email,
                ':avatar' => $avatar,
                ':regdate' => $regdate,
                ':birth' => $birth,
                ':rights' => $rights,
                ':display_name' => $display_name,
                ':name' => $name,
                ':lastname' => $lastname,
                ':active' => $active,
                ':invited' => $invited,
                ':ip' => $ip,
                ':browser' => $browser,
            ]
        );

        return $this->get_user_by_login_password($login, $password);
    }

    public function check_inviting(): int
    {
        if (isset($_COOKIE['invited'])) {
            $_COOKIE['invited'] = clean($_COOKIE['invited'], "int");
        }

        if (empty($_COOKIE['invited'])) {
            $_COOKIE['invited'] = 0;
        }

        $db_response = $this->pdo->prepare("SELECT id FROM users WHERE id = :id");
        $db_response->setFetchMode(PDO::FETCH_OBJ);
        $db_response->execute(array(':id' => $_COOKIE['invited']));
        $row = $db_response->fetch();
        return $row->id ?? 0;
    }

    public function get_user_by_login_password(string $login, string $password)
    {
        $db_response = $this->pdo->prepare("SELECT * FROM users WHERE (login=:login) AND password=:password");

        $db_response->execute([':login' => $login, ':password' => $password]);

        return $db_response->fetch(PDO::FETCH_OBJ);
    }

    public function get_user_by_email(string $email)
    {
        $db_response = $this->pdo->query("SELECT * FROM users WHERE email = '$email'");
        $db_response->setFetchMode(PDO::FETCH_OBJ);
        $row = $db_response->fetch();
        if (isset($row->id)) {
            return $row;
        } else {
            return false;
        }
    }

    function user_random_password(int $length): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $randstring = '';

        for ($i = 0; $i < $length; $i++) {
            $randstring = $characters[rand(0, strlen($characters))];
        }

        return $randstring;
    }

    public function after_registration_actions(SessionsCookies $session_cookies, string $salt, string $site_name, int $user_id, string $full_site_host): array
    {
        inc_notifications();

        $user = self::get_user_data($this->pdo, $user_id);

        if (empty($user->id)) {
            $answer['message'] = 'error';
            return $answer;
        }

        if ($user->active == 1) {
            if ($user->id == 1) {
                $db_response = $this->pdo->prepare("UPDATE users SET rights = :rights WHERE id = $user->id");
                $db_response->execute([':rights' => '1']);
                $user->rights = 1;
            }

            $this->auth_user($session_cookies, $user->protect, $user->password, $user->login, $user->id, $user->rights);

            $answer['letter'] = reg_letter($site_name, $user->login);
            $answer['message'] = "You have successfully registered!";
        } else {
            $code = $this->convert_password($user->login, $salt);
            $link = $full_site_host . "?id=" . $user->id . "&key=" . $code;

            $answer['letter'] = reg_letter_with_key($site_name, $user->login, $link);
            $answer['message'] = 'You have successfully registered! Instructions for activating your account are indicated in the letter that we sent to your e-mail address.';
        }

        return $answer;
    }

    public static function get_user_data(PDO $pdo, $userId = 0)
    {
        $db_response = $pdo->prepare("SELECT * FROM users WHERE id=:id LIMIT 1");
        $db_response->setFetchMode(PDO::FETCH_OBJ);
        $db_response->execute([':id' => $userId]);
        $row = $db_response->fetch();

        return (empty($row->id)) ? false : $row;
    }

    public function auth_user(SessionsCookies $session_cookies, int $protect, string $password, string $login, int $id, string $rights): array
    {
        if ($protect == 1) {
            $session_cookies->ip = get_ip();
        }

        $_SESSION['cache'] = $session_cookies->get_cache($password);
        $_SESSION['login'] = $login;
        $_SESSION['id'] = $id;
        $_SESSION['rights'] = $rights;

        $session_cookies->set_user_cookie();

        if (is_worthy("z")) {
            $session_cookies->unset_user_session();

            return ['status' => false, 'response' => 'You are blocked. Please try again later.'];
        } elseif (is_worthy("x")) {
            $session_cookies->unset_user_session();

            $db_response = $pdo->prepare("INSERT INTO users__blocked (ip) VALUES (:ip)");
            $db_response->execute(array('ip' => $ip));
            $session_cookies->set_cookie("point", "1");

            return ['status' => false, 'response' => 'You are blocked. Please try again later'];
        } else {
            return ['status' => true];
        }
    }

    public function convert_password(string $password, string $salt = ''): string
    {
        $password = md5($password . $salt);
        $password = strrev($password);
        return $password . "a";
    }

    public function get_default_playlists()
    {
        $db_response = $this->pdo->prepare("SELECT * FROM users__playlists WHERE user_id = :user_id AND private = 2");
        $db_response->setFetchMode(PDO::FETCH_OBJ);
        $db_response->execute([':user_id' => $_SESSION['id']]);
        return $db_response->fetchAll();
    }
}