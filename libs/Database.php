<?php 
    class Database {
        private $mysqli = NULL;
        private $result = NULL;
        
        function __construct(){
            require 'config/database.php';
            $mysqli = new mysqli($servername, $username, $password);
            if($mysqli->connect_error){
                die('Connection failed: '.$this->mysqli->connect_error);
            }
            $mysqli->select_db($databasename);
            $this->mysqli = $mysqli;
        }
        
        public function getResult(){
            return $this->result;
        }
        
        public function query($query){
            if(is_string($query)){
                $this->result = $this->mysqli->query($query);
                return $this;
            }
            if(is_object($query) && get_class($query) == 'QBuilder'){
                $this->result = $this->mysqli->query($query->generate());
                return $this;
            }
            throw new Exception('Query method didnt get a right data type.');
        }
        
        public function getSingle(){
            if(!is_object($this->result)) return false;
            return $this->result->fetch_array(MYSQLI_ASSOC);
        }
        
        public function getAll(){
            $data = [];
            if(!is_object($this->result)) return false;
            while($row = $this->result->fetch_array(MYSQLI_ASSOC)){
                $data[] = $row;
            }
            return $data;
        }
        
        public function getDB(){
            return $this->mysqli;
        }
        
        function __destruct(){
            $this->mysqli->close();
        }
    }