<!DOCTYPE html>
<html>
    <head>
        <title>
            To Do List
        </title>
        
        <!--Import Google Icon Font-->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <!--Import materialize.css-->
        <link type="text/css" rel="stylesheet" href="/todolist//css/materialize.min.css"  media="screen,projection"/>
        <!--JQuery-->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
                
        <!--Let browser know website is optimized for mobile-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        
        <style>
            td {
                padding: 3px;
            }
            
            .inline-icon {
               vertical-align: bottom;
               font-size: 20px !important;
            }
            
            .images {
                display: inline-block;
            }
            
            .attachment {
                display: flex;
                flex-wrap: wrap;
            }
            
            .attachment * {
                display: flex;
                max-height: 8rem;
                max-width: 8rem;
                margin: 1rem;
            }
            
            .attachment img {
                background-position: center;
                background-size: contain;
            }
            
            .drop-area {
                display: flex;
                text-align: center;
                border: 3px dotted #aaa;
                height: 8rem;
                width: 8rem;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .drop-area:hover, .drop-area.active {
                border: 3px dotted #2196f3;
            }
            
            .drop-area:hover *, .drop-area.active * {
                color: #2196f3 !important;
            }
        </style>
        
    </head>

    <body>
    