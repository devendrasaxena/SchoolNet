<?php
error_reporting(E_ALL);
ini_set('display_errors','1');
include_once('../../header/lib.php');
include_once('../../header/global_config.php');

include_once('centerLimit.php');
$centerObj = new centerController();
$cid =filter_query($_POST['cid']);
//echo "<pre>";print_r( $_POST);exit;
if(isset($cid) && $cid!=''){
	//echo "<pre>";print_r( $_POST);exit;
        $name = addslashes(trim(filter_string($_POST["name"])));
	    $center_description = addslashes(trim(filter_string($_POST["center_description"])));

		$centerAddress = addslashes(trim(filter_string($_POST["centerAddress"])));
		$centerCountry = addslashes(trim($_POST["country_dropdown"]));
		$region = addslashes(trim($_POST["region_dropdown"]));
		
		if($centerCountry=='India'){
			//$centerState = addslashes(trim($_POST["state_dropdown"]));
		    //$centerCity = addslashes(trim($_POST["city_dropdown"]));
			$centerState = 'State';
		    $centerCity = 'City';
		}else{
			//$centerState = addslashes(trim(filter_string($_POST["other_state"])));
		   // $centerCity = addslashes(trim(filter_string($_POST["other_city"])));
		   $centerState = 'State';
		    $centerCity = 'City';
		}
		$productList =$_POST["product_id"];
		//$centerPincode = addslashes(trim(filter_string($_POST["centerPincode"])));
		$centerPincode = 11111;
		$centerAdminName = addslashes(trim(filter_string($_POST["centerAdminName"])));
		$emailId = trim(filter_string($_POST["used_email"]));
		$mobileNumber = addslashes(trim(filter_string($_POST["mobileNumber"])));
		$centerContactNumber = addslashes(trim(filter_string($_POST["centerContactNumber"])));
		//$password = addslashes(trim($_POST["password"]));
		$lmode = addslashes(trim($_POST["lmode"]));
		$fld_password = addslashes(trim(filter_string($_POST["fld_password"])));
		$cnfPassword = addslashes(trim(filter_string($_POST["cnfPassword"])));
		//$license_key = addslashes(trim($_POST["license_key"]));
		$old_trainer_limit = addslashes(trim($_POST["trainer_limit"]));
		$old_student_limit = addslashes(trim($_POST["student_limit"]));
		$old_expiry_days = addslashes(trim($_POST["expiry_days"]));
		$old_expiry_date = addslashes(trim($_POST["expiry_date"]));
        $used_license=     addslashes(trim(filter_string($_POST['used_license'])));
		$new_license_key = addslashes(trim(filter_string($_POST["new_license_key"])));
		if(isset($_POST['shortcode']) && $_POST['shortcode']!="")
		{
			$shortcode = addslashes(trim(filter_string($_POST['shortcode'])));
			$shortcode = strtolower($shortcode);
			
		}else{
			$shortcode = "";
		}
		
		$obj = new stdClass();
		$obj->center_description = $center_description;
	    $obj->name = $name;
		$obj->user_full_name = $centerAdminName;
		$obj->email_id = $emailId;
		$obj->user_mobile = $mobileNumber;
		$obj->center_phone = $centerContactNumber;
		$obj->password = $fld_password;
		$obj->learning_mode = $lmode;
	    $obj->mac_address = PRODUCTMODE;
		$obj->license_key = $used_license;
		//$obj->client_id =$_POST['clentid'];//'86';
		$obj->address = $centerAddress;
		$obj->city = $centerCity;
		$obj->state = $centerState;
		$obj->country = $centerCountry;
		$obj->region = $region;
		$obj->postal_code = $centerPincode;
		$obj->custom = IS_PREPACKAGED;
		$obj->center_id=filter_query($_POST['cid']);
		$obj->user_id=filter_query($_POST['uid']);
		$obj->user_group_id=filter_query($_POST['ugid']);
		$obj->address_id=filter_query($_POST['aid']);
		$obj->shortcode=$shortcode; 
		$obj->new_license_key = $new_license_key;
		//echo "<pre>";print_r($obj);exit;
		
		
		$centerUpdate =$centerObj->updateCenterOnline($obj,$cid);	
	
		  //echo "<pre>";print_r($centerUpdate);exit;
		 if($centerUpdate){
			  $deleteCenterProductMapDetails = $centerObj->deleteCenterProductMapDetails($region,$cid);
				foreach($productList as $key=>$val){
					 $productData  = $centerObj->addCenterProductMap($region,$cid,$val);
				  } 
			   $_SESSION['succ'] = 2;
			   header("location:../centerList.php");
			   exit;
			  
		}else{
			  $_SESSION['error'] = 0;
              header("location:../createCenter.php?cid=".base64_encode($cid));
			  exit;
			}
 }else{

		$name = addslashes(trim(filter_string($_POST["name"])));
		$centerAddress = addslashes(trim(filter_string($_POST["centerAddress"])));
		$centerCountry = addslashes(trim($_POST["country_dropdown"]));
		$region = addslashes(trim($_POST["region_dropdown"]));
		if($centerCountry=='India'){
			//$centerState = addslashes(trim($_POST["state_dropdown"]));
		   // $centerCity = addslashes(trim($_POST["city_dropdown"]));
		   $centerState = 'State';
		    $centerCity = 'City';
		}else{
			//$centerState = addslashes(trim(filter_string($_POST["other_state"])));
		    //$centerCity = addslashes(trim(filter_string($_POST["other_city"])));
			$centerState = 'State';
		    $centerCity = 'City';
		}
		$productList =$_POST["product_id"];
		//$centerPincode = addslashes(trim(filter_string($_POST["centerPincode"])));
		$centerPincode = 11111;
		$centerAdminName = addslashes(trim(filter_string($_POST["centerAdminName"])));
		$emailId = trim(filter_string($_POST["emailId"]));
		$mobileNumber = addslashes(trim(filter_string($_POST["mobileNumber"])));
		$centerContactNumber = addslashes(trim(filter_string($_POST["centerContactNumber"])));
		$password = addslashes(filter_string(trim($_POST["password"])));
		$lmode = addslashes(trim($_POST["lmode"]));
		$mainCenter= '';
		$shortcode = addslashes(trim(filter_string($_POST["shortcode"])));
		$shortcode = strtolower($shortcode);
		$obj = new stdClass();
	
		$obj->client_id =$client_id;//$myArray["CLIENT_ID"];
		$obj->client_name ='wiley';//$myArray["CLIENT_NAME"];
		$obj->product ='wiley';//$myArray["PRODUCT_NAME"];
		$obj->expiry_date ='';//$myArray["EXP_DATE"];
		$obj->expiry_days =365;//$myArray["EXP_DAYS"];
		$obj->sync_days = '';//$myArray["SYNC_TYPE"];
		$obj->trainer_limit =100;//$myArray["NO_OF_TRAINERS"];
		$obj->student_limit =1000;//$myArray["NO_OF_LEARNERS"];
		$obj->course ='CRS-2'; //$myArray["APP_COURSES"];
		$obj->license_issue_date = '';//$myArray["LIC_ISSUED_ON"];
		
		$obj->name = $name;
		$obj->user_full_name = $centerAdminName;
		$obj->email_id = $emailId;
		$obj->user_mobile = $mobileNumber;
		$obj->center_phone = $centerContactNumber;
		$obj->password = $password;
		$obj->learning_mode = $lmode;
		$obj->mac_address = PRODUCTMODE; 
		$obj->license_key = filter_string($_POST['license_key']);

		$obj->address = $centerAddress;
		$obj->city = $centerCity;
		$obj->state = $centerState;
		$obj->country = $centerCountry;
		$obj->region = $region;
		$obj->postal_code = $centerPincode;
		$obj->custom = IS_PREPACKAGED;
		$obj->shortcode = $shortcode;
		$obj2 =  $obj;
		$obj2 = (array) $obj2;
		$_SESSION['center_details'] = $obj2; 
		//echo "<pre>";print_r($obj);exit;
		$center = $centerObj->createCenterOnServer($obj);
		//echo "<pre>";print_r($center);exit;
		 
		 if($center){
			 $cid= $center->center_id;
				foreach($productList as $key=>$val){

					 $cpmapData  = $centerObj->addCenterProductMap($region,$cid,$val);
				  }
			$center = (object) $center;
			 $_SESSION['center_dev'] = $center;
			header("Location: makeClientCenter.php");
			exit;
		 }else{
			 header("location:../createCenter.php?error");
			  exit;
		} 

}

?>