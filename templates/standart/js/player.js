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
    sessionStorage.setItem('current_volume', '0.25');
    sessionStorage.setItem('current_track_time', '0.00');
}

// булевые значения для проверки ивентов плеера
let is_playing = false;
let is_track_skipping = false;
let is_track_repeat = JSON.parse(sessionStorage.getItem('track_repeat'));
let is_query_shuffle = JSON.parse(sessionStorage.getItem('shuffled'));
let is_can_play = false;

// индекс текущего трека в массиве очереди
let track_query_index = JSON.parse(sessionStorage.getItem('track_query_index'));

// массив очереди треков
let track_query = JSON.parse(sessionStorage.getItem('track_query'));
let random_track_query = JSON.parse(sessionStorage.getItem('random_track_query'));

// текущий звук (для плавности включения и выключения трека)
let current_volume = JSON.parse(sessionStorage.getItem('current_volume'));

// текущее время воспроизведения трека
let current_track_time = sessionStorage.getItem('current_track_time');

let audio = document.createElement('audio');
audio.id = 'player';
audio.autoplay = false;
audio.src = '';
document.getElementById('footer').appendChild(audio);

$(document).ready(function () {
    const track_bar = $('.player_playback_bar_block');
    track_bar.on('click', function (e) {
        skip_track_time($(this), e)
    }).on('mousedown', function (e) {
        volumeDrag = true;
        skip_track_time($(this), e)
    }).on('mouseup', function (e) {
        if (volumeDrag) {
            volumeDrag = false;
        }
    }).on('mousemove', function (e) {
        if (volumeDrag) {
            skip_track_time($(this), e)
        }
    }).on('mouseleave', function (e) {
        volumeDrag = false;
        is_track_skipping = false;
    })

    let volumeDrag = false;
    const volume_bar = $('.volume_bar_block')
    volume_bar.on('mousedown', function (e) {
        volumeDrag = true;
        update_volume(e.pageX);
    });

    volume_bar.on('mouseup', function (e) {
        if (volumeDrag) {
            volumeDrag = false;
            update_volume(e.pageX);
        }
    });

    volume_bar.on('mousemove', function (e) {
        if (volumeDrag) {
            update_volume(e.pageX);
        }
    })

    volume_bar.bind('mousewheel', function (e) {
        if (e.originalEvent.wheelDelta / 120 > 0) {
            update_volume(null, audio.volume + 0.1);
        } else {
            update_volume(null, audio.volume - 0.1);
        }
    });

    audio.addEventListener('timeupdate', () => {
        change_track_data();
    });

    audio.addEventListener('ended', () => {
        setTimeout(() => {
            play_next_track();
        }, 2000);
    });
});

function init_player() {
    if (random_track_query.length === 0 && track_query.length === 0) {
        play_random_tracks();
    } else if (random_track_query.length === 0) {
        change_track_src(track_query[track_query_index]);
    } else {
        change_track_src(random_track_query[track_query_index])
    }

    get_track_info(get_track_src())
    current_track_time = audio.currentTime;

    $('#btn_play').attr('data-src', get_track_src());
    $('#player_data_src').attr('data-src', get_track_src());
    $('.player_playback_container .player_playback_total_time').text('0:00');

    if (track_query_index === 0) {
        $('#prev_track_button').addClass('disabled');
    } else {
        $('#prev_track_button').removeClass('disabled');
    }

    audio.onloadedmetadata = () => {
        $('.player_playback_container[data-src="' + get_track_src() + '"] .player_playback_total_time').text(format_seconds_as_time(audio.duration));
    }

    update_volume(null, current_volume);

    is_can_play = true;
}

