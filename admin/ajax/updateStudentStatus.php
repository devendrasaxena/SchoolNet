<?php 
include_once('../../header/lib.php');

$adminObj = new centerAdminController();
			
if(isset($_POST['userId']) && $_POST['userId'] != "" && $_POST['uStatus'] != ""){
	$status = $adminObj->updateStudentStatus($_POST['userId'],$_POST['uStatus']);
	if($status){
		echo "YeS";
	}else{
		echo "nO";
	}
}else{
	echo "nO";
}
?>
