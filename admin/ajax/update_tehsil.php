<?php
@session_start();
/* error_reporting(E_ALL);
ini_set('display_errors','1'); */
include_once('../../header/lib.php');
include_once('../../header/global_config.php');
$tehsilObj = new tehsilController();

$cid = trim(filter_string($_POST["tehsilId"]));
$action = trim(filter_string($_POST["action"]));

if(isset($cid) && $cid!='' && isset($_SESSION['user_id']) && $action!='' && $action=='updatestatus'){
		
		$userIdVal = trim($_SESSION['user_id']);
		$data = $tehsilObj->updateTehsilStatus($cid); 
		if($data==true){
			 $_SESSION['succ']=3;
			 $_SESSION['msg']="$tehsil deleted successfully.";
			
			echo json_encode(array('success' => 1)); die();	
		}else{
			 $_SESSION['error']=1;
			$_SESSION['error']="$tehsil not deleted! Try again.";
		echo json_encode(array('success' => 0)); die();	
		}

}
	
?>