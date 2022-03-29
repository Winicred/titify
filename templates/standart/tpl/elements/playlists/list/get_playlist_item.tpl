<div class="get_playlist_item">
    <img src="{track_cover}" alt="{track_author_login}">

    <div class="get_playlist_item_preview">
        <span>{track_title}</span>

        <div class="get_playlist_item_preview_author">
            <a href="profile?id={track_author_id}">{track_author_login}</a>
        </div>
    </div>

    <div class="get_playlists_play_button">
        <div class="dropdown">
            <span class="dropbtn">
                <i class="fa-solid fa-ellipsis-vertical"></i>
            </span>
            <div class="dropdown-content">
                <button onload="append_track_query('{track_path}')">Add To Queue</button>
                <button onload="play('{track_path}')">Play Now</button>
                <button onload="set_like_to_track('{track_path}')">Like Track</button>
            </div>
        </div>
    </div>
</div>