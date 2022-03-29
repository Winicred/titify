<?php

class GetData
{
    private PDO $pdo;
    private Template $tpl;

    function __construct($pdo, $tpl = null)
    {
        if (!isset($pdo)) {
            return '[Class GetData]: No connection to the database';
        }

        if (isset($tpl)) {
            $this->tpl = $tpl;
        }
        $this->pdo = $pdo;
    }

    public function news($start, $class, $limit = 10)
    {
        $start = check($start, "int");
        $class = check($class, "int");
        $limit = check($limit, "int");

        global $users_groups;

        if (empty($start)) {
            $start = 0;
        }
        if (empty($limit)) {
            $limit = 10;
        }

        $date = date("Y-m-d H:i:s");
        if (is_worthy("q")) {
            if (empty($class)) {
                $STH = $this->pdo->query("SELECT news.id,news.class,news.new_name,news.img,news.short_text,news.date,news.author,news.views,users.login,users.avatar,news.views FROM news LEFT JOIN users ON news.author = users.id ORDER BY news.date DESC LIMIT $start, $limit");
            } else {
                $STH = $this->pdo->query("SELECT news.id,news.class,news.new_name,news.img,news.short_text,news.date,news.author,news.views,users.login,users.avatar,news.views FROM news LEFT JOIN users ON news.author = users.id WHERE news.class = $class ORDER BY news.date DESC LIMIT $start, $limit");
            }
            $STH->setFetchMode(PDO::FETCH_OBJ);
        } else {
            if (empty($class)) {
                $STH = $this->pdo->query("SELECT news.id,news.class,news.new_name,news.img,news.short_text,news.date,news.author,news.views,users.login,users.avatar,news.views FROM news LEFT JOIN users ON news.author = users.id WHERE news.date < '$date' ORDER BY news.date DESC LIMIT $start, $limit");
                $STH->setFetchMode(PDO::FETCH_OBJ);
            } else {
                $STH = $this->pdo->query("SELECT news.id,news.class,news.new_name,news.img,news.short_text,news.date,news.author,news.views,users.login,users.avatar,news.views FROM news LEFT JOIN users ON news.author = users.id WHERE news.date < '$date' and news.class = $class ORDER BY news.date DESC LIMIT $start, $limit");
                $STH->setFetchMode(PDO::FETCH_OBJ);
            }
        }

        $this->tpl->result['local_content'] = '';
        while ($row = $STH->fetch()) {
            if ($row->date > $date) {
                $row->new_name = '(Ожидает публикации) ' . $row->new_name;
            }
            $this->tpl->load_template('elements/new.tpl');
            $this->tpl->set("{id}", $row->id);
            $this->tpl->set("{new_name}", $row->new_name);
            $this->tpl->set("{img}", $row->img);
            $this->tpl->set("{short_text}", $row->short_text);
            $this->tpl->set("{author}", $row->author);
            $this->tpl->set("{login}", $row->login);
            $this->tpl->set("{date}", expand_date($row->date, 2));
            $this->tpl->set("{avatar}", $row->avatar);
            $this->tpl->set("{views}", $row->views);
            $this->tpl->compile('local_content');
            $this->tpl->clear();
        }
        if ($this->tpl->result['local_content'] == '') {
            $this->tpl->result['local_content'] = '<span class="empty-element">Новостей нет</span>';
        }

        return $this->tpl->result['local_content'];
    }

