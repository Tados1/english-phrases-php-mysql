<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/src/Exception.php';
require 'vendor/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/src/SMTP.php';

class Page {
    public static function getInfo($connection, $id = 1) {
        $sql = "SELECT *
                FROM page
                WHERE id = :id";
        
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        try {
            if($stmt->execute()) {
                return $stmt->fetch();
            } else {
                throw new Exception("Retrieving page data failed");
            }
        } catch (Exception $e) {
            error_log("Error with function getInfo, failed to get data\n", 3, "../errors/error.log");
            echo "Error Type: " . $e->getMessage();
        }
    }

    public static function send_email($email, $token, $page_name) {
        $mail = new PHPMailer(true);
    
        $connection = Database::databaseConnection();
        $info = Page::getInfo($connection);
    
        try {
            $mail->isSMTP();
            $mail->Host = "smtp.gmail.com";
            $mail->SMTPAuth = true;
    
            $mail->CharSet = "UTF-8";
            $mail->Encoding = "base64";
    
            $mail->Username = $info["email"];
            //password generation via myaccount.google.com/apppasswords
            $mail->Password = $info["password"];
            $mail->SMTPSecure = "ssl";
            $mail->Port = 465;
        
            $mail->setFrom($info["email"]);
            $mail->addAddress($email);
            $mail->Subject = "English Phrases";
    
            if($page_name === "sign-up.php") {
                $email_template = "
                    <h2>You have registered on the English Phrases website</h2>
                    <p>Verify your email address to login with the below given link</p>
                    <a href='http://localhost/english-phrases-php/verify-email.php?token=$token'> Click here</a>
                ";
            } elseif($page_name === "password-reset.php") {
                $email_template = "
                    <p>You are receiving this email because we received a password reset request for your account.</p>
                    <a href='http://localhost/english-phrases-php/new-password.php?token=$token'> Click here</a>
                ";
            }
    
            $mail->Body = $email_template;
            $mail->isHTML(true);
        
            $mail->send();
        } catch (Exception $e) {
              echo "Message has not been sent: ", $mail->ErrorInfo;
        }
    }
}