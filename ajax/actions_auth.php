<?php

// include start.php
include_once "../inc/start.php";

// check if post phpaction is empty
if (empty($_POST['phpaction'])) {
    echo 'Error: [Direct call include]';
    exit();
}

// if config object tokes equals to post token
if ($config->token == 1 && ($_SESSION['token'] != clean($_POST['token'],))) {
    echo 'Error: [Invalid token]';
    exit();
}

// check if isset session id
if (empty($_SESSION['id'])) {
    echo 'Error: [Only available to authorized users]';
    exit();
}

/* CHANGE USER AVATAR */
if (isset($_POST['change_avatar'])) {

    $id = check_js($_POST['id'], 'int');

    // upload avatar
    $result = file_uploads("files/avatars", $_FILES['image']);

    // if upload error
    if ($result['alert'] == 'error') {
        exit(json_encode(['status' => 'error', 'message' => $result['message']]));
    }

    // if upload success change avatar in database
    $db_response = pdo()->prepare("UPDATE users SET avatar = :avatar WHERE id = :uid");
    $db_response->execute([':uid' => $id, ':avatar' => $result['full_dir']]);

    exit(json_encode(['status' => 'success', 'file' => $result['full_dir']]));
}

/* CHANGE USER COVER */
if (isset($_POST['change_cover'])) {

    $id = check_js($_POST['id'], 'int');

    // upload cover
    $result = file_uploads("files/cover", $_FILES['image']);

    // if upload error
    if ($result['alert'] == 'error') {
        exit(json_encode(['status' => 'error', 'message' => $result['message']]));
    }

    // if upload success change cover in database
    $db_response = pdo()->prepare("UPDATE users SET cover = :cover WHERE id = :uid");

    // execute query with params
    $db_response->execute([':uid' => $id, ':cover' => $result['full_dir']]);

    exit(json_encode(['status' => 'success', 'file' => '/' . $result['full_dir']]));
}

/* SAVE USER STATUS */
if (isset($_POST['change_user_status'])) {

    // clean post data
    $status = check_js($_POST['status']);

    // if message is empty
    if (!empty($status)) {

        // strip tags from message
        $status = strip_tags($status);
    } else {

        // set message to none
        $status = null;
    }


    $db_response = pdo()->prepare("UPDATE users SET status_message = :status WHERE id = :uid");

    // if status changed
    if ($db_response->execute([':uid' => $_SESSION['id'], ':status' => $status])) {
        exit(json_encode(['status' => 'success', 'message' => $status]));
    }

    exit(json_encode(['status' => 'error']));
}

if (isset($_POST['get_user_volume'])) {

    // get user volume
    $user_volume = $pdo->query("SELECT volume FROM users WHERE id = " . $_SESSION['id'])->fetchColumn();

    // exit with json
    exit(json_encode(['volume' => $user_volume]));
}

if (isset($_POST['update_volume_value'])) {

    if (is_auth()) {
        $volume = check_js($_POST['volume'], 'int');

        $pdo->query("UPDATE users SET volume = $volume WHERE id = " . $_SESSION['id']);

        // exit with json
        exit(json_encode(['volume' => $volume]));
    } else {
        exit();
    }
}

if (isset($_POST['get_notifications'])) {

    $tpl->result['notifications'] = '';

    $db_response = $pdo->prepare("SELECT * FROM notifications WHERE user_id = :uid ORDER BY date, status DESC");
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    $db_response->execute([':uid' => $_SESSION['id']]);

    while ($noty = $db_response->fetch()) {
        $tpl->load_template('elements/notifications/notification.tpl');
        $tpl->set('{id}', $noty->id);
        $tpl->set('{message}', $noty->message);
        $tpl->set('{date}', expand_date($noty->date, 0));

        if ($noty->status == 0) {
            $tpl->set('{status}', 'unread');
        } else {
            $tpl->set('{status}', 'read');
        }

        $tpl->compile('notifications');
        $tpl->clear();
    }

    if ($tpl->result['notifications'] == '') {
        $tpl->load_template('elements/notifications/empty_notifications.tpl');
        $tpl->compile('notifications');
        $tpl->clear();
    }

    $tpl->show($tpl->result['notifications']);
    $tpl->global_clear();

    exit();
}

