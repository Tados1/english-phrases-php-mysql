<?php 

require "classes/Database.php";
require "classes/Url.php";
require "classes/Users.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $connection = Database::databaseConnection();
    $token = $_GET["token"];
    $password = htmlspecialchars($_POST["password"]);
    $repeat_password = htmlspecialchars($_POST["repeat_password"]);

    if($password === $repeat_password) {
        if(Users::checkUserDataExists($connection, $token, "verify_token")) {
            if(Users::updateForgottenPassword($connection, $token, $password)) {        
                $_SESSION["status"] = "Password has been changed";
                Url::redirectUrl("/english-phrases-php/index.php");
            } else {
                $_SESSION["status"] = "Something went wrong.";
            }
        } else {
            $_SESSION["status"] = "Email address is not registered";
        }
    } else {
        $_SESSION["status"] = "Passwords are not correct!";
    }  
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/new-password.css">
    <title>New Password</title>
</head>

<body>
    <form method="POST" class="new-password">
        <h1>New Password</h1>
        <input type="password" placeholder="Password" name="password" required>
        <input type="password" placeholder="Repeat Password" name="repeat_password" required>
        <button>New Password</button>

        <?php if(isset($_SESSION['status'])) : ?>
            <div class="alert">
                <p><?= $_SESSION['status']; ?></p>
            </div>
            <?php unset($_SESSION['status']); ?>
        <?php endif; ?>
    </form>

    
</body>
</html>
