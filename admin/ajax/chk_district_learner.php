<?php 

include_once('../../header/lib.php');
include_once('../../header/global_config.php');
$dsgObj = new designationController();
$tehsilObj = new tehsilController();
$centerObj = new centerController();
$adminObj = new centerAdminController();
$district_id = base64_decode($_POST['district_id']);

$options = array();
$options['district_id'] = $district_id;

$response_result= $tehsilObj->getTehsilList($options,'','','tehsil_id','asc'); 
$total =$response_result['total'];
/*  For Update edit and update batch*/

$chk = $centerObj->chkDistrictUser($district_id); 
	
  
if($total >0 || ($chk==true)){
	  echo json_encode(array('hasData'=>1,'district_id'=>$district_id));
}else{
	 echo json_encode(array('hasData'=>0,'district_id'=>$district_id));
}
  
   


?>
