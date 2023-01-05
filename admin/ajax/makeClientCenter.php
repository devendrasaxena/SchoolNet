<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors','1');
include_once('../../header/lib.php');



 $centerObj = new centerController();

 if(!empty($_SESSION['center_dev'])){ 

 //echo "<pre>";print_r($_SESSION['center_dev']); exit;
   $obj =$_SESSION['center_dev'];
   $code =$obj->code;
   $email_id =$obj->email_id;
   $password =$obj->password;
    $centerAdminName=$obj->user_full_name;
 //echo $code;exit;
	$_SESSION['center_code'] =$code;
	$_SESSION['email_id'] =$email_id;
	$_SESSION['password'] =$password;
	require_once('../../mail_send_template.php');	//Sending crdential in email	
	
   header("location:../registerSuccess.php");
   exit;
  }else{
	  $_SESSION['error'] = 0;
	header("location:../createCenter.php?error");
	exit;
 }

?>