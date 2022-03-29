<div class="track_container">
    <div class="track_header">
        <div class="track_header_left">
            <div class="track_info">
                <div class="track_play">
                    <button>
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

            <div class="track_player">
                <span class="play_current_time">0:00</span>

                <div class="player_bar">
                    <div class="player_bar_progress"></div>
                </div>

                <span class="play_total_time">0:00</span>
            </div>
        </div>

        <div class="track_header_right">
            <img id="preview_image" src="{cover}" alt="Track photo">
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
                    <div class="entry_text toggle_show">
                        {if ('{description}' == '')}
                            <span class="text-muted">No description</span>
                        {else}
                            <span>{description}</span>
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

    $(window).on('load', function () {
        const img = document.getElementById("preview_image");
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

    });
</script>