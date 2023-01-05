<?php

$_html_relative_path='../';//dirname(dirname(__FILE__)).'/';
$_html_relative_path = isset($_html_relative_path) ? $_html_relative_path : '';
// $_html_relative_path;exit;
include_once($_html_relative_path.'header/lib.php');
if(!isset($_SESSION['user_id'])){
	header('location:'.$_html_relative_path.'index.php');
}
include_once($_html_relative_path.'header/global.php');

//echo "<pre>";print_r($_SESSION);exit;
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$roleId = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : null;

$userInfo=userdetails($userId);
$first_name=$userInfo->first_name;
$last_name=$userInfo->last_name;
$username=$first_name;//.' '.$last_name;
$email_id=$userInfo->email_id;
$roleName =$userInfo->roleName;
$user_group_id = $userInfo->user_group_id;
$client_id = $userInfo->client_id;
$center_id = $userInfo->center_id;
$firstVisit= $userInfo->firstTime_login;
//echo "<pre>";print_r($userInfo);exit;

$studentData = $adminObj->getUserDataByID($_SESSION['user_id'], 2); // student role 2 
$userBatchInfo = $adminObj->getUserBatch($userId);
$batch_id=$userBatchInfo[0]['batch_id'];
/* echo "<pre>";print_r($userBatchInfo[0]['batch_id']);exit;	
if($userBatchInfo){
  $totalUserBatch=$userBatchInfo;
} */ 

//echo "<pre>";print_r($_SESSION['user_group_id']);exit;	
$clientUserId=$assessmentObj->getSuperClientId($user_group_id);

$profileImgPath=$_html_relative_path."profile_pic/";
$profileImgDefault=$_html_relative_path."images/avatar.jpg";
$studentData = $adminObj->getUserDataByID($userId, 2);
$courseType='0';
$standardId='1';
$courseArr = $adminObj->getCourseListByLevel($courseType,$standardId,$clientUserId);
$levelTotalRang=10; 
if($roleId==2){
	$uToken=getUserRefreshToken($email_id);
    $userToken=$uToken->token;	
	$package_code = isset($_SESSION['package_code']) ? $_SESSION['package_code'] : null;
	$checkScore = checkLTIScoreByUserToken($userToken);
	//echo "<pre>";print_r($checkScore);
	$default=$_SESSION['default'];
	if($default==1){
		$score=$checkScore->score;
		$user_start_level=$checkScore->user_start_level;
		$user_current_level=$checkScore->user_current_level;
		$user_current_description=$checkScore->user_current_description;
		$user_current_mapto=$checkScore->user_current_mapto;
	}else{
		$score=78;
		$user_start_level=1;
		$user_current_level=4;
		$user_current_description="Advanced Score";
		$user_current_mapto="B1";
	}
	
	$getRange=$user_current_level; 
	$level='Level '.$getRange;
	$levelJump=$level;
	//echo "<pre>";print_r($courseArr);
	$enableRange=count($courseArr);
	//echo $enableRange;
	$courseRangeArr=array();
	foreach($courseArr as $key=>$val){
	 $courseRangeArr[]=$val['course_id'];
	}
	
}


//$courseDetials= $adminObj->getBatchCourseMapList($batch_id);
/*$ _FEEDBACK_FORM_COMPLETE_URL = FEEDBACK_FORM_URL;
$_FEEDBACK_FORM_COMPLETE_URL .= '?user_id='.$userToken;
$_FEEDBACK_FORM_COMPLETE_URL .= '&platform=Online';
$_FEEDBACK_FORM_COMPLETE_URL .= '&product='. urlencode(json_encode($client_name));
$_FEEDBACK_FORM_COMPLETE_URL .= '&device=DESKTOP';
$_FEEDBACK_FORM_COMPLETE_URL .= '&device_id=web'; */
?>
<!DOCTYPE html>
<html lang="en" class="app loginRegSection">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
	<title><?php echo APP_NAME;?></title>

	<link rel="shortcut icon" href="<?php echo $_html_relative_path; ?>images/favicon.ico" type="image/vnd.microsoft.icon"/>
	<link rel="stylesheet" type="text/css" href="<?php echo $_html_relative_path; ?>css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo $_html_relative_path; ?>css/app.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo $_html_relative_path; ?>css/animate.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo $_html_relative_path; ?>css/font-awesome.min.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo $_html_relative_path; ?>css/font.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo $_html_relative_path; ?>css/common.css?<?php echo date('Y-m-d'); ?>"/>
	<!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
	<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<!-- Le styles -->
	<!-- Le fav and touch icons -->
	
	
	<!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
	<script src="<?php echo $_html_relative_path; ?>js/jquery.min.js"></script>
	<script src="<?php echo $_html_relative_path; ?>js/popper.min.js"></script>
	<script src="<?php echo $_html_relative_path; ?>js/bootstrap.min.js"></script>
  </head>  
  <body class="bgDiv">

<div class="submitPopup" id="loaderDiv" >
   <div class="overlay"></div><div class="loaderImageDiv"></div>
</div>


  <div class="boxZindexBG"></div>
  <section class="vbox">
	<div class="header relative">
		<div class="logo"><img src="<?php echo $_html_relative_path.applogo; ?>" class="logoImg "></div>
		<div class="headerRight">
		</div>
	</div>
	 <section>
      <section class="hbox stretch">
 <section id="contentDiv" class="contentDiv">
        <section class="vbox vBoxContent">          
          

 