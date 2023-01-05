<?php
$_html_relative_path='../../';//dirname(dirname(__FILE__)).'/';
$_html_relative_path = isset($_html_relative_path) ? $_html_relative_path : '';
// $_html_relative_path;exit;
include_once($_html_relative_path.'header/lib.php');


$email=$_POST['email'];
$adminObj = new centerAdminController();
$resEmail=$adminObj->checkEmailExits($email);	
//echo "<pre>";print_r($resEmail);//exit;
$status=0;
if($resEmail) {
	if(isset($resEmail['user_id']) && $resEmail['user_id']!='' && $resEmail['loginid']!=''){
		// case : user is already registered
		$status=1;
		 echo $status;
		 die();
	 }else{
		 $status=0;
	    echo $status;
	    die();
	  }
}else{ 
     $status=0;
	 echo $status;
	 die();
}
?>