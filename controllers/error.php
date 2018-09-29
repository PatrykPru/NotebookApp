<?php
    class HelpController extends Controller {
        function __construct(){
            $view = new View;
            $view->Render(__CLASS__, null);
        }
    }