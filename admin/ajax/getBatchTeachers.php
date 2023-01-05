<?php
include_once('../../header/lib.php');
$adminObj = new centerAdminController();

if(isset($_POST['teacherID']) && $_POST['teacherID'] != ''){
	
	$teahcer_id = trim($_POST['teacherID']);
	$arrTeacherID=explode('^',$teahcer_id);
	$teahcer_id = $arrTeacherID[0];

	$batch_code_sel = trim($_POST['BatchCode']);

	$batches = $adminObj->getTeacherBatches($teahcer_id);
	
	//echo "<pre>";print_r($batches);
	if(count($batches)>0){
		$i=1;
		echo '<option value=""> Select</option>';
		foreach($batches as $key => $value){
			$batch_code = $value['batch_code'];
			$batch_name = $value['batch_name'];
		
			$optionSelected = ($batch_code == $batch_code_sel) ? "selected" : "";
			
			echo '<option value="'.$batch_code.'" '.$optionSelected.' count="'.$i++.'" >'.$batch_name.'</option>';
		}
		$i++;
	}else{
		echo '<option value="">Not Available</option>';
	}
}
?>
