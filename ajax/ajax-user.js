// создание полей формы
let data = {};

// очистка полей формы
function clear_data() {
    data = {};
}

// функция для динамического изменение контента сайта
function load_template(template, data_array = null, module_file = null) {
    data['load_template'] = 1;
    data['template'] = template;

    if (module_file) {
        data['module_file'] = module_file;
    }

    if (data_array) {
        data['data_array'] = data_array;
    }

    send_query('ajax/actions.php', data, (result) => {
        document.title = result.title;

        if (data_array) {
            let history_get = '';

            for (let i = 0; i < Object.keys(data_array).length; i++) {
                if (i === 0) {
                    history_get += '?' + Object.keys(data_array)[i] + '=' + data_array[Object.keys(data_array)[i]];
                } else {
                    history_get += '&' + Object.keys(data_array)[i] + '=' + data_array[Object.keys(data_array)[i]];
                }
            }

            if (module_file) {
                history.pushState(null, null, module_file + history_get);
            } else {
                history.pushState(null, null, template + history_get);
            }
        } else {
            history.pushState(null, null, template);
        }

        $('body main > .container').html(result.content);
    });
}

/* ================= АВТОРИЗАЦИЯ И РЕГИСТРАЦИЯ ================= */

// метод авторизации
function user_login() {

    // получение данных формы
    data['user_login'] = 1;
    data['login_email'] = $('#login_email').val();
    data['password'] = $('#password').val();

    // отправка данных на сервер
    send_query('ajax/actions.php', data, (result) => {

        // если авторизация прошла успешно
        if (result.status === 'success') {

            $('#message_result').text(result.message);
            $('#message_result').css('color', 'green');

            setTimeout(() => {
                go_to('index');
            }, 3000);
        } else {
            $('#message_result').text(result.message);
            $('#message_result').css('color', 'red');
        }
    });
}

// выход из аккаунта
function user_exit() {

    // получение данных формы
    data['user_exit'] = 1;

    // отправка данных на сервер
    send_query('ajax/actions.php', data, (res) => {

        // выход из аккаунта google
        const auth2 = gapi.auth2.getAuthInstance();
        if (auth2.isSignedIn.get()) {
            auth2.signOut();
        }

        // выход из аккаунта facebook
        if (typeof FB !== 'undefined') {
            FB.getLoginStatus(function (response) {
                if (response && response.status === 'connected') {
                    FB.logout();
                }
            });
        }

        // перезагрузка страницы
        location.reload();
    }, 'text');
}

function auth_by_api(type = 'google', response, accessToken = null) {
    data['auth_by_api'] = 1;

    if (type === 'google') {
        const profile = response.getBasicProfile();
        data['login'] = profile.getEmail();
        data['password'] = profile.getId();
        data['avatar'] = profile.getImageUrl() ? profile.getImageUrl() : 'https://lh3.googleusercontent.com/-XdUIqdMkCWA/AAAAAAAAAAI/AAAAAAAAAAA/4252rscbv5M/photo.jpg';
        data['email'] = profile.getEmail();
        data['display_name'] = profile.getName();
        data['firstname'] = profile.getGivenName() ? profile.getGivenName() : profile.getName();
        data['lastname'] = profile.getFamilyName() ? profile.getFamilyName() : '';

        send_query('ajax/actions.php', data, (result) => {
            // если авторизация прошла успешно
            if (result.status === 'success') {

                $('#message_result').text(result.message);
                $('#message_result').css('color', 'green');

                setTimeout(() => {
                    go_to('index');
                }, 3000);
            } else {
                $('#message_result').text(result.message);
                $('#message_result').css('color', 'red');
            }
        });
    } else if (type === 'facebook') {

        FB.api('/' + response.id + '/picture?type=large&redirect=false&access_token=' + accessToken, function (image) {
            data['login'] = response.email;
            data['password'] = response.id;
            data['avatar'] = image.data.url;
            data['email'] = response.email;
            data['display_name'] = response.name;
            data['firstname'] = response.first_name;
            data['lastname'] = response.last_name;

            send_query('ajax/actions.php', data, (result) => {
                // если авторизация прошла успешно
                if (result.status === 'success') {

                    $('#message_result').text(result.message);
                    $('#message_result').css('color', 'green');

                    setTimeout(() => {
                        go_to('index');
                    }, 3000);
                } else {
                    $('#message_result').text(result.message);
                    $('#message_result').css('color', 'red');
                }
            });
        });
    }
}

