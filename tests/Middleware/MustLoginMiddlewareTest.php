<?php

namespace rakafebriansy\phpmvc\Middleware;
require_once __DIR__ . '/../Helper/helper.php';
use PHPUnit\Framework\TestCase;
use rakafebriansy\phpmvc\Config\Database;
use rakafebriansy\phpmvc\Domain\Session;
use rakafebriansy\phpmvc\Domain\User;
use rakafebriansy\phpmvc\Repository\SessionRepository;
use rakafebriansy\phpmvc\Repository\UserRepository;
use rakafebriansy\phpmvc\Service\SessionService;

class MustLoginMiddlewareTest extends TestCase
{
    private MustLoginMiddleware $middleware;
    private UserRepository $user_repository;
    private SessionRepository $session_repository;

    public function setUp():void
    {
        $this->middleware = new MustLoginMiddleware();
        putenv('mode=test');

        $conn = Database::getConnection();
        $this->user_repository = new UserRepository($conn);
        $this->session_repository = new SessionRepository($conn);

        $this->session_repository->deleteAll();
        $this->user_repository->deleteAll();
    }
    public function testBeforeGuest()
    {
        $this->middleware->before();
        $this->expectOutputRegex('[Location: /users/login]');
    }
    public function testBeforeLoginUser()
    {
        $user = new User();
        $user->id = 'raka';
        $user->name = 'Raka';
        $user->password = '12345';
        $this->user_repository->save($user);

        $session = new Session();
        $session->id = uniqid();
        $session->user_id = $user->id;
        $this->session_repository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->middleware->before();
        $this->expectOutputString('');
    }
}


?>