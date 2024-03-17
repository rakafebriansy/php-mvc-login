<?php

namespace rakafebriansy\phpmvc\Core 
{
    function header(string $value) {
        echo $value;
    }
}

namespace rakafebriansy\phpmvc\Service 
{
    function setcookie(string $name, string $value) {
        echo "$name: $value";
    }
}

?>