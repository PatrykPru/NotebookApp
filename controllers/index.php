<?php
    class IndexController extends Controller {
        
        public function index(){
            $user = new UserService();
            if($user->checkSession()){
                header('Location: /todolist/index/notes/');
            } else {
                $this->welcome();
            }
        }
        
        public function welcome(){
            $view = new View;
            
            $view->render($this->className(__CLASS__), 'index', NULL);
        }
        
        public function signin(){
            $user = new UserService();
            if(!$user->checkSession()){
                $user->signIn($_POST['login'], $_POST['password']);
            }
            header('Location: /todolist/index/');
        }
        
        public function register(){
            $user = new UserService();
            $msg = $user->create($_POST['name'], $_POST['login'], $_POST['password'], $_POST['secpassword'], $_POST['email']);
            if($msg !== true){
                $view = new View();
                $view->render('index', 'index', [ 'msg' => $msg ]);
            } else {
                $view = new View();
                $view->render('index', 'index', [ 'msg' => 'User account was created.' ]);
            }
        }
        
        public function create(){
            $user = new UserService();
            if($user->checkSession()){
                $note = new Notes($user->get()['id']);
                $note->create();
            }
        }
        
        public function update($id){
            $user = new UserService();
            if($user->checkSession()){
                $note = new Notes($user->get()['id']);
                $note->update($id);
            }
        }
        
        public function delete($id){
            $user = new UserService();
            if($user->checkSession()){
                $note = new Notes($user->get()['id']);
                $note->delete($id);
            }
        }
        
        public function getjson(){
            $user = new UserService();
            if($user->checkSession()){
                $db = new Database();
                $result = $db->query(QBuilder::new()
                          ->select('*')
                          ->from('notes')
                          ->where(['user_id' => $user->get()['id']])
                          ->order(['date' => 'DESC', 'hours'  => 'DESC'])
                    )->getAll();
                echo json_encode($result);
            }
        }
        
        public function notes(){
            $user = new UserService();
            if(!$user->checkSession()) header('Location: /todolist/index/welcome/'); 
            $view = new View;
            
            $view->Render($this->className(__CLASS__), __FUNCTION__, NULL);
        }
        
        public function signout(){
            $user = new UserService();
            $user->checkSession();
            $user->signOut();
            header('Location: /todolist/index/');
        }
        
    }