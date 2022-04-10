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
                <button class="player_song_tools_secondary" data-bs-toggle="tooltip"
                        title="Shuffle query" onclick="shuffle_query($(this))">
                    <i class="fa-solid fa-shuffle"></i>
                </button>

                <button class="player_song_tools_main" data-bs-toggle="tooltip" id="prev_track_button"
                        title="Play previous track" onclick="play_prev_track()">
                    <i class="fa-solid fa-backward-step"></i>
                </button>

                <button class="play_and_stop_track track_action_button" id="btn_play" data-src=""
                        data-bs-toggle="tooltip" title="Play" onclick="player_play();">
                    <i class="fa-solid fa-circle-play"></i>
                </button>

                <button class="player_song_tools_main" data-bs-toggle="tooltip" id="next_track_button"
                        title="Play next track" onclick="play_next_track()">
                    <i class="fa-solid fa-forward-step"></i>
                </button>

                <button onclick="repeat_track($(this))" class="player_song_tools_secondary" data-bs-toggle="tooltip"
                        title="Repeat track">
                    <i class="fa-solid fa-repeat"></i>
                </button>
            </div>

            <div class="player_playback_container" id="player_data_src" data-src="">
                <span class="player_playback_current_time" id="progress_time">0:00</span>

                <div class="player_playback_bar_block" id="progress">
                    <div class="player_playback_bar">
                        <div class="player_playback_bar_progress" id="progress_bar"></div>
                    </div>
                </div>

                <span class="player_playback_total_time" id="progress_time_total">0:00</span>
            </div>
        </div>
    </div>

    <div class="player_sub_tools">
        <div class="volume_bar_container">
            <button data-bs-toggle="tooltip" title="Mute" id="volume_button" onclick="mute_volume($(this))">
                <i class="fa-solid fa-volume-xmark"></i>

                <i class="fa-solid fa-volume-low" style="display: none"></i>

                <i class="fa-solid fa-volume-high" style="display: none"></i>
            </button>

            <div class="volume_bar_block" id="volume_bar">
                <div class="volume_bar">
                    <div class="volume_bar_progress" id="volume_progress_bar"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    if (sessionStorage.getItem('current_volume') === '') {
        get_user_volume();
    }
</script>