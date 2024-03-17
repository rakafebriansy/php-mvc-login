<?php

namespace rakafebriansy\phpmvc\Repository;

use PHPUnit\Framework\TestCase;
use rakafebriansy\phpmvc\Config\Database;
use rakafebriansy\phpmvc\Domain\User;

class UserRepositoryTest extends TestCase
{
    private UserRepository $user_repository;
    private SessionRepository $session_repository;

    protected function setUp(): void
    {
        $conn = Database::getConnection();
        $this->session_repository = new SessionRepository($conn);
        $this->session_repository->deleteAll();
        $this->user_repository = new UserRepository($conn);
        $this->user_repository->deleteAll();
    }

    public function testSaveSuccess()
    {
        $user = new User();
        $user->id = 'raka';
        $user->name = 'Raka';
        $user->password = '12345';

        $this->user_repository->save($user);

        $result = $this->user_repository->findById($user->id);

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->name, $result->name);
        self::assertEquals($user->password, $result->password);
    }
    public function testFindByIdNotFound()
    {
        $user = $this->user_repository->findById('404');
        self::assertNull($user);
    }
    public function testupdate()
    {
        $user = new User();
        $user->id = 'raka';
        $user->name = 'Raka';
        $user->password = '12345';

        $this->user_repository->save($user);

        $user->name = 'Budi';
        $this->user_repository->update($user);

        $result = $this->user_repository->findById($user->id);

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->name, $result->name);
        self::assertEquals($user->password, $result->password);
    }
}

?>