<?php
class Db {
    private static $_instance = null;
    private $_pdo, 
            $_query, 
            $_error = false, 
            $_results, 
            $_count = 0;
    
    private function __construct(){
        try {
            $this->_pdo = new PDO('mysql:host=' . Config::get('mysql/host') . ';dbname=' . Config::get('mysql/db'), Config::get('mysql/username'), Config::get('mysql/password'));
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
     
    public static function getInstance(){
        if(!isset(self::$_instance)){
            self::$_instance = new Db();
        }
        return self::$_instance;
    }
    
    //PROVIDE ABILITY TO DYNAMICALLY ESTABLISH SECURITY TO QUERIES
    public function query($sql, $params = array()){
    //SET THE ERROR TO FALSE TO MAKE SURE WE DONT RETURN ERRORS FROM A PREVIOUS QUERY
        $this->_error = false;
        if($this->_query = $this->_pdo->prepare($sql)){
            $x =1;
            if(count($params)){
                foreach($params as $param){
    //POSITION AND PARAMS
                    $this->_query->bindValue($x, $param);
                    $x++;
                }
            }
            if($this->_query->execute()){
    //STORE THE RESULT SET
                $this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
                $this->_count = $this->_query->rowCount();
            } else {
                $this->_error = true;
            }
        }
        
        return $this;
    }
    
    public function action($action, $table, $where = array()){
    //CHECK THE COUNT OF $WHERE IS === 3 // WE NEED A FIELD, OPERATOR, VALUE
        if(count($where) === 3){
            $operators = array('=', '>', '<' , '>=', '<=');
    
            $field = $where[0];
            $operator = $where[1];
            $value = $where[2];
            
            if(in_array($operator, $operators)){
                $sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";
                if(!$this->query($sql, array($value))->error()){
                    return $this;
                }
            }
        }
        return false;
    }
    // GET FROM TABLE WHERE X = X
    public function get($table, $where){
        return $this->action('SELECT *', $table, $where);
    }
    //DELETE FROM TABLE WHERE X = X
    public function delete($table, $where){
        return $this->action('DELETE', $table, $where);
    }
    
    //INSERT QUERY METHOD
    public function insert($table, $fields = array()){
            $keys = array_keys($fields);
            $values = '';
            $x = 1;
            
            foreach($fields as $field){
                $values .= '?';
            
                if($x < count($fields)){
                    $values .= ', ';
                }
                $x++;
            }

            $sql = "INSERT INTO users (`" . implode('`, `', $keys) . "`) VALUES ({$values})";
            
            if(!$this->query($sql, $fields)->error()){
                return true;
            }
        
        return false;
    }
    
    //UPDATE QUERY METHOD 
    public function update($table, $id, $fields){
        $set = '';
        $x = 1;
        
        foreach($fields as $name => $value){
            $set .= "{$name} = ?";
            if($x < count($fields)){
                $set .= ', ';
            }
            $x++;
        }
        
        $sql = "UPDATE {$table} SET {$set} WHERE id = {$id}";
        
        if(!$this->query($sql, $fields)->error()){
            return true;
        }
        return false;
    }
    
    //RESULTS OF LOGIN USER
    public function results(){
        return $this->_results;
    }
    //RETURN FIRST RESULT FROM QUERY 
    public function first(){
        return $this->results()[0];
    }
    
    public function error(){
        return $this->_error;
    }
    
    public function count(){
        return $this->_count;
    }
    
}

