<?php
include_once dirname(dirname(dirname(__FILE__))).'/header/lib.php';

$center_id= trim($_POST['center_id']);
$accesscode= trim($_POST['accesscode']);
$region_id= trim($_POST['region_id']);
$status= trim($_POST['status']);
$valSelected=(isset($_POST['valSelected']) && !empty($_POST['valSelected'])) ? $_POST['valSelected'] : 0;
$centerObj = new centerController();
$optionSelected = "";
$users_arr = $centerObj->getAccessCodeBySearchkey($center_id,$accesscode,$region_id,$status);
	  
	 if(count($users_arr)>0){
		 $optionSelected = ($valSelected == 'All' || $valSelected == 0) ? "selected" : "";
		// echo '<option value="" '.$optionSelected.'>Select Student</option>';
		 foreach($users_arr  as $key => $value){
				
				$access_code = $value->access_code;
				
				//$name = $value->name;
				$optionSelected = ($valSelected == $$access_code) ? "selected" : "";
				echo '<option   value="'.$access_code.'" '.$optionSelected.' >'.$access_code.'</option>';
					
		 }
	 }
	 else{
		echo '<option value="">Not Available</option>';
	}

?>