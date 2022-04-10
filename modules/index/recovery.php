<?php

$message = '';
$short_message = '';
if (isset($_GET['a']) && isset($_GET['data'])) {
    $id = check_js($_GET['a'], "int");

    $db_response = $pdo->prepare("SELECT `id`, `login`, `email`, `password` FROM `users` WHERE `id`=:id LIMIT 1");
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    $db_response->execute([':id' => $id]);
    $row = $db_response->fetch();
    if (empty($row->id)) {
        show_error_page();
    }

    if ($_GET['data'] != md5($row->id . $config->salt . $row->password . $row->email . date("Y-m-d"))) {
        $message = '<p class=\'text-danger\'>Link is not active!</p>';
        $short_message = 'Link is not active!';
    }
}

$tpl->load_template('elements/title/title.tpl');
$tpl->set("{title}",  page()->page_info('recovery')->title);
$tpl->set("{name}", config()->name);
$tpl->compile('title');
$tpl->clear();

$tpl->load_template('authorization/recovery_page.tpl');
$tpl->set("{site_host}", $site_host);
$tpl->set("{message}", $message);
$tpl->set("{short_message}", $short_message);
$tpl->compile('content');
$tpl->clear();

