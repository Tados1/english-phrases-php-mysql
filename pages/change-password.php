<?php 

require "../classes/Database.php";
require "../classes/Users.php";
require "../classes/Auth.php";
require "../classes/Friendship.php";
require "../classes/Duels.php";
require "../classes/Url.php";

session_start();

if (!Auth::isLoggedIn() ) {
    Url::redirectUrl("/english-phrases-php/index.php");
    die();
}

$id_user = $_SESSION["logged_in_user_id"];
$error = null;
$success = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $old_password = htmlspecialchars($_POST["old-password"]);
    $new_password = htmlspecialchars($_POST["new-password"]);
    $repeat_new_password = htmlspecialchars($_POST["repeat-new-password"]);

    $connection = Database::databaseConnection();
    
    if (Users::passwordCheck($connection, $id_user, $old_password)) {
        if ($new_password === $repeat_new_password) {
            if(Users::updatePassword($connection, $id_user, $new_password)) {
                $success = "Your password has been updated";
            } else {
                $error = "Something went wrong";
            }
        } else {
            $error = "Your passwords do not match";
        }
    } else {
        $error = "You entered an incorrect password.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/change-password.css">
    <title>Change Password</title>
</head>
<body>
    <?php require "../assets/header.php"; ?>

    <form method="POST" class="change-password">
        <h1>Change Password</h1>
        <input type="password" placeholder="Old Password" name="old-password" required>
        <input type="password" placeholder="New Password" name="new-password" required>
        <input type="password" placeholder="Repeat New Password" name="repeat-new-password" required>
        <button>Update</button>

        <?php if($error) : ?>
            <div class="alert">
                <p><?= $error ?></p>
            </div>
        <?php elseif($success) : ?>
            <div class="updated">
                <p><?= $success ?></p>
            </div>
        <?php endif; ?>
    </form>
</body>
</html>