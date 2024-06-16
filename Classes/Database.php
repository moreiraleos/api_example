<?php

namespace Classes;

require_once "./vendor/autoload.php";

class Database
{
    private static $conn;

    private function __construct()
    {
    }

    // Prevent cloning the object
    private function __clone()
    {
    }

    public static function connect()
    {

        if (self::$conn == null) {
            try {
                self::$conn = new \PDO('mysql:host=localhost;dbname=php_auth_api', 'root', '');
                self::$conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } catch (\PDOException $e) {
                echo "Connection error " . $e->getMessage();
            }
        }
        return self::$conn;
    }
}
