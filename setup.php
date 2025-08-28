<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Config\Config;

try {
    echo "Setting up database...\n";
    
    // Get database instance
    $db = Database::getInstance();
    $pdo = $db->getConnection();  // Fixed - call on instance, not statically
    
    // Read and execute the schema file
    $schema = file_get_contents(__DIR__ . '/schema.sql');
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "Database setup completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error setting up database: " . $e->getMessage() . "\n";
    exit(1);
}