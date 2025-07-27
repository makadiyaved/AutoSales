<?php
require_once 'config/database.php';

try {
    // Check if the column already exists
    $checkColumn = $pdo->query("SHOW COLUMNS FROM users LIKE 'remember_token'");
    
    if ($checkColumn->rowCount() === 0) {
        // Add the remember_token column
        $sql = "ALTER TABLE users ADD COLUMN remember_token VARCHAR(64) DEFAULT NULL";
        $pdo->exec($sql);
        echo "Successfully added remember_token column to users table.";
    } else {
        echo "remember_token column already exists.";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 