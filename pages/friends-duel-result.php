<?php

require "../classes/Auth.php";
require "../classes/Duels.php";
require "../classes/Users.php";
require "../classes/Database.php";

session_start();

if (!Auth::isLoggedIn() ) {
    die("Unauthorized access");
}

$id_user = $_SESSION["logged_in_user_id"];
$friend_id = isset($_GET["id"]) ? $_GET["id"] : null;

$connection = Database::databaseConnection();

$data = Duels::getAllDatas($connection, $id_user, $friend_id);

$receiver_name = Users::getUserInfoById($connection, $data[0]["receiver"], "name");
$receiver_right = $data[0]["receiver_right"];
$receiver_wrong = $data[0]["receiver_wrong"];
$receiver_check = $data[0]["first_player_check"];

$sender = $data[0]["sender"];
$sender_name = Users::getUserInfoById($connection, $data[0]["sender"], "name");
$sender_right = $data[0]["sender_right"];
$sender_wrong = $data[0]["sender_wrong"];
$sender_check = $data[0]["second_player_check"];

$deleteDuel = $receiver_check === 1 && $sender_check === 1 ? true : false;

if ($deleteDuel) {
    Duels::deleteDuel($connection, $id_user, $friend_id);
}

?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/bd1040f7a7.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/friends-duel-result.css">
    <title>Friend's Duel Result</title>
</head>
<body>
    <?php require "../assets/header.php"; ?>

    <section class="results">
        <section class="first-player player">
            <h1><?= $sender_name; ?></h1>
            <div class="right">
                <i class="fa-solid fa-circle-check"></i>
                <p><?= $sender_right !== null ? $sender_right : 0; ?></p>
            </div>
            <div class="wrong">
                <i class="fa-solid fa-circle-xmark"></i>
                <p><?= $sender_wrong !== null ? $sender_wrong : 0; ?></p>
            </div>
            <?php if($sender_right > $receiver_right): ?>
                <div class="main-icon win">  
                    <i class="fa-solid fa-award"></i>
                </div>
            <?php elseif($receiver_right > $sender_right): ?>
                <div class="main-icon loss">   
                    <i class="fa-solid fa-face-sad-tear"></i>
                </div>
            <?php else: ?>
                <div class="main-icon tie">   
                    <i class="fa-solid fa-scale-balanced"></i>
                </div>
            <?php endif; ?>
        </section>

        <section class="second-player player">
            <h1><?= $receiver_name; ?></h1>
            <div class="right">
                <i class="fa-solid fa-circle-check"></i>
                <p><?= $receiver_right !== null ? $receiver_right : 0; ?></p>
            </div>
            <div class="wrong">
                <i class="fa-solid fa-circle-xmark"></i>
                <p><?= $receiver_wrong !== null ? $receiver_wrong : 0; ?></p>
            </div>
            <?php if($receiver_right > $sender_right): ?>
                <div class="main-icon win">
                    <i class="fa-solid fa-award"></i>
                </div>
            <?php elseif($sender_right > $receiver_right): ?>
                <div class="main-icon loss">              
                    <i class="fa-solid fa-face-sad-tear"></i>
                </div>
            <?php else: ?>
                <div class="main-icon tie">  
                    <i class="fa-solid fa-scale-balanced"></i>
                </div>
            <?php endif; ?>
        </section>   
    </section>
</body>
</html>