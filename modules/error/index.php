<?php

if (isset($_SESSION['error_msg'])) {
    $error_msg = clean($_SESSION['error_msg']);
} else {
    $error_msg = 'Page Not Found';
}

unset($_SESSION['error_msg']);

$tpl->load_template('elements/title/title.tpl');
$tpl->set("{title}", $page->title);
$tpl->set("{name}", $config->name);
$tpl->compile('title');
$tpl->clear();

$tpl->load_template('error.tpl');
$tpl->set("{site_host}", $site_host);
$tpl->set("{message}", $error_msg);
$tpl->compile('content');
$tpl->clear();

if ((isset($error_type) && $error_type == '404') || ($error_msg == 'Page Not Found')) {
    $tpl->load_template("footer.tpl");
    $tpl->set("{site_host}", $site_host);
    $tpl->set("{site_name}", $config->name);
    $tpl->compile('content');
    $tpl->clear();

    $tpl->set('{content}', $tpl->result['content']);
    $tpl->load_template('main.tpl');

    $tpl->compile('main');
    eval('?>' . $tpl->result['main'] . '<?php ');
    $tpl->global_clear();
    exit();
}

