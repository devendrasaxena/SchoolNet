<?php
error_reporting(E_ALL);
ini_set('display_errors','1');
include_once('../../header/lib.php');
include_once('../../header/global_config.php');

$centerObj = new centerController();
$rid =filter_query($_POST['rid']);
//echo "<pre>";print_r( $_POST);exit;
if(isset($rid) && $rid!=''){
	//echo "<pre>";print_r( $_POST);exit;
	    $region = addslashes(trim(filter_string($_POST["region"])));
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
		    $centerCity = addslashes(trim(filter_string($_POST["other_city"])));
		}
		$centerPincode = addslashes(trim(filter_string($_POST["centerPincode"])));
		
		$mobileNumber = addslashes(trim(filter_string($_POST["mobileNumber"])));
		$centerContactNumber = addslashes(trim(filter_string($_POST["centerContactNumber"])));
		

		
		$obj = new stdClass();
		
		$obj->region = $region;
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
		
		
		$adminUpdate =$centerObj->updateRegionAdminOnline($obj,$rid);	
		
		  //echo "<pre>";print_r(adminUpdate);exit;
		
		 if($adminUpdate){
			  $_SESSION['succ'] = 2;
			  $_SESSION['msg'] ="Region Admin updated successfully.";
			 header("location:../regionAdminList.php");
			   exit;
			}else{
				$_SESSION['error'] = 0;
				$_SESSION['msg'] ="Region Admin not saved. Please try again.";
                  header("location:../createRegionAdmin.php?rid=".base64_encode($_POST['rid']));
			  exit;
			} 
	}else{
         
		$region = filter_query($_POST["region"]);
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
		$centerContactNumber = addslashes(filter_string(trim($_POST["centerContactNumber"])));
	 
		$obj = new stdClass();
		
		    $obj->client_id =filter_query($_POST['client_id']);
			$obj->region = $region;
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
	        $adminData = $centerObj->createRegionAdminOnline($obj);
			//echo "<pre>";print_r($adminData);exit;
	  if($adminData){
		$adminData = (object) $adminData;
		 $_SESSION['admin_dev'] = $adminData;
		 $_SESSION['succ'] = 1;
		 $_SESSION['msg'] ="Region Admin created successfully.";
		header("Location: ../regionAdminList.php");
		exit;
	 }else{
		 $_SESSION['error'] =0;
		  $_SESSION['msg'] ="Region Admin not saved. Please try again.";
		 header("location:../createRegionAdmin.php?error");
		  exit;
		} 
		

}

?>