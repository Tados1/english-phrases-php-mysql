<?php 

require "../classes/Url.php";
require "../classes/Auth.php";

session_start();

if (!Auth::isLoggedIn() ) {
    die("Unauthorized access");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $_SESSION["counter_to_10"] = 0;
    $_SESSION["correct_to_10"] = 0;
    $_SESSION["incorrect_to_10"] = 0;

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
        <div class="start-button" onclick="return delayRedirect(this)">
            <div class="hover bt-1"></div>
            <div class="hover bt-2"></div>
            <div class="hover bt-3"></div>
            <div class="hover bt-4"></div>
            <div class="hover bt-5"></div>
            <div class="hover bt-6"></div>
            <button></button>
        </div>
    </form>

    <script>
        function delayRedirect(element) {
            element.classList.add("clicked");

            var hoverElements = document.querySelectorAll(".hover");
            hoverElements.forEach(function(element) {
                element.classList.add("clicked");
            });

            setTimeout(function() {
                document.getElementById("redirectForm").submit(); 
            }, 1500);

            return false;
        }
    </script>
</body>
</html>