if (isset($_POST['clear_all_notifications'])) {

    $db_response = pdo()->prepare("DELETE FROM notifications WHERE user_id = :uid");
    if ($db_response->execute([':uid' => $_SESSION['id']])) {
        exit(json_encode(['status' => 'success']));
    }

    exit(json_encode(['status' => 'error']));
}

if (isset($_POST['delete_notification'])) {

    $id = check_js($_POST['id'], 'int');

    $db_response = pdo()->prepare("DELETE FROM notifications WHERE id = :id");
    if ($db_response->execute([':id' => $id])) {
        exit(json_encode(['status' => 'success']));
    }

    exit(json_encode(['status' => 'error']));
}

if (isset($_POST['follow_actions'])) {

    $id = check_js($_POST['id'], 'int');
    $type = check_js($_POST['type']);

    if ($type == 'follow') {

        $db_response = pdo()->prepare("INSERT INTO users__friends (id_taker, id_sender) VALUES (:friend_id, :user_id)");
        if ($db_response->execute([':user_id' => $_SESSION['id'], ':friend_id' => $id])) {
            exit(json_encode(['status' => 'success']));
        }
    } elseif ($type == 'unfollow') {
        $db_response = pdo()->prepare("DELETE FROM users__friends WHERE id_sender = :user_id AND id_taker = :friend_id");
        if ($db_response->execute([':user_id' => $_SESSION['id'], ':friend_id' => $id])) {
            exit(json_encode(['status' => 'success']));
        }
    }

    exit(json_encode(['status' => 'error']));
}

if (isset($_POST['confirm_track'])) {

    $file = $_POST['file'];
    $name = check_js($_POST['name']);
    $cover = $_POST['cover'];
    $private = check_js($_POST['private'], 'int');

    $db_response = pdo()->prepare("INSERT INTO tracks (author, path, title, cover, private) VALUES (:author, :path, :title, :cover, :private)");
    if ($db_response->execute([':author' => $_SESSION['id'], ':path' => $file, ':title' => $name, ':cover' => $cover, ':private' => $private])) {
        exit(json_encode(['status' => 'success']));
    }

    exit(json_encode(['status' => 'error']));
}

if (isset($_POST['set_comment_to_track'])) {

    $id = check_js($_POST['id'], 'int');
    $comment = check_js($_POST['comment']);

    $db_response = pdo()->prepare("INSERT INTO users__tracks_comments (track_id, user_id, comment) VALUES (:track_id, :user_id, :comment)");
    if ($db_response->execute([':track_id' => $id, ':user_id' => $_SESSION['id'], ':comment' => $comment])) {
        $db_response = pdo()->prepare("UPDATE tracks SET comments = comments + 1 WHERE id = :track_id");
        $db_response->execute([':track_id' => $id]);

        $author = get_author_by_track_id($id);

        inc_notifications();
        $result = new_comment_notification(user()->id);
        send_noty(pdo(), $result, $author->id);

        exit(json_encode(['status' => 'success']));
    }

    exit(json_encode(['status' => 'error']));
}

if (isset($_POST['default_cover'])) {

    $id = check_js($_POST['id'], 'int');

    $db_response = pdo()->prepare("UPDATE users SET cover = DEFAULT WHERE id = :id");
    if ($db_response->execute([':id' => $id])) {
        exit(json_encode(['status' => 'success', 'cover' => '/files/cover/standart.png']));
    }

    exit(json_encode(['status' => 'error']));
}

