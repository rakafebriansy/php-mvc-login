<?php

namespace rakafebriansy\phpmvc\Service;

use Exception;
use rakafebriansy\phpmvc\Config\Database;
use rakafebriansy\phpmvc\Exception\ValidationException;
use rakafebriansy\phpmvc\Domain\User;
use rakafebriansy\phpmvc\Model\UserLoginRequest;
use rakafebriansy\phpmvc\Model\UserLoginResponse;
use rakafebriansy\phpmvc\Model\UserPasswordUpdateRequest;
use rakafebriansy\phpmvc\Model\UserPasswordUpdateResponse;
use rakafebriansy\phpmvc\Model\UserProfileUpdateRequest;
use rakafebriansy\phpmvc\Model\UserProfileUpdateResponse;
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
        $this->validateUserRegistrationRequest($request);

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

    private function validateUserRegistrationRequest(UserRegisterRequest $request)
    {
        if ($request->id == null || $request->name == null || $request->password == null ||
            trim($request->id) == '' || trim($request->name) == '' || trim($request->password) == '') {
            throw new ValidationException(('Id, Name, and Password can\'t blank.'));
        }
    }

    public function login(UserLoginRequest $request): UserLoginResponse
    {
        $this->validateUserLoginRequest($request);
        $user = $this->user_repository->findById($request->id);

        if($user == null){
            throw new ValidationException('Id or password is wrong.');
        }
        
        if(password_verify($request->password, $user->password)){
            $response = new UserLoginResponse();
            $response->user = $user;
            return $response;
        } else {
            throw new ValidationException('Id or password is wrong.');
        }
    }

    private function validateUserLoginRequest(UserLoginRequest $request)
    {
        if ($request->id == null || $request->password == null ||
            trim($request->id) == '' || trim($request->password) == '') {
            throw new ValidationException(('Id and Password can\'t blank.'));
        }
    }

    public function updateProfile(UserProfileUpdateRequest $request): UserProfileUpdateResponse
    {
        $this->validateUserProfileUpdateRequest($request);

        try {
            Database::beginTransaction();

            $user = $this->user_repository->findById($request->id);
            if ($user == null) {
                throw new ValidationException('User is not found.');
            }

            $user->name = $request->name;
            $this->user_repository->update($user);

            Database::commitTransaction();

            $response = new UserProfileUpdateResponse();
            $response->user = $user;
            return $response;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception; 
        }
    }

    private function validateUserProfileUpdateRequest(UserProfileUpdateRequest $request)
    {
        if ($request->id == null || $request->name == null || trim($request->id) == '' || trim($request->name) == '')
        {
            throw new ValidationException('Id and Name can\'t blank.');
        }
    }

    public function updatePassword(UserPasswordUpdateRequest $request): UserPasswordUpdateResponse
    {
        $this->validateUserPasswordUpdateRequest($request);
        try {
            Database::beginTransaction();

            $user = $this->user_repository->findById($request->id);
            if ($user == null) {
                throw new ValidationException('User is not found');
            }

            if (!password_verify($request->old_password, $user->password)) {
                throw new ValidationException('Old password is wrong');
            }

            $user->password = password_hash($request->new_password, PASSWORD_BCRYPT);
            $this->user_repository->update($user);

            Database::commitTransaction();

            $response = new UserPasswordUpdateResponse();
            $response->user = $user;
            return $response;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }
    
    private function validateUserPasswordUpdateRequest(UserPasswordUpdateRequest $request)
    {
        if ($request->id == null || $request->old_password == null || $request->new_password == null || trim($request->id) == '' || trim($request->old_password) == '' || trim($request->new_password) == '')
        {
            throw new ValidationException('Id, Old Password, and New Password can\'t blank.');
        }
    }
}


?>