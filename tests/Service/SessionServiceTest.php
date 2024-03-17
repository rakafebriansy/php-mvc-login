<?php

namespace rakafebriansy\phpmvc\Service;
require_once __DIR__ . '/../Helper/helper.php';

use PHPUnit\Framework\TestCase;
use rakafebriansy\phpmvc\Config\Database;
use rakafebriansy\phpmvc\Domain\Session;
use rakafebriansy\phpmvc\Domain\User;
use rakafebriansy\phpmvc\Repository\SessionRepository;
use rakafebriansy\phpmvc\Repository\UserRepository;

class SessionServiceTest extends TestCase
{
    private SessionService $session_service;
    private SessionRepository $session_repository;
    private UserRepository $user_repository;

    protected function setUp(): void
    {
        $conn = Database::getConnection();
        $this->session_repository = new SessionRepository($conn);
        $this->user_repository = new UserRepository($conn);
        $this->session_service = new SessionService($this->session_repository, $this->user_repository);

        $this->session_repository->deleteAll();
        $this->user_repository->deleteAll();

        $user = new User();
        $user->id = 'raka';
        $user->name = 'Raka';
        $user->password = '12345';
        $this->user_repository->save($user);
    }

    public function testCreate()
    {
        $session = $this->session_service->create('raka');
        $this->expectOutputRegex("[X-RKFBRNS-SESSION: $session->id]");
        $result = $this->session_repository->findById($session->id);
        self::assertEquals('raka', $result->user_id);
    }

    public function testDestroy()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->user_id = 'raka';

        $this->session_repository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->session_service->destroy();

        $this->expectOutputRegex("[X-RKFBRNS-SESSION: ]");

        $result = $this->session_repository->findById($session->id);
        self::assertNull($result);
    }

    public function testCurrent()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->user_id = 'raka';

        $this->session_repository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $user = $this->session_service->current();

        self::assertEquals($session->user_id,$user->id);
    }
}
