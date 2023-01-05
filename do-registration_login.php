<?php  
require_once dirname(__FILE__) .'/header/lib.php'; 
//error_reporting(E_ALL);
//ini_set('display_errors',1);
//echo "<pre>";print_r($_SESSION);exit;
if( isset($_SESSION['reg_email']) && $_SESSION['reg_email']!= "" && isset($_SESSION['user_id']) && isset($_SESSION['token']) && $_SESSION['reg_status'] == 1){
	  $username=$_SESSION['reg_email'];
      $password=$_SESSION['reg_password'];
      $status = userLogin($username,$password,$client_name);
			//echo "<pre>";print_r($status->roleId);exit;
		  if($status->user_id != ""){
					$_SESSION['user_id'] = $status->user_id;
					$_SESSION['role_id'] = $status->roleId;
					if($_SESSION['role_id']==2){
						$_SESSION['token'] = $status->token;
						$_SESSION['package_code'] = $status->package_code;
					} 
					$_SESSION['user_group_id'] = $status->user_group_id;
					$user_dtl = userdetails($status->user_id);
					$center_id = centerDetails($status->user_id);
					$_SESSION['username'] = $user_dtl->first_name;
					$_SESSION['client_id'] = $user_dtl->client_id;
					$_SESSION['center_id'] = $center_id->center_id;
					$_SESSION['username'] = $user_dtl->first_name;
					$_SESSION['client_id'] = $user_dtl->client_id;
					$_SESSION['center_id'] = $center_id->center_id;
					$_SESSION['center_name'] = $center_id->center_name;
					$configClientId=$customer_id;
					//echo $status->roleId."<pre>";echo $configClientId;exit;
				
					if($configClientId == $user_dtl->client_id){// super Admin
						//echo $configClientId ."==".$_SESSION['client_id'];exit;
							if($_SESSION['role_id'] == 3){// super Admin
								header('location:dashboard.php');
								exit();
							}else if($_SESSION['role_id'] ==4){// center Admin
								header('location:centerAdmin/dashboard.php');
								exit();
							}else if($_SESSION['role_id'] ==1 ){// 1  teacher
								header('location:trainer/dashboard.php');
								exit();
							}else if($_SESSION['role_id']==2){// 2 Student
							   
								//$centerAdminName=$_SESSION['center_name'];
								//$email_id=$_POST['reg_email'];
								//$password=$_POST['reg_password'];	
							    //require_once('mail_send_template.php');
							   header('location:product.php');
							  exit();
								
						 }else{
							   $_SESSION['error']=1;
								header('location:signup.php');
								exit();
								//$hide="displayNone";
							}
						}else{
							  $_SESSION['error']=1;
							  header('location:signup.php');
						   // header('location:login.php?err=1');
							exit();
							//$hide="displayNone";
		            }	

			
		}//success end
       	else{
			$_SESSION['error']=1;
			header('location:signup.php');
			exit();
			//$hide="displayNone";
		}
  
}else{
	$_SESSION['error']=1;
	header('location:signup.php');
	exit();		//$hide="displayNone";
}