if (isset($_POST['save_profile_setting'])) {
    if (isset($_POST['id'])) {
        $id = check_js($_POST['id'], 'int');
    } else {
        $id = $_SESSION['id'];
    }

    $display_name = check_js($_POST['display_name']);
    $name = check_js($_POST['name']);
    $lastname = check_js($_POST['lastname']);
    $date = $_POST['date'];
    $email = check_js($_POST['email']);
    $email_noty = check_js($_POST['email_noty']);
    $gender = check_js($_POST['gender']);
    $facebook = check_js($_POST['facebook']);
    $twitter = check_js($_POST['twitter']);
    $instagram = check_js($_POST['instagram']);
    $youtube = check_js($_POST['youtube']);
    $telegram = check_js($_POST['telegram']);
    $vk = check_js($_POST['vk']);
    $github = check_js($_POST['github']);
    $website = check_js($_POST['website']);

    // поверка на длину имени
    if (!$user_obj->check_display_name_length($display_name) && user_by_id($id)->display_name != $display_name) {
        exit(json_encode(['status' => 'error', 'message' => 'The name must be at least 2 characters and not more than 50.']));
    }

    // проверка на занятость имени
    if (!$user_obj->check_for_display_name_exist($display_name) && user_by_id($id)->display_name != $display_name) {
        exit(json_encode(['status' => 'error', 'message' => 'Display name is already exist.']));
    }

    // проверка на валидность почты
    if (!$user_obj->check_email($email)) {
        exit(json_encode(['status' => 'error', 'message' => 'E-mail entered incorrectly.']));
    }

    // проверка на занятость почты
    if (!$user_obj->check_email_busyness($email) && user_by_id($id)->email != $email) {
        exit(json_encode(['status' => 'error', 'message' => 'The E-mail you entered is already registered.']));
    }

    if (isset($_POST['password']) && isset($_POST['password_repeat'])) {
        $password = check_js($_POST['password']);
        $password_repeat = check_js($_POST['password_repeat']);

        if (!$user_obj->check_password_length($password) && !empty($password)) {
            exit(json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters long']));
        }

        if ($password != $password_repeat && !empty($password) && !empty($password_repeat)) {
            exit(json_encode(['status' => 'error', 'message' => 'Passwords do not match.']));
        }
    }

    $db_response = pdo()->prepare("UPDATE users SET name = :name, lastname = :lastname, birth = :date, email = :email, email_notice = :email_noty, gender = :gender, facebook = :facebook, twitter = :twitter, instagram = :instagram, youtube = :youtube, telegram = :telegram, vk = :vk, github = :github, website = :website WHERE users.id = :user_id");
    if ($db_response->execute([':name' => $name, ':lastname' => $lastname, ':date' => $date, ':email' => $email, ':email_noty' => $email_noty, ':gender' => $gender, ':facebook' => $facebook, ':twitter' => $twitter, ':instagram' => $instagram, ':youtube' => $youtube, ':telegram' => $telegram, ':vk' => $vk, ':github' => $github, ':website' => $website, ':user_id' => $id])) {
        if (!empty($password)) {
            $db_response = pdo()->prepare("UPDATE users SET password = :password WHERE users.id = :user_id");
            $db_response->execute([':password' => $user_obj->convert_password($password, $config->salt), ':user_id' => $_SESSION['id']]);
        }

        exit(json_encode(['status' => 'success', 'message' => 'Profile updated successfully.', 'date' => $date]));
    }

    exit(json_encode(['status' => 'error', 'message' => 'Error while updating profile.']));
}

if (isset($_POST['delete_account'])) {
    if (isset($_POST['id'])) {
        $id = check_js($_POST['id'], 'int');
    } else {
        $id = $_SESSION['id'];
    }

    $db_response = pdo()->prepare("UPDATE users SET deleted = 1 WHERE id = :id");
    if ($db_response->execute([':id' => $id])) {
        exit(json_encode(['status' => 'success', 'message' => 'Account deleted successfully.']));
    }

    exit(json_encode(['status' => 'error', 'message' => 'Error while deleting account.']));
}

if (isset($_POST['edit_track_comment']) && is_worthy('q')) {
    $id = check_js($_POST['id'], 'int');
    $comment = check_js($_POST['comment']);

    $db_response = pdo()->prepare("UPDATE users__tracks_comments SET comment = :comment WHERE id = :id");
    if ($db_response->execute([':comment' => $comment, ':id' => $id])) {
        exit(json_encode(['status' => 'success', 'message' => 'Comment updated successfully.']));
    }

    exit(json_encode(['status' => 'error', 'message' => 'Error while updating comment.']));
}

if (isset($_POST['delete_comment'])) {
    $id = check_js($_POST['id'], 'int');

    $db_response = pdo()->prepare("DELETE FROM users__tracks_comments WHERE id = :id");
    if ($db_response->execute([':id' => $id])) {
        exit(json_encode(['status' => 'success', 'message' => 'Comment deleted successfully.']));
    }

    exit(json_encode(['status' => 'error', 'message' => 'Error while deleting comment.']));
}

if (isset($_POST['unvery_user'])) {
    $id = check_js($_POST['id'], 'int');

    $db_response = pdo()->prepare("UPDATE users SET verification = 0 WHERE id = :id");
    if ($db_response->execute([':id' => $id])) {
        exit(json_encode(['status' => 'success', 'message' => 'User unverified successfully.']));
    }

    exit(json_encode(['status' => 'error', 'message' => 'Error while unverify user.']));
}

if (isset($_POST['very_user'])) {
    $id = check_js($_POST['id'], 'int');

    $db_response = pdo()->prepare("UPDATE users SET verification = 1 WHERE id = :id");
    if ($db_response->execute([':id' => $id])) {
        exit(json_encode(['status' => 'success', 'message' => 'User verified successfully.']));
    }

    exit(json_encode(['status' => 'error', 'message' => 'Error while unvery user.']));
}

if (isset($_POST['send_very_request'])) {

    if (isset($_POST['id'])) {
        $id = check_js($_POST['id'], 'int');
    } else {
        $id = $_SESSION['id'];
    }

    $db_response = pdo()->prepare("UPDATE users SET is_very_request = 1 WHERE id = :id");
    if ($db_response->execute([':id' => $id])) {
        exit(json_encode(['status' => 'success', 'message' => 'Very request sent successfully.']));
    }

    exit(json_encode(['status' => 'error', 'message' => 'Error while sending very request.']));
}

if (isset($_POST['cancel_very_request'])) {
    if (isset($_POST['id'])) {
        $id = check_js($_POST['id'], 'int');
    } else {
        $id = $_SESSION['id'];
    }

    $db_response = pdo()->prepare("UPDATE users SET is_very_request = 0 WHERE id = :id");
    if ($db_response->execute([':id' => $id])) {
        exit(json_encode(['status' => 'success', 'message' => 'Very request canceled successfully.']));
    }

    exit(json_encode(['status' => 'error', 'message' => 'Error while canceling very request.']));
}

if (isset($_POST['restore_user'])) {
    $id = check_js($_POST['id'], 'int');

    $db_response = pdo()->prepare("UPDATE users SET deleted = 0 WHERE id = :id");
    if ($db_response->execute([':id' => $id])) {
        exit(json_encode(['status' => 'success', 'message' => 'User restored successfully.']));
    }

    exit(json_encode(['status' => 'error', 'message' => 'Error while restoring user.']));
}

if (isset($_POST['create_playlist'])) {
    $name = check_js($_POST['name']);
    $type = $_POST['type'] == 'private' ? 1 : 0;

    if (empty($name)) {
        exit(json_encode(['status' => 'error', 'message' => 'Playlist name is empty.']));
    }

    if (strlen($name) > 100) {
        exit(json_encode(['status' => 'error', 'message' => 'Playlist name is too long.']));
    }

    if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬]/', $name)) {
        exit(json_encode(['status' => 'error', 'message' => 'Playlist name contains special characters.']));
    }

    // Check if playlist exists
    $db_response = pdo()->prepare("SELECT id FROM users__playlists WHERE name = :name");
    $db_response->execute([':name' => $name]);
    if (!empty($db_response->fetchAll())) {
        exit(json_encode(['status' => 'error', 'message' => 'Playlist already exists.']));
    }

    $db_response = pdo()->prepare("INSERT INTO users__playlists (user_id, name,  private) VALUES (:user_id, :name, :private)");
    if ($db_response->execute([':user_id' => $_SESSION['id'], ':name' => $name, ':private' => $type])) {
        exit(json_encode(['status' => 'success', 'message' => 'Playlist created successfully.']));
    } else {
        exit(json_encode(['status' => 'error', 'message' => 'Error while creating playlist.']));
    }
}

