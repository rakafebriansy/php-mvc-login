<?php

namespace rakafebriansy\phpmvc\Service;

use rakafebriansy\phpmvc\Config\Database;
use rakafebriansy\phpmvc\Exception\ValidationException;
use rakafebriansy\phpmvc\Domain\User;
use rakafebriansy\phpmvc\Model\UserRegisterRequest;
use rakafebriansy\phpmvc\Model\UserRegisterResponse;
use rakafebriansy\phpmvc\Repository\UserRepository;

class UserService
{
    private UserRepository $user_repository;

    public function __construct(UserRepository $user_repository) 
    {
        $this->user_repository = $user_repository;
    }

    public function register(UserRegisterRequest $request): UserRegisterResponse
    {
        $this->validateRegistrationRequest($request);

        try {
            Database::beginTransaction();
            $user = $this->user_repository->findById($request->id);
            if ($user != null) {
                throw new ValidationException('User already exists.');
            }
    
            $user = new User();
            $user->id = $request->id;
            $user->name = $request->name;
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);
    
            $this->user_repository->save($user);
    
            $response = new UserRegisterResponse();
            $response->user = $user;

            Database::commitTransaction();
            return $response;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateRegistrationRequest(UserRegisterRequest $request)
    {
        if ($request->id == null || $request->name == null || $request->password == null ||
        trim($request->id) == '' || trim($request->name) == '' || trim($request->password) == '') {
            throw new ValidationException(('Id, Name, and Password can\'t blank.'));
        }
    }
}


?>