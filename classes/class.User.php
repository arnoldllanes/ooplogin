<?php
class User {
    private $_db;
    
    public function __construct($user = null){
        //CONNECT TO DATABASE
        $this->_db = Db::getInstance();
    }
    //CREATE A USER
    public function create($fields){
        if($this->_db->insert('users', $fields)){
           throw new Exception('There was a problem creating a new account');
            
        }
    }
}