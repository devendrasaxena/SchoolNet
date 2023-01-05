<?php 
$_html_relative_path='../../';//dirname(dirname(__FILE__)).'/';
$_html_relative_path = isset($_html_relative_path) ? $_html_relative_path : '';
// $_html_relative_path;exit;
include_once($_html_relative_path.'header/lib.php');

$adminObj = new centerAdminController();
$batchName=$_POST['batch'];
$center_id=$_POST['centerId'];
$status=0;
$batchNameArr=array();
$allBatch=$adminObj->getBatchDeatils($center_id);
for( $i=0; $i<count($allBatch); $i++){
	$batchNameArr[]=$allBatch[$i]['batch_name'];
}	
$batchExit = in_array($batchName,$batchNameArr);
if($batchExit){
		$status=1;
		echo $status;
		die();
	}else{
		$status=0;
	    echo $status;
	    die();
	}


?>
