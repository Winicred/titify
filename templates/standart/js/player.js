/* -------------------- TTPlayer -------------------- */

// проверка на наличие объектов в sessionStorage
if (sessionStorage.getItem('track_repeat') == null &&
    sessionStorage.getItem('shuffled') == null &&
    sessionStorage.getItem('track_query') == null &&
    sessionStorage.getItem('random_track_query') == null &&
    sessionStorage.getItem('track_query_index') == null &&
    sessionStorage.getItem('current_volume') == null &&
    sessionStorage.getItem('current_track_time') == null
) {
    sessionStorage.setItem('track_repeat', 'false');
    sessionStorage.setItem('shuffled', 'false');
    sessionStorage.setItem('track_query', '[]');
    sessionStorage.setItem('random_track_query', '[]');
    sessionStorage.setItem('track_query_index', '0');
    sessionStorage.setItem('current_volume', '1');
    sessionStorage.setItem('current_track_time', '0.    00');
}

// получение node по id
const track = document.getElementById('track');

// булевые значения для проверки ивентов плеера
let is_playing = false;
let is_track_skipping = false;
let is_track_repeat = JSON.parse(sessionStorage.getItem('track_repeat'));
let is_query_shuffle = JSON.parse(sessionStorage.getItem('shuffled'));

// индекс текущего трека в массиве очереди
let track_query_index = JSON.parse(sessionStorage.getItem('track_query_index'));

// массив очереди треков
let track_query = JSON.parse(sessionStorage.getItem('track_query'));
let random_track_query = JSON.parse(sessionStorage.getItem('random_track_query'));

// текущий звук (для плавности включения и выключения трека)
let current_volume = JSON.parse(sessionStorage.getItem('current_volume'));

// текущее время воспроизведения трека
let current_track_time = sessionStorage.getItem('current_track_time');

function init_player() {
    if (random_track_query.length === 0 && track_query.length === 0) {
        play_random_tracks();
    } else if (random_track_query.length === 0) {
        change_track_src(track_query[track_query_index]);
    } else {
        change_track_src(random_track_query[track_query_index])
    }

    get_track_info(get_track_src())

    update_volume(null, current_volume);

    skip_track_time(null, null, current_track_time);

    set_title($('.volume_bar_container button'), current_volume === 0 ? 'Unmute' : 'Mute');

    $('.volume_bar_container button').tooltip('hide');
}

function play(src = null, type = 0) {
    track.volume = 0;

    if (check_if_music_plays() === true) {
        pause();
    }

    if (src !== null) {
        change_track_src(src);
        track.play(get_track_src())
    } else {
        track.play();
    }
    get_track_info(get_track_src());
    up_track_history(get_track_src());

    $(track).animate({
        'volume': current_volume
    }, 500);

    $('.track_action_button').find('i').addClass('fa-circle-pause').removeClass('fa-circle-play');

    set_title($('.track_action_button'), 'Pause');

    is_playing = true;
}

function pause() {
    current_volume = track.volume;

    $(track).animate({
        'volume': 0
    }, 500, () => {
        track.pause();
    });

    $('.track_action_button').find('i').addClass('fa-circle-play').removeClass('fa-circle-pause');
    set_title($('.track_action_button'), 'Play');

    is_playing = false;
}

function check_if_music_plays() {
    return is_playing;
}

function change_track_src(src) {
    track.src = src;
}

function change_track_data() {

    // изменение длины прогресc-бара музыки
    if (!is_track_skipping) {

        $('.player_playback_container .player_playback_bar .player_playback_bar_progress').width(Math.floor(100 / track.duration) * track.currentTime + '%');

        $('.player_playback_bar_progress').css('width', `${track.currentTime / track.duration * 100}%`);

        // изменение текущего времени трека
        let current_track_time = format_seconds_as_time(Math.floor(track.currentTime).toString());
        $('.player_playback_container .player_playback_current_time').text(current_track_time);

        sessionStorage.setItem('current_track_time', (track.currentTime).toFixed(2).toString());
    }

    if (!isNaN(track.duration)) {

        // изменение итогового времени трека
        $('.player_playback_container .player_playback_total_time').text(format_seconds_as_time(track.duration));
    }
}

function skip_track_time(element, event, time = null) {
    is_track_skipping = true;

    let parentOffset;
    let relativeXPosition;

    if (time !== null) {
        element = $('.player_playback_bar_block')
        relativeXPosition = $(element).width() - current_track_time;
    } else {
        parentOffset = $(element).children().offset().left;
        relativeXPosition = (event.pageX - parentOffset);
    }

    if (relativeXPosition > 0 && relativeXPosition < $('.player_playback_container .player_playback_bar').width()) {
        $('.player_playback_container .player_playback_bar .player_playback_bar_progress').width((relativeXPosition / $(element).width()) * 100 + '%');

        if (track.src !== '') {
            current_track_time = format_seconds_as_time(Math.floor((relativeXPosition / element.children().width()) * track.duration).toString());
            $('.player_playback_container .player_playback_current_time').text(current_track_time);

            sessionStorage.setItem('current_track_time', (track.currentTime).toFixed(2).toString());

            $(element).on('mouseup', function () {
                track.currentTime = (relativeXPosition / element.children().width()) * track.duration;
            });
        }
    }
}

