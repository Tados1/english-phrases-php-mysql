<?php

require "classes/Database.php";
require "classes/Url.php";
require "classes/Users.php";

session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/src/Exception.php';
require 'vendor/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/src/SMTP.php';

function sendemail_verify($name, $email, $verify_token) {
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
        $mail->Subject = 'You have registered with English Phrases';

        $email_template = "
            <h2>You have registered with English Phrases</h2>
            <p>Verify your email address to Login with the below given link</p>
            <a href='http://localhost/english-phrases-php/verify-email.php?token=$verify_token'> Click me </a>
        ";

        $mail->Body = $email_template;
        $mail->isHTML(true);
    
        $mail->send();

        echo "Sent message";

    } catch (Exception $e) {
          echo "Message has not been sent: ", $mail->ErrorInfo;
    }

}


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = $_POST["email"];
    $name = $_POST["name"];
    $password = $_POST["password"];
    $repeat_password = $_POST["repeat_password"];
    $verify_token = md5(rand());

    $connection = Database::databaseConnection();

    $check_email_query = "SELECT email FROM users WHERE email = :email LIMIT 1";
    $stmt = $connection->prepare($check_email_query);
    $stmt->execute(array(':email' => $email));

    if (Users::checkEmailExists($connection, $email)) {
        $_SESSION["status"] = "Email already exists";
        header("Location: registration.php");
        exit;
    } 
    
    if ($password === $repeat_password) {
        if(Users::createUser($connection, $email, $name, $password, $verify_token)) {
            sendemail_verify("$name", "$email", "$verify_token");
            $_SESSION['status'] = "Registration Successfull! Please verify your email address.";
            header("Location: registration.php");
        }
    } else {
        $error = "Your passwords do not match";
    }
    
}

?>