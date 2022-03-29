<?php

$session_cookies->unset_user_session();

header('Location: ../');
exit('<script>reset_page()</script>');
