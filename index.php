<?php

require "classes/Database.php";
require "classes/Url.php";
require "classes/Users.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $connection = Database::databaseConnection();
    $email = htmlspecialchars($_POST["email"]);
    $password = htmlspecialchars($_POST["password"]);

    $login_result = Users::authentication($connection, $email, $password);
    
    if (is_bool($login_result)) {
        if ($login_result) {
            $id = Users::getUserInfo($connection, $email, "id_user");

            session_regenerate_id(true);

            $_SESSION["is_logged_in"] = true;
            $_SESSION["logged_in_user_id"] = $id;

            Url::redirectUrl("/english-phrases-php/pages/start.php");
        } else {
            $_SESSION["status"] = "Incorrect login name or password";
        }
    } else {
        $_SESSION["status"] = $login_result;
        Url::redirectUrl("/english-phrases-php/index.php");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/log-in.css">
    <title>Log In</title>
</head>
<body>
    <form method="POST" class="log-in">
        <h1>Log In</h1>
        <input type="email" placeholder="Email" name="email" required>
        <input type="password" placeholder="Password" name="password" required>
        <button>Log In</button>
        
        <div class="password-reset">
            <a href="password-reset.php">Forgot your password?</a>
        </div>

        <div class="sign-up">
            <p>Don't have an account?</p>
            <a href="sign-up.php">Sign Up</a>
        </div>

        <?php if(isset($_SESSION["status"])) : ?>
            <div class="alert">
                <p><?= $_SESSION["status"]; ?></p>
            </div>
            <?php unset($_SESSION["status"]); ?>
        <?php endif; ?>
    </form>
</body>
</html>