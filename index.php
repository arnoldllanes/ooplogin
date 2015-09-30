<?php 
require_once 'core/init.php';

//CALLS TO POP A MESSAGE ONCE AND DOES NOT SHOW AGAIN AFTER REFRESH
if(Session::exists('success')){
    echo Session::flash('success');
}