// регистрация нового пользователя
function registration() {

    // получение данных формы
    data['registration'] = 1;
    data['login'] = $('#reg_login').val();
    data['display_name'] = $('#reg_display_name').val();
    data['email'] = $('#reg_email').val();
    data['password'] = $('#reg_password').val();
    data['password_repeat'] = $('#reg_password_repeat').val();

    // отправка данных на сервер
    send_query('ajax/actions.php', data, (result) => {
        if (result.status === 'success') {
            $('#reg_message_result').text(result.message);
            $('#reg_message_result').css('color', 'green');

            if (result.redirect) {

                // перенаправление на главную страницу
                setInterval(() => {
                    go_to('index');
                }, 3000);
            }

        } else {
            $('#reg_message_result').text(result.message);
            $('#reg_message_result').css('color', 'red');
        }
    });
}

// отправка пароля для восстановления
function send_new_pass() {

    // получение данных формы
    data['send_new_pass'] = 1;
    data['email'] = $('#email_recovery').val();

    // отправка данных на сервер
    send_query('ajax/actions.php', data, (result) => {

        // вывод результата в виде текста
        $("#recovery_result").html(result);
    }, 'text')
}

/* ================================== */

/* ================= ДЕЙСТВИЯ ПРОФИЛЯ ================= */

// сохранение статуса пользователя
function change_user_status(status) {

    // получение данных формы
    data['change_user_status'] = 1;
    data['status'] = status;

    // отправка данных на сервер
    send_query('ajax/actions_auth.php', data, (result) => {
        if (result.status === 'success') {
            $('.profile_about_text').html('<span class="change_status_element" onclick="change_status_element()">' + result.message + '</span>');
        }
    });
}

function get_notifications() {
    data['get_notifications'] = 1;

    send_query('ajax/actions_auth.php', data, (result) => {
        $('.notification_window .notifications_window_list').html(result);

        if ($(result).hasClass('empty_notifications')) {
            $('.notification_window .notification_window_header').css({justifyContent: 'end'});
            $('.notification_window .notification_window_header span').css({display: 'none'});
        } else {
            $('.notification_window .notification_window_header').css({justifyContent: 'space-between'});
            $('.notification_window .notification_window_header span').css({display: 'block'});
        }
    }, 'text');
}

function clear_all_notifications() {
    data['clear_all_notifications'] = 1;

    send_query('ajax/actions_auth.php', data, (result) => {
        if (result.status === 'success') {
            get_notifications();
        }
    });
}

function delete_notification(id, element) {
    data['delete_notification'] = 1;
    data['id'] = id;

    send_query('ajax/actions_auth.php', data, (result) => {
        if (result.status === 'success') {
            $(element).parent().parent().hide('fast');
        }
    });
}

function follow_actions(id, type, element) {
    data['follow_actions'] = 1;
    data['id'] = id;
    data['type'] = type;

    send_query('ajax/actions_auth.php', data, (result) => {
        if (result.status === 'success') {

            $(element).attr('disabled', true);

            if (type === 'follow') {
                $(element).find('i').removeClass('fa-plus');
                $(element).find('i').addClass('fa-minus');
                $(element).find('span').text('Unfollow');
                $(element).attr('onclick', 'follow_actions(\'' + id + '\', \'unfollow\', $(this))');
            } else {
                $(element).find('i').removeClass('fa-minus');
                $(element).find('i').addClass('fa-plus');
                $(element).find('span').text('Follow');
                $(element).attr('onclick', 'follow_actions(\'' + id + '\', \'follow\', $(this))');
            }
            setTimeout(() => {
                $(element).attr('disabled', false);
            }, 2000);
        }
    });
}

