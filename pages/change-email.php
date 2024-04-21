<?php 
require "../classes/Database.php";
require "../classes/Users.php";
require "../classes/Auth.php";
require "../classes/Friendship.php";
require "../classes/Duels.php";

session_start();

if (!Auth::isLoggedIn() ) {
    die("Unauthorized access");
}

$id_user = $_SESSION["logged_in_user_id"];
$error = null;
$success = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password = htmlspecialchars($_POST["password"]);
    $email = htmlspecialchars($_POST["email"]);

    $connection = Database::databaseConnection();
    
    if (!Users::emailsAvailability($connection, $email)) {
        if (Users::passwordCheck($connection, $id_user, $password)) {
            if(Users::updateEmail($connection, $email, $id_user)) {
                $success = "Your email has been updated";
            } else {
                $error = "Something went wrong";
            }
        } else {
            $error = "The entered data is wrong.";
        }
    } else {
        $error = "The email is already taken";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/change-email.css">
    <title>Change Email</title>
</head>
<body>
    <?php require "../assets/header.php"; ?>

    <form method="POST" class="change-email">
        <h1>Change Email</h1>
        <input type="email" placeholder="New Email" name="email" required>
        <input type="password" placeholder="Verify your password" name="password" required>
        <button>Change Email</button>

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