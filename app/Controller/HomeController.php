<?php

namespace rakafebriansy\phpmvc\Controller;
use rakafebriansy\phpmvc\Core\View;

class HomeController
{
    function index(): void 
    {
        View::render('Home/index',[
            'title' => 'Home'
        ]);
    }
}

?>