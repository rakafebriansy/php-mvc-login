<?php

namespace rakafebriansy\phpmvc\Controller;

use rakafebriansy\phpmvc\Config\Database;
use rakafebriansy\phpmvc\Core\View;
use rakafebriansy\phpmvc\Repository\SessionRepository;
use rakafebriansy\phpmvc\Repository\UserRepository;
use rakafebriansy\phpmvc\Service\SessionService;

class HomeController
{
    private SessionService $session_service;

    public function __construct()
    {
        $connection = Database::getConnection();
        $session_repository = new SessionRepository($connection);
        $user_repository = new UserRepository($connection);
        $this->session_service = new SessionService($session_repository, $user_repository);
    }
    public function index(): void 
    {
        $user = $this->session_service->current();
        if ($user == null) {
            View::render('Home/index',[
                'title' => 'Home'
            ]);
        } else {
            View::render('Home/dashboard',[
                'title' => 'Dashboard',
                'user' => [
                    'name' => $user->name
                ]
            ]);
        }

    }
}

?>