function save_profile_setting(id = null) {
    data['save_profile_setting'] = 1;

    if (id !== null) {
        data['id'] = id;
    }

    data['display_name'] = $('#display_name').val();
    data['name'] = $('#name').val();
    data['lastname'] = $('#lastname').val();

    let date = '';
    $('.profile_settings_content_item_select_field').find('select').each(function () {
        date += $(this).val() + '-';
    });

    date = date.substring(0, date.length - 1);
    data['date'] = date;
    data['email'] = $('#email').val();

    if ($('#email_notice').is(':checked')) {
        data['email_noty'] = 1;
    } else {
        data['email_noty'] = 0;
    }

    data['gender'] = $('#gender').val();
    data['facebook'] = $('#facebook').val();
    data['twitter'] = $('#twitter').val();
    data['instagram'] = $('#instagram').val();
    data['youtube'] = $('#youtube').val();
    data['telegram'] = $('#telegram').val();
    data['vk'] = $('#vk').val();
    data['github'] = $('#github').val();
    data['website'] = $('#website').val();

    if ($('#password').val() !== '') {
        data['password'] = $('#password').val();
        data['password_repeat'] = $('#password_repeat').val();
    }

    send_query('ajax/actions_auth.php', data, (result) => {
        if (result.status === 'success') {
            $('.save_settings').attr('disabled', true);
            $('.save_settings_msg').addClass('text-success');

            setTimeout(() => {
                $('.save_settings').attr('disabled', false);
            }, 2000);
        } else {
            $('.save_settings_msg').addClass('text-danger');
        }

        $('.save_settings_msg').text(result.message);
    });
}

function delete_account(id = null) {
    data['delete_account'] = 1;

    if (id !== null) {
        data['id'] = id;
    }

    send_query('ajax/actions_auth.php', data, (result) => {
        if (result.status === 'success') {
            $('.save_settings_msg').addClass('text-success');

            if (id === null) {
                user_exit();
            }
        } else {
            $('.save_settings_msg').addClass('text-danger');
        }

        $('.save_settings_msg').text(result.message);
    });
}

function restore_user(id, element) {
    data['restore_user'] = 1;
    data['id'] = id;

    send_query('ajax/actions_auth.php', data, (result) => {
        if (result.status === 'success') {
            $('.save_settings_msg').addClass('text-success');

            $(element).attr('disabled', true);
            $(element).attr('onclick', 'call_modal(\'delete_account_alert\'{if(is_worthy(\'g\'))}, {id: {' + id + '}}{/if})')
            $(element).text('Account delete');
            setInterval(() => {
                $(element).attr('disabled', false);
            }, 2000);
        } else {
            $('.save_settings_msg').addClass('text-danger');
        }

        $('.save_settings_msg').text(result.message);
    });
}

/* ================================== */

/* ================= ГРОМКОСТЬ ПОЛЬЗОВАТЕЛЯ ================= */

function get_user_volume() {

    // получение данных формы
    data['get_user_volume'] = 1;

    // отправка данных на сервер
    send_query('ajax/actions_auth.php', data, (result) => {
        update_volume(null, result.volume / 100);
    });
}

function update_volume_value(value) {

    // получение данных формы
    data['update_volume_value'] = 1;
    data['volume'] = value;

    // отправка данных на сервер
    send_query('ajax/actions_auth.php', data, () => {
        sessionStorage.setItem('current_volume', (value / 100).toString());
    });
}

/* ================================== */

function get_tracks() {

    // получение данных формы
    data['get_tracks'] = 1;

    // отправка данных на сервер
    send_query('ajax/actions.php', data, (result) => {
        console.log(result);
        $('.tracks').html(result);
    }, 'text');
}

function play_random_tracks() {
    data['play_random_tracks'] = 1;

    // отправка данных на сервер
    send_query('ajax/actions.php', data, (result) => {
        if (result.status === 'success') {
            for (let i = 0; i < result.path.length; i++) {
                random_track_query.push(result.path[i]);
            }
            sessionStorage.setItem('random_track_query', JSON.stringify(random_track_query));
        }
    });

}

function call_modal(modal, data_array = null) {
    data['call_modal'] = 1;
    data['modal'] = modal;

    if (data !== null) {
        data['data_array'] = data_array;
    }

    // отправка данных на сервер
    send_query('ajax/actions.php', data, (result) => {
        $(document.body).prepend(result)
        $(`#${modal}`).modal('show')
    }, 'text');
}

