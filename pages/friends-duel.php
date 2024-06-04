<?php

require "../classes/Database.php";
require "../classes/Phrases.php";
require "../classes/Friendship.php";
require "../classes/Duels.php";
require "../classes/Users.php";
require "../classes/Auth.php";
require "../classes/Url.php";

session_start();

if (!Auth::isLoggedIn() ) {
    Url::redirectUrl("/english-phrases-php/index.php");
    die();
}

$id_user = $_SESSION["logged_in_user_id"];
$friend_id = $_SESSION["friend_id"];

$connection = Database::databaseConnection();

if (!isset($_SESSION["random_phrase_$friend_id"])) {
    $_SESSION["random_phrase_$friend_id"] = Phrases::getRandomFriendsPhrase($connection, $id_user, $friend_id);
}

$phrases_counter = Users::getUserInfoById($connection, $id_user, "phrases_counter");
$right_answers = Users::getUserInfoById($connection, $id_user, "right_answers");
$wrong_answers = Users::getUserInfoById($connection, $id_user, "wrong_answers");

if($phrases_counter) {
    $counter_all_answers = $phrases_counter;
} else {
    $counter_all_answers = 0;
}

if($right_answers) {
    $counter_right_answers = $right_answers;
} else {
    $counter_right_answers = 0;
}

if($wrong_answers) {
    $counter_wrong_answers = $wrong_answers;
} else {
    $counter_wrong_answers = 0;
}

//duel 
$check_exist = Duels::checkExistDuel($connection, $friend_id, $id_user);
$check_sender = Duels::getInfo($connection, $friend_id, $id_user, "sender");
$check_receiver = Duels::getInfo($connection, $friend_id, $id_user, "receiver");

$check_sender["sender"];

$user_word = null;
$correct_guess = false;
$incorrect_guess = false;
$friend_random_phrase = $_SESSION["random_phrase_$friend_id"];
$feedback_message = "";
$class = "phrase";

if($_SESSION["counter_to_10"] === 10) {
    $_SESSION["correct_to_10"] = 0;
    if($check_receiver["receiver"] === $id_user) {
        Duels::updateDuel($connection, "second_player_check", $id_user, $id_user, $friend_id);
    } 
        Url::redirectUrl("/english-phrases-php/pages/specific-friend.php?id=$friend_id");
}
elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    $counter_all_answers++;

    Users::updateScore($connection, $id_user, "phrases_counter", $counter_all_answers);

    $_SESSION["counter_to_10"]++;

    ob_start(); 
    $user_word = htmlspecialchars($_POST["user_word"]);
    if (strtolower(preg_replace('/[^a-z0-9]+/i', '', $user_word)) === strtolower(preg_replace('/[^a-z0-9]+/i', '', $friend_random_phrase["english"]))) {
        $counter_right_answers++;
        Users::updateScore($connection, $id_user, "right_answers", $counter_right_answers);

        $_SESSION["correct_to_10"]++;

        if($check_sender["sender"] === $id_user) {
            Duels::updateScore($connection, "sender_right", $_SESSION["correct_to_10"], $friend_id, $id_user);
            Duels::recordPhrases($connection, $friend_random_phrase["english"], null, $id_user, $friend_id, $id_user);
        } else {
            Duels::updateScore($connection, "receiver_right", $_SESSION["correct_to_10"], $id_user, $friend_id);
            Duels::recordPhrases($connection, $friend_random_phrase["english"], null, $friend_id, $id_user, $id_user);
        }

        $correct_guess = true;
        $feedback_message = "You guessed it!"; 
        $class = "right-answer";
        $refresh_time = 1;
    } else {
        $counter_wrong_answers++;
        Users::updateScore($connection, $id_user, "wrong_answers", $counter_wrong_answers);

        $_SESSION["incorrect_to_10"]++;

        if($check_sender["sender"] === $id_user) {
            Duels::updateScore($connection, "sender_wrong", $_SESSION["incorrect_to_10"], $friend_id, $id_user);
            Duels::recordPhrases($connection, null, $friend_random_phrase["english"], $id_user, $friend_id, $id_user);
        } else {
            Duels::updateScore($connection, "receiver_wrong", $_SESSION["incorrect_to_10"], $id_user, $friend_id);
            Duels::recordPhrases($connection, null, $friend_random_phrase["english"], $friend_id, $id_user, $id_user);
        }

        $incorrect_guess = true;
        $feedback_message = "You didn't guess!";
        $old_guessing_phrase = $friend_random_phrase["english"];
        $class = "wrong-answer";
        $refresh_time = 1.5;
    }

    ob_end_flush(); 
    
    header("Refresh: $refresh_time");
    $_SESSION["random_phrase_$friend_id"] = Phrases::getRandomFriendsPhrase($connection, $id_user, $friend_id);
    $friend_random_phrase = $_SESSION["random_phrase_$friend_id"];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/guess-phrase.css">
    <title>Friends Duel</title>
</head>
<body>
    <?php require "../assets/header.php"; ?>

    <div class="content">
        <?php if(!$friend_random_phrase): ?>
            <div class="no-phrases">
                <p>There are no phrases...</p>
            </div>
        <?php else: ?>
            <div class="guess-word"> 
                <div class="counter">
                    <h1> <?= $_SESSION["counter_to_10"];?> /10</h1>
                </div>
            <div class="<?= $class; ?> additional-class">
                    <?php if ($correct_guess): ?>
                        <h1>Great job, you got it!</h1>
                    <?php elseif($incorrect_guess): ?> 
                        <h1>Oops! Not quite, it was:</h1>
                        <h3><?= $old_guessing_phrase; ?></h3>
                    <?php else: ?>
                        <h1><?= $friend_random_phrase["slovak"]; ?></h1>
                    <?php endif; ?>
                </div>

                <form method="POST" id="guessForm">
                    <input type="text" name="user_word" placeholder="Guess the phrase" value="<?= $user_word ?>" />
                    <button type="submit">SUBMIT</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.querySelector('input[type="text"]').focus();
    </script>
</body>
</html>