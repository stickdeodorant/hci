<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Database;

echo "Setting up database...\n";

try {
    $db = Database::getConnection();
    
    // Read and execute schema
    $sql = file_get_contents(__DIR__ . '/database/schema.sql');
    $db->exec($sql);
    
    echo "Database setup completed successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}