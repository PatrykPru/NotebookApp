<?php
    class View {
        public function render($controller, $method, $data){
            include 'views/'.$controller.'/'.$method.'.php';
        }
    }