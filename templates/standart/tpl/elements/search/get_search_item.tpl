<div class="find_tracks_item">
    <div class="find_tracks_item_image">
        <img src="{track_cover}" alt="{track_title}">
    </div>

    <div class="find_track_item_description">
        <span>{track_title}</span>

        <div class="find_track_item_description_author">
            <span>{track_author_login}</span>
        </div>
    </div>

    <div class="find_track_item_play_button">
        <button onclick="check_if_music_plays_by_src('{track_path}') === false ? play('{track_path}') : pause()" class="track_action_button" track-src="{track_path}" type="button" data-bs-toggle="tooltip" title="Play">
            <i class="fa-solid fa-circle-play"></i>
        </button>
    </div>
</div>