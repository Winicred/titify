<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{title}</title>

    <link rel="icon" type="image/x-icon" href="{site_host}templates/standart/img/logo.png">

    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap"
          rel="stylesheet">

    <meta name="developers" content="2022, Danil Barsukov, Vladislav Hodžajev, Kirill Goritski">
    <meta name="google-site-verification" content="cSSWagpkUMHyYKEkp-C9mL4DI_pO0dFqYTSshkMiHso"/>
    <meta name="google-signin-client_id"
          content="622357418343-tftvgp5515braf77dvg0tuktvgf0ikdd.apps.googleusercontent.com">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="creators" content="kutsehariduskeskus.ee">

    <script src="{site_host}templates/standart/js/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"
            integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"
            integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13"
            crossorigin="anonymous"></script>

    <script src="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone-min.js"></script>
    <link href="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone.css" rel="stylesheet" type="text/css" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
          integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>

    <link rel="stylesheet" href="{site_host}templates/standart/css/main.css">
    <link rel="stylesheet" href="{site_host}templates/standart/css/media.css">

    {if ($page->url != 'login')}
        <script src="{site_host}templates/standart/js/player.js" defer></script>
    {/if}
{*    <script src="{site_host}templates/standart/js/History.js" defer></script>*}
    <script src="{site_host}templates/standart/js/jquery.history.js" defer></script>
    <script src="{site_host}ajax/helpers.js"></script>
    <script src="{site_host}ajax/ajax-user.js"></script>

    <script>
        function on_load() {
            gapi.load('auth2', function () {
                gapi.auth2.init();
            });
        }
    </script>
    
    <script src="https://apis.google.com/js/platform.js?onload=on_load" async defer></script>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/ru_RU/sdk.js#xfbml=1&version=v13.0&appId=227392456211200&autoLogAppEvents=1" nonce="X9PkIYqx"></script>

    <script>
        function statusChangeCallback(response, access_token) {  // Called with the results from FB.getLoginStatus().
            FB.api(
                '/me',
                'GET',
                    {"fields":"email,first_name,last_name,picture{url},name"},
                function(fb_login) {
                    auth_by_api('facebook', fb_login, access_token);
                }
            );
        }

        function checkLoginState() {
            let access_token = '';
            FB.getLoginStatus(function(response) {
                if (response.status === 'connected') {
                    access_token = response.authResponse.accessToken;
                    statusChangeCallback(response, access_token);
                }
            });
        }


        window.fbAsyncInit = function() {
            FB.init({
                appId      : '227392456211200',
                cookie     : true,                     // Enable cookies to allow the server to access the session.
                xfbml      : true,                     // Parse social plugins on this webpage.
                version    : 'v13.0'           // Use this Graph API version for this call.
            });
        };

    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/vibrant.js/1.0.0/Vibrant.min.js" integrity="sha512-V6rhYmJy8NZQF8F0bhJiTM0iI6wX/FKJoWvYrCM15UIeb6p38HjvTZWfO0IxJnMZrHWUJZJqLuWK0zslra2FVw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>

<input id="token" value="{token}">

{if ($page->url != 'login')}
    <body onload="init_player()">

    {include file="elements/main_navigation.tpl"}

<main>

    {include file="elements/head_navigation.tpl"}

    <div class="container">
{else}
    <body>
{/if}