function update_volume(x, vol) {
    const volume = $('.volume_bar');

    volume.children().css('display', 'block');
    track.muted = false;

    let percentage;

    if (vol) {
        percentage = vol * 100;
    } else {
        const position = x - volume.offset().left;
        percentage = 100 * position / volume.width();
    }

    if (percentage > 100) {
        percentage = 100;
    }
    if (percentage < 0) {
        percentage = 0;
    }

    $('.volume_bar_progress').css('width', percentage + '%');
    track.volume = percentage / 100;

    if (track.volume === 0) {
        $('.volume_bar_container button i').hide().eq(0).show();
        $('.volume_bar_container button').addClass('active')
    } else if (track.volume < 0.5) {
        $('.volume_bar_container button i').hide().eq(1).show();
        $('.volume_bar_container button').removeClass('active')
    } else {
        $('.volume_bar_container button i').hide().eq(2).show();
        $('.volume_bar_container button').removeClass('active')
    }

    current_volume = track.volume;

    update_volume_value(track.volume * 100);
}

function mute_volume(element) {
    if (track.muted) {
        track.muted = false;

        if (track.volume === 0) {
            element.find('i').hide().eq(0).show();
        } else if (track.volume < 0.5) {
            element.find('i').hide().eq(1).show();
        } else {
            element.find('i').hide().eq(2).show();
        }

        $('.volume_bar_progress').animate({'width': track.volume * 100 + '%'}, 150);
        element.removeClass('active')
        set_title($(element), 'Mute');

        update_volume_value(track.volume * 100);
    } else {
        track.muted = true;
        element.find('i').hide().eq(0).show();

        $('.volume_bar_progress').animate({'width': '0%'}, 150);

        element.addClass('active')
        set_title($(element), 'Unmute');

        update_volume_value(0);
    }
}


function play_prev_track() {
    if (track_query_index > 0) {
        track_query_index--;
        if (track_query.length === 0) {
            play(track_query[track_query_index], 1);
        } else {
            play(random_track_query[track_query_index], 1);
        }
    }
}

function get_track_src() {
    return decodeURIComponent(track.src.replace('https://sound.liveonahigh.ru/', ''));
}

function play_next_track() {
    if ((track_query_index + 1 < track_query.length || track_query_index + 1 < random_track_query.length) && is_track_repeat === false) {
        track_query_index++;
    } else {
        if (is_track_repeat === false) {
            play_random_tracks();
        }
    }

    if (track_query.length === 0) {
        change_track_src(track_query[track_query_index]);
    } else {
        change_track_src(random_track_query[track_query_index]);
    }

    sessionStorage.setItem('track_query_index', track_query_index);

    play(get_track_src(), 1);
}

function shuffle_query(element) {
    if (is_query_shuffle) {
        if (sessionStorage.getItem('track_query') === '') {
            sessionStorage.setItem('track_query', JSON.stringify(track_query));
        }

        if ((sessionStorage.getItem('random_track_query') !== '') && track_query.length === 0) {
            sessionStorage.setItem('random_track_query', JSON.stringify(random_track_query));
        }

        is_query_shuffle = false;
        track_query = JSON.parse(sessionStorage.getItem('track_query'));

        $(element).removeClass('active');
    } else {
        is_query_shuffle = true;
        track_query = shuffle_array(track_query);
        track_query_index = 0;

        $(element).addClass('active');
    }

    update_user_shuffle_status(is_query_shuffle);
}

function repeat_track(element) {
    if (is_track_repeat) {
        is_track_repeat = false;

        $(element).removeClass('active');
    } else {
        is_track_repeat = true;

        $(element).addClass('active');
    }

    update_user_repeat_track_status(is_track_repeat);
}

function append_track_query(path) {
    track_query.push(path)
}

function set_like_to_track(element) {

    if ($(element).hasClass('active')) {
        $(element).removeClass('active');
        $(element).find('i').removeClass('fa-solid').addClass('fa-regular');

        set_title($(element), 'Like it!')
    } else {
        $(element).addClass('active');
        $(element).find('i').removeClass('fa-regular').addClass('fa-solid');

        set_title($(element), 'Dislike it!')
    }

    add_favorite_track(get_track_src());
}

function check_if_music_plays_by_src(src) {
    return track.src === src;
}