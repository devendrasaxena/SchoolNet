<?php  
require_once dirname(__FILE__) .'/header/lib.php'; 
//error_reporting(E_ALL);
//ini_set('display_errors',1);
if( isset($_POST['registration_form']) || isset($_POST['reg_email']) && $_POST['reg_email'] != ""){

	//echo "<pre>";print_r($_POST);exit;
  $regObj = new registrationController();
  $is_otp_based=0;

	 if($is_otp_based==1){
		  $arr1 = $regObj->regGenerateOTP($_POST);
		  if($arr1['status'] == 1){
			  $_SESSION['REGISTRATION'] = array();
			  $_SESSION['REGISTRATION']['FIELDS']=$_POST;
			  $_SESSION['REGISTRATION']['expires_on']=$arr1['expires_on'];
			  $_SESSION['REGISTRATION']['success']=1;
			   header('location:activation.php');
			   exit();

		  }else{
			    $_SESSION['REGISTRATION'] = array();
				$_SESSION['REGISTRATION']['FIELDS'] = $_POST;
				$_SESSION['REGISTRATION']['ERR']['MSG'] = $arr1['msg'];
				$_SESSION['reg_status']=0;
				header('Location:signup.php');
				die;
		  }
      }else{
		  	$is_email_verified=0;
			
			//$_POST['reg_email']=base64_decode($_POST['reg_email']);
			//$_POST['reg_name']=base64_decode($_POST['reg_name']);
			//$_POST['user_email']=base64_decode($_POST['user_email']);

			$arr = $regObj->register($is_email_verified,$is_otp_based,$_POST);
			$_SESSION['REGISTRATION'] = array();
			$_SESSION['REGISTRATION']['ERR'] = array();
			$_SESSION['REGISTRATION']['SUCCESS'] = array();
		   // echo "<pre>";print_r($arr);exit;
		   if($arr['status'] == 0){
				
				$_SESSION['REGISTRATION']['FIELDS'] = $_POST;
				$_SESSION['REGISTRATION']['ERR']['MSG'] = $arr['msg'];
				$_SESSION['reg_status']=0;
				header('Location:signup.php');
				die;
			}else if($arr['status'] == 1){ //success start
				$_SESSION['REGISTRATION']['FIELDS'] = $_POST;
				$_SESSION['REGISTRATION']['SUCCESS']['MSG'] = $arr['msg'];
				$username=$_POST['reg_email'];
				$password=$_POST['reg_password'];
				$_SESSION['reg_email']=$username;
				$_SESSION['reg_password']=$password;
				$_SESSION['reg_status']=1;
				$_SESSION['user_id']=$arr['user_id'];
				$_SESSION['token']=$arr['token'];
				header('location:do-registration_login.php');
				exit();//success end
			 }else{
				$_SESSION['error']=1;
				header('location:signup.php');
				exit();
				//$hide="displayNone";
		   } 

	  
   }	 
}

