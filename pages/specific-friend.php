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
    Url::redirectUrl("/english-phrases-php/index.php");
    die();
}

$id_user = $_SESSION["logged_in_user_id"];
$friend_id = isset($_GET["id"]) ? $_GET["id"] : null;
$_SESSION["friend_id"] = $friend_id;

$connection = Database::databaseConnection();

$friends_list_id = Friendship::getFriends($connection, $id_user);
$found = false;

foreach($friends_list_id as $friend) {
    if($friend["friend_id"] == $friend_id) {
        $found = true;
    }
}


$user_name = Users::getUserInfoById($connection, $friend_id, "name");
$friend_phrases_counter = Users::getUserInfoById($connection, $friend_id, "phrases_counter");
$friend_right_answers = Users::getUserInfoById($connection, $friend_id, "right_answers");
$friend_wrong_answers = Users::getUserInfoById($connection, $friend_id, "wrong_answers");
$my_phrases_counter = Phrases::countingPhrases($connection, $friend_id);
$hidden_phrases_counter = Phrases::countingHiddenPhrases($connection, $friend_id);

if($friend_right_answers) {
    $accuracy_percentage = round(($friend_right_answers / $friend_phrases_counter) * 100);
} else {
    $friend_right_answers = 0;
    $accuracy_percentage = 0;
}

//for duel
$check_exist = Duels::checkExistDuel($connection, $friend_id, $id_user);
$check_user_phrases_exist = Phrases::get($connection, $id_user); 
$check_friend_phrases_exist = Phrases::get($connection, $friend_id); 
$first_player_check = Duels::getInfo($connection, $friend_id, $id_user, "first_player_check");
$second_player_check = Duels::getInfo($connection, $friend_id, $id_user, "second_player_check");
$check_sender = Duels::getInfo($connection, $friend_id, $id_user, "sender");
$check_receiver = Duels::getInfo($connection, $friend_id, $id_user, "receiver");


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["play"])) {
         //for scoring up to 10
        $_SESSION["counter_to_10"] = 0;
        $_SESSION["correct_to_10"] = 0;
        $_SESSION["incorrect_to_10"] = 0;

        Url::redirectUrl("/english-phrases-php/pages/friend-phrases.php");
    } elseif (isset($_POST["play-with-friend"])) {
        //for scoring up to 10
        $_SESSION["counter_to_10"] = 0;
        $_SESSION["correct_to_10"] = 0;
        $_SESSION["incorrect_to_10"] = 0;

        if(!$check_exist) {
            Duels::createDuel($connection, $friend_id, $id_user, $id_user);
            Url::redirectUrl("/english-phrases-php/pages/friends-duel.php");
        } 

        if(!$second_player_check["second_player_check"]) {
            Url::redirectUrl("/english-phrases-php/pages/friends-duel.php");
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
            Url::redirectUrl("/english-phrases-php/pages/friends-duel-result.php");
        } elseif($second_player_check["second_player_check"] === $id_user) { 
            Duels::updateDuel($connection, "second_player_check", 1, $id_user, $friend_id);
            Url::redirectUrl("/english-phrases-php/pages/friends-duel-result.php");
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

    <?php if($found): ?>
        <section class="friend-statistics-container">
        <h1>Insight into the progress of <?= $user_name; ?></h1>

        <section class="friend-statistics">
            <div class="stats all-answers">
                <div class="icon-heading all-answers">
                    <i class="fa-regular fa-thumbs-up"></i>
                    <h3><?= $friend_phrases_counter; ?></h3>
                </div>
                <p>Total Phrases Tried</p>
            </div>

            <div class="stats right-answers">
                <div class="icon-heading right-answers">
                    <i class="fa-solid fa-circle-check"></i>
                    <h3><?= $friend_right_answers; ?></h3>
                </div>
                <p>Spot-On Guesses</p>
            </div>

            <div class="stats wrong-answers">
                <div class="icon-heading wrong_answers">
                    <i class="fa-solid fa-circle-xmark"></i>
                    <h3><?= $friend_wrong_answers; ?></h3>
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

        <section class="phrases-counter">
            <div class="one-counter all-phrases">
                <h3> <span><?= $my_phrases_counter[0] ?></span> Phrases in Total</h3>
                <i class="fa-solid fa-pen"></i>
            </div>

            <div class="one-counter hidden-phrases">
                <h3> <span><?= $hidden_phrases_counter[0] ?></span> Hidden Phrases</h3>
                <i class="fa-solid fa-eye-slash"></i>
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
    <?php else: ?>
        <h1>ERROR</h1>
    <?php endif; ?>
</body>
</html>