<?php
require_once __DIR__ . '/../vendor/autoload.php';

use rakafebriansy\phpmvc\Config\Database;
use rakafebriansy\phpmvc\Core\Router;
use rakafebriansy\phpmvc\Controller\HomeController;
use rakafebriansy\phpmvc\Controller\UserController;

// Database::getConnection('prod');

Router::add('GET', '/', HomeController::class, 'index');

Router::add('GET', '/users/register', UserController::class, 'register');
Router::add('POST', '/users/register', UserController::class, 'postRegister');
Router::add('GET', '/users/login', UserController::class, 'login');
Router::add('POST', '/users/login', UserController::class, 'postLogin');

Router::run();