function get_track_info(track) {
    data['get_track_info'] = 1;
    data['track'] = track;

    send_query('ajax/actions.php', data, (result) => {
        $('.player_song').children().attr('src', result.image);

        $('.player_song .player_song_author .player_song_author_title').attr('onclick', 'load_template("track", {name: "' + (result.path).replace('files/tracks/', '') + '"})');
        $('.player_song .player_song_author .player_song_author_title').text((result.title).replaceAll('&amp;', '&').replaceAll('&quot;', '"').replaceAll('&#039;', '\'').replaceAll('&amp;', '&').replaceAll('&quot;', '"').replaceAll('&#039;', '\''));

        $('.player_song .player_song_author .player_song_author_name').attr('onclick', 'load_template("profile", {id: ' + result.author_id + '})');
        $('.player_song .player_song_author .player_song_author_name').text(result.author);

        const like_element = $('.player .player_song .player_song_tools button');

        if (result.liked === 1) {
            like_element.addClass('active')
            like_element.find('i').removeClass('fa-regular').addClass('fa-solid');
            set_title(like_element, 'Dislike it!')
        } else {
            like_element.removeClass('active');
            like_element.find('i').removeClass('fa-solid').addClass('fa-regular');
            set_title(like_element, 'Like it!')
        }

        $(like_element).tooltip('hide');
    });
}

function update_user_shuffle_status(status) {
    data['update_user_shuffle_status'] = 1;

    if (status === null) {
        status = false;
    }

    data['status'] = status;

    send_query('ajax/actions.php', data, () => {
    });

    is_query_shuffle = status;
    sessionStorage.setItem('shuffled', status);
}

function update_user_repeat_track_status(status) {
    data['update_user_repeat_track_status'] = 1;

    data['status'] = status;

    send_query('ajax/actions.php', data, () => {
    });

    is_track_repeat = status;
    sessionStorage.setItem('track_repeat', status);
}

function up_track_history(track) {
    if (track !== '') {
        data['up_track_history'] = 1;
        data['track'] = track;

        send_query('ajax/actions.php', data, () => {
        });
    }
}

function get_playlists() {

    // получение данных формы
    data['get_playlists'] = 1;

    // отправка данных на сервер
    send_query('ajax/actions.php', data, (result) => {
        $('#playlists').html(result);
    }, 'text');
}

function add_favorite_track(id, type = 'track') {
    let element = '';

    data['add_favorite_track'] = 1;
    data['id'] = id;
    data['type'] = type;

    send_query('ajax/actions.php', data, (result) => {
        if (result.status === 'success') {
            if (type === 'track') {
                element = $('.likes[data-id=' + id + ']');
            } else {
                element = $('.playlists_likes[data-id=' + id + ']');
            }

            const likes_count = parseInt($(element).find('span').html());

            if (result.likes_status === true) {
                $(element).addClass('active');
                $(element).find('span').html(likes_count + 1);
            } else {
                $(element).removeClass('active');
                $(element).find('span').html(likes_count - 1);
            }

            load_tracks_likes(id);
        }
    });
}

function set_like_to_track(element, src = null) {
    data['set_like_to_track'] = 1;

    if (src) {
        data['track'] = src;
    } else {
        data['track'] = $('#btn_play').attr('data-src');
    }

    send_query('ajax/actions.php', data, (result) => {
        if (result.status === 'success') {
            if (!src) {
                if ($(element).hasClass('active')) {
                    $(element).removeClass('active');
                    $(element).find('i').removeClass('fa-solid').addClass('fa-regular');

                    set_title($(element), 'Like it!')
                    $(element).tooltip('show');
                } else {
                    $(element).addClass('active');
                    $(element).find('i').removeClass('fa-regular').addClass('fa-solid');

                    set_title($(element), 'Dislike it!')
                    $(element).tooltip('show');
                }
            } else {
                if ($(element).text() === 'Like Track') {
                    $(element).text('Dislike Track');
                } else {
                    $(element).text('Like Track');
                }
            }
        }
    });
}

function load_tracks_likes(id) {
    data['load_tracks_likes'] = 1;
    data['id'] = id;

    send_query('ajax/actions.php', data, (result) => {
        if (result.status === 'success') {
            $('.track_likes_list').html(result.data);
        }
    });
}

function load_playlist_likes(id) {
    data['load_playlist_likes'] = 1;
    data['id'] = id;

    send_query('ajax/actions.php', data, (result) => {
        if (result.status === 'success') {
            $('.track_likes_list').html(result.data);
        }
    });
}

function load_playlist_reposts(id) {
    data['load_playlist_reposts'] = 1;
    data['id'] = id;

    send_query('ajax/actions.php', data, (result) => {
        if (result.status === 'success') {
            $('.track_reposts_list').html(result.data);
        }
    });
}

