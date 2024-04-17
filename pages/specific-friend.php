<?php 
require "../classes/Database.php";
require "../classes/Users.php";
require "../classes/Friendship.php";
require "../classes/Duels.php";
require "../classes/Phrases.php";
require "../classes/Auth.php";
require "../classes/Url.php";

session_start();

if (!Auth::isLoggedIn() ) {
    die("Unauthorized access");
}

$id_user = $_SESSION["logged_in_user_id"];
$friend_id = isset($_GET["id"]) ? $_GET["id"] : null;

$connection = Database::databaseConnection();
$user_name = Users::getUserInfoById($connection, $friend_id, "name");

//for duel
$check_exist = Duels::checkExistDuel($connection, $friend_id, $id_user);
$check_user_phrases_exist = Phrases::get($connection, $id_user); 
$check_friend_phrases_exist = Phrases::get($connection, $friend_id); 
$first_player_check = Duels::getInfo($connection, $friend_id, $id_user, "first_player_check");
$second_player_check = Duels::getInfo($connection, $friend_id, $id_user, "second_player_check");
$check_sender = Duels::getInfo($connection, $friend_id, $id_user, "sender");
$check_receiver = Duels::getInfo($connection, $friend_id, $id_user, "receiver");



if(isset($_COOKIE["user_$friend_id"])) {
    $all_answers = $_COOKIE["user_$friend_id"];
} else {
    $all_answers = 0;
}

if(isset($_COOKIE["right_answers_$friend_id"])) {
    $right_answers = $_COOKIE["right_answers_$friend_id"];
    $accuracy_percentage = round(($right_answers / $all_answers) * 100);
} else {
    $right_answers = 0;
    $accuracy_percentage = 0;
}

if(isset($_COOKIE["wrong_answer_$friend_id"])) {
    $wrong_answers = $_COOKIE["wrong_answer_$friend_id"];
} else {
    $wrong_answers = 0;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["play"])) {
         //for scoring up to 10
        $_SESSION["counter_to_10"] = 0;
        $_SESSION["correct_to_10"] = 0;
        $_SESSION["incorrect_to_10"] = 0;

        Url::redirectUrl("/english-phrases-php/pages/friend-phrases.php?id=$friend_id");
    } elseif (isset($_POST["play-with-friend"])) {
        //for scoring up to 10
        $_SESSION["counter_to_10"] = 0;
        $_SESSION["correct_to_10"] = 0;
        $_SESSION["incorrect_to_10"] = 0;

        if(!$check_exist) {
            Duels::createDuel($connection, $friend_id, $id_user, $id_user);
            Url::redirectUrl("/english-phrases-php/pages/friends-duel.php?id=$friend_id");
        } 

        if(!$second_player_check["second_player_check"]) {
            Url::redirectUrl("/english-phrases-php/pages/friends-duel.php?id=$friend_id");
        }
        
    } elseif (isset($_POST["delete-friend"])) {
        Friendship::declineRequest($connection, $id_user, $friend_id);
        if($check_exist) {
            Duels::deleteDuel($connection, $id_user, $friend_id);
        }
        Url::redirectUrl("/english-phrases-php/pages/friends.php");
    } elseif (isset($_POST["result"])) {
        if ($first_player_check["first_player_check"] === $id_user) {
            Duels::updateDuel($connection, "first_player_check", 1, $id_user, $friend_id);
            Url::redirectUrl("/english-phrases-php/pages/friends-duel-result.php?id=$friend_id");
        } elseif($second_player_check["second_player_check"] === $id_user) { 
            Duels::updateDuel($connection, "second_player_check", 1, $id_user, $friend_id);
            Url::redirectUrl("/english-phrases-php/pages/friends-duel-result.php?id=$friend_id");
        }
        Url::redirectUrl("/english-phrases-php/pages/friends.php");
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/bd1040f7a7.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/specific-friend.css">
    <title>Friend</title>
</head>
<body>
    <?php require "../assets/header.php"; ?>

    <section class="friend-statistics-container">
        <h1>Insight into the progress of <?= $user_name; ?></h1>

        <section class="friend-statistics">
            <div class="stats all-answers">
                <div class="icon-heading all-answers">
                    <i class="fa-regular fa-thumbs-up"></i>
                    <h3><?= $all_answers; ?></h3>
                </div>
                <p>Total Phrases Tried</p>
            </div>

            <div class="stats right-answers">
                <div class="icon-heading right-answers">
                    <i class="fa-solid fa-circle-check"></i>
                    <h3><?= $right_answers; ?></h3>
                </div>
                <p>Spot-On Guesses</p>
            </div>

            <div class="stats wrong-answers">
                <div class="icon-heading wrong_answers">
                    <i class="fa-solid fa-circle-xmark"></i>
                    <h3><?= $wrong_answers; ?></h3>
                </div>
                <p>Oopsie Moments</p>
            </div>

            <div class="stats wrong-answers">
                <div class="icon-heading percentage">
                    <i class="fa-solid fa-chart-line"></i>
                    <h3><?= $accuracy_percentage; ?>%</h3>
                </div>
                <p>Accuracy Percentage</p>
            </div>  
        </section>

        <form method="POST" class="play-friend-phrases">
            <p>Let's see if you can guess your friend's phrases!</p>
            <button type="submit" name="play">Play</button>
        </form>

        <div class="play-with-friend">
            <?php if(!$check_user_phrases_exist): ?>
                <p>Oopsie! Looks like your phrase bank is as empty as a ghost town! Time to fill it up with some wordy treasures!</p>

            <?php elseif(!$check_friend_phrases_exist): ?>
                <p>Hold up! Your friend's phrase vault seems to be on a vacation! Let's give them a friendly reminder to add some phrases!</p>

            <?php elseif(!$check_exist): ?>
                <form method="POST">
                    <p>Time for a phrase showdown! Guess 10 mutual phrases. On your mark, guess!</p>
                    <button type="submit" name="play-with-friend">Play</button>
                </form>

            <?php elseif(!$second_player_check["second_player_check"] && $check_receiver["receiver"] === $id_user): ?>
                <form method="POST">
                    <p>Your buddy has already taken you on in a duel, now it's your turn to show what you've got!</p>
                    <button type="submit" name="play-with-friend">Play</button>
                </form>

            <?php elseif($first_player_check["first_player_check"] === $id_user && !$second_player_check["second_player_check"]): ?>
                <p>Wait your turn! Your mate's still battling it out. Your chance will come!</p>

            <?php elseif(($first_player_check && $second_player_check) && ($first_player_check["first_player_check"] === $id_user) || ($second_player_check["second_player_check"] === $id_user)): ?>
                <form method="POST" class="result">
                    <p>Ding ding! Round's over! Ready to peek at the scorecards?</p>
                    <button type="submit" name="result">See Result</button>
                </form>

            <?php else: ?>
                <p>Hold on! Your friend will want to see how the game turned out too!</p>
            <?php endif; ?>
        </div>

        <form method="POST" class="delete-friend">
            <p>Ready to say goodbye to <?= $user_name; ?>? Hit Unfriend to make it official!</p>
            <button type="submit" name="delete-friend">Unfriend</button>
        </form>
    </section>
        
    
</body>
</html>