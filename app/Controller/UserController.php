<?php

namespace rakafebriansy\phpmvc\Controller;

use rakafebriansy\phpmvc\Config\Database;
use rakafebriansy\phpmvc\Core\View;
use rakafebriansy\phpmvc\Exception\ValidationException;
use rakafebriansy\phpmvc\Model\UserLoginRequest;
use rakafebriansy\phpmvc\Model\UserPasswordUpdateRequest;
use rakafebriansy\phpmvc\Model\UserProfileUpdateRequest;
use rakafebriansy\phpmvc\Model\UserRegisterRequest;
use rakafebriansy\phpmvc\Repository\SessionRepository;
use rakafebriansy\phpmvc\Repository\UserRepository;
use rakafebriansy\phpmvc\Service\SessionService;
use rakafebriansy\phpmvc\Service\UserService;

class UserController
{
    private UserService $user_service;
    private SessionService $session_service;

    public function __construct()
    {
        $connection = Database::getConnection();
        $user_repository = new UserRepository($connection);
        $this->user_service = new UserService($user_repository);

        $session_repository = new SessionRepository($connection);
        $this->session_service = new SessionService($session_repository, $user_repository);
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
            $response = $this->user_service->login($request);
            $this->session_service->create($response->user->id);
            View::redirect('/');
        } catch (ValidationException $exception) {
            View::render('User/login', [
                'title' => 'Login user',
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function logout()
    {
        $this->session_service->destroy();
        View::redirect('/');
    }

    public function updateProfile()
    {
        $user = $this->session_service->current();
        View::render('User/profile', [
            'title' => 'Update user profile',
            'user' => [
                'id' => $user->id,
                'name' => $user->name
            ]
        ]);
    }
    public function postUpdateProfile()
    {
        $user = $this->session_service->current();

        $request = new UserProfileUpdateRequest();
        $request->id = $user->id;
        $request->name = $_POST['name'];
        try {
            $this->user_service->updateProfile($request);
            View::redirect('/');
        } catch (\Exception $exception) {
            View::render('User/profile', [
                'title' => 'Update user profile',
                'user' => [
                    'id' => $user->id,
                    'name' => $_POST['name']
                ],
                'error' => $exception->getMessage()
            ]);
        }

    }
    public function updatePassword()
    {
        $user = $this->session_service->current();
        View::render('User/password',[
            'title' => 'Update user password',
            'user' => [
                'id' => $user->id
            ]
        ]);
    }

    public function postUpdatePassword()
    {
        $user = $this->session_service->current();
        $request = new UserPasswordUpdateRequest();
        $request->id = $user->id;
        $request->old_password = $_POST['oldPassword'];
        $request->new_password = $_POST['newPassword'];

        try {
            $this->user_service->updatePassword($request);
            View::redirect('/');
        } catch (\Exception $exception) {
            View::render('User/password', [
                'title' => 'Update user password',
                'error' => $exception->getMessage(),
                'user' => [
                    'id' => $user->id
                ]
            ]);
        }
    }
}

?>