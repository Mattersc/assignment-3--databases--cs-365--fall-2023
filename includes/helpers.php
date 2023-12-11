<?php

require_once 'config.php';

function search($search){
    try{
        $db = new PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME . ";charset=utf8", DBUSER, DBPASS);

        $sql = "SET @key_str = UNHEX('" . ENCRYPTION_KEY . "')";
        $db->exec($sql);

        $sql2 = "
            SELECT
                websites.website_name,
                websites.website_url,
                accounts.username,
                CONVERT(AES_DECRYPT(accounts.password, @key_str) USING utf8) AS true_password,
                accounts.email,
                accounts.comment
            FROM websites JOIN accounts ON websites.website_id = accounts.website_id WHERE
                websites.website_name LIKE :search OR
                websites.website_url LIKE :search OR
                accounts.username LIKE :search OR
                CONVERT(AES_DECRYPT(accounts.password, @key_str) USING utf8) LIKE :search OR
                accounts.email LIKE :search OR
                accounts.comment LIKE :search
        ";

        $statement = $db->prepare($sql2);
        $statement->bindvalue(':search', "%" . $search . "%", PDO::PARAM_STR);
        $statement->execute();

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        if(count($results) === 0){
            return 0;
        } else{
            echo "<table>\n";
            echo "<thead>\n";
            echo "<tr>\n";

            foreach($results[0] as $key => $value){
                echo "<th>" . htmlspecialchars($key) . "<th>\n";
            }

            echo "<tr>\n";
            echo "<thead>\n";
            echo "<tbody>\n";

            foreach($results as $row){
                echo "<tr>\n";
                foreach($row as $key => $value){
                    if($key === 'true_password'){
                        echo "<td>" . ($value !== null ? htmlspecialchars($value) : 'N/A') . "<td>\n";
                    }else{
                        echo "<td>" . htmlspecialchars($value) . "<td>\n";
                    }
                }
                echo "<tr>\n";
            }
            echo "<tbody>\n";
            echo "<table>\n";
        }
    }
    catch(PDOException $e){
        echo '<p>The search has failed, try again! ';
        exit;
    }
}

function update($table, $current_attribute, $new_value, $query_attribute, $pattern) {
    try {
        $db = new PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME . ";charset=utf8", DBUSER, DBPASS);

        $sql = "SET @key_str = UNHEX('" . ENCRYPTION_KEY . "')";
        $db->exec($sql);

        $update_query = "UPDATE {$table} SET {$current_attribute} = :new_value WHERE {$query_attribute} = :pattern";

        if ($current_attribute === 'password') {
            $update_query = "UPDATE {$table} SET {$current_attribute} = AES_ENCRYPT(:new_value, @UNHEXEncryptionKey) WHERE {$query_attribute} = :pattern";
        } elseif ($query_attribute === 'password') {
            $update_query = "UPDATE {$table} SET {$current_attribute} = :new_value WHERE {$query_attribute} = AES_ENCRYPT(:pattern, @UNHEXEncryptionKey)";
        }

        $statement = $db->prepare($update_query);
        $statement->bindParam(':new_value', $new_value);

        if ($current_attribute === 'password' || $query_attribute === 'password') {
            $statement->bindParam(':pattern', $pattern, PDO::PARAM_STR);
        } else {
            $statement->bindParam(':pattern', $pattern);
        }

        $statement->execute();
    } catch (PDOException $e) {
        echo 'Update has failed, try again! ' . $e->getMessage();
    }
}

function insert($website_name, $website_url, $username, $email, $password, $comment){
    try {
        $db = new PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME . ";charset=utf8", DBUSER, DBPASS);

        $sql1 = "INSERT INTO websites (website_name, website_url) VALUES (?, ?)";
        $statement1 = $db->prepare($sql1);
        $statement1->execute([$website_name, $website_url]);

        $website_id = $db->lastInsertId();

        $sql2 = "INSERT INTO accounts (website_id, username, email, password, comment) VALUES (?, ?, ?, ?, ?)";
        $statement2 = $db->prepare($sql2);
        $statement2->execute([$website_id, $username, $email, $password, $comment]);

    } catch (PDOException $e) {
        echo 'Insertion has failed, try again! ' . $e->getMessage();
    }
}

function delete($websiteName) {
    try {
        $db = new PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME . ";charset=utf8", DBUSER, DBPASS);

        $sql1 = "DELETE FROM accounts WHERE website_id IN (SELECT website_id FROM websites WHERE website_name = :websiteName)";
        $statement1 = $db->prepare($sql1);
        $statement1->bindParam(':websiteName', $websiteName, PDO::PARAM_STR);
        $statement1->execute();

        $sql2 = "DELETE FROM websites WHERE website_name = :websiteName";
        $statement2 = $db->prepare($sql2);
        $statement2->bindParam(':websiteName', $websiteName, PDO::PARAM_STR);
        $statement2->execute();
    } catch (PDOException $e) {
        exit('<div id="error">ERROR, deletion has failed.</div>'. $e->getMessage() .'</p>' . "\n");
    }
}

?>
