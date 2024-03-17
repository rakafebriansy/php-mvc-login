<?php

namespace rakafebriansy\phpmvc\Controller;

use PHPUnit\Framework\TestCase;
use rakafebriansy\phpmvc\Config\Database;
use rakafebriansy\phpmvc\Domain\Session;
use rakafebriansy\phpmvc\Domain\User;
use rakafebriansy\phpmvc\Repository\SessionRepository;
use rakafebriansy\phpmvc\Repository\UserRepository;
use rakafebriansy\phpmvc\Service\SessionService;

class HomeControllerTest extends TestCase
{
    private HomeController $home_controller;
    private UserRepository $user_repository;
    private SessionRepository $session_repository;

    public function setUp(): void
    {
        $conn = Database::getConnection();
        $this->home_controller = new HomeController();
        $this->session_repository = new SessionRepository($conn);
        $this->user_repository = new UserRepository($conn);

        $this->session_repository->deleteAll();
        $this->user_repository->deleteAll();
    }
    public function testGuest()
    {
        $this->home_controller->index();

        $this->expectOutputRegex('[Login Management]');
    }
    public function testUserLogin()
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

        $this->home_controller->index();

        $this->expectOutputRegex('[Hello Raka]');
    }
}

?>