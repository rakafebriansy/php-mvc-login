<?php

namespace rakafebriansy\phpmvc\Repository;

use PHPUnit\Framework\TestCase;
use rakafebriansy\phpmvc\Config\Database;
use rakafebriansy\phpmvc\Domain\Session;
use rakafebriansy\phpmvc\Domain\User;

class SessionRepositoryTest extends TestCase
{
    private SessionRepository $session_repository;
    private UserRepository $user_repository;

    protected function setUp(): void
    {
        $conn = Database::getConnection();
        $this->user_repository = new UserRepository($conn);
        $this->session_repository = new SessionRepository($conn);

        $this->session_repository->deleteAll();
        $this->user_repository->deleteAll();

        $user = new User();
        $user->id = 'raka';
        $user->name = 'Raka';
        $user->password = '12345';
        $this->user_repository->save($user);
    }
    public function testSaveSuccess()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->user_id = 'raka';

        $this->session_repository->save($session);

        $result = $this->session_repository->findById($session->id);
        self::assertEquals($session->id, $result->id);
        self::assertEquals($session->user_id, $result->user_id);
    }
    public function testFindByIdNotFound()
    {
        $result = $this->session_repository->findById('notfound');
        self::assertNull($result);
    }
    public function testDeleteByIdSuccess()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->user_id = 'raka';

        $this->session_repository->save($session);

        $result = $this->session_repository->findById($session->id);
        self::assertEquals($session->id, $result->id);
        self::assertEquals($session->user_id, $result->user_id);
        
        $this->session_repository->deleteById($session->id);
        
        $result = $this->session_repository->findById($session->id);
        self::assertNull($result);
    }
}

?>