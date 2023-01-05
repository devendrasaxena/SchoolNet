<?php
$_html_relative_path='../../';//dirname(dirname(__FILE__)).'/';
$_html_relative_path = isset($_html_relative_path) ? $_html_relative_path : '';
// $_html_relative_path;exit;
include_once($_html_relative_path.'header/lib.php');
include_once('../../header/global_config.php');

$centerObj = new centerController(); 
$clientObj = new clientController();

if(isset($_POST['center_id']) && $_POST['center_id']!='' && $_POST['qty'] && $_POST['qty']!='' && $_POST['qty'] > 0 && isset($_SESSION['user_id']) /*&& $_SESSION['role_id']==3*/){
		$client_id = trim($_POST['client_id']);
		$center_id = trim($_POST['center_id']);
		$centerdata = $clientObj->getCenterDetailsById($center_id);
		if($centerdata) {
			$student_limit=$centerdata[0]['student_limit'];
			$accCountData= $centerObj->getAccCountByCenter($center_id); 
			$accCount=count($accCountData);
			
			/* Dev*/
			$inactive_student = $centerObj->getInactiveStudentsDetail($center_id);
			$student_limit += $inactive_student['total_inactive'];
			/* Dev ends*/

			if($student_limit==$accCount){
			   $_SESSION['error']=2;	
			   $_SESSION['msg']="Access code generation limit exceed.";
			   header("location:../generateAccessCode.php");
               exit;
			}
			if($accCount<$student_limit){
			
				$limit=$student_limit-$accCount;

				if($limit >= $_POST['qty']){

					$obj = new stdClass();
					$obj->client_id = $client_id;
					$obj->center_id = $center_id;
					$obj->student_limit = stripTags($_POST['qty']);
					$accGenerateData=$centerObj->generateAccessCode($obj); 
					if($accGenerateData){
					   $_SESSION['succ']=1;	
					   $_SESSION['msg']="Access code  created successfully.";
					   header("location:../user_access_list.php");
					   exit;
					}else{
					   $_SESSION['error']=1;	
					   $_SESSION['msg']== "Access code not saved. Please try again.";
					   header("location:../generateAccessCode.php");
					   exit;  
				  } 
				} else {
					$_SESSION['error']=3;	
				    $_SESSION['msg']=="Access code not saved. Please try again.";
				    header("location:../generateAccessCode.php");
				   exit;
				}
		   }	
	  }else{
		 $_SESSION['error']=1;	
		 $_SESSION['msg']= "Access code not saved. Please try again.";
		 header("location:../generateAccessCode.php");
         exit;  
	  }
}

/*Dev start line*/
if(isset($_POST['center']) && $_POST['center'] != ''){
	$center = $_POST['center'];
	$centerdata = $clientObj->getCenterDetailsById($center);
	$limit = 0;
	if($centerdata) {
		$student_limit=$centerdata[0]['student_limit'];
		$accCountData= $centerObj->getAccCountByCenter($center); 
		$accCount=count($accCountData);
		$inactive_student = $centerObj->getInactiveStudentsDetail($center);
		$student_limit += $inactive_student['total_inactive'];
		if($student_limit==$accCount){
		   $limit += 0;
		}
		if($accCount<$student_limit){
			$limit=$student_limit-$accCount;
	   	}	
	}
	echo json_encode(['status'=>'1', 'limit'=>$limit, 'created'=>$accCount, 'inactive'=>$inactive_student['total_inactive']]); die;
	//echo json_encode(['status'=>'1', 'limit'=>$limit]); die;

}
/*Dev end line*/

?>