function player_play(src = null) {
    if (!is_can_play) {
        return;
    }
    
    if (src !== get_track_src() && src !== null) {
        $('.play_track_button[data-src="' + get_track_src() + '"]').attr('onclick', 'player_play("' + get_track_src() + '")');
        set_title($('.play_track_button[data-src="' + get_track_src() + '"]'), 'Click to play track');

        change_track_src(src);

        up_track_history(get_track_src());

        $('.play_track_button').find('i').removeClass('fa-circle-pause').addClass('fa-circle-play');

        $('#btn_play').attr('data-src', get_track_src());
        $('#player_data_src').attr('data-src', get_track_src());

        $('.player_playback_container .player_playback_bar_block .player_playback_bar .player_playback_bar_progress').css('width', '0');
        $('.player_playback_container .player_playback_current_time').text('0:00');
    } else {
        if (src === get_track_src() && !audio.paused) {
            player_pause();
        }
    }

    $('#btn_play').find('i').addClass('fa-circle-pause').removeClass('fa-circle-play');
    set_title($('#btn_play'), 'Pause');
    set_title($('.play_track_button[data-src="' + get_track_src() + '"]'), 'Click to pause track');

    if ($('#btn_play:hover').length > 0) {
        $('#btn_play').tooltip('show');
    }

    if ($('.play_track_button[data-src="' + get_track_src() + '"]:hover').length > 0) {
        $('.play_track_button[data-src="' + get_track_src() + '"]').tooltip('show');
    }

    $('#btn_play').attr('onclick', 'player_pause()');
    $('.play_track_button[data-src="' + get_track_src() + '"]').attr('onclick', 'player_pause()');

    $('.play_track_button[data-src="' + get_track_src() + '"]').find('i').addClass('fa-circle-pause').removeClass('fa-circle-play');

    get_track_info(get_track_src());

    audio.play();
    $(audio).animate({
        'volume': current_volume
    }, 250);

    is_playing = true;
}

function player_play_playlist(id, element) {
    data["phpaction"] = '1';
    data["token"] = $("#token").val();
    data['get_playlists_tracks'] = 1;
    data['id'] = id;

    $.ajax({
        url: 'ajax/actions.php',
        type: 'POST',
        data: data,
        async: false,
        dataType: 'json',
        success: function (response) {

            if (response.status === 'success') {
                track_query = response.data;

                sessionStorage.setItem('track_query', JSON.stringify(track_query));

                track_query_index = 0;

                change_track_src(track_query[track_query_index]);

                player_play(get_track_src());
            } else {
                alert(response.data);
            }
        }
    });
}

function player_pause() {
    current_volume = audio.volume;

    $(audio).animate({
        'volume': 0
    }, 100, () => {
        audio.pause();
    });


    $('.play_track_button[data-src="' + get_track_src() + '"]').find('i').removeClass('fa-circle-pause').addClass('fa-circle-play');
    $('#btn_play').find('i').removeClass('fa-circle-pause').addClass('fa-circle-play');
    set_title($('.track_action_button'), 'Play');
    set_title($('.play_track_button[data-src="' + get_track_src() + '"]'), 'Click to play track');

    if ($('#btn_play:hover').length > 0) {
        $('#btn_play').tooltip('show');
    }

    if ($('.play_track_button[data-src="' + get_track_src() + '"]:hover').length > 0) {
        $('.play_track_button[data-src="' + get_track_src() + '"]').tooltip('show');
    }

    $('#btn_play').attr('onclick', 'player_play()');
    $('.play_track_button[data-src="' + get_track_src() + '"]').attr('onclick', 'player_play("' + get_track_src() + '")');

    is_playing = false;
}

function check_if_music_plays() {
    return is_playing;
}

function change_track_src(src) {
    audio.src = src;
}

function change_track_data() {

    // изменение длины прогресc-бара музыки
    if (!is_track_skipping) {

        // $('.player_playback_container[data-src="' + get_track_src() + '"] .player_playback_bar_block .player_playback_bar .player_playback_bar_progress').width(Math.floor(100 / audio.duration) * audio.currentTime + '%');

        $('.player_playback_container[data-src="' + get_track_src() + '"] .player_playback_bar_block .player_playback_bar .player_playback_bar_progress').css('width', `${audio.currentTime / audio.duration * 100}%`);

        // изменение текущего времени трека
        let current_track_time = format_seconds_as_time(Math.floor(audio.currentTime).toString());
        $('.player_playback_container[data-src="' + get_track_src() + '"] .player_playback_current_time').text(current_track_time);

        sessionStorage.setItem('current_track_time', (audio.currentTime).toFixed(2).toString());

        // изменение итогового времени трека

        // check if nan or infinity
        if (!isNaN(audio.duration) || audio.duration !== Infinity) {
            $('.player_playback_container[data-src="' + get_track_src() + '"] .player_playback_total_time').text(format_seconds_as_time(audio.duration));
        }
    }
}

