<?php

tpl()->result['all'] = '';
tpl()->result['popular_tracks_data'] = '';
tpl()->result['tracks_data'] = '';
tpl()->result['playlists_data'] = '';
tpl()->result['playlists_mini'] = '';
tpl()->result['tracks_mini'] = '';

// получить пользователя по id из массива (если он не пустой) или из url (если id в массиве пустой)
$profile = user_by_id(!empty($data_array) ? $data_array['id'] : $_GET['id']);

if (empty($profile)) {
    show_error_page();
}

// загрузки шаблона заголовка страницы
tpl()->load_template('elements/title/title.tpl');

// компиляция строки (замена значения {value} из строки)
tpl()->set("{title}", page()->compile_str(page()->page_info('profile')->title, $profile->display_name));

// установка названия сайта
tpl()->set("{name}", config()->name);

// компляция шаблона
tpl()->compile('title');

// очистка переменных
tpl()->clear();


// получить недавние треки (1-ая вкладка)
$db_response = pdo()->prepare("SELECT tracks.id, tracks.title, tracks.path, tracks.cover, tracks.likes, tracks.auditions, tracks.reposts, tracks.private, tracks.comments, tracks.date_add, users.display_name FROM tracks LEFT JOIN users ON tracks.author = users.id WHERE tracks.author = :user_id AND tracks.private != 1 ORDER BY tracks.date_add DESC LIMIT 0, 10");
$db_response->setFetchMode(PDO::FETCH_OBJ);
$db_response->execute([':user_id' => $profile->id]);

// выборка данных из запроса
while ($tracks = $db_response->fetch()) {
    if (isset($tracks->id) && $tracks->private == 0 || isset($tracks->id) && isset($_SESSION['id']) && $profile->id == $_SESSION['id']) {
        // загрузка шаблона плейлиста
        tpl()->load_template('elements/profile/profile_data/recent_tracks.tpl');

        $is_liked = pdo()->query("SELECT id FROM users__favorite_actions WHERE track_id = $tracks->id")->fetchColumn();

        $copy_link = str_replace('files/tracks/', '', $tracks->path);
        $copy_link = substr($copy_link, 0, strrpos($copy_link, '.'));

        // передача переменных в шаблон
        tpl()->set("{id}", $tracks->id);
        tpl()->set("{path}", $tracks->path);
        tpl()->set("{track_name}", $tracks->title);
        tpl()->set("{cover}", $tracks->cover);
        tpl()->set("{likes}", $tracks->likes);
        tpl()->set("{auditions}", $tracks->auditions);
        tpl()->set("{reposts}", $tracks->reposts);
        tpl()->set("{comments}", $tracks->comments);
        tpl()->set("{display_name}", $tracks->display_name);
        tpl()->set("{date}", expand_date($tracks->date_add, 6));
        tpl()->set("{is_liked}", $is_liked ? 'true' : 'false');
        tpl()->set("{track_url}", $full_site_host . 'track?name=' . $copy_link);

        // компиляция шаблона
        tpl()->compile('all');

        // очистка переменных
        tpl()->clear();

    }
}

if (tpl()->result['all'] == '') {
    tpl()->load_template('elements/profile/profile_data/empty_data.tpl');
    tpl()->set("{text}", "The user hasn't gone missing yet.");
    tpl()->compile('all');
    tpl()->clear();
}

// получить популярные треки (2-ая вкладка)
$db_response = pdo()->prepare("SELECT tracks.id, tracks.title, tracks.path, tracks.cover, tracks.likes, tracks.auditions, tracks.reposts, tracks.comments, tracks.private, tracks.date_add, users.display_name FROM tracks LEFT JOIN users ON tracks.author = users.id WHERE tracks.author = :user_id AND tracks.private != 1 ORDER BY tracks.likes, tracks.auditions DESC LIMIT 20");
$db_response->setFetchMode(PDO::FETCH_OBJ);
$db_response->execute([':user_id' => $profile->id]);

