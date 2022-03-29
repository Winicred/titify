<div class="left_sidebar">
    <div class="left_sidebar_container">
        <div class="sidebar_logo">
            <div class="logo">
                <img src="templates/standart/img/logo.png" alt="">
            </div>
            <h2>Titify</h2>
        </div>

        <div class="sidebar_nav">
            <ul class="sidebar_main">
                <li>
                    <a onclick="load_template('index')" class="active">
                        <span>Home</span>
                    </a>
                </li>

                {if (is_auth())}
                <li>
                    <a onclick="load_template('playlists', {'id': {{$user->id}}})">
                        <span>Library</span>
                    </a>
                </li>
                {/if}

                <li>
                    <a onclick="load_template('search')">
                        <span>Search</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>