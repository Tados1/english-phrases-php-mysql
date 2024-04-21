<?php

require "../classes/Database.php";
require "../classes/Url.php";
require "../classes/Phrases.php";
require "../classes/Auth.php";
require "../classes/Friendship.php";
require "../classes/Duels.php";

session_start();

if (!Auth::isLoggedIn() ) {
    die("Unauthorized access");
}

$connection = Database::databaseConnection();

if ( isset($_GET["id"]) ){
    $phrase = Phrases::getOnePhrase($connection, $_GET["id"]);

    if ($phrase) {
        $slovak = $phrase["slovak"];
        $english = $phrase["english"];
        $id = $phrase["id_phrase"];

    } else {
        die("Phrase not found");
    }

} else {
    die("ID not entered, phrase not found");
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $slovak = $_POST["slovak"];
    $english = $_POST["english"];

    if(Phrases::edit($connection, $slovak, $english, $id)) {
        Url::redirectUrl("/english-phrases-php/pages/phrases.php");
    } else {
        return false;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/edit-phrase.css">
    <title>Edit Phrase</title>
</head>
<body>
    <?php require "../assets/header.php"; ?>
        
    <form method="POST" class="edit-phrase">
        <h1>EDIT PHRASE</h1>

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

        <button>Edit</button>
    </form>
    
</body>
</html>