if (isset($_POST['find_user_playlists'])) {
    $id = check_js($_POST['id'], 'int');
    $name = check_js($_POST['name']);

    tpl()->result['playlists_mini'] = '';

    $db_response = pdo()->prepare("SELECT users__playlists.* FROM users__playlists LEFT JOIN users__playlists_tracks ON users__playlists.id = users__playlists_tracks.playlist_id WHERE (users__playlists_tracks.track_id != :track_id OR users__playlists_tracks.track_id IS NULL) AND users__playlists.user_id = :user_id AND users__playlists.name LIKE :search GROUP BY users__playlists.id");
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    if ($db_response->execute([':track_id' => $id, ':user_id' => $_SESSION['id'], ':search' => '%' . $name . '%'])) {
        while ($playlists = $db_response->fetch()) {
            tpl()->load_template('elements/modals/modal_data/playlists_mini.tpl');
            tpl()->set('{cover}', $playlists->cover);
            tpl()->set('{name}', $playlists->name);
            tpl()->set('{id}', $playlists->id);
            tpl()->set('{user_id}', $playlists->user_id);
            tpl()->compile('playlists_mini');
            tpl()->clear();
        }

        if (tpl()->result['playlists_mini'] == '') {
            tpl()->result['playlists_mini'] = '<div class="no_results">No results</div>';
        }

        exit(json_encode(['status' => 'success', 'data' => tpl()->getShow(tpl()->result['playlists_mini'])]));
    }

    exit(json_encode(['status' => 'error', 'message' => 'Error while finding playlists.']));
}

