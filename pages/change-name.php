<?php

require "../classes/Database.php";
require "../classes/Users.php";
require "../classes/Auth.php";

session_start();

if (!Auth::isLoggedIn() ) {
    die("Unauthorized access");
}

$id_user = $_SESSION["logged_in_user_id"];
$error = null;
$success = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_name = htmlspecialchars($_POST["name"]);
    $password = htmlspecialchars($_POST["password"]);

    $connection = Database::databaseConnection();
    
    if (Users::passwordCheck($connection, $id_user, $password)) {
        if(Users::updateName($connection, $new_name, $id_user)) {
            $success = "Your name has been updated";
        } else {
            $error = "Something went wrong";
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
    <link rel="stylesheet" href="../css/change-name.css">
    <title>Change Name</title>
</head>
<body>
    <?php require "../assets/header.php"; ?>

    <form method="POST" class="change-name">
        <h1>Change Name</h1>
        <input type="text" placeholder="New Name" name="name" required>
        <input type="password" placeholder="Verify your password" name="password" required>
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