function skip_track_time(element, event, time = null) {
    is_track_skipping = true;

    let parentOffset;
    let relativeXPosition;

    if (time !== null) {
        element = $('.player_playback_container[data-src="' + get_track_src() + '"] .player_playback_bar_block')
        relativeXPosition = $(element).width() - current_track_time;
    } else {
        parentOffset = $(element).children().offset().left;
        relativeXPosition = (event.pageX - parentOffset);
    }

    if (relativeXPosition > 0 && relativeXPosition < $('.player_playback_container .player_playback_bar').width()) {
        $('.player_playback_container[data-src="' + get_track_src() + '"] .player_playback_bar_block .player_playback_bar .player_playback_bar_progress').width((relativeXPosition / $(element).width()) * 100 + '%');

        if (audio.src !== '' ?? audio.src !== 'profile?id=' + user_id) {
            current_track_time = format_seconds_as_time(Math.floor((relativeXPosition / element.children().width()) * audio.duration).toString());
            $('.player_playback_container[data-src="' + get_track_src() + '"] .player_playback_current_time').text(current_track_time);

            sessionStorage.setItem('current_track_time', (audio.currentTime).toFixed(2).toString());

            $(element).on('mouseup', function () {
                audio.currentTime = (relativeXPosition / element.children().width()) * audio.duration;
            });
        }
    }
}

function update_volume(x, vol) {
    const volume = $('.volume_bar');

    volume.children().css('display', 'block');
    audio.muted = false;

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
    audio.volume = percentage / 100;

    if (audio.volume === 0) {
        $('.volume_bar_container button i').hide().eq(0).show();
        $('.volume_bar_container button').addClass('active')
    } else if (audio.volume < 0.5) {
        $('.volume_bar_container button i').hide().eq(1).show();
        $('.volume_bar_container button').removeClass('active')
    } else {
        $('.volume_bar_container button i').hide().eq(2).show();
        $('.volume_bar_container button').removeClass('active')
    }

    current_volume = audio.volume;
    sessionStorage.setItem('current_volume', current_volume);

    update_volume_value(audio.volume * 100);
}

function mute_volume(element) {
    if (audio.muted) {
        audio.muted = false;

        if (audio.volume === 0) {
            element.find('i').hide().eq(0).show();
        } else if (audio.volume < 0.5) {
            element.find('i').hide().eq(1).show();
        } else {
            element.find('i').hide().eq(2).show();
        }

        $('.volume_bar_progress').animate({'width': audio.volume * 100 + '%'}, 150);
        element.removeClass('active');
        set_title($(element), 'Mute');

        sessionStorage.setItem('current_volume', (audio.volume * 100).toString());
    } else {
        audio.muted = true;
        element.find('i').hide().eq(0).show();

        $('.volume_bar_progress').animate({'width': '0%'}, 150);

        element.addClass('active')
        set_title($(element), 'Unmute');

        sessionStorage.setItem('current_volume', '0');
    }
}

function play_prev_track() {
    let prev_track_src = '';

    if (track_query_index > 0) {
        track_query_index--;
        if (track_query.length === 0) {
            prev_track_src = random_track_query[track_query_index];
        } else {
            prev_track_src = track_query[track_query_index];
        }

        player_play(prev_track_src);
    }

    sessionStorage.setItem('track_query_index', track_query_index.toString());

    if (track_query_index === 0) {
        $('#prev_track_button').addClass('disabled');
    } else {
        $('#prev_track_button').removeClass('disabled');
    }

}

function get_track_src() {
    return decodeURIComponent(audio.src.replace('https://sound.liveonahigh.ru/', ''));
}

function play_next_track() {
    if ((track_query_index + 1 < track_query.length || track_query_index + 1 < random_track_query.length) && is_track_repeat === false) {
        track_query_index++;
    } else {
        if (is_track_repeat === false) {
            play_random_tracks();
        }
    }

    let next_track_src = '';
    if (track_query.length === 0) {
        next_track_src = random_track_query[track_query_index];
    } else {
        next_track_src = track_query[track_query_index];
    }

    if (track_query_index === 0) {
        $('#prev_track_button').addClass('disabled');
    } else {
        $('#prev_track_button').removeClass('disabled');
    }

    player_play(next_track_src);

    sessionStorage.setItem('track_query_index', track_query_index);
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

    sessionStorage.setItem('is_query_shuffle', is_query_shuffle);
}

function repeat_track(element) {
    if (is_track_repeat) {
        is_track_repeat = false;

        $(element).removeClass('active');
    } else {
        is_track_repeat = true;

        $(element).addClass('active');
    }

    sessionStorage.setItem('is_track_repeat', is_track_repeat);
}

function add_to_track_query(path) {
    track_query.push(path)
    sessionStorage.setItem('track_query', JSON.stringify(track_query));
}

function check_if_music_plays_by_src(src) {
    return audio.src === src;
}