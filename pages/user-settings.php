<?php 

require "../classes/Auth.php";
require "../classes/Database.php";
require "../classes/Friendship.php";
require "../classes/Duels.php";
require "../classes/Url.php";

session_start();

if (!Auth::isLoggedIn() ) {
    Url::redirectUrl("/english-phrases-php/index.php");
    die();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/user-settings.css">
    <title>Settings</title>
</head>
<body>
    <?php require "../assets/header.php"; ?>
    
    <section class="settings">
        <h1>Settings</h1>

        <div class="settings-container">
            <div class="options">
                <div class="option">
                    <p>Change Name</p>
                    <a href="change-name.php">></a>
                </div>

                <div class="option">
                    <p>Change Email</p>
                    <a href="change-email.php">></a>
                </div>

                <div class="option">
                    <p>Change Password</p>
                    <a href="change-password.php">></a>
                </div>

                <div class="option">
                    <p>Delete Profile</p>
                    <a href="delete-user.php">></a>
                </div>
            </div>
        </div>
    </section>
</body>
</html>