<?php
include_once dirname(dirname(dirname(__FILE__))).'/header/lib.php';

$region_id = $_POST['region_id'];
$name= trim($_POST['uname']);
$status= trim($_POST['status']);
$valSelected=(isset($_POST['valSelected']) && !empty($_POST['valSelected'])) ? $_POST['valSelected'] : 0;
$reportObj = new reportController();
$optionSelected = "";
$users_arr =	$reportObj->searchRegionAdminByRegionIdAndName($region_id,'7',$name,$status);
	 
	 if(count($users_arr)>0){
		 
		 $optionSelected = ($valSelected == 'All' || $valSelected == 0) ? "selected" : "";
		// echo '<option value="" '.$optionSelected.'>Select Student</option>';
		 foreach($users_arr  as $key => $value){
				
				$user_id = $value->user_id;
				
				$first_name = $value->first_name;
				$last_name = $value->last_name;
				$fullname = $first_name." ".$last_name;
		
				$optionSelected = ($valSelected == $user_id) ? "selected" : "";
				echo '<option   value="'.$user_id.'" '.$optionSelected.' >'.$fullname.'</option>';
					
		 }
	 }
	 else{
		echo '<option value="">Not Available</option>';
	}

?>