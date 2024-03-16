<?php

namespace rakafebriansy\phpmvc\Repository;

use rakafebriansy\phpmvc\Domain\User;

class UserRepository
{
    private \PDO $conn;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }
    public function save(User $user): User
    {
        $statement = $this->conn->prepare("INSERT INTO users(id, name, password) VALUES (?, ?, ?)");
        $statement->execute([
            $user->id, $user->name, $user->password
        ]);
        return $user;
    }
    public function findById(string $id): ?User
    {
        $statement = $this->conn->prepare("SELECT id, name, password FROM users WHERE id=?");
        $statement->execute([$id]);

        try {
            if($row =  $statement->fetch()) {
                $user = new User();
                $user->id = $row['id'];
                $user->name = $row['name'];
                $user->password = $row['password'];
                return $user;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor(); //close connection cursor
        }
    }
    public function deleteAll(): void
    {
        $this->conn->exec('DELETE FROM users');
    }
}

?>