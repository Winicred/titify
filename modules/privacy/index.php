<?php

tpl()->load_template('elements/title/title.tpl');
tpl()->set("{title}", get_page_title(__DIR__ . '/' . __FILE__));
tpl()->set("{name}", config()->name);
tpl()->compile('title');
tpl()->clear();

tpl()->load_template('privacy/privacy.tpl');
tpl()->compile('content');
tpl()->clear();

return json_encode(['title' => tpl()->result['title'], 'content' => tpl()->result['content']]);