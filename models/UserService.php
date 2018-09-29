<?php
    class UserService {
        private $db;
        public $user = false;
        const PREFIX = 'TODO...';
        
        function __construct(){        
            session_start();
            $this->db = new Database();
        }
        
        private function validate($name, $col){
            if(empty($name)) return false;
            $result = $this->db->query(
                QBuilder::new()
                ->select(QBuilder::func('id', 'count', 'quantity'))
                ->from('users')
                ->where([$col => $name])
            )->getSingle();
            return !$result['quantity'];
        }
        
        public function create($name, $login, $password, $secpassword, $email){
            $password = empty($password) ? '' : md5(self::PREFIX.$password);
            $secpassword = md5(self::PREFIX.$secpassword);
            
            if(!self::validate($login, 'login')){return 'Login is not available.';}
            if(!self::validate($name, 'name')){return 'Name is not available';}
            
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                return 'Check the correctness of the email/';
            }
            
            if($password != $secpassword || empty($password)) {
                return 'The password is not the same.';
            }
            
            return $this->db->query(QBuilder::new()
                                   ->insert('users', [
                                       'id' => NULL,
                                       'login' => $login,
                                       'password' => $password,
                                       'email' => $email,
                                       'name' => $name,
                                       'created_at' => date('Y-m-d H:i:s')
                                   ])
                    )->getResult();
        }
        
        public function get(){
            return $this->user;
        }
        
        private function extendTime($session_id){
            return $this->db->query(QBuilder::new()
                            ->update('userssession')
                            ->set([
                                'delete_at' => date("Y-m-d H:i:s", strtotime("+5 minutes"))
                            ])
                            ->where(['id' => $session_id])
                        );
        }
        
        public function checkSession(){
            if(!empty($_SESSION['hash'])){
                $hash = $_SESSION['hash'];
                
                $session = $this->db->query(
                    QBuilder::new()
                    ->select('id')
                    ->from('userssession')
                    ->where(QBuilderExpr::new()->andX([
                        'hash' => $hash,
                        QBuilderExpr::new()->btwn(date('Y-m-d H:i:s'), 'created_at', 'delete_at', '\'') 
                    ]))
                );
                
                $session = $session->getSingle();
                
                if($session){
                    $user = $this->db->query(
                        QBuilder::new()
                        ->select('*')
                        ->from('users')
                        ->where(['session_id' => $session['id']])
                    )->getSingle();
                    if($user){
                        $this->extendTime($session['id']);
                        $this->user = $user;
                        return true;
                    }
                }        
            }
            return false;
        }
        
        public function signIn($login, $password){
            if($this->checkSession()) return true;
            $login = $this->db->getDb()->real_escape_string($login);
            $password = md5(self::PREFIX.$password);
    
            $user = $this->db->query(
                QBuilder::new()
                ->select('*')
                ->from('users')
                ->where(QBuilderExpr::new()->andX([
                    'login' => $login,
                    'password' => $password
                ]))
            )->getSingle();
            
            if($user) {
                $this->user = $user;
                $hash = md5(self::PREFIX.date('Y-m-d?H:i:s'));
                $hash = substr($hash, 0, 12);
                
                $this->db->query(
                    QBuilder::new()
                    ->insert('userssession',[
                        'id' => NULL,
                        'user_id' => $user['id'],
                        'created_at' => date("Y-m-d H:i:s"),
                        'delete_at' => date("Y-m-d H:i:s", strtotime("+5 minutes")),
                        'hash' => $hash
                    ])
                );
                
                $session = $this->db->query(
                    QBuilder::new()
                    ->select('id')
                    ->from('userssession')
                    ->where(['hash' => $hash])
                )->getSingle();

                $this->db->query(
                    QBuilder::new()
                    ->update('users')
                    ->set(['session_id' => $session['id']])
                    ->where(['id' => $user['id']])
                );
                
                $_SESSION['hash'] = $hash;
                return true;
            }
        }
        
        public function signOut(){
            if($this->user){
                $this->db->query(
                    QBuilder::new()
                    ->update('users')
                    ->set(['session_id' => NULL])
                    ->where(['id' => $this->user['id']])
                );
                $this->user = false;
            }
        }
    }