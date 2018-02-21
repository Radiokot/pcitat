<?php

include_once dirname(__FILE__)."/ApiHelper.php";
error_reporting(E_ALL & ~E_DEPRECATED);

if (isset($_COOKIE[EMAIL_HEADER])) {
    unset($_COOKIE[EMAIL_HEADER]);
    setcookie(EMAIL_HEADER, null, time() - 3600);
}
if (isset($_COOKIE[KEY_HEADER])) {
    unset($_COOKIE[KEY_HEADER]);
    setcookie(KEY_HEADER, null, time() - 3600);
}

header("Location: /");

?>