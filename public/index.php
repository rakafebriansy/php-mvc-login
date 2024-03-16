<?php
require_once __DIR__ . '/../vendor/autoload.php';

use rakafebriansy\phpmvc\Core\Router;
use rakafebriansy\phpmvc\Controller\HomeController;

Router::add('GET', '/', HomeController::class, 'index');

Router::run();
?>