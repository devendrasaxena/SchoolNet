<?php
include_once('../../header/lib.php');
include_once('../../header/global_config.php');

$centerObj = new centerController();
$status=0;
if(isset($_POST['userId']) && $_POST['userId']!=''){
	 $deleteData = $centerObj->deleteUserByUserId($_POST['userId']);
	
	if($deleteData){
		/* $_SESSION['error']=3;
		 $_SESSION['msg'] ="$teacher deleted successfully.";
		header('location: ../teacherList.php');
		  exit; */
		  $status=1;
		 echo $status;
		 die();
	 }else{
		 $status=0;
	    echo $status;
	    die();
	  }
}else{ 
     $status=0;
	 echo $status;
	 die();

		/* $_SESSION['error']=0;
		 $_SESSION['msg'] ="$teacher is not deleted. Please try again.";
		header('location: ../teacherList.php');
		 exit; */

}

?>