if (isset($_POST['add_track_to_playlist'])) {
    $playlist_id = check_js($_POST['playlist_id'], 'int');
    $track_id = check_js($_POST['track_id'], 'int');

    $db_response = pdo()->prepare("INSERT INTO 	users__playlists_tracks (playlist_id, track_id) VALUES (:playlist_id, :track_id)");
    if ($db_response->execute([':playlist_id' => $playlist_id, ':track_id' => $track_id])) {
        exit(json_encode(['status' => 'success', 'message' => 'Track added to playlist successfully.']));
    }

    exit(json_encode(['status' => 'error', 'message' => 'Error while adding track to playlist.']));
}

if (isset($_POST['get_library_playlists'])) {

    tpl()->result['playlists_mini'] = '';

    $db_response = pdo()->prepare("SELECT * FROM users__playlists WHERE user_id = :user_id ORDER BY id DESC LIMIT 6");
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    if ($db_response->execute([':user_id' => $_SESSION['id']])) {
        while ($playlists = $db_response->fetch()) {
            tpl()->load_template('elements/playlists/playlist_item.tpl');
            tpl()->set('{playlist_cover}', $playlists->cover);
            tpl()->set('{playlist_name}', $playlists->name);
            tpl()->set('{script}', "call_modal('edit_playlist', {id: " . $playlists->id . "})");
            tpl()->set('{title}', "Click to edit playlist");
            tpl()->set('{author}', '');
            tpl()->compile('playlists_mini');
            tpl()->clear();
        }

        if (tpl()->result['playlists_mini'] == '') {
            tpl()->result['playlists_mini'] = "<div class='empty_message'><span>You haven't created any playlist yet</span></div>";
        }

        exit(json_encode(['status' => 'success', 'data' => tpl()->getShow(tpl()->result['playlists_mini'])]));
    }

    exit(json_encode(['status' => 'error', 'message' => 'Error while finding playlists.']));
}

if (isset($_POST['delete_track_from_playlist'])) {
    $playlist_id = check_js($_POST['playlist_id'], 'int');
    $track_id = check_js($_POST['track_id'], 'int');

    $db_response = pdo()->prepare("DELETE FROM users__playlists_tracks WHERE playlist_id = :playlist_id AND track_id = :track_id");
    if ($db_response->execute([':playlist_id' => $playlist_id, ':track_id' => $track_id])) {
        exit(json_encode(['status' => 'success', 'message' => 'Track deleted from playlist successfully.']));
    }

    exit(json_encode(['status' => 'error', 'message' => 'Error while deleting track from playlist.']));
}

if (isset($_POST['update_playlist_cover'])) {
    $playlist_id = check_js($_POST['playlist_id'], 'int');
    $cover = $_FILES['cover'];

    $result = file_uploads('files/playlists_covers', $cover);
    if ($result['alert'] == 'error') {
        exit(json_encode(['status' => 'error', 'message' => $result['message']]));
    }

    $db_response = pdo()->prepare("UPDATE users__playlists SET cover = :cover WHERE id = :playlist_id");
    if ($db_response->execute([':playlist_id' => $playlist_id, ':cover' => $result['full_dir']])) {
        exit(json_encode(['status' => 'success', 'message' => 'Playlist cover updated successfully.']));
    }

    exit(json_encode(['status' => 'error', 'message' => 'Error while updating playlist cover.']));
}

