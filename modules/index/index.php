<?php

if (isset($_GET['ref'])) {
    $invited = clean($_GET['ref'], "int");
    $db_response = pdo()->prepare("SELECT id FROM users WHERE id = :id LIMIT 1");
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    $db_response->execute([':id' => $invited]);
    $row = $db_response->fetch();
    if (isset($row->id)) {
        $session_cookies->set_cookie("invited", $row->id);
    }
}

tpl()->load_template('elements/title/title.tpl');
tpl()->set("{title}", get_page_title(__DIR__ . '/' . __FILE__));
tpl()->set("{name}", config()->name);
tpl()->compile('title');
tpl()->clear();

tpl()->load_template('index/body.tpl');
tpl()->set("{site_host}", $site_host);
tpl()->set('{welcome_message}', get_welcome_message());
tpl()->compile('content');
tpl()->clear();

return json_encode(['title' => tpl()->result['title'], 'content' => tpl()->result['content']]);