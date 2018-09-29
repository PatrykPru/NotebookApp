<?php
    class QBuilderExpr extends QBuilder {
        public $expr = ' ';
        public $clause = NULL;
        
        public function __toString(){
            return $this->expr;
        }
        
        public static function new(){
            return new self();
        }
        
        public function expr($name, $value, $oper, $c1 = '`', $c2 = '\''){
            $this->expr = parent::wrap($name, $c1, $c1).' '.$oper.' '.parent::wrap($value,$c2,$c2);
            return $this->expr;
        }
        
        public function not($expr){
            $this->expr = 'NOT '.$expr;
            $this->clause = 'NOT';
            return $this;
        }
        
        public function eq($name, $value){
            return $this->expr($name, $value, '=');
        }     
        
        public static function neq($name, $value){     
           return $this->not(self::eq($name, $value));   
        }
        
        public static function lt($name, $value){
            return $this->expr($name, $value, '<');
        } 
        
        public function lte($name, $value){
            return $this->expr($name, $value, '<=');;
        } 
        
        public function gt($name, $value){
            return $this->expr($name, $value, '>');
        } 
        
        public function gte($name, $value){
            return $this->expr($name, $value, '>=');
        } 
        
        public function inull($name){
            $this->expr = $this->expr($name, 'NULL', 'IS', '`', '');
            $this->clause = 'IS NULL';
            return $this;
        }
        
        public function innull($name){
            $this->expr = $this->expr($name, 'NOT NULL', 'IS', '`', '');
            $this->clause = 'IS NOT NULL';
            return $this;
        }
        
        public function like($name, $value){
            $this->expr = $this->expr($name, $value, 'LIKE');
            $this->clause = 'LIKE';
            return $this;
        } 
        
        public function nlike($name, $value){
            $this->expr = $this->expr($name, $value, 'NOT LIKE');
            $this->clause = 'NOT LIKE';
            return $this;
        } 
        
        public function btwn($name, $value1, $value2, $wrap = '`'){
            $this->expr = $this->expr($name, self::wrap($value1).' AND '.self::wrap($value2), 'BETWEEN', $wrap, '');
            $this->clause = 'BETWEEN';
            return $this;
        } 
        
        public function in($name, $data){
            $this->expr = QBuilder::wrap($name).' ';
            if(is_array($data)){          
                $this->expr .= 'IN '.QBuilder::wrap(QBuilder::obo($data),'(',')');
                $this->clause = 'IN';
                return $this;
            }
            elseif(is_string($data)){
                $this->expr = 'IN '.QBuilder::wrap($data,'(',')');
                $this->clause = 'IN';
                return $this;
            }
            else {
                throw new Exception('Method '.__FUNCTION__.' get a invalid data type on line: '.__LINE__);
            }
        }
        
        public function nin($name, $data){
            $this->expr = QBuilder::wrap($name).' ';
            if(is_array($data)){          
                $this->expr .= 'NOT IN '.QBuilder::wrap(QBuilder::obo($data),'(',')');
                $this->clause = 'NOT IN';
                return $this;
            }
            elseif(is_string($data)){
                $this->expr = 'NOT IN '.QBuilder::wrap($data,'(',')');
                $this->clause = 'NOT IN';
                return $this;
            }
            else {
                throw new Exception('Method '.__FUNCTION__.' get a invalid data type on line: '.__LINE__);
            }
        }
            
        private function X(Array $data, $opr){
            $output = '';
            $clear = [];
            foreach($data as $name => $value){
                if(is_string($name))
                    $clear[] = $this->eq($name, $value);
                if(is_numeric($name)){
                    $clear[] = (!is_null($value->clause)) ? parent::wrap((string)$value, '(',')') : (string)$value;
                }
            }
            $output = $this->obo($clear,' '.$opr.' ', '');
            return $output;
        }
        
        public function andX(Array $data){
            $this->expr = $this->X($data, 'AND');
            if(count($data)>1) $this->clause = 'AND';
            return $this;
        } 
        
        public function orX(Array $data){
            $this->expr = $this->X($data, 'OR');
            if(count($data)>1) $this->clause = 'OR';
            return $this;
        }
    }