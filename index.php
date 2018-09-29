<?php
    require 'libs/Bootstrap.php';
    require 'libs/View.php';
    require 'libs/Controller.php';
    require 'libs/Database.php';
    require 'libs/QBuilder.php';
    require 'libs/QBuilderExpr.php';
    require 'libs/QBuilderType.php';

    function __autoload($classname){
        $filename = 'models/'.$classname.'.php';
        include $filename;
    }

    $app = new Bootstrap;