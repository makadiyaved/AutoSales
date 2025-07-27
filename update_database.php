<?php
require_once 'config/database.php';

try {
    // Add remember_token column
    $sql = "ALTER TABLE users ADD COLUMN IF NOT EXISTS remember_token VARCHAR(64) DEFAULT NULL";
    $pdo->exec($sql);
    echo "Database updated successfully! The remember_token column has been added.";
} catch(PDOException $e) {
    echo "Error updating database: " . $e->getMessage();
}
?> 