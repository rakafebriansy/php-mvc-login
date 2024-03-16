<?php

namespace rakafebriansy\phpmvc\Core;
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    public function testRender()
    {
        View::render('Home/index',['PHP Login Management']);

        $this->expectOutputRegex('[PHP Login Management]'); //regex string checking
        $this->expectOutputRegex('[html]'); //checking from the structures
        $this->expectOutputRegex('[body]');
        $this->expectOutputRegex('[Login]');
        $this->expectOutputRegex('[Register]');
    }
}

?>