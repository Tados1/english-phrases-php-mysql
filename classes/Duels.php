<?php

class Duels {
    public static function createDuel($connection, $receiver, $sender, $first_player_check) {

        $sql = "INSERT INTO duels (receiver, sender, first_player_check) 
        VALUES (:receiver, :sender, :first_player_check)";

        $stmt = $connection->prepare($sql);

        $stmt->bindValue(":receiver", $receiver, PDO::PARAM_INT);
        $stmt->bindValue(":sender", $sender, PDO::PARAM_INT);
        $stmt->bindValue(":first_player_check", $first_player_check, PDO::PARAM_INT);

        try {
            if($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Failed to create duel");
            }
        } catch (Exception $e) {
            error_log("Error with function createDuel\n", 3, "../errors/error.log");
            echo "Error Type: " . $e->getMessage();
        }
    }

    public static function deleteDuel($connection, $receiver, $sender) {
        $sql = "DELETE FROM duels WHERE (receiver = :receiver AND sender = :sender) OR (receiver = :sender AND sender = :receiver)";

        $stmt = $connection->prepare($sql);

        $stmt->bindValue(":receiver", $receiver, PDO::PARAM_INT);
        $stmt->bindValue(":sender", $sender, PDO::PARAM_INT);

        try {
            if($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Failed to delete duel");
            }
        } catch (Exception $e) {
            error_log("Error with function deleteDuel\n", 3, "../errors/error.log");
            echo "Error Type: " . $e->getMessage();
        }
    }

    public static function deleteDuelAfterDeletedUser($connection, $id_user) {
        $sql = "DELETE FROM duels WHERE receiver = :id_user OR sender = :id_user";

        $stmt = $connection->prepare($sql);

        $stmt->bindValue(":id_user", $id_user, PDO::PARAM_INT);

        try {
            if($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Failed to delete duel");
            }
        } catch (Exception $e) {
            error_log("Error with function deleteDuelAfterDeletedUser\n", 3, "../errors/error.log");
            echo "Error Type: " . $e->getMessage();
        }
    }

    public static function updateScore($connection, $column, $value, $receiver, $sender) {

        $sql = "UPDATE duels SET $column = :value WHERE receiver = :receiver AND sender = :sender";
    
        $stmt = $connection->prepare($sql);
    
        $stmt->bindValue(":value", $value, PDO::PARAM_STR);
        $stmt->bindValue(":receiver", $receiver, PDO::PARAM_INT);
        $stmt->bindValue(":sender", $sender, PDO::PARAM_INT);
    
        try {
            if($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Failed to update score");
            }
        } catch (Exception $e) {
            error_log("Error with function updateScore\n", 3, "../errors/error.log");
            echo "Error Type: " . $e->getMessage();
        }
    }

    public static function updateDuel($connection, $column, $value, $receiver, $sender) {

        $sql = "UPDATE duels SET $column = :value WHERE (receiver = :receiver AND sender = :sender) OR (receiver = :sender AND sender = :receiver)";
    
        $stmt = $connection->prepare($sql);
    
        $stmt->bindValue(":value", $value, PDO::PARAM_STR);
        $stmt->bindValue(":receiver", $receiver, PDO::PARAM_INT);
        $stmt->bindValue(":sender", $sender, PDO::PARAM_INT);
    
        try {
            if($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Failed to update score");
            }
        } catch (Exception $e) {
            error_log("Error with function updateScore\n", 3, "../errors/error.log");
            echo "Error Type: " . $e->getMessage();
        }
    }

    public static function getInfo($connection, $receiver, $sender, $info) {
        $sql = "SELECT $info FROM duels WHERE (receiver = :receiver AND sender = :sender) OR (receiver = :sender AND sender = :receiver)";

        $stmt = $connection->prepare($sql);

        $stmt->bindValue(":receiver", $receiver, PDO::PARAM_INT);
        $stmt->bindValue(":sender", $sender, PDO::PARAM_INT);

        try {
            if($stmt->execute()) {
                return $stmt->fetch();
            } else {
                throw new Exception("Failed to get info");
            }
        } catch (Exception $e) {
            error_log("Error with function getInfo\n", 3, "../errors/error.log");
            echo "Error Type: " . $e->getMessage();
        }
    }

    public static function getAllDatas($connection, $receiver, $sender) {
        $sql = "SELECT * FROM duels WHERE (receiver = :receiver AND sender = :sender) OR (receiver = :sender AND sender = :receiver)";

        $stmt = $connection->prepare($sql);

        $stmt->bindValue(":receiver", $receiver, PDO::PARAM_INT);
        $stmt->bindValue(":sender", $sender, PDO::PARAM_INT);

        try {
            if($stmt->execute()) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                throw new Exception("Failed to get info");
            }
        } catch (Exception $e) {
            error_log("Error with function getInfo\n", 3, "../errors/error.log");
            echo "Error Type: " . $e->getMessage();
        }
    }

    public static function checkExistDuel($connection, $receiver, $sender) {
        $sql = "SELECT * FROM duels WHERE (receiver = :receiver AND sender = :sender) OR (receiver = :sender AND sender = :receiver)";

        $stmt = $connection->prepare($sql);

        $stmt->bindValue(":receiver", $receiver, PDO::PARAM_INT);
        $stmt->bindValue(":sender", $sender, PDO::PARAM_INT);

        try {
            if($stmt->execute()) {
                $count = $stmt->fetchColumn();
                return $count > 0;
            } else {
                throw new Exception("Failed to check exist duel");
            }
        } catch (Exception $e) {
            error_log("Error with function checkExistDuel\n", 3, "../errors/error.log");
            echo "Error Type: " . $e->getMessage();
        }
    }
}