if (isset($_POST['edit_playlist'])) {
    $playlist_id = check_js($_POST['playlist_id'], 'int');
    $type = $_POST['type'] == 'private' ? 1 : 0;
    $name = check_js($_POST['name']);

    $db_response = pdo()->prepare("UPDATE users__playlists SET private = :private, name = :name WHERE id = :playlist_id");
    if ($db_response->execute([':private' => $type, ':name' => $name, ':playlist_id' => $playlist_id])) {
        exit(json_encode(['status' => 'success', 'message' => 'Playlist updated successfully.']));
    }

    exit(json_encode(['status' => 'error', 'message' => 'Error while updating playlist.']));
}

if (isset($_POST['delete_playlist'])) {
    $id = check_js($_POST['id'], 'int');
    $password = check_js($_POST['password']);

    $db_response = pdo()->prepare("SELECT password FROM users WHERE id = :id");
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    if ($db_response->execute([':id' => $_SESSION['id']])) {
        $user = $db_response->fetch();
        if ($user_obj->convert_password($password, $config->salt) == $user->password) {
            $db_response = pdo()->prepare("DELETE FROM users__playlists WHERE id = :id");
            if ($db_response->execute([':id' => $id])) {
                exit(json_encode(['status' => 'success', 'message' => 'Playlist deleted successfully.']));
            }
        }
        exit(json_encode(['status' => 'error', 'message' => 'Wrong password.']));
    }
    exit(json_encode(['status' => 'error', 'message' => 'Error while deleting playlist.']));
}

if (isset($_POST['upload_track_file'])) {
    $file = $_FILES['file'];

    $result = file_uploads('files/tracks', $file);
    if ($result['alert'] == 'error') {
        exit(json_encode(['status' => 'error', 'message' => $result['message']]));
    }

    exit(json_encode(['status' => 'success', 'message' => 'Track uploaded successfully.', 'data' => $result['full_dir']]));
}

if (isset($_POST['upload_image_file'])) {
    $image = $_FILES['image'];

    $result = file_uploads('files/track_covers', $image);
    if ($result['alert'] == 'error') {
        exit(json_encode(['status' => 'error', 'message' => $result['message']]));
    }

    exit(json_encode(['status' => 'success', 'message' => 'Track image uploaded successfully.', 'data' => $result['full_dir']]));
}

// upload track
if (isset($_POST['upload_track'])) {
    $name = check_js($_POST['name']);
    $file = check_js($_POST['file']);
    $image = check_js($_POST['image']);
    $genre = check_js($_POST['genre']);
    $description = check_js($_POST['description']);
    $privacy = $_POST['privacy'] == 'private' ? 1 : 0;

    // get today date and time
    $date = date('Y-m-d H:i:s');

    $db_response = pdo()->prepare("INSERT INTO tracks (author, title, path, cover, description, genre, private, date_add) VALUES (:user_id, :name, :file, :image, :description, :genre, :privacy, :date)");
    if ($db_response->execute([':user_id' => $_SESSION['id'], ':name' => $name, ':file' => $file, ':image' => $image, ':description' => $description, ':genre' => $genre, ':privacy' => $privacy, ':date' => $date])) {
        exit(json_encode(['status' => 'success', 'message' => 'Track uploaded successfully.']));
    }

    exit(json_encode(['status' => 'error', 'message' => 'Error while uploading track.']));
}

if (isset($_POST['block_user'])) {
    $id = check_js($_POST['id'], 'int');

    // find blocked user in users__black_list table
    $db_response = pdo()->prepare("SELECT * FROM users__black_list WHERE who = :who AND whom = :whom");
    $db_response->setFetchMode(PDO::FETCH_OBJ);
    if ($db_response->execute([':who' => $_SESSION['id'], ':whom' => $id])) {
        $user = $db_response->fetch();
        if ($user) {
            $db_response = pdo()->prepare("DELETE FROM users__black_list WHERE who = :who AND whom = :whom");
            if ($db_response->execute([':who' => $_SESSION['id'], ':whom' => $id])) {
                exit(json_encode(['status' => 'success', 'message' => 'User unblocked successfully.', 'blocked' => false]));
            }
        } else {
            $db_response = pdo()->prepare("INSERT INTO users__black_list (who, whom) VALUES (:who, :whom)");
            if ($db_response->execute([':who' => $_SESSION['id'], ':whom' => $id])) {
                exit(json_encode(['status' => 'success', 'message' => 'User blocked successfully.', 'blocked' => true]));
            }
        }
    }
}

