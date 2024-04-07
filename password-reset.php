<?php

require "classes/Database.php";
require "classes/Users.php";

session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/src/Exception.php';
require 'vendor/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/src/SMTP.php';

function send_reset_password($email, $token) {
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;

        $mail->CharSet = "UTF-8";
        $mail->Encoding = "base64";

        $mail->Username = "tadeas.strba@gmail.com";
        //password generation via myaccount.google.com/apppasswords
        $mail->Password = "zzbqrhhjgvscojod";
        $mail->SMTPSecure = "ssl";
        $mail->Port = 465;
    
        $mail->setFrom("tadeas.strba@gmail.com");
        $mail->addAddress($email);
        $mail->Subject = 'Here is your password reset link';

        $email_template = "
            <p>You are receiving this email because we received a password reset request for your account.</p>
            <a href='http://localhost/english-phrases-php/new-password.php?token=$token'> Click me </a>
        ";

        $mail->Body = $email_template;
        $mail->isHTML(true);
    
        $mail->send();  
    } catch (Exception $e) {
            echo "Message has not been sent: ", $mail->ErrorInfo;
      }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $connection = Database::databaseConnection();
    $email = $_POST["email"];

    if(Users::checkEmailExists($connection, $email)) {
        $id = Users::getUserInfo($connection, $email, "id_user");
        $token = md5(rand());
       
        if(Users::updateToken($connection, $token, $email)) {        
            send_reset_password($email, $token);
            $_SESSION["status"] = "Reset password link has been send";
        } else {
            $_SESSION["status"] = "Something went wrong.";
        }

    } else {
        $_SESSION["status"] = "Email address is not registered";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/password-reset.css">
    <title>Password Reset</title>
</head>
<body>
    <form method="POST" class="sign-in">
        <h1>Password Reset</h1>
        <input type="email" placeholder="Enter email address" name="email" required>
        <button>Send</button>
        

        <?php if(isset($_SESSION["status"])): ?>
            <div class="message">
                <p><?= $_SESSION["status"]; ?></p>
            </div>
        <?php endif; ?>
    </form>
</body>
</html>
