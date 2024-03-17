<?php
namespace rakafebriansy\phpmvc\Controller ;
require_once __DIR__ . '/../Helper/helper.php';
use PHPUnit\Framework\TestCase;
use rakafebriansy\phpmvc\Config\Database;
use rakafebriansy\phpmvc\Domain\Session;
use rakafebriansy\phpmvc\Domain\User;
use rakafebriansy\phpmvc\Repository\SessionRepository;
use rakafebriansy\phpmvc\Repository\UserRepository;
use rakafebriansy\phpmvc\Service\SessionService;

class UserControllerTest extends TestCase
{
    private UserController $user_controller;
    private UserRepository $user_repository;
    private SessionRepository $session_repository;

    public function setUp(): void
    {
        $this->user_controller = new UserController();

        
        $conn = Database::getConnection();
        $this->session_repository = new SessionRepository($conn);
        $this->session_repository->deleteAll();
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
        $this->expectOutputRegex('[X-RKFBRNS-SESSION: ]');
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
    public function testLogout()
    {
        $user = new User();
        $user->id = 'raka';
        $user->name = 'Raka';
        $user->password = password_hash('12345',PASSWORD_BCRYPT);
        $this->user_repository->save($user);

        $session = new Session();
        $session->id = uniqid();
        $session->user_id = $user->id;
        $this->session_repository->save($session);
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->user_controller->logout();

        $this->expectOutputRegex('[Location: /]');
        $this->expectOutputRegex('[X-RKFBRNS-SESSION: ]');
    }
    public function testUpdateProfile()
    {
        $user = new User();
        $user->id = 'raka';
        $user->name = 'Raka';
        $user->password = password_hash('12345',PASSWORD_BCRYPT);
        $this->user_repository->save($user);

        $session = new Session();
        $session->id = uniqid();
        $session->user_id = $user->id;
        $this->session_repository->save($session);
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->user_controller->updateProfile();

        $this->expectOutputRegex('[Profile]');
        $this->expectOutputRegex('[Id]');
        $this->expectOutputRegex('[raka]');
        $this->expectOutputRegex('[Name]');
        $this->expectOutputRegex('[Raka]');
    }
    public function testPostUpdateProfile()
    {
        $user = new User();
        $user->id = 'raka';
        $user->name = 'Raka';
        $user->password = password_hash('12345',PASSWORD_BCRYPT);
        $this->user_repository->save($user);

        $session = new Session();
        $session->id = uniqid();
        $session->user_id = $user->id;
        $this->session_repository->save($session);
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $_POST['name'] = 'Mimi';
        $this->user_controller->postUpdateProfile();

        $this->expectOutputRegex('[Location: /]');

        $result= $this->user_repository->findById('raka');
        self::assertEquals('Mimi', $result->name);
    }
    public function testUpdateProfileValidationError()
    {
        $user = new User();
        $user->id = 'raka';
        $user->name = 'Raka';
        $user->password = password_hash('12345',PASSWORD_BCRYPT);
        $this->user_repository->save($user);

        $session = new Session();
        $session->id = uniqid();
        $session->user_id = $user->id;
        $this->session_repository->save($session);
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $_POST['name'] = '';
        $this->user_controller->postUpdateProfile();

        $this->expectOutputRegex('[Profile]');
        $this->expectOutputRegex('[Id]');
        $this->expectOutputRegex('[raka]');
        $this->expectOutputRegex('[Name]');
        $this->expectOutputRegex('[Raka]');
        $this->expectOutputRegex('[Id and Name can\'t blank.]');
    }
    
    public function testUpdatePassword()
    {
        $user = new User();
        $user->id = 'raka';
        $user->name = 'Raka';
        $user->password = password_hash('12345',PASSWORD_BCRYPT);
        $this->user_repository->save($user);

        $session = new Session();
        $session->id = uniqid();
        $session->user_id = $user->id;
        $this->session_repository->save($session);
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->user_controller->updatePassword();

        $this->expectOutputRegex('[Password]');
        $this->expectOutputRegex('[Id]');
        $this->expectOutputRegex('[raka]');
    }
    public function testUpdatePasswordSuccess()
    {
        $user = new User();
        $user->id = 'raka';
        $user->name = 'Raka';
        $user->password = password_hash('12345',PASSWORD_BCRYPT);
        $this->user_repository->save($user);

        $session = new Session();
        $session->id = uniqid();
        $session->user_id = $user->id;
        $this->session_repository->save($session);
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $_POST['oldPassword'] = '12345';
        $_POST['newPassword'] = 'new';

        $this->user_controller->postUpdatePassword();

        $this->expectOutputRegex('[Location: /]');

        $result = $this->user_repository->findById($user->id);
        self::assertTrue(password_verify('new', $result->password));
    }
    public function testUpdatePasswordValidationError()
    {
        $user = new User();
        $user->id = 'raka';
        $user->name = 'Raka';
        $user->password = password_hash('12345',PASSWORD_BCRYPT);
        $this->user_repository->save($user);

        $session = new Session();
        $session->id = uniqid();
        $session->user_id = $user->id;
        $this->session_repository->save($session);
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $_POST['oldPassword'] = '';
        $_POST['newPassword'] = '';

        $this->user_controller->postUpdatePassword();

        $this->expectOutputRegex('[Password]');
        $this->expectOutputRegex('[Id]');
        $this->expectOutputRegex('[raka]');
        $this->expectOutputRegex('[Id, Old Password, and New Password can\'t blank.]');
    }
    public function testUpdatePasswordOldPasswordWrong()
    {
        $user = new User();
        $user->id = 'raka';
        $user->name = 'Raka';
        $user->password = password_hash('12345',PASSWORD_BCRYPT);
        $this->user_repository->save($user);

        $session = new Session();
        $session->id = uniqid();
        $session->user_id = $user->id;
        $this->session_repository->save($session);
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $_POST['oldPassword'] = 'salah';
        $_POST['newPassword'] = 'new';

        $this->user_controller->postUpdatePassword();

        $this->expectOutputRegex('[Password]');
        $this->expectOutputRegex('[Id]');
        $this->expectOutputRegex('[raka]');
        $this->expectOutputRegex('[Old password is wrong.]');
    }
}

?>