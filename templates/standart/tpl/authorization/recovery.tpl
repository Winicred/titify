<div class="recovery">
    <h2>Password recovery</h2>
    <div class="alert alert-primary" role="alert" style="margin-top: 1rem">
        <span>
            Please enter your email address and we will send you a link to reset your password.
        </span>
    </div>
    <input type="text" id="email_recovery" placeholder="Email" data-bs-toggle="tooltip" title="Enter your email for recovery link" data-bs-placement="left">

    <button onclick="send_new_pass()" class="button_send">Send new pass</button>

    <div id="recovery_result">{message}</div>

    <div>
        <a id="back_to_login" data-bs-toggle="tooltip" title="Back to login form">Back to login</a>
    </div>
</div>

<style>
    .recovery {
        display: flex;
        flex-direction: column;
        width: 413px;
    }

    .recovery input {
        width: 100%;
        height: 100%;
        border: none;
        background: none;
        padding: 6px 0;
        outline: none;
        color: #9D9D9D;
        font-size: 16px;
        border-bottom: 2px solid #C9C9C9;
        margin: 10px 0;
        line-height: 35px;
        letter-spacing: 0.08em;
    }

    .recovery input:focus {
        border-bottom: 2px solid #00A8FF;
    }

    .recovery .button_send {
        border: none;
        outline: none;
        padding: 12px 0;
        background-color: var(--dark-main-color);
        color: var(--white-color);
        border-radius: 0.5rem;
        cursor: pointer;
        margin-top: 1rem;
    }

    .recovery #recovery_result {
        margin: 2rem 0;
    }

    .recovery #back_to_login {
        color: #C9C9C9;
        transition: var(--transition);
        cursor: pointer;
    }

    .recovery #back_to_login:hover {
        color: var(--main-color);
    }
</style>