function load_other_users_playlists(user_id) {
    data['load_other_users_playlists'] = 1;
    data['user_id'] = user_id;

    send_query('ajax/actions.php', data, (result) => {
        if (result.status === 'success') {
            $('.other_playlists_list').html(result.data);
        }
    });
}

function open_playlist(playlist_id) {
    data['open_playlist'] = 1;
    data['playlist_id'] = playlist_id;

    send_query('ajax/actions.php', data, (result) => {
        $('body main > .container').append(result);
        open_playlist_window();
    }, 'text');
}

function find_tracks() {

    if ($('#find_tracks_input').val() !== '') {
        data['find_tracks'] = 1;
        data['search'] = $('#find_tracks_input').val();

        send_query('ajax/actions.php', data, (result) => {
            if ($('#find_tracks_input').val() === '') {
                $('.input_search .search_icon').attr('onclick', '');
            } else {
                $('.input_search .search_icon').attr('onclick', "load_template('search', {data: '" + $('#find_tracks_input').val() + "'})");
            }

            $('.find_tracks_results').addClass('active').html(result);
        }, 'text');
    } else {
        $('.find_tracks_results').removeClass('active');
    }
}

function site_search() {

    data['site_search'] = 1;
    data['search'] = $('#site_search_input').val();

    send_query('ajax/actions.php', data, (result) => {
        $('.site_search_result').html(result);
    }, 'text');
}

function confirm_track() {
    data['confirm_track'] = 1;
    data['file'] = $('#confirm_track_input').val();

    $("html").on("drop", function (e) {
        e.preventDefault();
        e.stopPropagation();
    });
    send_query('ajax/actions_auth.php', data, (result) => {
        if (result.status === 'success') {
            console.log('ok');
        }
    }, 'text');
}

function set_comment_to_track(id, comment) {
    var regex = /[а-яА-Яa-zA-Z0-9_]/;

    if (regex.test(comment)) {
        data['set_comment_to_track'] = 1;
        data['id'] = id;
        data['comment'] = comment;

        send_query('ajax/actions_auth.php', data, (result) => {
            if (result.status === 'success') {
                let element = $('.comments[data-id=' + id + ']');
                $(element).find('span').html(parseInt($(element).find('span').html()) + 1);
                load_track_comment(id);
            }
        });
    }
}

function default_cover(id) {
    data['default_cover'] = 1;
    data['id'] = id;

    send_query('ajax/actions_auth.php', data, (result) => {
        if (result.status === 'success') {
            $('.profile_head_cover.cover_preview').css('background-image', 'url("' + result.cover + '")');
            vibrant_background(result.cover);
        }
    });
}

function upload_file(file) {
    data['upload_file'] = 1;
    data['file'] = file;

    Dropzone.discover();

    send_query('ajax/actions_auth.php', data, (result) => {
        if (result.status === 'success') {
            console.log('ok');
        }
    }, 'text');
}


function load_track_comment(track_id) {
    data['load_track_comment'] = 1;
    data['track_id'] = track_id;

    send_query('ajax/actions.php', data, (result) => {
        if (result.status === 'success') {
            $('.comments_title').find('span').html(result.count + ' Comments');
            $('.comments_list').html(result.data);
        }
    });
}

function edit_track_comment(id) {
    data['edit_track_comment'] = 1;
    data['id'] = id;


    send_query('ajax/actions_auth.php', data, (result) => {
        if (result.status === 'success') {
            let element = $('.comment_item[data-id=' + id + ']');
            $(element).find('.comment_text').html(comment);

            $('#edit_track_comment').modal('hide');
        }
    });
}

function delete_comment(id) {
    data['delete_comment'] = 1;
    data['id'] = id;

    send_query('ajax/actions_auth.php', data, (result) => {
        if (result.status === 'success') {
            let element = $('.comment_item[data-id=' + id + ']');
            $(element).remove();

            $('#delete_track_comment').modal('hide');

            load_track_comment($('.comment_field input').attr('id'));
        }
    });
}

function unvery_user(id, element) {
    data['unvery_user'] = 1;
    data['id'] = id;

    send_query('ajax/actions_auth.php', data, (result) => {
        if (result.status === 'success') {
            $(element).attr('disabled', 'disabled');
            $(element).html('Verify user');
            setTimeout(() => {
                $(element).attr('onclick', 'very_user(' + id + ', $(this))');
                $(element).removeAttr('disabled');
            }, 2000);
        }
    });
}

