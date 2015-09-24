<?php 
require_once 'core/init.php';

$user = Db::getInstance()->get('users', array('username', '=', 'arnold'));

if(!$user->count()){
    echo 'No user';
} else {
    echo 'OK!';
}