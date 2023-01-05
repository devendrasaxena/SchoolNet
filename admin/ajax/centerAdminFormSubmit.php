<?php
error_reporting(E_ALL);
ini_set('display_errors','1');
include_once('../../header/lib.php');
include_once('../../header/global_config.php');

$centerObj = new centerController();
$uid =filter_query($_POST['uid']);
//echo "<pre>";print_r( $_POST);exit;
if(isset($uid) && $uid!=''){
	//echo "<pre>";print_r( $_POST);exit;
	    $center = addslashes(trim(filter_string($_POST["center"])));
		$centerAdminName = addslashes(trim(filter_string($_POST["centerAdminName"])));
		$emailId = trim(filter_string($_POST["emailId"]));
		//$password = addslashes(trim($_POST["password"]));
		$fld_password = addslashes(trim(filter_string($_POST["fld_password"])));
		$cnfPassword = addslashes(trim(filter_string($_POST["cnfPassword"])));
		$centerAddress = addslashes(trim(filter_string($_POST["centerAddress"])));
		$centerCountry = addslashes(trim($_POST["country_dropdown"]));
		if($centerCountry=='India'){
			$centerState = addslashes(trim($_POST["state_dropdown"]));
		    $centerCity = addslashes(trim($_POST["city_dropdown"]));
		}else{
			$centerState = addslashes(trim(filter_string($_POST["other_state"])));
		    $centerCity = addslashes(filter_string(trim($_POST["other_city"])));
		}
		$centerPincode = addslashes(trim(filter_string($_POST["centerPincode"])));
		
		$mobileNumber = addslashes(trim(filter_string($_POST["mobileNumber"])));
		$centerContactNumber = addslashes(trim(filter_string($_POST["centerContactNumber"])));
		

		
		$obj = new stdClass();
		
		$obj->center = $center;
		$obj->user_full_name = $centerAdminName;
		$obj->email_id = $emailId;
		$obj->user_mobile = $mobileNumber;
		$obj->center_phone = $centerContactNumber;
		$obj->password = $fld_password;

        $obj->client_id =filter_query($_POST['client_id']);
		$obj->address = $centerAddress;
		$obj->city = $centerCity;
		$obj->state = $centerState;
		$obj->country = $centerCountry;
		$obj->postal_code = $centerPincode;
		$obj->user_id=filter_query($_POST['uid']);
		$obj->user_group_id=filter_query($_POST['ugid']);
		$obj->address_id=filter_query($_POST['aid']);
		//echo "<pre>";print_r($obj);exit;
		

		$adminUpdate =$centerObj->updateCenterAdminOnline($obj,$uid);	
		
		  //echo "<pre>";print_r(adminUpdate);exit;
		
		 if($adminUpdate){
			$_SESSION['succ'] = 2;
			header("location:../centerAdminList.php");
			exit;
			}else{
				$_SESSION['error'] = 0;
                  header("location:../createCenterAdmin.php?rid=".base64_encode($_POST['uid']));
			  exit;
			} 
	}else{
         
		$center = filter_string($_POST["center"]);
		$centerAdminName = addslashes(trim(filter_string($_POST["centerAdminName"])));
		$emailId = trim(filter_string($_POST["emailId"]));
		
	    $password = addslashes(trim(filter_string($_POST["password"])));
		$centerAddress = addslashes(trim(filter_string($_POST["centerAddress"])));
		$centerCountry = addslashes(trim($_POST["country_dropdown"]));
		if($centerCountry=='India'){
			$centerState = addslashes(trim($_POST["state_dropdown"]));
		    $centerCity = addslashes(trim($_POST["city_dropdown"]));
		}else{
			$centerState = addslashes(trim(filter_string($_POST["other_state"])));
		    $centerCity = addslashes(trim(filter_string($_POST["other_city"])));
		}
		$centerPincode = addslashes(trim(filter_string($_POST["centerPincode"])));
		
		$mobileNumber = addslashes(trim(filter_string($_POST["mobileNumber"])));
		$centerContactNumber = addslashes(trim(filter_string($_POST["centerContactNumber"])));
	 
		$obj = new stdClass();
		
		    $obj->client_id =filter_query($_POST['client_id']);
			$obj->center = $center;
			$obj->user_full_name = $centerAdminName;
			$obj->email_id = $emailId;
			$obj->user_mobile = $mobileNumber;
			$obj->center_phone = $centerContactNumber;
			$obj->password = $password;

			$obj->address = $centerAddress;
			$obj->city = $centerCity;
			$obj->state = $centerState;
			$obj->country = $centerCountry;
			$obj->postal_code = $centerPincode;
		// echo "<pre>";print_r($obj);exit;
		    $_SESSION['admin_details'] = $obj;
	        $adminData = $centerObj->createCenterAdminOnline($obj);
			//echo "<pre>";print_r($adminData);exit;
	  if($adminData){
		$_SESSION['succ'] = 1;
		header("location:../centerAdminList.php");
		exit;
	 }else{
		 $_SESSION['error'] = 0;
		 header("location:../createCenterAdmin.php?error");
		  exit;
		} 
		

}