function very_user(id, element) {
    data['very_user'] = 1;
    data['id'] = id;

    send_query('ajax/actions_auth.php', data, (result) => {
        if (result.status === 'success') {
            $(element).attr('disabled', 'disabled');
            $(element).html('Unverify user');
            setTimeout(() => {
                $(element).attr('onclick', 'unvery_user(' + id + ', $(this))');
                $(element).removeAttr('disabled');
            }, 2000);
        }
    });
}

function send_very_request(element, id) {
    data['send_very_request'] = 1;

    if (id) {
        data['id'] = id;
    }

    send_query('ajax/actions_auth.php', data, (result) => {
        console.log(result)
        if (result.status === 'success') {
            $(element).attr('disabled', 'disabled');
            $(element).html('Request has been sent');
            $(element).attr('onclick', 'cancel_very_request($(this))');
            setTimeout(() => {
                $(element).removeAttr('disabled');
            }, 2000);
        }
    });
}

function cancel_very_request(element, id) {
    data['cancel_very_request'] = 1;

    if (id) {
        data['id'] = id;
    }

    send_query('ajax/actions_auth.php', data, (result) => {
        console.log(result)
        if (result.status === 'success') {
            $(element).attr('disabled', 'disabled');
            $(element).html('Send verification request');
            $(element).attr('onclick', 'send_very_request($(this))');
            setTimeout(() => {
                $(element).removeAttr('disabled');
            }, 2000);
        }
    });
}

function create_playlist() {
    data['create_playlist'] = 1;
    data['name'] = $('#playlist_title').val();

    let radios = document.getElementsByName('playlist_type');
    for (let i = 0; i < radios.length; i++) {
        if (radios[i].checked) {
            data['type'] = radios[i].value;
        }
    }

    send_query('ajax/actions_auth.php', data, (result) => {
        console.log(result)
        if (result.status === 'success') {
            $('#playlist_action').modal('hide')
        } else {
            $('.modal_result').addClass('text-danger');
            $('.modal_result').html(result.message);
        }
    });
}

function find_user_playlists(id, name) {
    data['find_user_playlists'] = 1;
    data['id'] = id;
    data['name'] = name;

    send_query('ajax/actions_auth.php', data, (result) => {
        if (result.status === 'success') {
            $('.search_playlists_result').html(result.data);
        }
    });
}

function add_track_to_playlist(playlist_id, track_id) {
    data['add_track_to_playlist'] = 1;
    data['playlist_id'] = playlist_id;
    data['track_id'] = track_id;

    send_query('ajax/actions_auth.php', data, (result) => {
        if (result.status === 'success') {
            $('.playlist_action button[data-id="' + playlist_id + '"]').attr('disabled', 'disabled');
            $('.playlist_action button[data-id="' + playlist_id + '"]').html('Added');
        }
    });
}

function get_library_playlists() {
    data['get_library_playlists'] = 1;

    send_query('ajax/actions_auth.php', data, (result) => {
        if (result.status === 'success') {
            $('.my_playlists').html(result.data);
        }
    });
}

function delete_track_from_playlist(playlist_id, track_id, element = null) {
    data['delete_track_from_playlist'] = 1;
    data['playlist_id'] = playlist_id;
    data['track_id'] = track_id;

    send_query('ajax/actions_auth.php', data, (result) => {
        if (result.status === 'success') {
            if (element !== null) {
                $(element).parent().fadeOut(300, function () {
                    $(this).remove();
                });
            }
        }
    });
}

function edit_playlist(playlist_id) {
    data['edit_playlist'] = 1;
    data['playlist_id'] = playlist_id;
    data['name'] = $('#title').val();

    let radios = document.getElementsByName('playlist_type');
    for (let i = 0; i < radios.length; i++) {
        if (radios[i].checked) {
            data['type'] = radios[i].value;
        }
    }

    send_query('ajax/actions_auth.php', data, (result) => {
        if (result.status === "success") {
            $('.modal_result').addClass('text-success');
            $('.modal_result').text(result.message);
        } else {
            $('.modal_result').addClass('text-danger');
            $('.modal_result').text(result.message);
        }
    });
}

