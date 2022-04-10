<div class="login_container">
    <div class="logo_content">
        <img src="templates/standart/img/loginform/Logo.png" alt="Logo" class="logo">
        <h1 class="logo-text">Titify</h1>
    </div>

    <div class="login_field" style="position:relative;">
        <div class="login_content login" style="position: relative">
            <h2>Log in</h2>

            <div class="auth_api_container">
                <div class="g-signin2" data-onsuccess="google_redirect"></div>
                <div id="fb-root"></div>
                <div class="fb-login-button" data-max-rows="1" data-size="medium" data-button-type="login_with"
                     scope="public_profile,email" onlogin="checkLoginState();"
                     data-show-faces="false" data-auto-logout-link="false" data-use-continue-as="false">
                </div>
            </div>

            <div class="input_field">
                <input type="text" id="login_email" placeholder="Email or login" data-bs-toggle="tooltip" title="Your email or login (only letters and numbers)" data-bs-placement="left">

                <div class="show_password">
                    <input type="password" id="password" placeholder="Password" data-bs-toggle="tooltip" title="Your password" data-bs-placement="left">

                    <div class="show_password_icon" data-bs-toggle="tooltip" title="Show password">
                        <i class="fa-solid fa-eye-slash"></i>

                        <i class="fa-solid fa-eye" style="display: none"></i>
                    </div>
                </div>
            </div>

            <span id="message_result">{message}</span>

            <div class="sign_in_on">
                <button class="submit_button" onclick="user_login()">Log in</button>

                <div>
{*                    <a class="recovery_password" data-bs-toggle="tooltip" title="Forgot your password? Restore password">Recovery password</a>*}
                    <span class="create_account" style="margin-top: 0">Don’t have an account? <a id="change_window" style="cursor:pointer;" data-bs-toggle="tooltip" title="Go to registration">Sign on</a></span>
                </div>

                <style>
                    .sign_in_on > div {
                        margin-top: 1rem;
                        margin-bottom: 0.25rem;
                    }

                    .recovery_password {
                        color: #C9C9C9;
                        transition: var(--transition);
                        text-decoration: none;
                        cursor: pointer;
                    }

                    .recovery_password:hover {
                        color: var(--main-color);
                    }
                </style>

            </div>
        </div>

        {include file='authorization/registration.tpl'}

{*        {include file='authorization/recovery.tpl'}*}
    </div>
</div>

<script>
    function google_redirect(google_user) {
        auth_by_api('google', google_user,);
    }

    $('.show_password_icon').on('click', () => {
        if ($('.show_password_icon i').hasClass('fa-eye-slash')) {
            $('.show_password_icon i').removeClass('fa-eye-slash');
            $('.show_password_icon i').addClass('fa-eye');
            $('.show_password input').attr('type', 'text');
            $('#reg_password_repeat').attr('type', 'text');
            $('.show_password_icon').attr('data-bs-original-title', 'Hide password');
        } else {
            $('.show_password_icon i').removeClass('fa-eye');
            $('.show_password_icon i').addClass('fa-eye-slash');
            $('.show_password input').attr('type', 'password');
            $('#reg_password_repeat').attr('type', 'password');
            $('.show_password_icon').attr('data-bs-original-title', 'Show password');
        }

        $('.show_password_icon').tooltip('show');
    });

    $('.registration').hide();
    $('.recovery').hide();
    $('#change_window').on('click', () => {
        $('.login').hide();
        $('.registration').show();

        $('.registration input').val('');
        $('.show_password input').attr('type', 'password');
        $('#reg_password_repeat').attr('type', 'password');
    });

    $('#change_to_login_window').on('click', () => {
        $('.registration').hide();
        $('.login').show();

        $('.login input').val('');
        $('.show_password input').attr('type', 'password');
        $('#reg_password_repeat').attr('type', 'password');
    });

    $('.recovery_password').on('click', () => {
        $('.login').hide();
        $('.recovery').show();
    });

    $('#back_to_login').on('click', () => {
        $('.recovery').hide();
        $('.login').show();
    });
</script>