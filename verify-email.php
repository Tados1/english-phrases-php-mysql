<?php

require "classes/Database.php";
require "classes/Url.php";
require "classes/Users.php";

session_start();

$connection = Database::databaseConnection();

if(isset($_GET["token"])) {
    $token = $_GET["token"];
    $row = Users::verifyToken($connection, $token);
    
    if($row) {
        if ($row['verify_status'] == "0" ) {
            $clicked_token = $row['verify_token']; 

            if(Users::updateVerifyStatus($connection, $token)) {
                $_SESSION['status'] = "Your account has been verified successfully!";
                Url::redirectUrl("/english-phrases-php/index.php");
                exit(0);
            } else {
                $_SESSION['status'] = "Verification failed!";
                Url::redirectUrl("/english-phrases-php/index.php");
                exit(0);
            }
        } else {
            $_SESSION['status'] = "Email already verified. Please login.";
            Url::redirectUrl("/english-phrases-php/index.php");
            exit(0);
        }
    } else {
        $_SESSION['status'] = "This token does not exist.";
        Url::redirectUrl("/english-phrases-php/index.php");
    }

} else {
    $_SESSION['status'] = "Not Allowed";
    Url::redirectUrl("/english-phrases-php/index.php");
}

?>