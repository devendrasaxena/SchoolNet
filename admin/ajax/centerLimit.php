<?php
include_once('../../header/lib.php');

 if(!isset($_SESSION['user_id'])){
	header('location:../../index.php');
}
$assessmentObj = new assessmentController();
$clientUserId=$assessmentObj->getSuperClientId($_SESSION['user_group_id']);
$course_arr=$assessmentObj->getCourseByClientId($clientUserId); 
//echo $course_arr;exit;	
$list_course = array();
 if(count($course_arr ) > 0 && !empty($course_arr)){
		// echo "<pre>";print_r($centers_arr);exit;	
		foreach($course_arr  as $key => $value){
			$courseCode=$course_arr[$key]['code'];
			array_push($list_course,$courseCode);
			//$testList=$assessmentObj->getTopicOrAssessmentByCourseId($value['course_id']);
		} 
 }	
$courseList = implode(',', $list_course);
//echo $courseList;exit;	 

  $totalTeacherLimit=10;
  $totalStudentLimit=1000 ;
  $trainer_limit =$totalTeacherLimit;	
  $student_limit =$totalStudentLimit;
  $license='';  
  $license_issue_date='';
  $expiry_date='';
  $expiry_days=1460;
  $sync_days=0;
  $course=$courseList; 
  $mac_address=PRODUCTMODE; 
?>

	
    