<?php

namespace rakafebriansy\phpmvc\Core 
{
    function header(string $value) {
        echo $value;
    }
}

namespace rakafebriansy\phpmvc\Controller 
{

    use PHPUnit\Framework\TestCase;
    use rakafebriansy\phpmvc\Config\Database;
    use rakafebriansy\phpmvc\Domain\User;
    use rakafebriansy\phpmvc\Repository\UserRepository;

    class UserControllerTest extends TestCase
    {
        private UserController $user_controller;
        private UserRepository $user_repository;

        public function setUp(): void
        {
            $this->user_controller = new UserController();
        
            $conn = Database::getConnection();
            $this->user_repository = new UserRepository($conn);
            $this->user_repository->deleteAll();

            putenv('mode=test');
        }
        public function testRegister()
        {
            $this->user_controller->register();
        
            $this->expectOutputRegex('[Register new User]');
            $this->expectOutputRegex('[Register]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Name]');
            $this->expectOutputRegex('[Password]');
        }
        public function testPostRegisterSuccess()
        {
            $_POST['id'] = 'raka';
            $_POST['name'] = 'Raka';
            $_POST['password'] = '12345';
            
            $this->user_controller->postRegister();

            $this->expectOutputRegex('[Location: /users/login]');
        }
        public function testPostRegisterValidationError()
        {
            $_POST['id'] = '';
            $_POST['name'] = 'Raka';
            $_POST['password'] = '12345';
            
            $this->user_controller->postRegister();

            $this->expectOutputRegex('[Register new User]');
            $this->expectOutputRegex('[Register]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Name]');
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Id, Name, and Password can\'t blank.]');
        }
        public function testPostRegisterDuplicate()
        {
            $user = new User();
            $user->id = 'raka';
            $user->name = 'Raka';
            $user->password = '12345';

            $this->user_repository->save($user);

            $_POST['id'] = 'raka';
            $_POST['name'] = 'Raka';
            $_POST['password'] = '12345';
            
            $this->user_controller->postRegister();
        
            $this->expectOutputRegex('[Register new User]');
            $this->expectOutputRegex('[Register]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Name]');
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[User already exists.]');
        }

        public function testLogin()
        {
            $this->user_controller->login();

            $this->expectOutputRegex('[Login user]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Password]');
        }
        public function testLoginSuccess()
        {
            $user = new User();
            $user->id = 'raka';
            $user->name = 'Raka';
            $user->password = password_hash('12345',PASSWORD_BCRYPT);

            $this->user_repository->save($user);

            $_POST['id'] = 'raka';
            $_POST['password'] = '12345';

            $this->user_controller->postLogin();
    
            $this->expectOutputRegex('[Location: /]');
        }
        public function testLoginValidationError()
        {
            $_POST['id'] = '';
            $_POST['password'] = '';

            $this->user_controller->postLogin();

            $this->expectOutputRegex('[Login user]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Id and Password can\'t blank.]');
        }
        public function testLoginNotFound()
        {
            $_POST['id'] = 'notfound';
            $_POST['password'] = 'notfound';

            $this->user_controller->postLogin();

            $this->expectOutputRegex('[Login user]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Id or password is wrong.]');
        }
        public function testLoginWrongPassword()
        {
            $user = new User();
            $user->id = 'raka';
            $user->name = 'Raka';
            $user->password = password_hash('12345',PASSWORD_BCRYPT);

            $this->user_repository->save($user);

            $_POST['id'] = 'raka';
            $_POST['password'] = 'notfound';

            $this->user_controller->postLogin();

            $this->expectOutputRegex('[Login user]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Id or password is wrong.]');
        }
    }
}

?>