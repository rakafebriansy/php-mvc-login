<?php

namespace rakafebriansy\phpmvc\Config;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    public function testGetConnection()
    {
        $conn = Database::getConnection();
        self::assertNotNull($conn); //check null value
    }
    public function testGetConnectionSingleton()
    {
        $conn1 = Database::getConnection();
        $conn2 = Database::getConnection();
        self::assertSame($conn1,$conn2); //check identic
    }
}

?>