<?php
include_once dirname(dirname(dirname(__FILE__))).'/header/lib.php';
 
$name= trim($_POST['cname']);
$center_id= trim($_POST['center_id']);
// echo $center_id; exit();
$valSelected=(isset($_POST['valSelected']) && !empty($_POST['valSelected'])) ? $_POST['valSelected'] : 0;
// $reportObj = new reportController();
$districtObj = new districtController();
$optionSelected = "";
 // $users_arr =	$reportObj->searchCenterByCenterName($client_id,$name,$region_id,$hide_b2c);
$users_arr =	$districtObj->searchDistrict($name,$center_id);
	 
	 if(count($users_arr)>0){
		  // print_r($users_arr); exit;
		 // $optionSelected = ($valSelected == 'All' || $valSelected == 0) ? "selected" : "";
		// echo '<option value="" '.$optionSelected.'>Select Student</option>';
		 foreach($users_arr  as $key => $value){
				$name = $value['district_name'];
				// echo "e".$value[0]->designation;
				// print_r($value['designation']);
				// $optionSelected = ($valSelected == $center_id) ? "selected" : "";
				echo '<option   value="'.$name.'"  >'.$name.'</option>';
					
		 }
	 }
	 else{
		echo '<option value="">Not Available</option>';
	}

?>