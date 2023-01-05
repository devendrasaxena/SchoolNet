<?php

require_once('config/config.php');
require_once('controller/commonController.php');

$con = check_internet_connection();

$arr = array('status' => 0);
if( $con ){
    $arr['status'] = 1;
}

echo json_encode($arr);
die;