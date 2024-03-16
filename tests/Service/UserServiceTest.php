<?php

namespace rakafebriansy\phpmvc\Service;

use PHPUnit\Framework\TestCase;
use rakafebriansy\phpmvc\Config\Database;
use rakafebriansy\phpmvc\Exception\ValidationException;
use rakafebriansy\phpmvc\Domain\User;
use rakafebriansy\phpmvc\Model\UserLoginRequest;
use rakafebriansy\phpmvc\Model\UserRegisterRequest;
use rakafebriansy\phpmvc\Repository\UserRepository;

class UserServiceTest extends TestCase
{
    private UserService $user_service;
    private UserRepository $user_repository;

    protected function setUp(): void
    {
        $conn = Database::getConnection();
        $this->user_repository = new UserRepository($conn);
        $this->user_service = new UserService($this->user_repository);
        $this->user_repository->deleteAll();
    }
    public function testRegisterSuccess()
    {
        $request = new UserRegisterRequest();
        $request->id = 'raka';
        $request->name = 'Raka';
        $request->password = '12345';
        
        $response = $this->user_service->register($request);
        
        self::assertEquals($request->id, $response->user->id);
        self::assertEquals($request->name, $response->user->name);
        self::assertNotEquals($request->password, $response->user->password);
        self::assertTrue(password_verify($request->password, $response->user->password));
    }
    public function testRegisterFailed()
    {
        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->id = '';
        $request->name = '';
        $request->password = '';
        
        $this->user_service->register($request);
    }
    public function testRegisterDuplicate()
    {
        $user = new User();
        $user->id = 'raka';
        $user->name = 'Raka';
        $user->password = '12345';

        $this->user_repository->save($user);

        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->id = 'raka';
        $request->name = 'Raka';
        $request->password = '12345';
        
        $this->user_service->register($request);
    }
    
    public function testLoginNotFound()
    {
        $this->expectException(ValidationException::class);
        
        $request = new UserLoginRequest();
        $request->id = 'raka';
        $request->password = '12345';

        $this->user_service->login($request);
    }
    public function testLoginWrongPassword()
    {
        $user = new User();
        $user->id = 'raka';
        $user->name = 'Raka';
        $user->password = password_hash('12345',PASSWORD_BCRYPT);

        $this->expectException(ValidationException::class);
    
        $request = new UserLoginRequest();
        $request->id = 'raka';
        $request->password = 'raka';
    
        $this->user_service->login($request);
    }
    public function testLoginSuccess()
    {
        $user = new User();
        $user->id = 'raka';
        $user->name = '12345';
        $user->password = password_hash('12345',PASSWORD_BCRYPT);

        $this->expectException(ValidationException::class);
    
        $request = new UserLoginRequest();
        $request->id = 'raka';
        $request->password = '12345';
    
        $response = $this->user_service->login($request);

        self::assertEquals($request->id, $response->user->id);
        self::assertTrue(password_verify($request->password, $response->user->password));
    }
}

?>