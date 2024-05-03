<?php 
require "../classes/Database.php";
require "../classes/Users.php";
require "../classes/Friendship.php";
require "../classes/Auth.php";
require "../classes/Url.php";
require "../classes/Duels.php";

session_start();

if (!Auth::isLoggedIn() ) {
    die("Unauthorized access");
}

$id_user = $_SESSION["logged_in_user_id"];

$connection = Database::databaseConnection();

$requests = Friendship::checkRequest($connection, $id_user, "requested");
$usersInfo = [];

foreach($requests as $request) {
    $user_name = Users::getUserInfoById($connection, $request["user_id"], "name");
    $usersInfo[] = [
        "id_user" => $request["user_id"],
        "user_name" => $user_name
    ];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["accept"])) {
        $friend_id = htmlspecialchars($_POST["friend_id"]);
        Friendship::acceptRequest($connection, $friend_id, $id_user);
        Url::redirectUrl("/english-phrases-php/pages/friends-request.php");
    } elseif (isset($_POST["decline"])) {
        $friend_id = htmlspecialchars($_POST["friend_id"]);
        Friendship::declineRequest($connection, $friend_id, $id_user);
        Url::redirectUrl("/english-phrases-php/pages/friends-request.php");
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/friends-request.css">
    <title>Friends Request</title>
</head>
<body>
    <?php require "../assets/header.php"; ?>

    <?php if(!$usersInfo): ?>
        <div class="no-friends-requests">
            <p>No friend requests on the horizon! You're good to <a href="friends.php">go</a>.</p>
        </div>
    <?php else: ?>
        <?php foreach($usersInfo as $userInfo) : ?>
        <form method="POST" class="friends-request">
            <p><span class="user-name"><?= $userInfo["user_name"] ?></span> wants to be your friend! Ready for awesome adventures? Hit 'Join' to get started!" </p>
            <input type="hidden" name="friend_id" value="<?= $userInfo["id_user"] ?>">
            <div class="btns">
                <button type="submit" name="accept">Join</button>
                <button type="submit" name="decline">Not now</button>
            </div>
        </form>
        <?php endforeach; ?>
    <?php endif; ?>

    
</body>
</html>