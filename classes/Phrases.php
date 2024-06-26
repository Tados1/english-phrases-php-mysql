<?php

class Phrases {

    public static function get($connection, $id_user) {
        $sql = "SELECT * FROM phrases 
                WHERE id_user = :id_user
                ORDER BY id_phrase DESC";
    
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

    
    public static function getOnePhrase($connection, $id_phrase, $id_user) {
        $sql = "SELECT *
                FROM phrases
                WHERE id_phrase = :id_phrase AND id_user = :id_user";
        
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(":id_phrase", $id_phrase, PDO::PARAM_INT);
        $stmt->bindValue(":id_user", $id_user, PDO::PARAM_INT);

        try {
            if($stmt->execute()) {
                return $stmt->fetch();
            } else {
                throw new Exception("Retrieving data about one phrase failed");
            }
        } catch (Exception $e) {
            error_log("Error with function getOnePhrase, failed to get data\n", 3, "../errors/error.log");
            echo "Error Type: " . $e->getMessage();
        }
    }

    public static function getRandomPhrase($connection, $id_user) {
        $sql = "SELECT * FROM phrases WHERE id_user = :id_user AND status = 'show' ORDER BY RAND() LIMIT 1";
        
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(":id_user", $id_user, PDO::PARAM_INT);

        try {
            if($stmt->execute()) {
                return $stmt->fetch();
            } else {
                throw new Exception("Retrieving data about one random phrase failed");
            }
        } catch (Exception $e) {
            error_log("Error with function getRandomPhrase, failed to get data\n", 3, "../errors/error.log");
            echo "Error Type: " . $e->getMessage();
        }
    }

    public static function getRandomFriendsPhrase($connection, $id_user, $friend_id) {
        $sql = "SELECT * FROM phrases WHERE (id_user = :id_user OR id_user = :friend_id) AND status = 'show' ORDER BY RAND() LIMIT 1";
        
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(":id_user", $id_user, PDO::PARAM_INT);
        $stmt->bindValue(":friend_id", $friend_id, PDO::PARAM_INT);

        try {
            if($stmt->execute()) {
                return $stmt->fetch();
            } else {
                throw new Exception("Retrieving data about one random phrase failed");
            }
        } catch (Exception $e) {
            error_log("Error with function getRandomPhrase, failed to get data\n", 3, "../errors/error.log");
            echo "Error Type: " . $e->getMessage();
        }
    }


    public static function create($connection, $slovak, $english, $id_user) {
        $sql = "INSERT INTO phrases (slovak, english, id_user) 
        VALUES (:slovak, :english, :id_user)";

        $stmt = $connection->prepare($sql);

        $stmt->bindValue(":slovak", $slovak, PDO::PARAM_STR);
        $stmt->bindValue(":english", $english, PDO::PARAM_STR);
        $stmt->bindValue(":id_user", $id_user, PDO::PARAM_INT);

        try {
            if($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Failed to create phrase");
            }
        } catch (Exception $e) {
            error_log("Error with function create\n", 3, "../errors/error.log");
            echo "Error Type: " . $e->getMessage();
        }
    }

    public static function edit($connection, $slovak, $english, $id) {
        $sql = "UPDATE phrases
                    SET slovak = :slovak,
                        english = :english
                    WHERE id_phrase = :id";
        
        $stmt = $connection->prepare($sql);

        $stmt->bindValue(":slovak", $slovak, PDO::PARAM_STR);
        $stmt->bindValue(":english", $english, PDO::PARAM_STR);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        try {
            if($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Edit phrase"); 
            }
        } catch (Exception $e) {
            error_log("Error with function edit\n", 3, "../errors/error.log");
            echo "Error Type: " . $e->getMessage();
        }
    }

    public static function delete($connection, $id){
        $sql = "DELETE
                FROM phrases
                WHERE id_phrase = :id";
        
        $stmt = $connection->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Deleting the phrase failed");
            }
        } catch (Exception $e) {
            error_log("Error with function delete\n", 3, "../errors/error.log");
            echo "Error Type: " . $e->getMessage();
        }
    }

    public static function deleteAllPhrases($connection, $id){
        $sql = "DELETE
                FROM phrases
                WHERE id_user = :id";
        
        $stmt = $connection->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Deleting all phrases failed");
            }
        } catch (Exception $e) {
            error_log("Error with function deleteAllPhrases\n", 3, "../errors/error.log");
            echo "Error Type: " . $e->getMessage();
        }
    }

    public static function showToggle($connection, $id, $value) {
        $sql = "UPDATE phrases
                    SET status = :value
                    WHERE id_phrase = :id";
        
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(":value", $value, PDO::PARAM_STR);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        try {
            if($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Ignore phrase"); 
            }
        } catch (Exception $e) {
            error_log("Error with function ignore\n", 3, "../errors/error.log");
            echo "Error Type: " . $e->getMessage();
        }
    }

    public static function countingPhrases($connection, $id) {
        $sql = "SELECT COUNT(*) 
                    FROM phrases
                    WHERE id_user = :id";

        $stmt = $connection->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        try {
            if($stmt->execute()) {
                return $stmt->fetch();
            } else {
                throw new Exception("Fail to count phrases"); 
            }
        } catch (Exception $e) {
            error_log("Error with function countingPhrases\n", 3, "../errors/error.log");
            echo "Error Type: " . $e->getMessage();
        }
    }

    public static function countingHiddenPhrases($connection, $id) {
        $sql = "SELECT COUNT(*) 
                    FROM phrases
                    WHERE id_user = :id AND status = 'hide'";

        $stmt = $connection->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        try {
            if($stmt->execute()) {
                return $stmt->fetch();
            } else {
                throw new Exception("Fail to count hidden phrases"); 
            }
        } catch (Exception $e) {
            error_log("Error with function countingHiddenPhrases\n", 3, "../errors/error.log");
            echo "Error Type: " . $e->getMessage();
        }
    }
}
