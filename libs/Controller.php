<?php
    class Controller {
        
        function __construct(){
            
        }
        
        protected function className($string){
            $class = preg_replace('/Controller/', '', $string);
            return $class;
        }
        
    }