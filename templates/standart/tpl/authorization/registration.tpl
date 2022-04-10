<div class="login_content registration">
    <h2>Registration</h2>

    <div class="input_field">
        <input type="text" id="reg_login" placeholder="Login (required)">

        <input type="text" id="reg_display_name" placeholder="Display name (required)">

        <div class="show_password">
            <input type="password" id="reg_password" placeholder="Password (required)">

            <div class="show_password_icon">
                <i class="fa-solid fa-eye-slash"></i>

                <i class="fa-solid fa-eye" style="display: none"></i>
            </div>
        </div>

        <input type="password" id="reg_password_repeat" placeholder="Repeat password (required)">

        <input type="text" id="reg_email" placeholder="Email {if ($config->conf_us == 0)}(required){/if}">

        {if ($config->conf_us == 0)}
            <div class="alert alert-primary" role="alert" style="margin-top: 1rem">
                <span class="fw-bold">Attention:</span> Please use your real email.
            </div>
        {/if}
    </div>

    <span id="reg_message_result">{message}</span>

    <div class="sign_in_on">
        <button class="submit_button" onclick="registration()">Log in</button>

        <span class="create_account">Already registered an account?
            <a id="change_to_login_window" style="cursor:pointer;">Log in</a>
        </span>
    </div>
</div>