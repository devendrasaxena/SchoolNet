<?php
include 'config.php';
// $_html_relative_path;exit;
session_start();
 if($_POST['action'] == 'skip_test'){
	 
	$is_skip=$_POST['is_skip'];
	
	echo json_encode( array('status' => 1, 'res' => $_SESSION['is_skip']));
   die;  
	
}
$_SESSION['is_skip']=1;
header('location:'.$globalLink.'/product.php');


?>