<?php

class Users {

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

    public static function deleteUser($connection, $id) {
        $sql = "DELETE FROM users WHERE id_user = :id";

        $stmt = $connection->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        try {
            if($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Failed to delete user");
            }
        } catch (Exception $e) {
            error_log("Error with function deleteUser\n", 3, "../errors/error.log");
            echo "Error Type: " . $e->getMessage();
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

    public static function getUserInfoById($connection, $id, $info) {
        $sql = "SELECT $info FROM users
                WHERE id_user = :id";

        $stmt = $connection->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_STR);

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
            error_log("Error with function getUserInfoById\n", 3, "../errors/error.log");
            echo "Error type: " . $e->getMessage();
        }       
    }

    public static function passwordCheck($connection, $id_user, $password) {
        $sql = "SELECT password
                FROM users
                WHERE id_user = :id_user";
    
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(":id_user", $id_user, PDO::PARAM_INT);
    
        try {
            if ($stmt->execute()) {
                $user = $stmt->fetch();
                if ($user) {
                    return password_verify($password, $user['password']);
                } else {
                    return false; 
                }
            } else {
                throw new Exception("Password check error");
            }
        } catch (Exception $e) {
            error_log("Error with function passwordCheck\n", 3, "../errors/error.log");
            echo "Error type: " . $e->getMessage();
        }
    }

    public static function checkUserDataExists($connection, $value, $column) {
        $sql = "SELECT * FROM users WHERE $column = :value";
    
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(":value", $value, PDO::PARAM_STR);
    
        try {
            if ($stmt->execute()) {
                return $stmt->rowCount() > 0;
            } else {
                throw new Exception("Error checking existence");
            }
        } catch (Exception $e) {
            error_log("Error with function checkUserDataExists\n", 3, "../errors/error.log");
            echo "Error type: " . $e->getMessage();
        }
    }

    public static function emailsAvailability($connection, $email) {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
    
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    
        try {
            if ($stmt->execute()) {
                $count = $stmt->fetchColumn();
                return $count > 0; 
            } else {
                throw new Exception("Failed to execute email check query");
            }
        } catch (Exception $e) {
            error_log("Error with function emailsAvailability\n", 3, "../errors/error.log");
            echo "Error type: " . $e->getMessage();
            return false; 
        }
    }
    
    public static function friendEmailAvailability($connection, $email, $ignore_email) {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email AND email != :ignore_email";
    
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':ignore_email', $ignore_email, PDO::PARAM_STR);
    
        try {
            if ($stmt->execute()) {
                $count = $stmt->fetchColumn();
                return $count > 0; 
            } else {
                throw new Exception("Failed to execute email check query");
            }
        } catch (Exception $e) {
            error_log("Error with function friendEmailAvailability\n", 3, "../errors/error.log");
            echo "Error type: " . $e->getMessage();
            return false; 
        }
    }

    public static function verifyToken($connection, $token) {
        $sql = "SELECT verify_token, verify_status FROM users WHERE verify_token = :token";
    
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(':token', $token, PDO::PARAM_STR);
    
        try {
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return $stmt->fetch(PDO::FETCH_ASSOC);
                } else {
                    return null;
                }
            } else {
                throw new Exception("Failed to verify token");
            }
        } catch (Exception $e) {
            error_log("Error with function verifyToken1\n", 3, "../errors/error.log");
            echo "Error type: " . $e->getMessage();
        }
    }

    public static function updateVerifyStatus($connection, $token) { 
        $sql = "UPDATE users SET verify_status='1' WHERE verify_token = :verify_token";

        $stmt = $connection->prepare($sql);
        $stmt->bindValue(':verify_token', $token, PDO::PARAM_STR);

        try {
            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Failed to update verify status");
            }
        } catch (Exception $e) {
            error_log("Error with function updateVerifyStatus\n", 3, "../errors/error.log");
            echo "Error type: " . $e->getMessage();
        }
    }

    public static function updateToken($connection, $verify_token, $email) { 
        $sql = "UPDATE users SET verify_token = :verify_token WHERE email = :email";

        $stmt = $connection->prepare($sql);
        $stmt->bindValue(':verify_token', $verify_token, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);

        try {
            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Failed to update token");
            }
        } catch (Exception $e) {
            error_log("Error with function updateToken\n", 3, "../errors/error.log");
            echo "Error type: " . $e->getMessage();
        }
    }

    public static function updateForgottenPassword($connection, $verify_token, $password) { 
        $sql = "UPDATE users SET password = :password WHERE verify_token= :verify_token";

        $stmt = $connection->prepare($sql);
        $stmt->bindValue(':verify_token', $verify_token, PDO::PARAM_STR);
        $stmt->bindValue(":password", password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);

        try {
            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Failed to update password");
            }
        } catch (Exception $e) {
            error_log("Error with function updateForgottenPassword\n", 3, "../errors/error.log");
            echo "Error type: " . $e->getMessage();
        }
    }

    public static function updatePassword($connection, $id_user, $password) { 
        $sql = "UPDATE users SET password = :password WHERE id_user = :id_user";

        $stmt = $connection->prepare($sql);
        $stmt->bindValue(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->bindValue(":password", password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);

        try {
            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Failed to update password");
            }
        } catch (Exception $e) {
            error_log("Error with function updatePassword\n", 3, "../errors/error.log");
            echo "Error type: " . $e->getMessage();
        }
    }

    public static function updateEmail($connection, $email, $id_user) { 
        $sql = "UPDATE users SET email = :email WHERE id_user = :id_user";

        $stmt = $connection->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':id_user', $id_user, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Failed to update email");
            }
        } catch (Exception $e) {
            error_log("Error with function updateEmail\n", 3, "../errors/error.log");
            echo "Error type: " . $e->getMessage();
        }
    }

    public static function updateName($connection, $name, $id_user) { 
        $sql = "UPDATE users SET name = :name WHERE id_user = :id_user";

        $stmt = $connection->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':id_user', $id_user, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Failed to update name");
            }
        } catch (Exception $e) {
            error_log("Error with function updateName\n", 3, "../errors/error.log");
            echo "Error type: " . $e->getMessage();
        }
    }
}