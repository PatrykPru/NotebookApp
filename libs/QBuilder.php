<?php
    
    class QBuilder {
        private $type;
        private $data = [];
        
        // Function to wrap text
        /**
        *   @parm $charFirst
        *   @parm $charSecond
        */
        protected static function wrap($string, $charFirst = '`', $charSecond = '`'){
            if(is_string($string))
                return $charFirst.$string.$charSecond;
            elseif(is_null($string))
                return 'NULL';
            else
                return $string;
        }
        
        // Write one by one 
        protected static function obo($data, $sepr = ', ', $wrap = '`'){
            $output = '';
            if(is_array($data)){
                foreach($data as $name => $value){
                    if(is_null($value)){
                        $output .= 'NULL'.$sepr;
                        continue;
                    }
                    if(!self::hasKeyWord((string)$value,['AS']))
                        $output .= self::wrap($value, $wrap, $wrap).$sepr;
                    else
                        $output .= self::wrap($value, '', '').$sepr;
                }
            } else {
                throw new Exception('Function '.__FUNCTION__.' need a array data.');
            }
            $output = rtrim($output, $sepr);
            return $output;
        }
        
        protected function hasKeyWord($string, $pattern){
            foreach($pattern as $name => $value){
                if(strpos($string, $value) !== false)
                    return true;
            }
            return false;
        }
        
        public static function new(){
            return new self();
        }
        
        public static function as($name, $alias){
            return self::wrap($name).' AS '.self::wrap($alias);
        }
        
        public static function func($name, $func, $alias = false){
            if($alias)
                return strtoupper($func).'('.self::wrap($name).') AS '.self::wrap($alias);
            else
                return strtoupper($func).'('.self::wrap($name).')';
        }
        
        public function select($select){
            $this->data = [];
            $this->type = QBuilderType::SELECT;
            if(is_null($select)){
                $this->data['select'] = ['*'];
                return $this;
            }
            if(is_string($select)){
                $this->data['select'] = [$select];
                return $this;
            }
            if(is_array($select)){
                $this->data['select'] = $select;
                return $this;
            }
            throw new Exception("Invalid data types. Must be null, string or array.");
        }
        
        public function distinct(){
            $this->data = [];
            $this->data['distinct'] = 'DISTINCT';
            return $this;
        }
        
        public function update($update){
            $this->data = [];
            $this->type = QBuilderType::UPDATE;
            $this->data['update'] = $update;
            
            return $this;
        }
        
        public function insert($where, $insert){
            $this->data = [];
            $this->type = QBuilderType::INSERT;
            if(!is_string($where)) throw new Exception("Invalid data type. Must be string.");
            $this->data['where'] = $where;
            if(!is_array($insert)) throw new Exception("Invalid data type. Must be array.");
            $this->data['insert'] = $insert;
            
            return $this;
        }
        
        public function delete(){
            $this->type = QBuilderType::DELETE;
            
            return $this;
        }
        
        public function from($from){
            if(is_string($from)){
                $this->data['from'] = [$from];
                return $this;
            }
            if(is_array($from)){
                $this->data['from'] = $from;
                return $this;
            }
            throw new Exception("Invalid data types. Must be string or array.");
        }
        
        public function set($set){
            if(!is_array($set)) throw new Exception("Invalid data types. Must be array.");
            $this->data['set'] = $set;
            
            return $this;
        }
        
        public function where($where){
            if(is_object($where) || is_string($where))
                $this->data['where'] = $where;
            if(is_array($where))
                foreach($where as $name => $value){
                    $this->data['where'] = self::wrap($name).' = '.self::wrap($value, '\'', '\'');
                }
            
            return $this;
        }
        
        public function order($order){
            if(is_array($order)){
                $this->data['order'] = $order; 
            } elseif(is_string($order)) {
                $this->data['order'] = [$order => 'ASC'];
            } else {
                throw new Exception("Invalid data type.");
            }
            return $this;
        }
        
        public function limit($amount, $offset = 0){
            $this->data['limit'] = [$amount, $offset];
            
            return $this;
        }
        
        public function generate(){
            $sql = '';
            switch($this->type){
                    
                case QBuilderType::SELECT:
                    $sql .= 'SELECT';
                    if(isset($this->data['distinct']))
                        $sql .= ' DISTINCT';
                    if(isset($this->data['select']))
                        $sql .= (self::obo($this->data['select']) == '`*`') 
                        ? ' *' 
                        : ' '.self::obo($this->data['select']);
                    if(isset($this->data['from']))
                        $sql .= ' FROM '.self::obo($this->data['from']);
                    if(isset($this->data['where']))
                        $sql .= ' WHERE '.$this->data['where'];
                    if(isset($this->data['order'])){
                        $sql .= ' ORDER BY ';
                        $order = [];
                        foreach($this->data['order'] as $name => $value){
                            $order[] = self::wrap($name).' '.$value;
                        }
                        $sql .= self::obo($order, ', ', '');
                    }
                    if(isset($this->data['limit'])){
                        $sql .= ' LIMIT';
                        if($this->data['limit'][1] > 0)
                            $sql .= ' '.$this->data['limit'][1].',';
                        $sql .= ' '.$this->data['limit'][0];
                    }
                    break;
                    
                case QBuilderType::UPDATE:
                    $sql .= 'UPDATE ';
                    if(isset($this->data['update']))
                        $sql .= self::wrap($this->data['update']);
                    if(isset($this->data['set'])){
                        $sql .= ' SET ';
                        $set = [];
                        foreach($this->data['set'] as $name => $value){
                            $line = self::wrap($name);
                            $line .= ' = ';
                            $line .= self::wrap($value,'\'','\'');
                            $set[] = $line;
                        }
                        $sql .= self::obo($set, ', ', '');
                    }
                    if(isset($this->data['where']))
                        $sql .= ' WHERE '.$this->data['where'];
                    break;
                    
                case QBuilderType::INSERT:
                    $sql .= 'INSERT INTO';
                    if(isset($this->data['where']))
                        $sql .= ' '.self::wrap($this->data['where']);
                    if(isset($this->data['insert'])){
                        $names = [];
                        $values = [];
                        foreach($this->data['insert'] as $name => $value){
                            $names[] = $name;
                            $values[] = $value;
                        }
                        $sql .= '('.self::obo($names).') VALUES ('.self::obo($values, ',', '\'').')';
                    }
                    break;
                    
                case QBuilderType::DELETE:
                    $sql .= 'DELETE';
                    if(isset($this->data['from']))
                        $sql .= ' FROM '.self::obo($this->data['from']);
                    if(isset($this->data['where']))
                        $sql .= ' WHERE '.$this->data['where'];
                    break;
            }
            return $sql;
        }
    }