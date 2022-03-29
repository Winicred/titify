<?php

tpl()->result['recent_playlists'] = '';
tpl()->result['likes_playlists'] = '';
tpl()->result['my_playlists'] = '';

// получить пользователя по id из массива (если он не пустой) или из url (если id в массиве пустой)
$user_id = !empty($data_array) ? $data_array['id'] : $_GET['id'];
$user = user_by_id($user_id);
$user_playlists = playlists_by_user_id($user_id);

if (!is_worthy('f')) {
    if ($user_id != $_SESSION['id']) {
        show_error_page('not_allowed');
    }
    show_error_page('not_allowed');
}

tpl()->load_template('elements/title/title.tpl');
tpl()->set("{title}", page()->compile_str(page()->page_info('playlists')->title, $user->display_name));
tpl()->set("{name}", config()->name);
tpl()->compile('title');
tpl()->clear();

// недавние плейлисты
$db_response = pdo()->prepare("SELECT tracks.id, tracks.title, tracks.cover, users.id AS author_id, users.display_name FROM track__history LEFT JOIN tracks ON track__history.track_id = tracks.id LEFT JOIN users ON tracks.author = users.id WHERE track__history.user_id = :user_id ORDER BY track__history.id DESC LIMIT 6");
$db_response->setFetchMode(PDO::FETCH_OBJ);
$db_response->execute([':user_id' => $user_id]);

while ($recent_playlists = $db_response->fetch()) {
    tpl()->load_template('elements/playlists/playlist_item.tpl');
    tpl()->set("{playlist_id}", $recent_playlists->id);
    tpl()->set("{playlist_cover}", $recent_playlists->cover);
    tpl()->set("{playlist_name}", $recent_playlists->title);
    tpl()->set("{script}", 'open_playlist(null, ' . $recent_playlists->id . ');');
    tpl()->set("{author}", 'true');
    tpl()->set("{playlist_author_id}", $recent_playlists->author_id);
    tpl()->set("{playlist_author_login}", $recent_playlists->display_name);
    tpl()->compile('recent_playlists');
    tpl()->clear();
}

// пролайканые треки
$db_response = pdo()->prepare("SELECT tracks.id, tracks.cover, tracks.title, users.id as author_id, users.display_name FROM users__favorite_actions LEFT JOIN tracks ON users__favorite_actions.track_id = tracks.id LEFT JOIN users ON users__favorite_actions.user_id = users.id WHERE users__favorite_actions.user_id = :user_id AND users__favorite_actions.track_id IS NOT NULL ORDER BY users__favorite_actions.id DESC LIMIT 6");
$db_response->setFetchMode(PDO::FETCH_OBJ);
$db_response->execute([':user_id' => $user_id]);

while ($likes = $db_response->fetch()) {
    tpl()->load_template('elements/playlists/playlist_item.tpl');
    tpl()->set("{playlist_id}", $likes->id);
    tpl()->set("{playlist_cover}", $likes->cover);
    tpl()->set("{playlist_name}", $likes->title);
    tpl()->set("{script}", '');
    tpl()->set("{author}", 'true');
    tpl()->set("{playlist_author_id}", $likes->author_id);
    tpl()->set("{playlist_author_login}", $likes->display_name);
    tpl()->compile('likes_playlists');
    tpl()->clear();
}

// пролаканные плейлисты/треки
tpl()->load_template("playlists/playlists.tpl");
tpl()->set("{id}", $user_playlists->id);
tpl()->set("{recent_playlists}", tpl()->result['recent_playlists']);
tpl()->set("{likes_playlists}", tpl()->result['likes_playlists']);
tpl()->set("{my_playlists}", tpl()->result['my_playlists']);
tpl()->compile('content');
tpl()->clear();

return json_encode(['title' => tpl()->result['title'], 'content' => tpl()->getShow(tpl()->result['content'])]);