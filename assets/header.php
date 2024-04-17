<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/bd1040f7a7.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/header.css">
    <title>Header</title>
</head>
<body>
    <header class="navbar">
        <div class="heading-icon">
            <i class="fa-regular fa-face-laugh-wink"></i>
            <h1>TIME TO GUESS!</h1>
        </div>

        <div class="burger" onclick="toggleFunction()">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <nav id="navContainer">
            <ul>
                <li><a href="start.php">Game</a></li>
                <li><a href="phrases.php">All Phrases</a></li>
                <li><a href="add-phrases.php">Add Phrase</a></li>
                <li><a href="statistics.php">Statistics</a></li>
                <li><a href="friends.php">Friends</a></li>
                <li><a class="logout" href="../log-out.php">Log Out</a></li>
                <li><a href="user-settings.php"><i class="fa-solid fa-gear"></i></a></li>
            </ul>
        </nav>
    </header>

    <script>
        function toggleFunction() {
            var burger = document.querySelector('.burger');
            burger.classList.toggle('open');
            
            var navContainer = document.getElementById("navContainer");
            navContainer.classList.toggle("open");
        }
    </script>
</body>
</html>