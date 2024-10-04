<?php declare(strict_types = 1);

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    protected static $host= 'postgres';
    protected static $db = 'tasks';
    protected static $user = 'postgres';
    protected static $password = 'postgres';
    protected static $port = 5432;
    
    public static function connection(): PDO 
    {
        try {
            $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', self::$host, self::$port, self::$db);
            $pdo = new PDO($dsn, self::$user, self::$password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
}
