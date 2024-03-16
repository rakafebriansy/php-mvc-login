<?php

namespace rakafebriansy\phpmvc\Controller;

use rakafebriansy\phpmvc\Config\Database;
use rakafebriansy\phpmvc\Core\View;
use rakafebriansy\phpmvc\Exception\ValidationException;
use rakafebriansy\phpmvc\Model\UserLoginRequest;
use rakafebriansy\phpmvc\Model\UserRegisterRequest;
use rakafebriansy\phpmvc\Repository\UserRepository;
use rakafebriansy\phpmvc\Service\UserService;

class UserController
{
    private UserService $user_service;

    public function __construct()
    {
        $conn = Database::getConnection();
        $user_repository = new UserRepository($conn);
        $this->user_service = new UserService($user_repository);
    }
    public function register()
    {
        View::render('User/register', [
            'title' => 'Register new User'
        ]);
    }
    public function postRegister()
    {
        $request = new UserRegisterRequest();
        $request->id = $_POST['id'];
        $request->name = $_POST['name'];
        $request->password = $_POST['password'];

        try {
            $this->user_service->register($request);
            View::redirect('/users/login');
        } catch (ValidationException $exception) {
            View::render('User/register', [
                'title' => 'Register new User',
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function login()
    {
        View::render('User/login',[
            'title' => 'Login user'
        ]);
    }

    public function postLogin()
    {
        $request = new UserLoginRequest();
        $request->id = $_POST['id'];
        $request->password = $_POST['password'];

        try {
            $this->user_service->login($request);
            View::redirect('/');
        } catch (ValidationException $exception) {
            View::render('User/login', [
                'title' => 'Login user',
                'error' => $exception->getMessage()
            ]);
        }
    }
}

?>