<div class="profile_data_item">
    <div class="item_image">
        <img src="{cover}" alt="{track_name}">
    </div>

    <div class="item_data">
        <div class="item_play_container">
            <button class="play_track_button" onclick="play('{path}')">
                <i class="fa-solid fa-circle-play"></i>
            </button>

            <div class="item_play_title">
                <div class="author_name">
                    <span>{display_name}</span>
                </div>

                <div class="track_name">
                    <span>{track_name}</span>
                </div>
            </div>

            <div class="item_date">
                <span>{date}</span>
            </div>
        </div>

        <div class="item_footer">
            <div class="player_container">
                <span class="play_current_time ttplayer-current-time">0:00</span>

                <div class="player_bar_container ttplayer-progress-bar">
                    <div class="player_bar">
                        <div class="player_bar_progress"></div>
                    </div>
                </div>

                <span class="play_total_time">0:00</span>
            </div>

            <div class="tracks_field">
                {tracks}
            </div>

            <div class="item_stats">
                <button class="playlists_likes {if('{is_liked}' == 'true')}active{/if}" onclick="add_favorite_track({id}, 'playlist')" data-id="{id}">
                    <i class="fa-solid fa-heart"></i>
                    <span>{likes}</span>
                </button>

                <button>
                    <i class="fa-solid fa-retweet"></i>
                    <span>{reposts}</span>
                </button>

                <button onclick="copy_to_clipboard('{track_url}', $(this))">
                    <i class="fa-solid fa-link"></i>
                    <span>Copy link</span>
                </button>

                <div class="item_stats">
                    <span>
                        <i class="fa-solid fa-heart"></i>
                        <span>{likes}</span>
                    </span>

                    <span>
                        <i class="fa-solid fa-retweet"></i>
                        <span>{reposts}</span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>