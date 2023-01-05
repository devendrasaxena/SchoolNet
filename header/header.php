<?php
$_html_relative_path='';//dirname(dirname(__FILE__)).'/';
include_once('header/lib.php');

 if(!isset($_SESSION['user_id'])){
	header('location:index.php');
}

include_once('header/global.php');
//echo "<pre>";print_r($_SESSION);exit;
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$roleId = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : null;
/* $user_group_id = isset($_SESSION['user_group_id']) ? $_SESSION['user_group_id'] : null;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
$client_id = isset($_SESSION['client_id']) ? $_SESSION['client_id'] : null;
$center_id = isset($_SESSION['center_id']) ? $_SESSION['center_id'] : null;
 */
$userInfo=userdetails($userId);
//echo "<pre>";print_r($roleId);exit;
$userInfo=userdetails($userId);
$first_name=$userInfo->first_name;
$last_name=$userInfo->last_name;
$username=$first_name;//.' '.$last_name;
$email_id=$userInfo->email_id;
$roleName =$userInfo->roleName;
$user_group_id = $userInfo->user_group_id;
$client_id = $userInfo->client_id;
$center_id = $userInfo->center_id;
$_SESSION['center_id']=$center_id;
$firstVisit= $userInfo->firstTime_login;
$user_from= $userInfo->user_from;
$loginid= $userInfo->loginid;
$is_active= $userInfo->is_active;
$expiry_date= $userInfo->expiry_date;
$center_description= $userInfo->center_description;
$center_status= $userInfo->center_status;
$region_id= $userInfo->region;
$user_ip_address= $userInfo->ip_address;
//echo "<pre>";print_r($userInfo);//exit;
include_once('header/check_ip.php');
//echo "<pre>";print_r($userInfo);exit;
$studentData = $adminObj->getUserDataByID($_SESSION['user_id'], $loginid,2); // student role 2 used function in dashoard
$userBatchInfo = $adminObj->getUserBatch($userId);

$batch_id=$userBatchInfo[0]['batch_id'];

if($roleId==2){
	$uToken=getUserRefreshToken($email_id,$userId);
	//echo "<pre>";print_r($uToken);exit;

    $userToken=$uToken->token;
	//echo "-->".$userToken;
	$package_code = isset($_SESSION['package_code']) ? $_SESSION['package_code'] : null;
	$checkCourseLevel = checkCourseLevelVisitByUserToken($userToken);
	//echo "<pre>";print_r($checkCourseLevel);exit;

}
$regionDetail=$adminObj->getLogoById($region_id);
$studentData = $adminObj->getUserDataByID($_SESSION['user_id'], $loginid,2);
?>
<!doctype html>
<html lang="en" class="app loginRegSection">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
	<title><?php echo APP_NAME;?></title>

	<link rel="shortcut icon" href="images/favicon.ico" type="image/vnd.microsoft.icon"/>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"/>
	<link rel="stylesheet" type="text/css" href="css/app.css"/>
	<link rel="stylesheet" type="text/css" href="css/animate.css"/>
	<link rel="stylesheet" type="text/css" href="css/font-awesome.min.css"/>
	<link rel="stylesheet" type="text/css" href="css/font.css"/>
	<link rel="stylesheet" type="text/css" href="css/common.css?<?php echo date('Y-m-d').time(); ?>; ?>"/>
   <link rel="stylesheet" href="css/owl.carousel.css"/> <!-- Owl Stylesheets -->
    <?php if(SHOW_REGION_THEME==1 && client_reg_id!=''){?>
		<link rel="stylesheet" href="css/theme<?php echo client_reg_id;?>.css?<?php echo date('Y-m-d').time(); ?>"/>
	<?php }?>
    <?php if(SHOW_THEME==1): ?>
		<link rel="stylesheet" href="css/theme.css?<?php echo date('Y-m-d').time(); ?>"/>
	<?php endif;?>
	<!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
	<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<!-- Le styles -->
	<!-- Le fav and touch icons -->
	
	
	<!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
	<script src="js/jquery.min.js"></script>
	<script src="js/popper.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/owl.carousel.js"></script>

  </head>  
  <body class="bgDiv">
   <div id="loaderDiv" class="submitPopup">
   <div class="overlay"></div><div class="loaderImageDiv"></div>
  </div>
  <section class="vbox">
		<div class="header relative">
		  <div class="logo">
		<?php if($regionDetail!='' && $regionDetail['is_app_logo_show']==1){?> 
		<img src="<?php echo $_html_relative_path.applogo; ?>" class="logoImg"/>
		<?php }elseif($regionDetail!='' && $regionDetail['is_app_logo_show']==0){}else{?> 
		  <img src="<?php echo $_html_relative_path.applogo; ?>" class="logoImg"/>
        <?php }?> 
		<?php if($regionDetail!='' && $regionDetail['is_region_logo_show']==1){?> 
		<img src="<?php echo $_html_relative_path."/images/region/".$regionDetail['region_logo']; ?>" class="logoImg"/>
		<?php }?>
		<?php if($is_secondary_logo==1){?>
		  <img class="logoImg2" src="<?php echo $_html_relative_path.SECONDARY_LOGO; ?>" />
		<?php }?>
		</div>
		<div class="headerRight hidden-xs">
		
		<span class="langDiv"  style="display:none">
			<a href="javascript:void(0)"><span class="lang">English</span>		
			<span class="userArroIcon"><img class="userArroIcon" src="images/arrowDown.png"/></span></a>
		</span>
		<span class="userBgDiv">
		<a class="" data-toggle="dropdown">
			<span class="userIcon">
			<?php if($studentData->system_name == ''){ ?><img class="userIcon" src="images/userIcon.png"/>
			<?php }else{ ?>
			<img class="userIcon" src="<?php echo $profile_img_hosting_url.$studentData->system_name; ?>" style="border:solid thin #111;border-radius:100%"/>
			<?php } ?></span> 
			<span class="user"><?php echo $username; ?></span>
			<span class="userArroIcon dropdown"><img class="userArroIcon" src="images/arrowDown.png"/></a>
			<ul class="dropdown-menu dropdown-menu-right" id="profileDrop" style="left:55px;min-height:30px;">
			  <li><a class="" href="logout.php"><span class="userIcon"><img class="userIcon" src="images/logout.png"/></span> <span class="liTxt"> Logout</span></a> </li>
			</ul>
			</span>
		</span>
		</div>
	</div>
	 <!-- mobile nav -->
	        <nav class="nav-primary navMobile" id="nav-mobile" >
                  <ul class="nav">
			      <li class="visible-xs"><a class="" href="logout.php"><span class="userIcon"><img class="userIcon" src="images/logout.png"/></span> <span class="liTxt"> Logout</span></a> </li>
                  </ul>
                </nav><!-- end mobile nav -->
	 <section>
      <section class="hbox stretch">	
 <section id="contentDiv" class="contentDiv">
   <section class="vbox vBoxContent">
     
	
	
	