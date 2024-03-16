<?php

namespace rakafebriansy\phpmvc\Core;

class View 
{
    public static function render(string $view, $model)
    {
        require __DIR__ . "/../View/header.php";
        require __DIR__ . "/../View/$view.php";
        require __DIR__ . "/../View/footer.php";
    }
}

?>