<?php

// получить пользователя по id из массива (если он не пустой) или из url (если id в массиве пустой)
$profile = user_by_id(!empty($data_array) ? $data_array['id'] : $_GET['id']);

if (empty($profile)) {
    show_error_page();
}

if (!is_auth()) {
    show_error_page('not_auth');
}

if ((!is_worthy('f') || !is_worthy('q') || !is_worthy('g')) && ($profile->id != $_SESSION['id'])) {
    show_error_page(404);
}

// загрузки шаблона заголовка страницы
tpl()->load_template('elements/title/title.tpl');
tpl()->set("{title}", page()->compile_str(page()->page_info('edit_profile')->title, $profile->display_name));
tpl()->set("{name}", config()->name);
tpl()->compile('title');
tpl()->clear();

if (!empty($profile->birth)) {
    $birth = explode('-',$profile->birth);
}

tpl()->load_template('profile/edit_profile.tpl');
tpl()->set("{user_id}", $_SESSION['id']);
tpl()->set("{id}", $profile->id);
tpl()->set("{cover}", $profile->cover);
tpl()->set("{avatar}", $profile->avatar);
tpl()->set("{display_name}", $profile->display_name);
tpl()->set("{name}", $profile->name);
tpl()->set("{lastname}", $profile->lastname);
tpl()->set("{email}", $profile->email);
tpl()->set("{email_notice}", $profile->email_notice == 1 ? 'true' : 'false');
tpl()->set("{gender}", $profile->gender);
tpl()->set("{reg_date}", expand_date($profile->regdate));
tpl()->set("{ip}", $profile->ip);
tpl()->set("{reg_ip}", $profile->reg_ip);
tpl()->set("{is_very}", $profile->verification == 1 ? 'true' : 'false');
tpl()->set("{is_very_request}", $profile->is_very_request == 1 ? 'true' : 'false');
tpl()->set("{facebook}", $profile->facebook ?? '');
tpl()->set("{twitter}", $profile->twitter ?? '');
tpl()->set("{instagram}", $profile->instagram ?? '');
tpl()->set("{youtube}", $profile->youtube ?? '');
tpl()->set("{telegram}", $profile->telegram ?? '');
tpl()->set("{vk}", $profile->vk ?? '');
tpl()->set("{github}", $profile->github ?? '');
tpl()->set("{website}", $profile->website ?? '');
tpl()->set("{deleted}", $profile->deleted == 1 ? 'true' : 'false');
tpl()->set("{year}", $birth[0] ?? '');
tpl()->set("{month}", str_replace('0', '', $birth[1] ?? ''));
tpl()->set("{day}", str_replace('0', '', $birth[2] ?? ''));
tpl()->compile('content');
tpl()->clear();

return json_encode(['title' => tpl()->result['title'], 'content' => tpl()->getShow(tpl()->result['content'])]);