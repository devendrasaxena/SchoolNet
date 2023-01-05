<?php 

include_once('../../header/lib.php');
include_once('../../header/global_config.php');
$dsgObj = new designationController();
$centerObj = new centerController();
$adminObj = new centerAdminController();
error_reporting(1);
ini_set('display_errors',1);
$center_id = base64_decode($_POST['stateId']); 

/*  For Update edit and update batch*/
if(isset($center_id) && $center_id != ''){
	

	$chkData  = $dsgObj->getClassByCenterFromDesignationMap($center_id);
	  
	if($chkData==true){
		  echo json_encode(array('hasBatch'=>1,'center_id'=>$center_id));
	}else{
		 echo json_encode(array('hasBatch'=>0,'center_id'=>$center_id));
	}
	  
   
}

?>
