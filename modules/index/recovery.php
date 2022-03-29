<?php

$message = '';
if (isset($_GET['a']) && isset($_GET['data'])) {
    $id = check($_GET['a'], "int");

    $db_response = $pdo->prepare("SELECT `id`, `login`, `email`, `password` FROM `users` WHERE `id`=:id LIMIT 1");
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    $db_response->execute(array(':id' => $id));
    $row = $db_response->fetch();
    if (empty($row->id)) {
        show_error_page();
    }

    if ($_GET['data'] != md5($row->id . $config->salt . $row->password . $row->email . date("Y-m-d"))) {
        $message = '<p class=\'text-danger\'>Ссылка не активна!</p>';
    } else {
        $user = new Users($pdo);

        $password = create_pass(7, 1);
        $password2 = $user->convert_password($password, $config->salt);

        $db_response = $pdo->prepare("UPDATE `users` SET `password`=:password WHERE `id`=:id LIMIT 1");
        if ($db_response->execute(array(':password' => $password2, ':id' => $id)) == '1') {
            inc_notifications();
            $letter = recovery_letter($config->name, $row->login, $password);
            send_mail($row->email, $letter['subject'], $letter['message'], $pdo);
            $message = '<p class=\'text-success\'>Ссылка на восстановление пароля выслана на почту <b>' . $row->email . '</b></p>';
        } else {
            $message = '<p class=\'text-danger\'>Ошибка</p>';
        }

        header('Location: ../');
    }
}

$tpl->load_template('elements/title/title.tpl');
$tpl->set("{title}", $page->title);
$tpl->set("{name}", $config->name);
$tpl->compile('title');
$tpl->clear();

$nav = array(
    $page_info->to_nav('main', 0, 0),
    $page_info->to_nav('recovery', 1, 0)
);

include_once "inc/not_authorized.php";

$tpl->load_template('authorization/recovery.tpl');
$tpl->set("{site_host}", $site_host);
$tpl->set("{message}", $message);
$tpl->compile('content');
$tpl->clear();
?>
