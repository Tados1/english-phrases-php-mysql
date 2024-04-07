<?php

class Users {
    public static function createUser($connection, $email, $name, $password, $verify_token) {

        $sql = "INSERT INTO users (email, name, password, verify_token) 
        VALUES (:email, :name, :password, :verify_token)";

        $stmt = $connection->prepare($sql);

        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->bindValue(":name", $name, PDO::PARAM_STR);
        $stmt->bindValue(":password", password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
        $stmt->bindValue(":verify_token", $verify_token, PDO::PARAM_STR);

        try {
            if($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Failed to create user");
            }
        } catch (Exception $e) {
            error_log("Error with function createUser\n", 3, "../errors/error.log");
            echo "Error Type: " . $e->getMessage();
        }
    }

    public static function authentication($connection, $email, $login_password) {
        $sql = "SELECT password, verify_status
                FROM users
                WHERE email = :email";
    
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
    
        try {
            if ($stmt->execute()) {
                $user = $stmt->fetch();
                if ($user) {
                    if ($user['verify_status'] == '1') {
                        return password_verify($login_password, $user['password']);
                    } else {
                        return "Your account needs to be verified. Please check your email.";
                    }
                } else {
                    return false; 
                }
            } else {
                throw new Exception("User login error");
            }
        } catch (Exception $e) {
            error_log("Error with function authentication\n", 3, "../errors/error.log");
            echo "Error type: " . $e->getMessage();
        }
    }

    public static function getUserInfo($connection, $email, $info) {
        $sql = "SELECT $info FROM users
                WHERE email = :email";

        $stmt = $connection->prepare($sql);

        $stmt->bindValue(":email", $email, PDO::PARAM_STR);

        try {
            if ($stmt->execute()) {
                if ($stmt->execute()) {
                    $result = $stmt->fetch();
                    $user_info = $result[0];
                    return $user_info;
                }
            } else {
                throw new Exception("Error getting user $info");
            }
        } catch (Exception $e) {
            error_log("Error with function getUser$info\n", 3, "../errors/error.log");
            echo "Error type: " . $e->getMessage();
        }       
    }

    public static function checkEmailExists($connection, $email) {
        $sql = "SELECT email FROM users WHERE email = :email LIMIT 1";

        $stmt = $connection->prepare($sql);

        $stmt->execute(array(':email' => $email));

        return $stmt->rowCount() > 0;
    }

    public static function checkTokenExists($connection, $token) {
        $sql = "SELECT * FROM users WHERE verify_token = :token LIMIT 1";

        $stmt = $connection->prepare($sql);

        $stmt->execute(array(':token' => $token));

        return $stmt->rowCount() > 0;
    }

    public static function verifyToken($connection, $token) {
        $sql = "SELECT verify_token, verify_status FROM users WHERE verify_token=:token LIMIT 1";
        $stmt = $connection->prepare($sql);
        $stmt->execute(array(':token' => $token));
    
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC); 
        } else {
            return null; 
        }
    }

    public static function updateVerifyStatus($connection, $token) { 
        $sql = "UPDATE users SET verify_status='1' WHERE verify_token = :verify_token LIMIT 1";

        $stmt = $connection->prepare($sql);
        $stmt->execute(array(':verify_token' => $token));

        return $stmt;
    }

    public static function updateToken($connection, $verify_token, $email) { 
        $sql = "UPDATE users SET verify_token = :verify_token WHERE email = :email LIMIT 1";

        $stmt = $connection->prepare($sql);
        $stmt->bindValue(':verify_token', $verify_token, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt;
    }

    public static function updatePassword($connection, $verify_token, $password) { 
        $sql = "UPDATE users SET password = :password WHERE verify_token= :verify_token LIMIT 1";

        $stmt = $connection->prepare($sql);
        $stmt->bindValue(':verify_token', $verify_token, PDO::PARAM_STR);
        $stmt->bindValue(":password", password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
        $stmt->execute();
        
        return true;
    }
}