// выборка данных из запроса
while ($tracks = $db_response->fetch()) {
    if (isset($tracks->id)) {

        $is_liked = pdo()->query("SELECT id FROM users__favorite_actions WHERE track_id = $tracks->id")->fetchColumn();

        $copy_link = str_replace('files/tracks/', '', $tracks->path);
        $copy_link = substr($copy_link, 0, strrpos($copy_link, '.'));

        // загрузка шаблона плейлиста
        tpl()->load_template('elements/profile/profile_data/popular_tracks.tpl');

        // передача переменных в шаблон
        tpl()->set("{id}", $tracks->id);
        tpl()->set("{track_name}", $tracks->title);
        tpl()->set("{path}", $tracks->path);
        tpl()->set("{cover}", $tracks->cover);
        tpl()->set("{likes}", $tracks->likes);
        tpl()->set("{auditions}", $tracks->auditions);
        tpl()->set("{reposts}", $tracks->reposts);
        tpl()->set("{comments}", $tracks->comments);
        tpl()->set("{display_name}", $tracks->display_name);
        tpl()->set("{date}", expand_date($tracks->date_add, 6));
        tpl()->set("{is_liked}", $is_liked ? 'true' : 'false');
        tpl()->set("{track_url}", $full_site_host . 'track?name=' . $copy_link);

        // компиляция шаблона
        tpl()->compile('popular_tracks_data');

        // очистка переменных
        tpl()->clear();

    }
}

if (tpl()->result['popular_tracks_data'] == '') {
    tpl()->load_template('elements/profile/profile_data/empty_data.tpl');
    tpl()->set("{text}", "The user hasn't added any tracks.");
    tpl()->compile('popular_tracks_data');
    tpl()->clear();
}

// получить все треки (3-ая вкладка)
$db_response = pdo()->prepare("SELECT tracks.id, tracks.title, tracks.path, tracks.cover, tracks.likes, tracks.auditions, tracks.reposts, tracks.comments, tracks.private, tracks.date_add, users.display_name FROM tracks LEFT JOIN users ON tracks.author = users.id WHERE tracks.author = :user_id AND tracks.private != 1 ORDER BY tracks.date_add DESC LIMIT 20");
$db_response->setFetchMode(PDO::FETCH_OBJ);
$db_response->execute([':user_id' => $profile->id]);

// выборка данных из запроса
while ($tracks = $db_response->fetch()) {
    if (isset($tracks->id) && $tracks->private == 0 || isset($tracks->id) && isset($_SESSION['id']) && $profile->id == $_SESSION['id']) {

        $is_liked = pdo()->query("SELECT id FROM users__favorite_actions WHERE track_id = $tracks->id")->fetchColumn();

        // загрузка шаблона плейлиста
        tpl()->load_template('elements/profile/profile_data/tracks.tpl');

        $copy_link = str_replace('files/tracks/', '', $tracks->path);
        $copy_link = substr($copy_link, 0, strrpos($copy_link, '.'));

        // передача переменных в шаблон
        tpl()->set("{id}", $tracks->id);
        tpl()->set("{track_name}", $tracks->title);
        tpl()->set("{path}", $tracks->path);
        tpl()->set("{cover}", $tracks->cover);
        tpl()->set("{likes}", $tracks->likes);
        tpl()->set("{auditions}", $tracks->auditions);
        tpl()->set("{reposts}", $tracks->reposts);
        tpl()->set("{comments}", $tracks->comments);
        tpl()->set("{display_name}", $tracks->display_name);
        tpl()->set("{date}", expand_date($tracks->date_add, 6));
        tpl()->set("{is_liked}", $is_liked ? 'true' : 'false');
        tpl()->set("{track_url}", $full_site_host . 'track?name=' . $copy_link);

        // компиляция шаблона
        tpl()->compile('tracks_data');

        // очистка переменных
        tpl()->clear();
    }
}

if (tpl()->result['tracks_data'] == '') {
    tpl()->load_template('elements/profile/profile_data/empty_data.tpl');
    tpl()->set("{text}", "The user hasn't added any tracks.");
    tpl()->compile('tracks_data');
    tpl()->clear();
}

// получить плейлисты (4-ая вкладка)
$db_response = pdo()->prepare("SELECT users__playlists.id, users__playlists.name, users__playlists.cover, users.id AS author_id, users.display_name, users__playlists.date_add, users__playlists.likes, users__playlists.auditions, users__playlists.comments, users__playlists.reposts, users__playlists.private FROM users__playlists LEFT JOIN users ON users__playlists.user_id = users.id WHERE users__playlists.private = 0 AND users__playlists.user_id = :user_id ORDER BY users__playlists.date_add DESC");
$db_response->setFetchMode(PDO::FETCH_OBJ);
$db_response->execute([':user_id' => $profile->id]);

