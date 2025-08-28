<?php

namespace App\Core;

use App\Config\Config;
use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;
    
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $config = Config::getInstance();
            
            $host = $config->get('database.host');
            $port = $config->get('database.port');
            $database = $config->get('database.database');
            $username = $config->get('database.username');
            $password = $config->get('database.password');
            
            try {
                $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
                self::$connection = new PDO($dsn, $username, $password);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                throw new \Exception("Database connection failed: " . $e->getMessage());
            }
        }
        
        return self::$connection;
    }
}