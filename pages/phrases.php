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
$phrases = Phrases::get($connection, $id_user);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    Phrases::delete($connection, $_POST["delete_id"]);
    header("Location: {$_SERVER['PHP_SELF']}");
    exit();
}

$filtered_phrases = [];
foreach ($phrases as $phrase) {
    $filtered_phrases[] = $phrase;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/bd1040f7a7.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/phrases.css">
    <title>Phrases</title>
</head>
<body>
    <?php require "../assets/header.php"; ?>

    <div class="all-phrases-container">

        <?php if(empty($phrases)): ?>
            <div class="no-phrases">
                <p>There are no phrases...</p>
            </div>
        <?php else: ?>
            <form class="search-form">
                <input class="search_input" type="text" placeholder="Search Phrase">
            </form>

            <div class="all-phrases">
                <?php foreach($filtered_phrases as $phrase): ?>
                    <div class="one-phrase">
                        <div class="phrases">
                            <p><?= htmlspecialchars($phrase["slovak"]) ?></p>
                            <p><?= htmlspecialchars($phrase["english"]) ?></p>
                        </div>
                        <div class="btns">
                            <form method="post">
                                <input type="hidden" name="delete_id" value="<?= $phrase["id_phrase"] ?>">
                                
                                <button type="submit" class="delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>

                            <button class="edit">
                                <a href="edit-phrase.php?id=<?= $phrase["id_phrase"] ?>">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </button>
                        </div>
                    </div>            
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="../js/filter.js"></script>
</body>
</html>