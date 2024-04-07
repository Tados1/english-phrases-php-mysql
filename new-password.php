<?php 

require "classes/Database.php";
require "classes/Users.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $connection = Database::databaseConnection();
    $token = $_GET["token"];
    $password = $_POST["password"];
    $repeat_password = $_POST["repeat_password"];

    if($password === $repeat_password) {
        if(Users::checkTokenExists($connection, $token)) {
            if(Users::updatePassword($connection, $token, $password)) {        
                $_SESSION["status"] = "Password has been changed";
            } else {
                $_SESSION["status"] = "Something went wrong.";
            }
    
        } else {
            $_SESSION["status"] = "Email address is not registered";
        }
    } else {
        $_SESSION["status"] = "Passwords are not correct!";
    }

    
   
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/registration.css">
    <title>New Password</title>
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
    
    <form method="POST" class="registration">
        <h1>New Password</h1>
        <input type="password" placeholder="Password" name="password" required>
        <input type="password" placeholder="Repeat Password" name="repeat_password" required>
        <button>New Password</button>
    </form>

</body>
</html>
