<?php

require "classes/Url.php";

session_start();

$_SESSION = array();

session_destroy();

if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', $_COOKIE['remember_token'], time() - 3600);
}

if (isset($_COOKIE['remember_id'])) {
    setcookie('remember_id', $_COOKIE['remember_id'], time() - 3600);
}


Url::redirectUrl("/english-phrases-php/index.php");

?>