    public function users($start, $group = 2, $limit = 15)
    {

        // очистка переменных
        $start = check($start, "int");
        $limit = check($limit, "int");

        // проверка группы
        if ($group === 'multi_accounts' && (is_worthy("f") || is_worthy("g"))) {

            // присвоение переменной значение - мульти аккаунтов
            $group = 'multi_accounts';
        } else {

            // очистка переменной группы
            $group = check($group, "int");
        }

        // вызов глобальной переменной
        global $users_groups;

        // если пустое стартовое значение, то поставить по умолчанию - 0
        if (empty($start)) {
            $start = 0;
        }

        // если указана пустая группа, заменить ее на группу на "все"
        if (empty($group)) {
            $group = 0;
        }

        // если пустое значение лимита, то поставить по умолчанию - 15
        if (empty($limit)) {
            $limit = 15;
        }

        // проверка прав и установка переменной для запроса
        // ! для администраторов сделал выборку как активированных пользователей, так и не активированных !
        if (is_worthy('g') || is_worthy('f')) {
            $is_active = '1 OR active = 0';
        } else {
            $is_active = 0;
        }

        // запрос в БД на получение пользователей из выбранной группы, если она есть
        if ($group === 'multi_accounts') {
            $STH = $this->pdo->query("SELECT id,login,avatar,rights, name, lastname FROM users WHERE multi_account != 0 AND (active = $is_active) LIMIT $start, $limit");
        } elseif ($group == 0) {
            $STH = $this->pdo->query("SELECT id,login,avatar,rights, name, lastname FROM users WHERE (active = $is_active) LIMIT $start, $limit");
        } else {
            $STH = $this->pdo->query("SELECT id, name, lastname, avatar, rights, name, lastname FROM users WHERE rights = $group AND (active = $is_active) LIMIT $start, $limit");
        }

        // установка режима выборки
        $STH->setFetchMode(PDO::FETCH_OBJ);

        // переменная для вывода результата
        $this->tpl->result['local_content'] = '';

        // выборка из БД
        while ($row = $STH->fetch()) {

            // установка переменных для проверки друзей
            $is_friends = 'false';
            $isset_friend_request_from_me = 'false';
            $isset_friend_request_from_him = 'false';

            // запрос для проверки друзей с id пользователем сессии
            $db_response = $this->pdo->prepare("SELECT id, id_sender, id_taker, accept FROM users__friends WHERE (id_sender=:friend_id AND id_taker=:my_id) OR (id_sender=:my_id AND id_taker=:friend_id) LIMIT 1");
            $db_response->setFetchMode(PDO::FETCH_OBJ);

            // выполнение запроса с параметрами
            $db_response->execute([':my_id' => $_SESSION['id'], ':friend_id' => $row->id]);

            // выборка (fetch, так как id друза берётся из цикла)
            $friends = $db_response->fetch();

            // если связь с друзьями есть
            if (isset($friends->id) && ($friends->accept == 1)) {
                $is_friends = 'true';
            }

            // если связь есть, но подтверждения на заявку нет
            if (isset($friends->id) && ($friends->accept == 0)) {

                // заявка подана пользователем сессии
                if ($friends->id_sender == $_SESSION['id']) {
                    $isset_friend_request_from_me = 'true';
                }

                // принимающий заявку - пользователь сессии
                if ($friends->id_taker == $_SESSION['id']) {
                    $isset_friend_request_from_him = 'true';
                }
            }

            // вызов шаблона
            $this->tpl->load_template('elements/users/mini_user.tpl');

            // запись переменных в шаблон
            $this->tpl->set("{name}", $row->name);
            $this->tpl->set("{lastname}", $row->lastname);
            $this->tpl->set("{id}", $row->id);
            $this->tpl->set("{avatar}", $row->avatar);
            $this->tpl->set("{is_friends}", $is_friends);
            $this->tpl->set("{isset_friend_request_from_me}", $isset_friend_request_from_me);
            $this->tpl->set("{isset_friend_request_from_him}", $isset_friend_request_from_him);

            // компиляция результата
            $this->tpl->compile('local_content');

            // очистка переменных результата
            $this->tpl->clear();
        }

        // вернуть массив развёртки с результатом после завершения выборки
        return $this->tpl->result['local_content'];
    }

