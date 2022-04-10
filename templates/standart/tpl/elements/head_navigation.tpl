<? global $user ?>

<div class="head_panel">
    <div class="input_search">
        <input type="text" id="find_tracks_input" placeholder="Find Track" data-bs-toggle="tooltip" data-bs-placement="left" title="Write search keyword here">

        <div class="find_tracks_results"></div>

        <button class="search_icon">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
    </div>

    <div class="user_panel_navigation">
        {if (is_auth())}

            <button class="upload_track_button" onclick="load_template('upload_track')" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Go to track uploading page">
                Add music
            </button>

            <div class="notification_window_container">
                <button onclick="open_noty_window()" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Notifications">
                    <i class="fa-regular fa-bell"></i>
                </button>

                <div class="notification_window inactive">
                    <div class="notification_window_header">
                        <span onclick="clear_all_notifications()">Clear all</span>
                        <button onclick="$('.notification_window').removeClass('active').addClass('inactive')">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <div class="notifications_window_list"></div>
                </div>
            </div>
            <div class="dropdown">
                <span class="dropbtn">{{$user->display_name}}</span>
                <div class="dropdown-content">
                    <a onclick="load_template('profile', {id: {{$user->id}}})">Profile</a>
                    <a onclick="load_template('profile', {id: {{$user->id}}}, 'edit_profile')">Settings</a>
                    <a onclick="user_exit()">Logout</a>
                </div>
            </div>
            <script>
                get_notifications();
            </script>
        {else}
            <a href="login" class="button_login" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Log in page">Log in</a>
        {/if}
    </div>
</div>

<script>
    keyboard_event('#find_tracks_input', 'find_tracks();');
</script>