<?php

class Bootstrap {
    function __construct(){
        $url = isset($_GET['url']) ? $_GET['url'] : 'index';
        $url = rtrim($url, '/ ');
        $url = explode('/', $url);
        
        if($url){       
            $controller_adr = 'controllers/'.$url[0].'.php';
            if(file_exists($controller_adr)){         
                require $controller_adr;
                $class = $url[0].'Controller';
                $controller = new $class;
                if(isset($url[1]) && method_exists($controller, $url[1])){
                    if(isset($url[2])){     
                        $controller->{$url[1]}($url[2]);                   
                        return;
                    }
                    if(isset($url[1])){
                        $controller->{$url[1]}();
                        return;
                    }
                   
                }
            $controller->index();
            } else {
                echo 'Error';
            }
        }
    }
}