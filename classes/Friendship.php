<?php

class Friendship {
    public static function getFriends($connection, $id_user) {
        $sql = "SELECT CASE
        WHEN user_id = :id_user THEN friend_id
        ELSE user_id
        END AS friend_id
        FROM friendships 
        WHERE (user_id = :id_user OR friend_id = :id_user)
        AND (user_id <> :id_user OR friend_id <> :id_user)
        AND status = 'accepted'";
    
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(":id_user", $id_user, PDO::PARAM_INT);
    
        try {
            if ($stmt->execute()) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                throw new Exception("Failed to get all data");
            }
        } catch (Exception $e) {
            error_log("Error with function getPhrases\n", 3, "../errors/error.log");
            echo "Error type: " . $e->getMessage();
        }
    }


    public static function sendRequest($connection, $user_id, $friend_id, $status) {

        $sql = "INSERT INTO friendships (user_id, friend_id, status) VALUES (:user_id, :friend_id, :status)";

        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':friend_id', $friend_id, PDO::PARAM_INT);
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    
        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Failed to send request\n", 3, "../errors/error.log");
            return false;
        }
    }

    public static function checkRequest($connection, $id, $status) {
        $sql = "SELECT user_id, status FROM friendships WHERE friend_id = :id AND status = :status";
    
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        
        try {
            if ($stmt->execute()) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                throw new Exception("Failed to check request");
            }
        } catch (Exception $e) {
            error_log("Error with checking request\n", 3, "../errors/error.log");
            echo "Error type: " . $e->getMessage();
            return false;
        }
    }

    public static function acceptRequest($connection, $user_id, $friend_id) {
        $sql = "UPDATE friendships SET status = 'accepted' WHERE (user_id = :user_id AND friend_id = :friend_id) OR (user_id = :friend_id AND friend_id = :user_id)";
        
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':friend_id', $friend_id, PDO::PARAM_INT);
    
        try {
            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Failed to accept friend request");
            }
        } catch (Exception $e) {
            error_log("Error with accepting friend request\n", 3, "../errors/error.log");
            echo "Error type: " . $e->getMessage();
            return false;
        }
    }
    
    public static function declineRequest($connection, $user_id, $friend_id) {
        $sql = "DELETE FROM friendships WHERE (user_id = :user_id AND friend_id = :friend_id) OR (user_id = :friend_id AND friend_id = :user_id)";
        
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':friend_id', $friend_id, PDO::PARAM_INT);
    
        try {
            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Failed to decline friend request");
            }
        } catch (Exception $e) {
            error_log("Error with declining friend request\n", 3, "../errors/error.log");
            echo "Error type: " . $e->getMessage();
            return false;
        }
    }

    public static function deleteUser($connection, $user_id) {
        $sql = "DELETE FROM friendships WHERE user_id = :user_id OR friend_id = :user_id";
        
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    
        try {
            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Failed to delete user");
            }
        } catch (Exception $e) {
            error_log("Error with function deleteUser\n", 3, "../errors/error.log");
            echo "Error type: " . $e->getMessage();
            return false;
        }
    }

    public static function checkUsers($connection, $user_id, $friend_id) {
        $sql = "SELECT status FROM friendships WHERE (user_id = :user_id AND friend_id = :friend_id) OR (user_id = :friend_id AND friend_id = :user_id)";
    
        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':friend_id', $friend_id, PDO::PARAM_INT);
        
        try {
            if ($stmt->execute()) {
                return $stmt->fetch();
            } else {
                throw new Exception("Failed to check users");
                return false;
            }
        } catch (Exception $e) {
            error_log("Error with checking request\n", 3, "../errors/error.log");
            echo "Error type: " . $e->getMessage();
            return false;
        }
    }

}
