<?php

tpl()->result['in_other_playlists_data'] = '';

$track = get_track_by_src((!empty($data_array) ? $data_array['name'] : $_GET['name']), true);
$author = get_author_by_track_id($track->author);

if (empty($track)) {
    show_error_page();
}

// получить плейлисты, в которых есть трек
$db_response = pdo()->prepare("SELECT users__playlists.id, users__playlists.name, users__playlists.cover, users.id AS author_id, users.display_name, users__playlists.date_add, users__playlists.likes, users__playlists.auditions, users__playlists.comments, users__playlists.reposts FROM users__playlists_tracks LEFT JOIN users__playlists ON users__playlists_tracks.playlist_id = users__playlists.id LEFT JOIN users ON users__playlists.user_id = users.id WHERE users__playlists_tracks.track_id = :track_id AND users__playlists.id IS NOT NULL GROUP BY users__playlists_tracks.id DESC LIMIT 0, 3");
$db_response->setFetchMode(PDO::FETCH_OBJ);
$db_response->execute([':track_id' => $track->id]);

// выборка данных из запроса
while ($playlists = $db_response->fetch()) {

    // загрузка шаблона плейлиста
    tpl()->load_template('elements/tracks/in_playlist.tpl');

    // передача переменных в шаблон
    tpl()->set("{cover}", $playlists->cover);
    tpl()->set("{name}", $playlists->name);
    tpl()->set("{name}", $playlists->name);
    tpl()->set("{author}", $playlists->display_name);
    tpl()->set("{author_id}", $playlists->author_id);
    tpl()->set("{auditions}", $playlists->auditions);
    tpl()->set("{likes}", $playlists->likes);
    tpl()->set("{comments}", $playlists->comments);
    tpl()->set("{reposts}", $playlists->reposts);

    // компиляция шаблона
    tpl()->compile('in_other_playlists_data');

    // очистка переменных
    tpl()->clear();
}

if (tpl()->result['in_other_playlists_data'] == '') {
    tpl()->result['in_other_playlists_data'] = '<div style="text-align: center; color: var(--gray-color)">Playlists not found</div>';
}

if (is_auth()) {
    $db_response = pdo()->prepare("SELECT id FROM users__favorite_actions WHERE user_id = :user_id AND track_id = :track_id");
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    $db_response->execute([':user_id' => $_SESSION['id'], ':track_id' => $track->id]);
} else {
    $db_response = pdo()->prepare("SELECT id FROM users__favorite_actions WHERE ip = :ip AND track_id = :track_id");
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    $db_response->execute([':ip' => get_ip(), ':track_id' => $track->id]);
}

$is_track_liked = $db_response->fetch();

tpl()->load_template('elements/title/title.tpl');
tpl()->set("{title}", page()->compile_str(page()->page_info('track')->title, $track->title));
tpl()->set("{name}", config()->name);
tpl()->compile('title');
tpl()->clear();

tpl()->load_template('track/track.tpl');
tpl()->set('{user_id}', $_SESSION['id'] ?? '');
tpl()->set('{id}', $track->id);
tpl()->set('{cover}', $track->cover);
tpl()->set('{author_avatar}', $author->avatar);
tpl()->set('{author_id}', $author->id);
tpl()->set('{auditions}', $track->auditions);
tpl()->set('{reposts}', $track->reposts);
tpl()->set('{likes}', $track->likes);
tpl()->set('{comments}', $track->comments);
tpl()->set('{author_auditions}', get_total_track_auditions_count_by_author($author->id));
tpl()->set('{tracks}', get_total_track_count_by_author($author->id));
tpl()->set('{author}', $author->display_name);
tpl()->set('{is_very}', $author->verification == 1 ? 'true' : 'false');
tpl()->set('{description}', $track->description);
tpl()->set('{title}', $track->title);
tpl()->set('{push_date}', expand_date($track->date_add, 6));
tpl()->set('{track_liked}', $is_track_liked == true ? 'true' : 'false');
tpl()->set('{in_other_playlists_data}', tpl()->getShow(tpl()->result['in_other_playlists_data']));

tpl()->compile('content');
tpl()->clear();

return json_encode(['title' => tpl()->getShow(tpl()->result['title']), 'content' => tpl()->getShow(tpl()->result['content'])]);