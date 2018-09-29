<?php
    class Notes {
        protected $db;
        private $user_id = 0;
        
        function __construct($id){
            $this->db = new Database();
            $this->user_id = $id;
        }
        
        public function get(){
            return $this->db->query(
                QBuilder::new()
                ->select('*')
                ->from('notes')
                ->where(['user_id' => $this->user_id])
            )->getAll();
        }
        
        public function create($id = 0){
            $date = date('Y-m-d');
            $hours = date('H:i:s');
            $title = htmlspecialchars($_POST['title']);
            $title = $this->db->getDb()->real_escape_string($title);
            $note = nl2br($_POST['note']);
            $note = $this->db->getDb()->real_escape_string($note);
            $done = (isset($_POST['done']) && $_POST['done'] == 'yes') ? 'yes' : 'no';
            
            return $this->db->query(
                QBuilder::new()->insert('notes', [
                    'id' => null,
                    'date' => $date,
                    'hours' => $hours,
                    'title' => $title,
                    'note' => $note,
                    'user_id' => $this->user_id,
                    'done' => $done
                ])
            );

        }      
        
        public function update($id){
            if(!is_numeric($id)) return false; 
            $title = htmlspecialchars($_POST['title']);
            $title = $this->db->getDb()->real_escape_string($title);
            $note = nl2br($_POST['note']);
            $note = $this->db->getDb()->real_escape_string($note);
            $done = (isset($_POST['done']) && $_POST['done'] == 'yes') ? 'yes' : 
            'no';
            
            return $this->db->query(
                QBuilder::new()
                ->update('notes')
                ->set([
                    'title' => $title,
                    'note' => $note,
                    'user_id' => $this->user_id,
                    'done' => $done
                ])
                ->where(['id' => $id])
            );
        }
        
        public function delete($id){
            return $this->db->query(
                QBuilder::new()->delete('notes')
                ->from('notes')
                ->where(QBuilderExpr::new()->andX([
                    'id' => $id, 
                    'user_id' => $this->user_id
                    ])
                )
            );
        }
        
    }