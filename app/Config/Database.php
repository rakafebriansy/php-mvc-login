<?php

namespace rakafebriansy\phpmvc\Config;

class Database
{
    private static ?\PDO $pdo = null;
    public static function getConnection(string $env = 'test'): \PDO
    {
        if (self::$pdo == null) {
            require_once __DIR__ . './../../config/database.php';
            $config = getDatabaseConfig()['database'][$env];
            self::$pdo = new \PDO($config['url'],$config['username'],$config['password']);
        } else {
            return self::$pdo;
        }
        return self::$pdo;
    }
    public static function beginTransaction()
    {
        self::$pdo->beginTransaction();
    }
    public static function commitTransaction()
    {
        self::$pdo->commit();
    }
    public static function rollbackTransaction()
    {
        self::$pdo->rollback();
    }
}

?>