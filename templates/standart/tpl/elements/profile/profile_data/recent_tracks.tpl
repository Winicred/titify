<div class="profile_data_item" track-label="{path}">
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

            {if (is_auth())}
                <div class="comment_field">
                    <div class="avatar_preview" style="background-image: url('<?=get_user_avatar($_SESSION['id']);?>'); background-size: cover"></div>
                    <input type="text" placeholder="Write a comment" track-id="{id}">
                </div>
            {/if}

            <div class="item_stats">
                <button class="likes {if('{is_liked}' == 'true')}active{/if}" onclick="add_favorite_track({id})" data-id="{id}">
                    <i class="fa-solid fa-heart"></i>
                    <span>{likes}</span>
                </button>

                <button class="comments" data-id="{id}">
                    <i class="fa-solid fa-comment"></i>
                    <span>{comments}</span>
                </button>

                <button>
                    <i class="fa-solid fa-retweet"></i>
                    <span>{reposts}</span>
                </button>

                <button onclick="copy_to_clipboard('{track_url}', $(this))">
                    <i class="fa-solid fa-link"></i>
                    <span>Copy link</span>
                </button>

                {if (is_auth())}
                    <button onclick="call_modal('playlist_action', {track_id: {id}})">
                        <i class="fa-solid fa-plus"></i>
                        <span>Add to playlist</span>
                    </button>
                {/if}

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