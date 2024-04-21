<?php

require "../classes/Database.php";
require "../classes/Phrases.php";
require "../classes/Auth.php";
require "../classes/Friendship.php";
require "../classes/Duels.php";

session_start();

if (!Auth::isLoggedIn() ) {
    die("Unauthorized access");
}

$slovak = null;
$english = null;
$id_user = $_SESSION["logged_in_user_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $slovak = $_POST["slovak"];
    $english = $_POST["english"];

    $connection = Database::databaseConnection();
    
    Phrases::create($connection, $slovak, $english, $id_user);

    $slovak = "";
    $english = "";
}

?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/add-phrase.css">
    <title>Add Phrase</title>
</head>
<body>
    <?php require "../assets/header.php"; ?>

    <form method="POST" class="add-phrase">
        <h1>ADD NEW PHRASE</h1>

        <input  type="text" 
                name="slovak" 
                placeholder="Slovak" 
                value="<?= htmlspecialchars($slovak)  ?>"
                required
        >

        <input  type="text" 
                name="english" 
                placeholder="English"
                value="<?= htmlspecialchars($english) ?>" 
                required
        >

        <button>Submit</button>

    </form>
</body>
</html>