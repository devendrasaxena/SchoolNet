<?php
include_once('header/lib.php');
 $regObj = new registrationController();
	
if($_POST['action'] == 'resend_otp'){
	$regArr=$_POST['reg'];
	
   $arr1 = $regObj->regGenerateOTP($regArr);
	  if($arr1['status'] == 1){
		  $_SESSION['REGISTRATION'] = array();
		  $_SESSION['REGISTRATION']=$regArr;
		  $_SESSION['expires_on']=$arr1['expires_on'];
		  $_SESSION['success']=1;
		   header('location:activation.php');
		   exit();

	  }else{
			$_SESSION['REGISTRATION']['FIELDS'] = $_POST;
			$_SESSION['REGISTRATION']['ERR']['MSG'] = $arr1['msg'];
			$_SESSION['error']=0;
			header('Location:activation.php');
			die;
	  } 
}

?>