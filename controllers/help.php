<?php
    class HelpController extends Controller {
        function Index(){
            $view = new View;
            $view->Render(
                $this->className(__CLASS__), 
                $this->className(__CLASS__), 
                null
            );
        }
    }