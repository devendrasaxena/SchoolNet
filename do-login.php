<?php 
require_once dirname(__FILE__) .'/header/lib.php'; 

if(isset($_POST['login_email']) && $_POST['login_email'] != "" && $_POST['login_password']){

	$username = $_POST['login_email'];
	$password = $_POST['login_password'];
	//echo "<pre>";print_r($_POST);exit;
	$status = userLogin($username,$password,$client_name);
	//echo "<pre>";print_r($status);
  if($status->user_id != ""){
	  
		if(!$status->is_active){
			header('location:index.php?err=deactivated');
			exit;
		}else{
			
			$_SESSION['user_id'] = $status->user_id;
			$_SESSION['role_id'] = $status->roleId;
			if($_SESSION['role_id']==2){
				$_SESSION['token'] = $status->token;
				$_SESSION['package_code'] = $status->package_code;
			} 
			$_SESSION['user_group_id'] = $status->user_group_id;
			$user_dtl = userdetails($_SESSION['user_id']);
			//echo "<pre>";print_r($user_dtl);exit;
			$center_id = centerDetails($_SESSION['user_id']);
			$_SESSION['username'] = $user_dtl->first_name;
			$_SESSION['client_id'] = $user_dtl->client_id;
			$_SESSION['center_id'] = $center_id->center_id;
			$configClientId=$lic_customer_id+1;
			//echo $status->roleId."<pre>";echo $configClientId;exit;
		
			if($configClientId == $_SESSION['client_id']){// super Admin
				//echo $configClientId ."==".$_SESSION['client_id'];exit;
					if($_SESSION['role_id'] == 3){// super Admin
						header('location:dashboard.php');
						exit();
					}else if($_SESSION['role_id'] ==4){// center Admin
						header('location:centerAdmin/dashboard.php');
						exit();
					}else if($_SESSION['role_id'] ==1 ){// 1  teacher
						header('location:user/dashboard.php');
						exit();
					}else if($_SESSION['role_id']==2){// 2 Student
						   if(walkThrough==1){
							  header('location:welcome.php');
							  exit();
							}else{
							   $_SESSION['default']=1;
							   header('location:score.php');
							   exit();
							}
						
			     }else{
						header('location:index.php?err=1');
						exit();
						//$hide="displayNone";
					}
				}else{
					  $_SESSION['error']=1;
					  header('location:index.php');
                   // header('location:index.php?err=1');
					exit();
					//$hide="displayNone";
				}	
				
		}////close active user
		
	}else{
		header('location:index.php');
		exit();
		//$hide="displayNone";
	}
}



/* 
if(isset($_REQUEST['LoginForm_password'])) {

    $username = isset($_REQUEST['LoginForm_username']) ? trim($_REQUEST['LoginForm_username']) : "";
    $password = isset($_REQUEST['LoginForm_password']) ? trim($_REQUEST['LoginForm_password']) : "";
    $unique_code = isset($_REQUEST['LoginForm_unique_code']) ? trim($_REQUEST['LoginForm_unique_code']) : "";

    $objAdmin = new AdminController();
    $objUser = new User();
    $obj_lc = new loginController();
    $user_arr = $obj_lc->userLogin($username, $password, $unique_code);
	if( count($user_arr) ){
		$_SESSION['roleID'] = $user_arr['role_id'];
		$_SESSION['userID'] = $user_arr['user_id'];
        $_SESSION['user'] = $user_arr;
        $center_details = $objAdmin->getCenterDetails();
        $center_detail = reset($center_details);
        User::setPackID($center_detail['license_key']);
        
		if($_SESSION['roleID'] == 3){
			header('location:./admin/admin.php');
			exit;
		}else{
			header('location:./user/index.php');
			exit;
		}
	}else{
        $_SESSION['LOGIN'] = array();
        $_SESSION['LOGIN']['ERR'] = array();
        $_SESSION['LOGIN']['ERR']['MSG'] = 'Invalid Username or Password';
		header('location:index.php');
		exit;
	}
}
 */