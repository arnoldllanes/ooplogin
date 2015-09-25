<?php 
require_once 'core/init.php';

$user = Db::getInstance()->update('users', 2, array(
    'password' => 'newpassword',
    'name'     => 'Arnold Llanes'
));


