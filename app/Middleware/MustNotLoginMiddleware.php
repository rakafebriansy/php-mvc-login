<?php

namespace rakafebriansy\phpmvc\Middleware;

use rakafebriansy\phpmvc\Config\Database;
use rakafebriansy\phpmvc\Core\View;
use rakafebriansy\phpmvc\Repository\SessionRepository;
use rakafebriansy\phpmvc\Repository\UserRepository;
use rakafebriansy\phpmvc\Service\SessionService;

class MustNotLoginMiddleware implements Middleware
{
    private SessionService $session_service;
    
    public function __construct() {
        $connection = Database::getConnection();
        $session_repository = new SessionRepository($connection);
        $user_repository = new UserRepository($connection);
        $this->session_service = new SessionService($session_repository, $user_repository);
    }
    public function before():void
    {
        $user = $this->session_service->current();
        if ($user != null) {
            View::redirect('/');
        }
    }
}

?>