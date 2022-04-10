<div class="find_tracks_item" title="Click to play track" data-bs-toggle="tooltip" data-bs-original-title="Click to play track" data-bs-placement="top">
    <div class="find_tracks_item_image">
        <img src="{track_cover}" alt="{track_title}">
    </div>

    <div class="find_track_item_description">
        <a class="track_page_link d-inline-block" tabindex="0" onclick="load_template('track', {name: '{track_link}'})" data-bs-toggle="tooltip" title="Go to track page">{track_title}</a>

        <div class="find_track_item_description_author">
            <a onclick="load_template('profile', {id: '{track_author_id}'})" data-bs-toggle="tooltip" title="Go to author profile page">{track_author_login}</a>
        </div>
    </div>

    <div class="find_track_item_play_button">
        <button onclick=" player_play('{track_path}')" class="track_action_button" data-path="{track_path}" type="button" data-bs-toggle="tooltip" title="Click to play">
            <i class="fa-solid fa-circle-play"></i>
        </button>
    </div>
</div>