<?php

require "classes/Database.php";
require "classes/Url.php";
require "classes/Users.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $connection = Database::databaseConnection();
    $email = $_POST["email"];
    $password = $_POST["password"];

    $login_result = Users::authentication($connection, $email, $password);
    
    if (is_bool($login_result)) {
        if ($login_result) {
            $id = Users::getUserInfo($connection, $email, "id_user");

            session_regenerate_id(true);

            $_SESSION["is_logged_in"] = true;
            $_SESSION["logged_in_user_id"] = $id;

            Url::redirectUrl("/english-phrases-php/pages/start.php");
        } else {
            $error = "Incorrect login name or password";
        }
    } else {
        $_SESSION['status'] = $login_result;
        header("Location: index.php");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/sign-in.css">
    <title>Log In</title>
</head>
<body>

    <?php
        if(isset($_SESSION['status'])) 
        {
            ?>
            <div class="alert alert-success">
                <h1> <?= $_SESSION['status']; ?></h1>
            </div>
            <?php
            unset($_SESSION['status']);
        }
    ?>
    <form method="POST" class="sign-in">
        <h1>Log In</h1>
        <input type="email" placeholder="Email" name="email" required>
        <input type="password" placeholder="Password" name="password" required>
        <button>Log In</button>
        
        <div class="password-reset">
            <a href="password-reset.php">Forgot your password?</a>
        </div>

        <div class="sign-up">
            <p>Don't have an account?</p>
            <a href="registration.php">Sign Up</a>
        </div>

        <?php if(isset($error)): ?>
            <div class="error">
                <p><?= $error; ?></p>
            </div>
        <?php endif; ?>
    </form>
</body>
</html>