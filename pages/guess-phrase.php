<?php

require "../classes/Database.php";
require "../classes/Phrases.php";
require "../classes/Auth.php";

session_start();

if (!Auth::isLoggedIn() ) {
    die("Unauthorized access");
}

$id_user = $_SESSION["logged_in_user_id"];

$connection = Database::databaseConnection();

if (!isset($_SESSION["random_phrase"])) {
    $_SESSION["random_phrase"] = Phrases::getRandomPhrase($connection, $id_user);
}

$user_word = null;
$correct_guess = false;
$incorrect_guess = false;
$random_phrase = $_SESSION["random_phrase"];
$feedback_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    ob_start(); 
    $user_word = $_POST["user_word"];
    if(strtolower($user_word) === strtolower($random_phrase["english"])) {
        $correct_guess = true;
        $feedback_message = "You guessed it!";
    } else {
        $incorrect_guess = true;
        $feedback_message = "You didn't guess!";
        $old_guessing_phrase = $random_phrase["english"];
    }
    ob_end_flush(); 
    header("Refresh:1.5");
    
    $_SESSION["random_phrase"] = Phrases::getRandomPhrase($connection, $id_user);
    $random_phrase = $_SESSION["random_phrase"];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/guess-phrase.css">
    <title>Start</title>
</head>
<body>
    <?php require "../assets/header.php"; ?>

    <div class="content">
        <?php if(!$random_phrase): ?>
            <div class="no-phrases">
                <p>There are no phrases...</p>
            </div>
        <?php else: ?>
            <div class="guess-word">
                <div class="slovak-guess-word">
                    <p><?= $random_phrase["slovak"]; ?></p>
                </div>

                <form method="POST" id="guessForm">
                    <input type="text" name="user_word" placeholder="Guess the phrase" value="<?= $user_word ?>" />
                    <button type="submit">SUBMIT</button>

                    <?php if($correct_guess || $incorrect_guess): ?>
                        <div class="<?php echo $correct_guess ? "right-answer" : "wrong-answer"; ?>">
                            <h1><?php echo $feedback_message; ?></h1>
                            <?php if ($incorrect_guess): ?>
                                <p><?php echo $old_guessing_phrase; ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </form>

            </div>
        <?php endif; ?>
    </div>

</body>
</html>

