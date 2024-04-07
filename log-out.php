<?php

require "classes/Url.php";

session_start();

$_SESSION = array();

session_destroy();

Url::redirectUrl("/english-phrases-php/index.php");

?>
