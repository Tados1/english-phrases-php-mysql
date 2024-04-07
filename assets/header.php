<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/header.css">
    <title>Header</title>
</head>
<body>
    <header class="navbar">
        <h1>GUESS THE CORRECT PHRASE</h1>

        <div class="burger" onclick="toggleFunction()">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <nav id="navContainer">
            <ul>
                <li><a href="start.php">Home</a></li>
                <li><a href="phrases.php">All Phrases</a></li>
                <li><a href="add-phrases.php">Add Phrase</a></li>
                <li><a class="logout" href="../log-out.php">Log Out</a></li>
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