<div class="track_container">
    <div class="track_header">
        <div class="track_header_left">
            <div class="track_info">
                <div class="track_play">
                    <button data-bs-toggle="tooltip" data-src="{path}" title="Play" onclick="player_play('{path}');">
                        <i class="fa fa-play"></i>
                    </button>
                </div>

                <div class="track_title">
                    <div class="title">
                        <span>{title}</span>
                    </div>

                    <div class="author">
                        <a onclick="load_template('profile', {id: {author_id}})">{author}</a>
                    </div>
                </div>

                <div class="track_push_date">
                    <span>{push_date}</span>
                </div>
            </div>

            <div class="track_player player_playback_container" data-src="{path}">
                <span class="play_current_time player_playback_current_time">0:00</span>

                <div class="player_bar player_bar_container player_playback_bar_block">
                    <div class="player_bar_progress player_bar player_playback_bar">
                        <div class="player_bar_progress player_playback_bar_progress"></div>
                    </div>
                </div>

                <span class="play_total_time player_playback_total_time" id="total_time">0:00</span>

                <script>
                    $(window).on("load", function() {
                    get_duration("{path}", function(length) {

                            $("#total_time").text(format_seconds_as_time(length));
                    });
                    });
                </script>
            </div>
        </div>

        <div class="track_header_right" style="background-color: rgba(0,0,0,0.1)">
            <div id="preview_image"
                 style="background-image: url('{cover}'); width: 100%; height: 100%; background-size: contain; background-position: center; background-repeat: no-repeat"></div>
        </div>
    </div>

    <div class="track_footer">
        <div class="track_about">
            {if (is_auth())}
                <div class="comment_field">
                    <div class="avatar_preview"
                         style="background-image: url('<?=get_user_avatar($_SESSION['id']);?>'); background-size: cover"></div>
                    <input type="text" placeholder="Write a comment" track-id="{id}">
                </div>
            {/if}

            <div class="action_tools">

                <button class="action_item likes {if ('{track_liked}' == 'true')}active{/if}"
                        onclick="add_favorite_track({id}, 'track')" data-id="{id}">
                    <i class="fa fa-heart"></i>
                    <span>{likes}</span>
                </button>
                {if (is_auth())}
                    <button class="action_item">
                        <i class="fa fa-share-alt"></i>
                        {if ('{track_reposted}' == 'true')}
                            <span>Reposted</span>
                        {else}
                            <span>Repost</span>
                        {/if}
                    </button>
                {/if}

                <div class="track_stats">
                    <div class="track_stats_item">
                        <i class="fa fa-play"></i>
                        <span>{auditions}</span>
                    </div>

                    <div class="track_stats_item">
                        <i class="fa fa-heart"></i>
                        <span>{likes}</span>
                    </div>

                    <div class="track_stats_item">
                        <i class="fa fa-share-alt"></i>
                        <span>{reposts}</span>
                    </div>
                </div>
            </div>

            <div class="track_info_container">
                <div class="author_info">
                    <div class="avatar_preview"
                         style="background-image: url('{author_avatar}'); background-size: cover"></div>
                    <div class="author_profile_info">
                        <a onclick="load_template('profile', {id: {author_id}})">{author} {if ('{is_very}' == 'true')}{include file='elements/verification/verification.tpl'}{/if}</a>

                        <div class="author_stats">
                            <div class="author_stats_item">
                                <i class="fa fa-play"></i>
                                <span>{author_auditions}</span>
                            </div>

                            <div class="author_stats_item">
                                <i class="fa-solid fa-microphone-lines"></i>
                                <span>{tracks}</span>
                            </div>
                        </div>
                    </div>

                    {if((is_auth() && '{author_id}' != '{user_id}'))}
                        <div class="offense_actions">
                            <button class="action_item">
                                <i class="fa fa-ban"></i>
                                <span>Block {author}</span>
                            </button>

                            <button class="action_item">
                                <i class="fa fa-flag"></i>
                                <span>Report</span>
                            </button>
                        </div>
                    {/if}
                </div>

                <div class="track_description">
                    <div class="entry_text toggle_show" style="display: flex;justify-content: space-between">
                        {if ('{description}' == '')}
                            <span class="text-muted">No description</span>
                        {else}
                            <span>{description}</span>
                        {/if}

                        {if (('{author_id}' == '{user_id}') || (is_auth() && is_worthy('f')))}
                            <div class="track_tools">
                                <button class="edit_track_button" onclick="call_modal('edit_track', {id: {id}})">edit</button>
                                <button class="delete_track_button" onclick="call_modal('delete_track', {id: {id}})">delete</button>
                            </div>

                            <style>
                                .track_tools {
                                    display: flex;
                                    gap: 5px
                                }

                                .edit_track_button,
                                .delete_track_button{
                                    width: 75px;
                                    background-color: var(--light-main-color);
                                    padding: 5px 10px;
                                    color: #FFFFFF;
                                    border-radius: 30px;
                                    transition: var(--transition);
                                }

                                .delete_track_button {
                                    background-color: var(--red-color);
                                }

                                .edit_track_button:hover,
                                .delete_track_button:hover {
                                    filter: brightness(80%);
                                }

                            </style>
                        {/if}
                    </div>

                    <div class="comments_container">
                        <div class="comments_title">
                            <i class="fa fa-comment"></i>
                            <span>{comments} Comments</span>
                        </div>

                        <div class="comments_list">
                            {comments_data}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="track_success">
            <div class="track_success_item">
                <div class="track_success_item_header">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <span>In playlists</span>
                </div>

                <div class="in_playlists_list">
                    {in_other_playlists_data}
                </div>
            </div>

            <div class="track_success_item">
                <div class="track_success_item_header">
                    <i class="fa-solid fa-heart"></i>
                    <span>Likes</span>
                </div>

                <div class="track_likes_list">
                    {likes_data}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    load_track_comment({id});
    load_tracks_likes({id});

    // Set dominant color from image to child div
    function vibrant_background_gradient(cover_url = '{cover}') {
        const img = new Image();
        img.src = cover_url;
        img.onload = function () {
            const vibrant = new Vibrant(img);
            const swatches = vibrant.swatches();

            let gradColors;
            if (swatches.Vibrant) {
                gradColors = [
                    vibrant.DarkVibrantSwatch.getHex(),
                    vibrant.VibrantSwatch.getHex(),
                    vibrant.MutedSwatch.getHex(),
                    vibrant.DarkMutedSwatch.getHex(),
                    vibrant.LightMutedSwatch.getHex(),
                ];
            } else {
                gradColors = [
                    '#555555',
                    '#555555',
                    '#555555',
                    '#555555',
                    '#555555',
                ];
            }

            const element = document.querySelector('.track_header');
            element.style.background = 'linear-gradient(180deg,' + gradColors.join() + ')';
            element.style.backgroundSize = '400% 400%';
        };
    }

    vibrant_background_gradient();

    function hexToRgb(color) {
        var r = parseInt(color.substring(1, 3), 16);
        var g = parseInt(color.substring(3, 5), 16);
        var b = parseInt(color.substring(5, 7), 16);
        return 'rgba(' + r + ',' + g + ',' + b + ', 0.8)';
    }
</script>