<?php 

include_once('../../header/lib.php');
include_once('../../header/global_config.php');
$dsgObj = new designationController();
$tehsilObj = new tehsilController();
$centerObj = new centerController();
$adminObj = new centerAdminController();
$tehsilId = base64_decode($_POST['tehsilId']);

$chk = $centerObj->chkUserListByTehsil($tehsilId); 
	
  
if($total >0 || ($chk==true)){
	  echo json_encode(array('hasData'=>1,'tehsilId'=>$tehsilId));
}else{
	 echo json_encode(array('hasData'=>0,'tehsilId'=>$tehsilId));
}
  
   


?>
