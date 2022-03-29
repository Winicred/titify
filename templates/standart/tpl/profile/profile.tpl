<div class="profile_container">
    <div class="profile_head_container">
        <div class="profile_head_cover cover_preview" style="background-image: url('{cover}')">
            <div class="profile_head_image_container">
                {if ('{id}' == '{user_id}' && '{user_id}' != '')}
                    <div class="profile_edit_avatar_container">
                        <div class="profile_head_image">
                            <div class="avatar_preview main_avatar"
                                 style="background-image: url('<?=get_user_avatar($_SESSION['id']);?>');"></div>
                        </div>

                        <div class="edit_avatar">
                            <input id="avatar_input" type="file" accept="image/*">

                            <button id="change_avatar_button">
                                <i class="fa-solid fa-camera-rotate"></i>
                                <span>Change avatar</span>
                            </button>
                        </div>
                    </div>
                {else}
                    <div class="avatar_preview main_avatar" style="background-image: url('{avatar}');"></div>
                {/if}

                <div class="profile_head_name_container">
                    <span>
                        {if ('{deleted}' == 'true')}
                            <span class="text-decoration-line-through">{display_name}</span>
                        {else}
                            <span>{display_name}</span>
                        {/if}

                        {if ('{is_very}' == 'true')}
                            {include file='elements/verification/verification.tpl'}
                        {/if}
                    </span>
                </div>
            </div>

            {if (is_auth())}
                {if ('{id}' == '{user_id}' || is_worthy('f'))}
                    <div class="edit_cover_container">
                        {*                    <button onclick="default_cover()">*}
                        {*                        <i class="fa-solid fa-trash"></i>*}
                        {*                        <span>Remove cover</span>*}
                        {*                    </button>*}

                        <input id="cover_input" type="file" accept="image/*">

                        <button id="change_cover_button">
                            <i class="far fa-edit" aria-hidden="true"></i>
                            <span>Edit cover</span>
                        </button>
                    </div>
                {/if}
            {/if}
        </div>

        <div class="profile_head_navigation_container">
            <ul>
                <li><a data-href="#all" class="active">All</a></li>
                <li><a data-href="#popular_tracks">Popular Tracks</a></li>
                <li><a data-href="#tracks">Tracks</a></li>
                <li><a data-href="#playlists">Playlists</a></li>
            </ul>

            <div class="profile_head_navigation_sub_tools_container">

                {if (is_auth())}
                    {if ('{id}' != '{user_id}')}
                        <button>
                            <i class="fa-regular fa-share-from-square"></i>
                            <span>Share</span>
                        </button>
                    {/if}

                    {if ('{id}' == '{user_id}' || is_worthy('f'))}
                        <button onclick="load_template('profile', {id: {id}}, 'edit_profile')">
                            <i class="fa-regular fa-pen-to-square"></i>
                            <span>Edit</span>
                        </button>
                    {/if}

                    {if ('{id}' != '{user_id}')}
                        {if ('{is_followed}' == 'false')}
                            <button onclick="follow_actions('{id}', 'follow', $(this))">
                                <i class="fa-solid fa-plus"></i>
                                <span>Follow</span>
                            </button>
                        {else}
                            <button onclick="follow_actions('{id}', 'unfollow', $(this))">
                                <i class="fa-solid fa-minus"></i>
                                <span>Unfollow</span>
                            </button>
                        {/if}
                    {/if}
                {/if}
            </div>
        </div>
    </div>

    <div class="profile_wall_data_container">
        <div class="profile_wall_data">
            <div class="profile_wall_data_item" id="all">
                {all_data}
            </div>

            <div class="profile_wall_data_item" id="popular_tracks" style="display: none">
                {popular_tracks_data}
            </div>
            <div class="profile_wall_data_item" id="tracks" style="display: none">{tracks_data}</div>
            <div class="profile_wall_data_item" id="playlists" style="display: none">{playlists_data}</div>
        </div>

        <div class="profile_about_container">
            <div class="profile_statistics_container">
                <a class="profile_user_data_statistics_item" onclick="load_template('users', {id: {id}})">
                    <span class="title">Followers</span>
                    <span>{followers_count}</span>
                </a>

                <a class="profile_user_data_statistics_item" onclick="load_template('users', {id: {id}})">
                    <span class="title">Following</span>
                    <span>{following_count}</span>
                </a>

                <a class="profile_user_data_statistics_item" onclick="load_template('users', {id: {id}})">
                    <span class="title">Tracks</span>
                    <span>{tracks_count}</span>
                </a>
            </div>

            {if ('{about}' != '')}
                <div class="profile_about_text">
                    {if (is_auth())}
                        {if ('{id}' != '{user_id}')}
                            <span>{about}</span>
                        {else}
                            {if ('{about}' != '')}
                                <span class="change_status_element" onclick="change_status_element()">{about}</span>
                            {else}
                                <span class="change_status_element"
                                      onclick="change_status_element()">Change your status</span>
                            {/if}
                        {/if}
                    {/if}
                </div>
            {/if}

            <div class="profile_about_social_container">
                {if ("{facebook}" != "")}
                    <a href="facebook.com/profile.php?id={facebook}" target="_blank">
                        <i class="fa-brands fa-facebook-square"></i>
                        <span>Facebook</span>
                    </a>
                {/if}

                {if ("{twitter}" != "")}
                    <a href="twitter.com/{twitter}" target="_blank">
                        <i class="fa-brands fa-twitter-square"></i>
                        <span>Twitter</span>
                    </a>
                {/if}

                {if ("{instagram}" != "")}
                    <a href="instagram.com/{instagram}" target="_blank">
                        <i class="fa-brands fa-instagram"></i>
                        <span>Instagram</span>
                    </a>
                {/if}

                {if ("{youtube}" != "")}
                    <a href="youtube.com/channel/{youtube}" target="_blank">
                        <i class="fa-brands fa-youtube-square"></i>
                        <span>YouTube</span>
                    </a>
                {/if}

                {if ("{telegram}" != "")}
                    <a href="t.me/{telegram}" target="_blank">
                        <i class="fa-brands fa-telegram-plane"></i>
                        <span>Telegram</span>
                    </a>
                {/if}

                {if ("{vk}" != "")}
                    <a href="vk.com/id{vk}" target="_blank">
                        <i class="fa-brands fa-vk"></i>
                        <span>VK</span>
                    </a>
                {/if}

                {if ("{github}" != "")}
                    <a href="github.com/{github}" target="_blank">
                        <i class="fa-brands fa-github-square"></i>
                        <span>Github</span>
                    </a>
                {/if}

                {if ("{website}" != "")}
                    <a href="{website}" target="_blank">
                        <i class="fa-solid fa-globe"></i>
                        <span>Website</span>
                    </a>
                {/if}
            </div>

            <div class="profile_user_last_likes">
                <div class="profile_user_last_likes_header">
                    <span><i class="fa-solid fa-heart"></i>{likes_count} Likes</span>
                </div>

                <div class="profile_user_last_likes_container">
                    {tracks_mini}
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    // Set dominant color from image to child div
    function vibrant_background(cover_url = '{cover}') {
        const img = new Image();
        img.src = cover_url;
        img.onload = function () {
            const vibrant = new Vibrant(img);
            const swatches = vibrant.swatches();
            const color = swatches.Vibrant.getHex();
            $('.profile_head_name_container').css('background-color', hexToRgb(color));
            $('.avatar_preview').css('background-color', hexToRgb(color));
            $('.avatar_preview.main_avatar').css('border', '3px solid ' + hexToRgb(color));
        };
    }

    vibrant_background();

    function hexToRgb(color) {
        var r = parseInt(color.substring(1, 3), 16);
        var g = parseInt(color.substring(3, 5), 16);
        var b = parseInt(color.substring(5, 7), 16);
        var result = 'rgba(' + r + ',' + g + ',' + b + ', 0.8)';
        return result;
    }

    function change_status_element() {
        let text;

        if ($('.change_status_element').text() != 'Change your status') {
            text = $('.change_status_element').text();
        } else {
            text = '';
        }

        $('.change_status_element').replaceWith(
            '<textarea class="profile_about_textarea" name="status" rows="1" maxlength="255" placeholder="' + text + '">' + text + '</textarea><span class="textarea_count_letter">' + text.length + ' | 255' + '</span>'
        );

        $('.profile_about_textarea').on('keyup paster change', function (e) {
            let textarea_count = $('.profile_about_textarea').val().length;
            $('.textarea_count_letter').text(textarea_count + ' | 255');

            if (textarea_count === 255) {
                $('.textarea_count_letter').css('color', 'red');
            } else {
                $('.textarea_count_letter').css('color', 'var(--light-gray-color)');
            }

            if (e.keyCode === 13) {
                change_user_status($(this).val());
            }
        });
    }


    $(document).ready(function () {
        $("#change_avatar_button").bind("click", function () {
            $("#avatar_input").click();
        });

        $("#change_cover_button").bind("click", function () {
            $("#cover_input").click();
        });

        $("#avatar_input").bind("change", function () {
            if (this.files[0].size > 100000000) {
                alert("Image must not exceed 1 mb.");
                return;
            }

            const data = new FormData();
            data.append("change_avatar", "1");
            data.append("image", this.files[0]);

            send_image_query('ajax/actions_auth.php', data, function (result) {
                if (result.status === 'success') {
                    const avatar = document.getElementsByClassName("avatar_preview");

                    for (let i = 0; i < avatar.length; i++) {
                        avatar[i].style.backgroundImage = "url('" + result.file + "')";
                    }
                } else {
                    alert(result.message);
                }
            });
        });

        $('.profile_head_navigation_container ul li a').on('click', function () {
            $('.profile_head_navigation_container ul li a').removeClass('active');
            $(this).addClass('active');

            $('.profile_wall_data_item').hide();

            $($(this).attr('data-href')).show();
        });

        $("#cover_input").bind("change", function () {
            if (this.files[0].size > 100000000) {
                alert("Image must not exceed 1 mb.");
                return;
            }

            const data = new FormData();
            data.append("change_cover", "1");
            data.append("image", this.files[0]);

            send_image_query('ajax/actions_auth.php', data, function (result) {
                if (result.status === 'success') {
                    const cover = document.getElementsByClassName("cover_preview");
                    cover[0].style.backgroundImage = "url('" + result.file + "')";
                    vibrant_background(result.file);
                } else {
                    alert(result.message);
                }
            });
        });

        $('.profile_head_navigation_container ul li a').on('click', function () {
            $('.profile_head_navigation_container ul li a').removeClass('active');
            $(this).addClass('active');

            $('.profile_wall_data_item').hide();

            $($(this).attr('data-href')).show();
        });

        $('.profile_head_navigation_item a').first().click();
    });
</script>