function delete_playlist(id, password) {
    data['delete_playlist'] = 1;
    data['id'] = id;
    data['password'] = password;

    send_query('ajax/actions_auth.php', data, (result) => {
        if (result.status === 'success') {
            get_library_playlists();
            $('#accept_delete_playlist').modal('hide');
        } else {
            $('.modal_result').addClass('text-danger');
            $('.modal_result').text(result.message);
        }
    });
}

// upload track
function upload_track() {
    data['upload_track'] = 1;
    data['name'] = $('#track-name').val();
    if ($('#default_option div p').text() === 'Custom') {
        data['genre'] = $('#custom_input').val();
    } else {
        data['genre'] = $('#default_option div p').text();
    }
    data['file'] = $('#track_input').attr('data-src');
    data['image'] = $('#track_image').attr('data-src');
    data['description'] = $('#description').val();
    // get checked radio
    let radios = document.getElementsByName('choice');
    for (let i = 0; i < radios.length; i++) {
        if (radios[i].checked) {
            data['privacy'] = radios[i].value;
        }
    }

    if (data['image'] === undefined || data['image'] === '') {
        data['image'] = 'files/track_covers/default.png';
    }

    send_query('ajax/actions_auth.php', data, (result) => {
        if (result.status === 'success') {
            $('.modal_result').addClass('text-success');
            $('.modal_result').text(result.message);
                window.location.href = 'track?name=' + ($('#track_input').attr('data-src')).replace('files/tracks/', '');
        } else {
            $('.modal_result').addClass('text-danger');
            $('.modal_result').text(result.message);
        }
    });
}

function block_user(id, element) {
    data['block_user'] = 1;
    data['id'] = id;

    const old_text = $(element).find('span').text();
    const splitted_text = old_text.split(' ');
    const name = splitted_text[1];

    send_query('ajax/actions_auth.php', data, (result) => {
        if (result.status === 'success') {
            if (result.blocked) {
                $(element).find('span').html(`Unblock <b>${name}</b>`);
                set_title($(element), `Unblock ${name}`);
                $(element).tooltip('show');
            } else {
                $(element).find('span').html(`Block <b>${name}</b>`);
                set_title($(element), `Block ${name}`);
                $(element).tooltip('show');
            }
        }
    });
}

function report_user(id) {
    data['report_user'] = 1;
    data['id'] = id;
    data['reason'] = $('#reason').val();

    if ($('#reason').val() === 'Other') {
        data['other_reason'] = $('#other_reason').val();
    }

    data['report_text'] = $('#report_text').val();

    data['is_need_to_block'] = $('#is_need_to_block').is(':checked');

    send_query('ajax/actions_auth.php', data, (result) => {
        if (result.status === 'success') {
            $('.modal_result').removeClass('text-danger');
            $('.modal_result').addClass('text-success');
            $('.modal_result').text(result.message);
            $('.modal-footer button').attr('disabled', 'disabled');
        } else {
            $('.modal_result').removeClass('text-success');
            $('.modal_result').addClass('text-danger');
            $('.modal_result').text(result.message);
        }
    });
}

function edit_track(id) {
    data['edit_track'] = 1;
    data['id'] = id;
    data['title'] = $('#title').val();
    data['genre'] = $('#genre').val();
    data['description'] = $('#description').val();

    const radios = document.getElementsByName('privacy');
    for (let i = 0; i < radios.length; i++) {
        if (radios[i].checked) {
            data['privacy'] = radios[i].value;
        }
    }

    send_query('ajax/actions_auth.php', data, (result) => {
        if (result.status === 'success') {
            $('.modal_result').removeClass('text-danger');
            $('.modal_result').addClass('text-success');
            $('.modal_result').text(result.message);

            load_template('track', {name: result.track_name})
        } else {
            $('.modal_result').removeClass('text-success');
            $('.modal_result').addClass('text-danger');
            $('.modal_result').text(result.message);
        }
    });
}

function delete_track(id) {
    data['delete_track'] = 1;
    data['id'] = id;

    send_query('ajax/actions_auth.php', data, (result) => {
        if (result.status === 'success') {
            $('.modal_result').removeClass('text-danger');
            $('.modal_result').addClass('text-success');
            $('.modal_result').text(result.message);

            setInterval(() => {
                load_template('profile', {id: [result.user_id]});
            }, 2000);
        } else {
            $('.modal_result').removeClass('text-success');
            $('.modal_result').addClass('text-danger');
            $('.modal_result').text(result.message);
        }
    });
}