<?php

if (!is_auth()) {
    show_error_page('not_auth');
}

tpl()->load_template('elements/title/title.tpl');
tpl()->set("{title}", page()->page_info('upload_track')->title);
tpl()->set("{name}", config()->name);
tpl()->compile('title');
tpl()->clear();

tpl()->load_template('upload/add_track.tpl');
tpl()->set('{display_name}', user()->display_name);
tpl()->set('{avatar}', user()->avatar);
tpl()->compile('content');
tpl()->clear();

return json_encode(['title' => tpl()->getShow(tpl()->result['title']), 'content' => tpl()->getShow(tpl()->result['content'])]);