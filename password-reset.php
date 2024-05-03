<?php

require "classes/Database.php";
require "classes/Users.php";
require "classes/Page.php";

session_start();

//Page Name for send email
$url = $_SERVER['REQUEST_URI'];
$url_parts = parse_url($url);
$path = $url_parts['path'];
$page_name = basename($path);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $connection = Database::databaseConnection();
    $email = htmlspecialchars($_POST["email"]);

    if(Users::checkUserDataExists($connection, $email, "email")) {
        $id = Users::getUserInfo($connection, $email, "id_user");
        $token = md5(rand());
       
        if(Users::updateToken($connection, $token, $email)) {        
            Page::send_email($email, $token, $page_name);
            $_SESSION["status"] = "Reset password link has been sent.";
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
    <form method="POST" class="password-reset">
        <h1>Password Reset</h1>
        <input type="email" placeholder="Enter email address" name="email" required>
        <button>Send</button>

        <div class="log-in">
            <p>Back to</p>
            <a href="index.php">Login</a>
        </div>

        <?php if(isset($_SESSION["status"])): ?>
            <div class="alert">
                <p><?= $_SESSION["status"]; ?></p>
            </div>
        <?php endif; ?>
    </form>
</body>
</html>
