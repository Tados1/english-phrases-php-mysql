<?php 

    session_start();
    
    $role = $_SESSION["role"];
    $error_text = $_GET["error_text"];
    $page = $_GET["page"];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/error-page.css">
    <title>Error Page</title>
</head>
<body>

    <div class="blue-background"></div>

    <main>
        <section class="error">
            <p><?= $error_text ?></p>
            <a href="../<?= $role; ?>/<?= $page?>.php">Back to <?= $page?> page</a>
        </section>
    </main>

</body>
</html>