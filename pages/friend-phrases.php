<?php

require "../classes/Database.php";
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

if (!isset($_SESSION["random_phrase_$friend_id"])) {
    $_SESSION["random_phrase_$friend_id"] = Phrases::getRandomPhrase($connection, $friend_id);
}

//for cookies
$expiration_time = strtotime('2038-01-19');
$guess_counter = 0;
$right_answer = 0;
$wrong_answer = 0;

$user_word = null;
$correct_guess = false;
$incorrect_guess = false;
$friend_random_phrase = $_SESSION["random_phrase_$friend_id"];
$feedback_message = "";
$class = "phrase";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if(isset($_COOKIE["user_$id_user"])) {
        $guess_counter = $_COOKIE["user_$id_user"];
    } else {
        $guess_counter = 0;
    }
    
    $guess_counter++;
    setcookie("user_$id_user", $guess_counter, $expiration_time, "/");

    $_SESSION["counter_to_10"]++;

    ob_start(); 
    $user_word = htmlspecialchars($_POST["user_word"]);
    if(strtolower($user_word) === strtolower($friend_random_phrase["english"])) {
        if(isset($_COOKIE["right_answers_$id_user"])) {
            $right_answer = $_COOKIE["right_answers_$id_user"];
        } else {
            $right_answer = 0;
        }
        
        $right_answer++;
        setcookie("right_answers_$id_user", $right_answer, $expiration_time, "/");

        $_SESSION["correct_to_10"]++;

        $correct_guess = true;
        $feedback_message = "You guessed it!"; 
        $class = "right-answer";
        $refresh_time = 0.5;
    } else {
        if(isset($_COOKIE["wrong_answer_$id_user"])) {
            $wrong_answer = $_COOKIE["wrong_answer_$id_user"];
        } else {
            $wrong_answer = 0;
        }

        $wrong_answer++;
        setcookie("wrong_answer_$id_user", $wrong_answer, $expiration_time, "/");

        $_SESSION["incorrect_to_10"]++;

        $incorrect_guess = true;
        $feedback_message = "You didn't guess!";
        $old_guessing_phrase = $friend_random_phrase["english"];
        $class = "wrong-answer";
        $refresh_time = 1.5;
    }

    if($_SESSION["counter_to_10"] === 10) {
        Url::redirectUrl("/english-phrases-php/pages/guess-result.php?id=$friend_id");
    }

    ob_end_flush(); 
    
    header("Refresh: $refresh_time");
    $_SESSION["random_phrase_$friend_id"] = Phrases::getRandomPhrase($connection, $friend_id);
    $friend_random_phrase = $_SESSION["random_phrase_$friend_id"];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/guess-phrase.css">
    <title>Guess Friend's Phrases</title>
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