<?php 

require "../classes/Url.php";
require "../classes/Auth.php";

session_start();

if (!Auth::isLoggedIn() ) {
    die("Unauthorized access");
}

$id =  $_SESSION["logged_in_user_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    Url::redirectUrl("/english-phrases-php/pages/guess-phrase.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/start.css">
    <title>Start</title>
</head>
<body>
    <?php require "../assets/header.php"; ?>

    <form id="redirectForm" method="POST">
        <button class='start-button' onclick="return delayRedirect()">Start Guessing</button>
    </form>

    <script>
        function delayRedirect() {
            setTimeout(function() {
                document.getElementById('redirectForm').submit(); 
            }, 1000);
            return false;
        }
    </script>
</body>
</html>