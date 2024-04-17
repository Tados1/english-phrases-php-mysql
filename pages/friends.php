<?php 
require "../classes/Database.php";
require "../classes/Users.php";
require "../classes/Friendship.php";
require "../classes/Auth.php";
require "../classes/Url.php";

session_start();

if (!Auth::isLoggedIn() ) {
    die("Unauthorized access");
}

$id_user = $_SESSION["logged_in_user_id"];
$error = null;
$success = null;

$connection = Database::databaseConnection();

$checkingRequests = Friendship::checkRequest($connection, $id_user, "requested");

$friends = [];
$friends_id = Friendship::getFriends($connection, $id_user);

foreach($friends_id as $friend) {
    $friend_info = Users::getUserInfoById($connection, $friend['friend_id'], "name");
    $friends[] = array(
        "friend_id" => $friend['friend_id'],
        "friend_info" => $friend_info
    );
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["friends-request"])) {
        Url::redirectUrl("/english-phrases-php/pages/friends-request.php?id=$id_user");
    }
    elseif (isset($_POST["send-request"])) { 
        $email = $_POST["email"];
        $ignore_email = Users::getUserInfoById($connection, $id_user, "email");

        if(Users::emailsAvailability($connection, $email)) {
            $friend_id = Users::getUserInfo($connection, $email, "id_user");
            $request_status = Friendship::checkUsers($connection, $id_user, $friend_id);
            if (!$request_status) {
                if ($email !== $ignore_email) {
                        $player_name = Users::getUserInfoById($connection, $id_user, "name");
                        Friendship::sendRequest($connection, $id_user, $friend_id, "requested");
                        $success = "Friendship request has been sent to your friend.";
                } else {
                        $error = "Oops! You're not allowed to add yourself.";
                }
            } elseif($request_status['status'] === "accepted") {
                $error = "You are already friends!";
            } else {
                $error = "You cannot send a request to the same user";
            }
        } else {
            $error = "No user with this email was found.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/bd1040f7a7.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/friends.css">
    <title>Friends</title>
</head>
<body>
    <?php require "../assets/header.php"; ?>
    <section class="friends-container">
        <form method="POST" class="incoming-request">
            <?php if($checkingRequests): ?>
                <p>New friend requests are waiting for you!</p>
                <button type="submit" name="friends-request" class="waiting-button">Friend's request<i class="fa-solid fa-bell"></i></button>
            <?php else: ?>
                <p>You don't have any new friend requests.</p>
                <button type="submit" name="friends-request" class="waiting-button">Friend's request</button>
            <?php endif; ?>
        </form>

        <form method="POST" class="send-request">
            <p>Elevate English with buddies! Learn, compete, enjoy the journey!</p>
            <input type="email" placeholder="Friend's Email" name="email" required>
            <button type="submit" name="send-request">Send Request<i class="fa-solid fa-user-plus"></i></button>
            
            <?php if($error) : ?>
                <div class="alert">
                    <p><?= $error ?></p>
                </div>
            <?php elseif($success) : ?>
                <div class="success">
                    <p><?= $success ?></p>
                </div>
            <?php endif; ?>
        </form>

        <?php if($friends): ?>
        <div class="friends">
            <h3>Buddy Club:</h3>
            <?php foreach($friends as $friend): ?>
                <a href="specific-friend.php?id=<?= $friend["friend_id"] ?>"><i class="fa-solid fa-circle-user"></i><?= $friend["friend_info"]?></a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <div class="friends">
                <p>Your buddy list is currently empty.</p>
            </div>
        <?php endif; ?>
    </section>
</body>
</html>