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
                <button onclick="add_to_track_query('{track_path}')">Add To Queue</button>
                <button onclick="player_play('{track_path}')">Play Now</button>
                <button onclick="set_like_to_track($(this), '{track_path}')">{if('{is_liked}' == 'true')}Dislike Track {else}Like Track{/if}</button>
            </div>
        </div>
    </div>
</div>