if (isset($_POST['report_user'])) {
    $id = check_js($_POST['id'], 'int');
    $reason = check_js($_POST['reason']);
    $report_text = check_js($_POST['report_text']);
    $is_need_to_block = $_POST['is_need_to_block'] == 'true' ? 1 : 0;

    if ($id == $_SESSION['id']) {
        exit(json_encode(['status' => 'error', 'message' => 'You can\'t report yourself.']));
    }

    if ($reason == 'Please select the reason for the user complaint') {
        exit(json_encode(['status' => 'error', 'message' => 'Please select a reason.']));
    }

    if ($reason == 'Other') {
        $reason = check_js($_POST['other_reason']);

        if (empty($reason)) {
            exit(json_encode(['status' => 'error', 'message' => 'Please, enter reason.']));
        }
    }

    if ($is_need_to_block) {
        $db_response = pdo()->prepare("SELECT * FROM users__black_list WHERE who = :who AND whom = :whom");
        $db_response->setFetchMode(PDO::FETCH_OBJ);
        if ($db_response->execute([':who' => $_SESSION['id'], ':whom' => $id])) {
            $user = $db_response->fetch();
            if (!$user) {
                $db_response = pdo()->prepare("INSERT INTO users__black_list (who, whom) VALUES (:who, :whom)");
                $db_response->execute([':who' => $_SESSION['id'], ':whom' => $id]);
            }
        }
    }

    $db_response = pdo()->prepare("INSERT INTO users__reports (from_user, to_user, reason, report_text, user_blocked) VALUES (:from_user, :to_user, :reason, :report_text, :user_blocked)");
    if ($db_response->execute([':from_user' => $_SESSION['id'], ':to_user' => $id, ':reason' => $reason, ':report_text' => $report_text, ':user_blocked' => $is_need_to_block])) {
        exit(json_encode(['status' => 'success', 'message' => 'User reported successfully.']));
    }

    exit(json_encode(['status' => 'error', 'message' => 'Error while reporting user.']));
}

if (isset($_POST['update_track_cover'])) {
    $id = check_js($_POST['track_id'], 'int');
    $cover = $_FILES['cover'];

    $result = file_uploads('files/track_covers', $cover);
    if ($result['alert'] == 'error') {
        exit(json_encode(['status' => 'error', 'message' => $result['message']]));
    }

    $db_response = pdo()->prepare("UPDATE tracks SET cover = :cover WHERE id = :id");
    if ($db_response->execute([':id' => $id, ':cover' => $result['full_dir']])) {
        exit(json_encode(['status' => 'success', 'message' => 'Track cover updated successfully.', 'cover' => $result['full_dir']]));
    }

    exit(json_encode(['status' => 'error', 'message' => 'Error while updating track cover.']));
}

if (isset($_POST['edit_track'])) {
    $id = check_js($_POST['id'], 'int');
    $title = check_js($_POST['title']);
    $genre = check_js($_POST['genre']);
    $description = check_js($_POST['description']);
    $private = $_POST['privacy'] == 'Private' ? 1 : 0;

    $db_response = pdo()->prepare("UPDATE tracks SET title = :title, genre = :genre, description = :description, private = :private WHERE id = :id");
    if ($db_response->execute([':id' => $id, ':title' => $title, ':genre' => $genre, ':description' => $description, ':private' => $private])) {

        $track_name = pdo()->prepare("SELECT path FROM tracks WHERE id = :id");
        $track_name->setFetchMode(PDO::FETCH_OBJ);
        $track_name->execute([':id' => $id]);
        $track_name = $track_name->fetch();
        $track_name = $track_name->path;
        $track_name = str_replace('.mp3', '', $track_name);
        $track_name = str_replace('files/tracks/', '', $track_name);


        exit(json_encode(['status' => 'success', 'message' => 'Track updated successfully.', 'track_name' => $track_name]));
    }

    exit(json_encode(['status' => 'error', 'message' => 'Error while updating track.']));
}

if (isset($_POST['delete_track'])) {
    $id = check_js($_POST['id'], 'int');

    $author_id = get_author_by_track_id($id)->id;

    $db_response = pdo()->prepare("DELETE FROM tracks WHERE id = :id");
    if ($db_response->execute([':id' => $id])) {

        exit(json_encode(['status' => 'success','message' => 'Track deleted successfully.', 'user_id' => $author_id]));
    }

    exit(json_encode(['status' => 'error', 'message' => 'Error while deleting track.']));
}