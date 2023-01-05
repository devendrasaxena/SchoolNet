<?php
include_once dirname(dirname(dirname(__FILE__))).'/header/lib.php';

$client_id= trim($_POST['client_id']);
$region_id= trim($_POST['region_id']);
$name= trim($_POST['cname']);
$hide_b2c= trim($_POST['hide_b2c']);
$valSelected=(isset($_POST['valSelected']) && !empty($_POST['valSelected'])) ? $_POST['valSelected'] : 0;
$reportObj = new reportController();
$optionSelected = "";
$users_arr =	$reportObj->searchCenterByCenterName($client_id,$name,$region_id,$hide_b2c);
	 
	 if(count($users_arr)>0){
		 
		 $optionSelected = ($valSelected == 'All' || $valSelected == 0) ? "selected" : "";
		// echo '<option value="" '.$optionSelected.'>Select Student</option>';
		 foreach($users_arr  as $key => $value){
				
				$center_id = $value->center_id;
				
				$name = $value->name;
				$optionSelected = ($valSelected == $center_id) ? "selected" : "";
				echo '<option   value="'.$center_id.'" '.$optionSelected.' >'.$name.'</option>';
					
		 }
	 }
	 else{
		echo '<option value="">Not Available</option>';
	}

?>