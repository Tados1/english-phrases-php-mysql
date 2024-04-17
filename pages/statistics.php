<?php 

require "../classes/Database.php";
require "../classes/Users.php";
require "../classes/Url.php";
require "../classes/Auth.php";

session_start();

if (!Auth::isLoggedIn() ) {
    die("Unauthorized access");
}

$id_user = $_SESSION["logged_in_user_id"];

$connection = Database::databaseConnection();
$user_name = Users::getUserInfoById($connection, $id_user, "name");

$message = "Play and see where you stand!";

if(isset($_COOKIE["user_$id_user"])) {
    $all_answers = $_COOKIE["user_$id_user"];
} else {
    $all_answers = 0;
}

if(isset($_COOKIE["right_answers_$id_user"])) {
    $right_answers = $_COOKIE["right_answers_$id_user"];
    $accuracy_percentage = round(($right_answers / $all_answers) * 100);

    switch (true) {
        case $accuracy_percentage >= 85:
            $message = "Congratulations! You're a Master of Words!";
            break;
        case $accuracy_percentage >= 65:
            $message = "You're on the right track! Keep it up!";
            break;
        case $accuracy_percentage >= 45:
            $message = "Not bad! You're doing well!";
            break;
        case $accuracy_percentage >= 20:
            $message = "Caution! You're On Thin Ice!";
            break;
        default:
            $message = "Oops! Looks like you're Lost in Translation!";
    }
} else {
    $right_answers = 0;
    $accuracy_percentage = 0;
}

if(isset($_COOKIE["wrong_answer_$id_user"])) {
    $wrong_answers = $_COOKIE["wrong_answer_$id_user"];
} else {
    $wrong_answers = 0;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/bd1040f7a7.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/statistics.css">
    <title>Statistics</title>
</head>
<body>
    <?php require "../assets/header.php"; ?>

    <section class="statistics-container">
        <h1>Hey <?= $user_name; ?>, let's dive into your performance stats:</h1>

        <section class="statistics">
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
            <div class="message">
                <h3><?= $message; ?></h3>
            </div>
    </section>

    
</body>
</html>