    public function search_users($search, $group = 2)
    {
        $search = check_js($search);

        if ($group === 'multi_accounts' && (is_worthy("f") || is_worthy("g"))) {
            $group = 'multi_accounts';
        } else {
            $group = check_js($group, "int");
        }

        if (empty($search)) {
            return '<span class="empty-element">Введите логин пользователя</span>';
        }

        if (empty($group)) {
            $group = 0;
        }

        if ($group === 'multi_accounts') {
            $STH = $this->pdo->prepare("SELECT id, login, avatar, rights, name, lastname FROM users WHERE (name LIKE :search OR lastname LIKE :search OR id = :search) AND multi_account != 0 ");
            $STH->setFetchMode(PDO::FETCH_OBJ);
            $STH->execute([":search" => "%" . $search . "%"]);
        } elseif ($group == 0) {
            $STH = $this->pdo->prepare("SELECT id, login, avatar, rights, name, lastname FROM users WHERE (name LIKE :search OR lastname LIKE :search OR id = :search)");
            $STH->setFetchMode(PDO::FETCH_OBJ);
            $STH->execute([":search" => "%" . $search . "%"]);
        } else {
            $STH = $this->pdo->prepare("SELECT id, login, avatar, rights, name, lastname FROM users WHERE rights = :group AND (name LIKE :search OR lastname LIKE :search OR id = :search)");
            $STH->setFetchMode(PDO::FETCH_OBJ);
            $STH->execute([':search' => '%' . $search . '%', ':group' => $group]);
        }

        $this->tpl->result['local_content'] = '';
        while ($row = $STH->fetch()) {
            $this->tpl->load_template('elements/users/mini_user.tpl');
            $this->tpl->set("{login}", $row->login);
            $this->tpl->set("{id}", $row->id);
            $this->tpl->set("{avatar}", $row->avatar);
            $this->tpl->set("{name}", $row->name);
            $this->tpl->set("{lastname}", $row->lastname);
            $this->tpl->compile('local_content');
            $this->tpl->clear();
        }

        if ($this->tpl->result['local_content'] == '') {
            $this->tpl->result['local_content'] = '<span class="empty-element">Пользователи не найдены</span>';
        }

        return $this->tpl->result['local_content'];
    }

    public function notifications($start, $limit = 10)
    {
        $start = check_start($start);
        $limit = check($limit, "int");

        if (empty($start)) {
            $start = 0;
        }
        if (empty($limit)) {
            $limit = 10;
        }

        $STH = $this->pdo->query("SELECT * FROM notifications WHERE user_id='$_SESSION[id]' ORDER BY date DESC LIMIT $start, $limit");
        $STH->setFetchMode(PDO::FETCH_OBJ);
        $this->tpl->result['local_content'] = '';
        while ($row = $STH->fetch()) {
            if ($row->type == 1) {
                $class = 'info';
            }
            if ($row->type == 2) {
                $class = 'success';
            }
            if ($row->type == 3) {
                $class = 'error';
            }

            $text = find_img_mp3($row->message, 1);
            $this->tpl->load_template('/elements/notifications/notification.tpl');
            $this->tpl->set("{class}", $class);
            $this->tpl->set("{date}", expand_date($row->date, 7));
            $this->tpl->set("{text}", $text);
            $this->tpl->set("{function}", 'dell_notification');
            $this->tpl->set("{id}", $row->id);
            $this->tpl->compile('local_content');
            $this->tpl->clear();
        }
        if (empty($this->tpl->result['local_content'])) {
            $this->tpl->load_template('/elements/notifications/notification.tpl');
            $this->tpl->set("{class}", "info");
            $this->tpl->set("{date}", expand_date(date("Y-m-d H:i:s"), 7));
            $this->tpl->set("{text}", "Уведомлений нет");
            $this->tpl->set("{function}", 'close_notification');
            $this->tpl->set("{id}", 1);
            $this->tpl->compile('local_content');
            $this->tpl->clear();
        } else {
            $this->tpl->result['local_content'] .= '<script>$("#notifications_line").removeClass("disp-n");</script>';
        }

        return $this->tpl->result['local_content'];
    }

    private function get_profile_by_id($id): int|string
    {
        global $users_groups;

        $STH = $this->pdo->prepare("SELECT `id`, `login`, `avatar`, `rights` FROM `users` WHERE `id`=:id LIMIT 1");
        $STH->setFetchMode(PDO::FETCH_OBJ);
        $STH->execute(array(':id' => $id));
        $row = $STH->fetch();

        if (isset($row->id)) {
            return '<a target="_blank" href="../profile?id=' . $row->id . '" title="' . $users_groups[$row->rights]['name'] . '"><img src="../' . $row->avatar . '" alt="' . $row->login . '" class="small_us_av"> <i style="color: ' . $users_groups[$row->rights]['color'] . '">' . $row->login . '</a></i>';
        } else {
            return 0;
        }
    }

    private function lvl_rank($value)
    {
        if (strripos($value, ';') === false) {
            return $value;
        } else {
            $value = explode(';', $value);
            return trim($value[0]);
        }
    }
}