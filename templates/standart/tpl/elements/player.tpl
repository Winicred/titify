<div class="player">
    <div class="player_song">
        <img src="files/avatars/no_avatar.jpg" alt="" data-bs-toggle="tooltip" title="">

        <div class="player_song_author">
            <a class="player_song_author_title" onclick="" data-bs-toggle="tooltip" title="">Loading...</a>

            <a class="player_song_author_name" onclick="" data-bs-toggle="tooltip" title="">Loading...</a>
        </div>

        <div class="player_song_tools">
            <button onclick="set_like_to_track($(this))" data-bs-toggle="tooltip" title="">
                <i class="fa-regular fa-heart"></i>
            </button>
        </div>
    </div>

    <div class="player_tools">
        <div class="player_tools_container">
            <div class="player_song_tools">
                <button onclick="shuffle_query($(this))" class="player_song_tools_secondary" data-bs-toggle="tooltip"
                        title="Shuffle query">
                    <i class="fa-solid fa-shuffle"></i>
                </button>

                <button onclick="play_prev_track()" class="player_song_tools_main" data-bs-toggle="tooltip"
                        title="Play previous track">
                    <i class="fa-solid fa-backward-step"></i>
                </button>

                <button class="play_and_stop_track track_action_button" onclick="check_if_music_plays() === false ? play() : pause()" track-src=""
                        data-bs-toggle="tooltip" title="Play">
                    <i class="fa-solid fa-circle-play"></i>
                </button>

                <button onclick="play_next_track()" class="player_song_tools_main" data-bs-toggle="tooltip"
                        title="Play next track">
                    <i class="fa-solid fa-forward-step"></i>
                </button>

                <button onclick="repeat_track($(this))" class="player_song_tools_secondary" data-bs-toggle="tooltip"
                        title="Repeat track">
                    <i class="fa-solid fa-repeat"></i>
                </button>
            </div>

            <div class="player_playback_container" track-src="">
                <span class="player_playback_current_time">0:00</span>

                <div class="player_playback_bar_block">
                    <div class="player_playback_bar">
                        <div class="player_playback_bar_progress"></div>
                    </div>
                </div>

                <span class="player_playback_total_time">0:00</span>
            </div>
        </div>
    </div>

    <div class="player_sub_tools">
        <div class="volume_bar_container">
            <button data-bs-toggle="tooltip" title="" onclick="mute_volume($(this))">
                <i class="fa-solid fa-volume-xmark"></i>

                <i class="fa-solid fa-volume-low" style="display: none"></i>

                <i class="fa-solid fa-volume-high" style="display: none"></i>
            </button>

            <div class="volume_bar_block">
                <div class="volume_bar">
                    <div class="volume_bar_progress"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    if (sessionStorage.getItem('current_volume') === '') {
        get_user_volume();
    }

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
                update_volume(null, track.volume + 0.1);
            } else {
                update_volume(null, track.volume - 0.1);
            }
        });

        track.addEventListener('timeupdate', () => {
            change_track_data();
        });

        track.addEventListener('ended', () => {
            setTimeout(() => {
                play_next_track();
            }, 2000);
        });
    });
</script>