<?php

require "classes/Database.php";
require "classes/Url.php";
require "classes/Users.php";
require "classes/Page.php";

session_start();

//Page Name for send email
$url = $_SERVER['REQUEST_URI'];
$url_parts = parse_url($url);
$path = $url_parts['path'];
$page_name = basename($path);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = htmlspecialchars($_POST["email"]);
    $name = htmlspecialchars($_POST["name"]);
    $password = htmlspecialchars($_POST["password"]);
    $repeat_password = htmlspecialchars($_POST["repeat_password"]);
    $verify_token = md5(rand());
    
    $connection = Database::databaseConnection();

    if (Users::checkUserDataExists($connection, $email, "email")) {
        $_SESSION["status"] = "Email already exists";
        Url::redirectUrl("/english-phrases-php/sign-up.php");
        exit;
    } else {
        if ($password === $repeat_password) {
            if(Users::createUser($connection, $email, $name, $password, $verify_token)) {
                Page::send_email($email, $verify_token, $page_name);
                $_SESSION['status'] = "Registration Successfull! Please verify your email address.";
                Url::redirectUrl("/english-phrases-php/sign-up.php");
            }
        } else {
            $_SESSION["status"] = "Your passwords do not match";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/sign-up.css">
    <title>Sign Up</title>
</head>
<body>
    
    <form method="POST" class="sign-up" >
        <h1>Sign Up</h1>
        <input type="email" placeholder="Email" name="email" required>
        <input type="text" placeholder="Name" name="name" required>
        <input type="password" placeholder="Password" name="password" required>
        <input type="password" placeholder="Repeat Password" name="repeat_password" required>
        <button>Sign Up</button>

        <div class="log-in">
            <p>Have an account?</p>
            <a href="index.php">Login here</a>
        </div>

        <?php if(isset($_SESSION['status'])) : ?>
            <div class="alert">
                <p><?= $_SESSION['status']; ?></p>
            </div>
            <?php unset($_SESSION['status']); ?>
        <?php endif; ?>
    </form>

</body>
</html>
