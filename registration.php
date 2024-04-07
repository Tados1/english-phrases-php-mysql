<?php 

session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/registration.css">
    <title>Sign Up</title>
</head>
<body>
    <div class="alert">
        <?php 
            if(isset($_SESSION['status'])) {
                echo "<h4>".$_SESSION['status']."</h4>";
                unset($_SESSION['status']);
            }
        ?>  
    </div>
    
    <form method="POST" class="registration" action="code.php">
        <h1>Sign Up</h1>
        <input type="email" placeholder="Email" name="email" required>
        <input type="text" placeholder="Name" name="name" required>
        <input type="password" placeholder="Password" name="password" required>
        <input type="password" placeholder="Repeat Password" name="repeat_password" required>
        <button>Sign Up</button>

        <div class="log-in">
            <p>Have an account?</p>
            <a href="index.php">Login here</a>
        </div>

        <?php if(isset($error)): ?>
            <div class="error">
                <p><?= $error; ?></p>
            </div>
        <?php endif; ?>
    </form>

</body>
</html>
