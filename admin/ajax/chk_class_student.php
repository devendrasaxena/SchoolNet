<?php 

include_once('../../header/lib.php');
include_once('../../header/global_config.php');
$dsgObj = new designationController();
$centerObj = new centerController();
$adminObj = new centerAdminController();
error_reporting(1);
ini_set('display_errors',1);
$center_id = base64_decode($_POST['stateId']); 
$classId = base64_decode($_POST['classId']); 

/*  For Update edit and update batch*/
if(isset($center_id) && $center_id != ''){
	
	$chkData  = $centerObj->getStudentByBatchUserMap($center_id,$classId);
	  
	if($chkData==true){
		  echo json_encode(array('hasStudent'=>1,'center_id'=>$center_id,'batch_id'=>$classId));
	}else{
		 echo json_encode(array('hasStudent'=>0,'center_id'=>$center_id,'batch_id'=>$classId));
	}
	  
   
}

?>
