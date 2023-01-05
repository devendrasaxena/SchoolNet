<?php
include_once('../../header/lib.php');
include_once('../../header/global_config.php');
$center_id= trim($_POST['center_id']);
if(isset($_SESSION['region_id'])){
    $region_id=$_SESSION['region_id'];
 }elseif(isset($_POST['region_id'])){
	 $region_id= trim($_POST['region_id']);
 }else{
	 $region_id='';
 }
 
 if(isset($_POST['country'])){
	 $country= trim($_POST['country']);
 }else{
	 $country='';
 }
 
$valSelected=(isset($_POST['valSelected']) && !empty($_POST['valSelected'])) ? $_POST['valSelected'] : 0;
$reportObj = new reportController();
$optionSelected = "";
echo $batchInfo = $reportObj->getBatchDeatils($center_id,$country,$region_id);
	if($center_id!='All'){
	 if(count($batchInfo)>0){
		 
		$optionSelected = ($valSelected == 'All') ? "selected" : "";
		echo '<option value="" '.$optionSelected.'>Select '.$batch.'</option>';
		if(!isset($_REQUEST['all']))
		echo '<option value="All" '.$optionSelected.'>All</option>';
		 foreach($batchInfo  as $key => $value){
				
				$batch_id = $value['batch_id'];
				
				$batch_name = $value['batch_name'];
		
				$optionSelected = ($valSelected == $batch_id) ? "selected" : "";
				echo '<option   value="'.$batch_id.'" '.$optionSelected.' >'.$batch_name.'</option>';
					
		 }
	 }
	 else{
		echo '<option value="">Not Available</option>';
	}
	}
	else{echo '<option value="" '.$optionSelected.'>Select '.$batch.'</option>';}
?>