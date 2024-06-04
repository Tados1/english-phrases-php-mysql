<?php 
require "../classes/Database.php";
require "../classes/Users.php";
require "../classes/Friendship.php";
require "../classes/Phrases.php";
require "../classes/Auth.php";
require "../classes/Url.php";
require "../classes/Duels.php";

session_start();

if (!Auth::isLoggedIn() ) {
    Url::redirectUrl("/english-phrases-php/index.php");
    die();
}

$id_user = $_SESSION["logged_in_user_id"];
$error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password = htmlspecialchars($_POST["password"]);

    $connection = Database::databaseConnection();
    
    if (Users::passwordCheck($connection, $id_user, $password)) {
        if(Users::deleteUser($connection, $id_user)) {
            Friendship::deleteUser($connection, $id_user);
            Phrases::deleteAllPhrases($connection, $id_user);
            Duels::deleteDuelAfterDeletedUser($connection, $id_user);
            Duels::deletePhrasesAfterDeletedUser($connection, $id_user);
            $_SESSION = array();
            session_destroy();
            Url::redirectUrl("/english-phrases-php/index.php");
        } else {
            $error = "Something went wrong";
        }
    } else {
        $error = "The entered password is wrong.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/delete-user.css">
    <title>Delete User</title>
</head>
<body>
    <?php require "../assets/header.php"; ?>

    <form method="POST" class="delete-user">
        <h1>Delete User</h1>
        <div class="delete-alert">
            <p>After entering the password, the account will be permanently deleted!</p>
            <a href="user-settings.php">Go Back</a>
        </div>
        <input type="password" placeholder="Password" name="password" required>
        <button>Delete</button>

        <?php if($error): ?>
            <div class="alert">
                <p><?= $error ?></p>
            </div>
        <?php endif; ?>
    </form>
</body>
</html>