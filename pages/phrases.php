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
$id_user = $_SESSION["logged_in_user_id"];

$connection = Database::databaseConnection();
$phrases = Phrases::get($connection, $id_user);

if(empty($phrases)) {
    unset($_SESSION["random_phrase"]);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if(isset($_POST['delete'])) {
        $id = htmlspecialchars($_POST["delete_id"]);
        Phrases::delete($connection, $id);
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }
    elseif(isset($_POST['hide'])) {
        $id = htmlspecialchars($_POST["id"]);
        Phrases::showToggle($connection, $id, 'hide');
        header("Location: {$_SERVER['PHP_SELF']}?scroll=phrase-$id");
        exit();
    }

    elseif(isset($_POST['show'])) {
        $id = htmlspecialchars($_POST["id"]);
        Phrases::showToggle($connection, $id, 'show');
        header("Location: {$_SERVER['PHP_SELF']}?scroll=phrase-$id");
        exit();
    }
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
                    <div class="one-phrase <?php echo $phrase['status'] === 'hide' ? 'hide' : ''; ?>" id="phrase-<?= $phrase["id_phrase"] ?>">
                        <div class="phrases">
                            <p><?= $phrase["slovak"] ?></p>
                            <p><?= $phrase["english"] ?></p>
                        </div>
                        <div class="btns">
                            <?php if($phrase["status"] === 'show'): ?>
                                <form method="post" action="?scroll=phrase-<?= $phrase["id_phrase"] ?>">
                                    <input type="hidden" name="id" value="<?= $phrase["id_phrase"] ?>">
                                    
                                    <button type="submit" class="hide" name="hide">
                                        <i class="fa-solid fa-eye-slash"></i>
                                    </button>
                                </form>
                            <?php elseif($phrase["status"] === 'hide'): ?>
                                <form method="post" action="?scroll=phrase-<?= $phrase["id_phrase"] ?>">
                                    <input type="hidden" name="id" value="<?= $phrase["id_phrase"] ?>">
                                    
                                    <button type="submit" class="show" name="show">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                            

                            <form method="post">
                                <input type="hidden" name="delete_id" value="<?= $phrase["id_phrase"] ?>">
                                
                                <button type="submit" class="delete" name="delete">
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
    <script src="../js/scroll-to-phrase.js"></script>
</body>
</html>