<?php

namespace rakafebriansy\phpmvc\Service;

use rakafebriansy\phpmvc\Domain\Session;
use rakafebriansy\phpmvc\Domain\User;
use rakafebriansy\phpmvc\Repository\SessionRepository;
use rakafebriansy\phpmvc\Repository\UserRepository;

class SessionService
{
    public static string $COOKIE_NAME = 'X-RKFBRNS-SESSION';
    private SessionRepository $session_repository;
    private UserRepository $user_repository;

    public function __construct(SessionRepository $session_repository, UserRepository $user_repository)
    {
        $this->session_repository = $session_repository;
        $this->user_repository = $user_repository;
    }
    public function create(string $user_id): Session
    {
        $session = new Session();
        $session->id = uniqid();
        $session->user_id = $user_id;

        $this->session_repository->save($session);

        setcookie(self::$COOKIE_NAME, $session->id, time() + (60 * 60 * 24 * 30), '/');
        
        return $session;
    }
    public function destroy()
    {
        $session_id = $_COOKIE[self::$COOKIE_NAME] ?? '';
        $this->session_repository->deleteById($session_id);

        setcookie(self::$COOKIE_NAME, '', 1, '/');
    }
    public function current(): ?User
    {
        $session_id = $_COOKIE[self::$COOKIE_NAME] ?? '';
        $session = $this->session_repository->findById($session_id);
        if ($session == null) {
            return null;
        } 
        return $this->user_repository->findById($session->user_id);
    }
}

?>