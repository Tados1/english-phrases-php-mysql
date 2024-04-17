<?php

require "../classes/Auth.php";
require "../classes/Url.php";

session_start();

if (!Auth::isLoggedIn() ) {
    die("Unauthorized access");
}

$friend_id = isset($_GET["id"]) ? $_GET["id"] : null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $_SESSION["counter_to_10"] = 0;
    $_SESSION["correct_to_10"] = 0;
    $_SESSION["incorrect_to_10"] = 0;
    if($friend_id) {
        Url::redirectUrl("/english-phrases-php/pages/friend-phrases.php?id=$friend_id");
    } else {
        Url::redirectUrl("/english-phrases-php/pages/guess-phrase.php");
    }
}

?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/guess-result.css">
    <title>Result</title>
</head>
<body>
    <form method="POST" class="result">
        <p>You've managed to guess 10 phrases! You got <span class="right"><?= $_SESSION["correct_to_10"]; ?> right</span> and missed <span class="wrong"><?= $_SESSION["incorrect_to_10"]; ?> phrases</span>.</p>
     
        <div class="btns">
            <button class="button">
                <svg class="svg-icon" fill="none" height="20" viewBox="0 0 20 20" width="20" xmlns="http://www.w3.org/2000/svg"><g stroke="#131F24" stroke-linecap="round" stroke-width="3.5"><path d="m3.33337 10.8333c0 3.6819 2.98477 6.6667 6.66663 6.6667 3.682 0 6.6667-2.9848 6.6667-6.6667 0-3.68188-2.9847-6.66664-6.6667-6.66664-1.29938 0-2.51191.37174-3.5371 1.01468"></path><path d="m7.69867 1.58163-1.44987 3.28435c-.18587.42104.00478.91303.42582 1.0989l3.28438 1.44986"></path></g></svg>
                <span class="lable">Play Again</span>
            </button>

            <?php if($friend_id): ?>
                <a href="specific-friend.php?id=<?=$friend_id; ?>">Decline</a>
            <?php else: ?>
                <a href="start.php">Decline</a>
            <?php endif; ?>
        </div>
    </form>
</body>
</html>