// выборка данных из запроса
while ($playlists = $db_response->fetch()) {
    if (isset($playlists->id)) {
        tpl()->result['playlists_tracks_data'] = '';

        $playlists_response = pdo()->prepare("SELECT tracks.id, tracks.path, tracks.title, tracks.cover, tracks.auditions FROM users__playlists_tracks LEFT JOIN tracks on users__playlists_tracks.track_id = tracks.id WHERE users__playlists_tracks.playlist_id = :playlist_id AND tracks.private = 0 LIMIT 10");
        $playlists_response->setFetchMode(PDO::FETCH_OBJ);
        $playlists_response->execute([':playlist_id' => $playlists->id]);

        $index = 1;
        while ($playlists_tracks = $playlists_response->fetch()) {

            if (isset($playlists_tracks->id)) {
                tpl()->load_template('elements/profile/profile_data/playlists_tracks.tpl');
                tpl()->set('{message}', '');
                tpl()->set('{id}', $playlists_tracks->id);
                tpl()->set('{cover}', $playlists_tracks->cover);
                tpl()->set('{name}', $playlists_tracks->title);
                tpl()->set('{auditions}', $playlists_tracks->auditions);
                tpl()->set('{path}', $playlists_tracks->path);
                tpl()->set('{title}', 'Click to play');
                tpl()->set('{index}', $index);

                tpl()->compile('playlists_tracks_data');
                tpl()->clear();

                $index++;
            }
        }

        if (tpl()->result['playlists_tracks_data'] == '') {
            tpl()->load_template('elements/profile/profile_data/playlists_tracks.tpl');
            tpl()->set('{message}', 'No tracks in this playlist.');

            tpl()->compile('playlists_tracks_data');
            tpl()->clear();
        }

        $is_liked = pdo()->query("SELECT id FROM users__favorite_actions WHERE playlist_id = $playlists->id")->fetchColumn();

        // загрузка шаблона плейлиста
        tpl()->load_template('elements/profile/profile_data/playlists.tpl');

        // передача переменных в шаблон
        tpl()->set("{id}", $playlists->id);
        tpl()->set("{track_name}", $playlists->name);
        tpl()->set("{cover}", $playlists->cover);
        tpl()->set("{likes}", $playlists->likes);
        tpl()->set("{auditions}", $playlists->auditions);
        tpl()->set("{reposts}", $playlists->reposts);
        tpl()->set("{comments}", $playlists->reposts);
        tpl()->set("{display_name}", $playlists->display_name);
        tpl()->set("{date}", expand_date($playlists->date_add, 6));
        tpl()->set("{is_liked}", $is_liked ? 'true' : 'false');
        tpl()->set("{tracks}", tpl()->result['playlists_tracks_data']);
        tpl()->set("{playlist_url}", $full_site_host . 'playlist?name=' . $playlists->name . '&user_id=' . $playlists->author_id);

        // компиляция шаблона
        tpl()->compile('playlists_data');

        // очистка переменных
        tpl()->clear();
    }
}

if (tpl()->result['playlists_data'] == '') {
    tpl()->load_template('elements/profile/profile_data/empty_data.tpl');
    tpl()->set("{text}", "The user hasn't added any playlists.");
    tpl()->compile('playlists_data');
    tpl()->clear();
}

// подписчики пользователя
$db_response = pdo()->prepare("SELECT COUNT(*) FROM users__friends WHERE users__friends.id_taker = :user_id");
$db_response->setFetchMode(PDO::FETCH_OBJ);
$db_response->execute([':user_id' => $profile->id]);
$followers_count = $db_response->fetchColumn();

// подписки пользователя
$db_response = pdo()->prepare("SELECT COUNT(*) FROM users__friends WHERE users__friends.id_sender = :user_id");
$db_response->setFetchMode(PDO::FETCH_OBJ);
$db_response->execute([':user_id' => $profile->id]);
$following_count = $db_response->fetchColumn();

// количество треков которые выложил пользователь
$db_response = pdo()->prepare("SELECT COUNT(*) FROM tracks WHERE tracks.author = :user_id");
$db_response->setFetchMode(PDO::FETCH_OBJ);
$db_response->execute([':user_id' => $profile->id]);
$track_count = $db_response->fetchColumn();

// количество лайков на всех треках, которые были поставлены пользователем
$db_response = pdo()->prepare("SELECT COUNT(*) FROM users__favorite_actions WHERE users__favorite_actions.user_id = :user_id");
$db_response->setFetchMode(PDO::FETCH_OBJ);
$db_response->execute([':user_id' => $profile->id]);
$tracks_likes_count = $db_response->fetchColumn();

// получить треки, на которые пользователь поставил лайк
$db_response = pdo()->prepare("SELECT tracks.id, tracks.path, tracks.title, tracks.cover, users.id, users.display_name FROM users__favorite_actions LEFT JOIN tracks on users__favorite_actions.track_id = tracks.id LEFT JOIN users ON tracks.author = users.id WHERE users__favorite_actions.user_id = :user_id LIMIT 10");
$db_response->setFetchMode(PDO::FETCH_OBJ);
$db_response->execute([':user_id' => $profile->id]);

