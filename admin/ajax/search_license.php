<?php
include_once dirname(dirname(dirname(__FILE__))).'/header/lib.php';

$license= trim($_POST['license']);
$center_id= trim($_POST['center_id']);
$region_id= trim($_POST['region_id']);
$status= trim($_POST['status']);
$valSelected=(isset($_POST['valSelected']) && !empty($_POST['valSelected'])) ? $_POST['valSelected'] : 0;
$clientObj = new clientController();
$optionSelected = "";
$license_arr =	$clientObj->searchLicense($lic_customer_id,$license, $center_id, $region_id,$status); 	 
	 if(count($license_arr)>0){
		 
		 $optionSelected = ($valSelected == 'All' || $valSelected == 0) ? "selected" : "";
		// echo '<option value="" '.$optionSelected.'>Select Student</option>';
		  foreach($license_arr  as $key => $value){
				
				$license_id = $value->license_id;
				
				$license_value = $value->license_value;
				
		
				$optionSelected = ($valSelected == $license_id) ? "selected" : "";
				echo '<option   value="'.$license_id.'" '.$optionSelected.' >'.$license_value.'</option>';
					
		 } 
	 }
	 else{
		echo 'No';
	}

?>