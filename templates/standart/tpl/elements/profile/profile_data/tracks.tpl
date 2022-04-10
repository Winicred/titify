<div class="profile_data_item" data-bs-toggle="tooltip" title="Go to track page" data-bs-placement="left" onclick="redirect_blank_page('{track_url}', $(this))">
    <div class="item_image">
        <div class="cover_image" style="background-image: url('{cover}'); width: 160px; height: 160px; background-repeat: no-repeat; background-position: center; background-size: contain" onclick="redirect_blank_page('{track_url}', $(this))"></div>
    </div>

    <div class="item_data">
        <div class="item_play_container">
            <button class="play_track_button" onclick="player_play('{path}')" data-src="{path}" title="Click to play track" data-bs-toggle="tooltip">
                <i class="fa-solid fa-circle-play"></i>
            </button>

            <div class="item_play_title">
                <div class="author_name">
                    <span title="Track author" data-bs-toggle="tooltip">{display_name}</span>
                </div>

                <div class="track_name">
                    <span title="Track title" data-bs-toggle="tooltip">{track_name}</span>
                </div>
            </div>

            <div class="item_date">
                <span title="Track upload date" data-bs-toggle="tooltip">{date}</span>
            </div>
        </div>

        <div class="item_footer">
            <div class="player_container player_playback_container" data-src="{path}">
                <span class="play_current_time player_playback_current_time">0:00</span>

                <div class="player_bar_container player_playback_bar_block">
                    <div class="player_bar player_playback_bar">
                        <div class="player_bar_progress player_playback_bar_progress"></div>
                    </div>
                </div>

                <span class="play_total_time player_playback_total_time">0:00</span>
                <script>
                    get_duration("{path}", function(length) {
                        $('.profile_data_item .player_playback_container[data-src="{path}"] .player_playback_total_time').text(format_seconds_as_time(length))
                    });
                </script>
            </div>

            {if (is_auth())}
                <div class="comment_field">
                    <div class="avatar_preview"
                         style="background-image: url('<?=get_user_avatar($_SESSION['id']);?>'); background-size: cover"></div>
                    <input type="text" placeholder="Write a comment" track-id="{id}" title="Send track comment (press enter to upload)" data-bs-toggle="tooltip" data-bs-placement="left">
                </div>
            {/if}

            <div class="item_stats">
                <button class="likes {if('{is_liked}' == 'true')}active{/if}"
                        onclick="add_favorite_track({id}, 'track')" data-id="{id}" title="Click to like the track" data-bs-toggle="tooltip" data-bs-placement="bottom">
                    <i class="fa-solid fa-heart"></i>
                    <span>{likes}</span>
                </button>

                <button class="comments" data-id="{id}" title="Comments count" data-bs-toggle="tooltip" data-bs-placement="bottom">
                    <i class="fa-solid fa-comment"></i>
                    <span>{comments}</span>
                </button>

                <button title="Reposts count" data-bs-toggle="tooltip" data-bs-placement="bottom">
                    <i class="fa-solid fa-retweet"></i>
                    <span>{reposts}</span>
                </button>

                <button onclick="copy_to_clipboard('{track_url}', $(this))" title="Click to copy track link" data-bs-toggle="tooltip" data-bs-placement="bottom">
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