// выборка данных из запроса
while ($playlist_mini = $db_response->fetch()) {

    if (isset($playlist_mini->id)) {
        // загрузка шаблона плейлиста
        tpl()->load_template('elements/profile/panel_tracks_mini.tpl');

        // передача переменных в шаблон
        tpl()->set("{id}", $playlist_mini->id);
        tpl()->set("{name}", $playlist_mini->title);
        tpl()->set("{cover}", $playlist_mini->cover);

        // компиляция шаблона
        tpl()->compile('playlists_mini');

        // очистка переменных
        tpl()->clear();
    }
}

if (tpl()->result['playlists_mini'] == '') {
    tpl()->result['playlists_mini'] = "<div>The user hasn't created any playlists yet</div>";
}

$db_response = pdo()->prepare("SELECT tracks.cover, tracks.title, tracks.likes, tracks.auditions, tracks.comments, tracks.reposts, users.id AS author_id, users.display_name FROM users__favorite_actions LEFT JOIN tracks ON users__favorite_actions.track_id = tracks.id LEFT JOIN users ON tracks.author = users.id WHERE users__favorite_actions.user_id = :user_id AND tracks.private = 0 ORDER BY users__favorite_actions.id DESC LIMIT 3");
$db_response->setFetchMode(PDO::FETCH_OBJ);
$db_response->execute([':user_id' => $profile->id]);
while($tracks_mini = $db_response->fetch()) {
    tpl()->load_template('elements/profile/panel_tracks_mini.tpl');
    tpl()->set('{cover}',$tracks_mini->cover);
    tpl()->set('{author_id}',$tracks_mini->author_id);
    tpl()->set('{author}',$tracks_mini->display_name);
    tpl()->set('{name}',$tracks_mini->title);
    tpl()->set('{auditions}',$tracks_mini->auditions);
    tpl()->set('{likes}',$tracks_mini->likes);
    tpl()->set('{comments}',$tracks_mini->comments);
    tpl()->set('{reposts}',$tracks_mini->reposts);

    tpl()->compile('tracks_mini');
    tpl()->clear();
}

if (tpl()->result['tracks_mini'] == '') {
    tpl()->result['tracks_mini'] = "<div class='empty_track_likes'>User hasn't liked any track yet</div>";
}

if (is_auth()) {
    $is_followed = pdo()->query("SELECT id FROM users__friends WHERE id_sender = {$_SESSION['id']} AND id_taker = $profile->id")->fetchColumn();
} else {
    $is_followed = false;
}

tpl()->load_template('profile/profile.tpl');
tpl()->set('{user_id}', $_SESSION['id'] ?? '');
tpl()->set("{id}", $profile->id);
tpl()->set("{cover}", $profile->cover);
tpl()->set("{avatar}", $profile->avatar);
tpl()->set("{display_name}", $profile->display_name);
tpl()->set("{about}", $profile->status_message);
tpl()->set("{facebook}", $profile->facebook);
tpl()->set("{twitter}", $profile->twitter);
tpl()->set("{instagram}", $profile->instagram);
tpl()->set("{youtube}", $profile->youtube);
tpl()->set("{telegram}", $profile->telegram);
tpl()->set("{vk}", $profile->vk);
tpl()->set("{github}", $profile->github);
tpl()->set("{website}", $profile->website);
tpl()->set("{all_data}", tpl()->result['all']);
tpl()->set("{followers_count}", empty($followers_count) ? 0 : $followers_count);
tpl()->set("{following_count}", empty($following_count) ? 0 : $following_count);
tpl()->set("{tracks_count}", empty($track_count) ? 0 : $track_count);
tpl()->set("{likes_count}", empty($tracks_likes_count) ? 0 : $tracks_likes_count);
tpl()->set("{playlists_mini}", tpl()->result['playlists_mini']);
tpl()->set("{is_followed}", $is_followed == true ? 'true' : 'false');
tpl()->set("{popular_tracks_data}", tpl()->result['popular_tracks_data']);
tpl()->set("{tracks_data}", tpl()->result['tracks_data']);
tpl()->set("{playlists_data}", tpl()->result['playlists_data']);
tpl()->set("{is_very}", $profile->verification == 1 ? 'true' : 'false');
tpl()->set("{tracks_mini}", tpl()->result['tracks_mini']);
tpl()->set("{deleted}", $profile->deleted == 1 ? 'true' : 'false');
tpl()->compile('content');
tpl()->clear();

return json_encode(['title' => tpl()->getShow(tpl()->result['title']), 'content' => tpl()->getShow(tpl()->result['content'])]);