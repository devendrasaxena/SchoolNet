
<?php
$centerObj = new centerController(); 
$adminObj = new centerAdminController();
$landingObj = new landingController();

$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$client_id = isset($_SESSION['client_id']) ? $_SESSION['client_id'] : null;
$region_id = isset($_SESSION['region_id']) ? $_SESSION['region_id'] : '';
$center_id = isset($_SESSION['center_id']) ? $_SESSION['center_id'] : null;
$user_group_id = isset($_SESSION['user_group_id']) ? $_SESSION['user_group_id'] : null;
$role_id = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : null;

$user = userdetails($userId);
$user_dtl= userdetails($userId);
$userInfo=userdetails($userId);
$first_name=$userInfo->first_name;
$last_name=$userInfo->last_name;
$username=$first_name;//.' '.$last_name;
$email_id=$userInfo->email_id;
$roleName =$userInfo->roleName;
$user_group_id = $userInfo->user_group_id;
$client_id = $userInfo->client_id;
$clientName=$clientProInfo[0]['name'];
$loginid= $userInfo->loginid;
$ip_address= $userInfo->ip_address;
//echo "<pre>";print_r($userInfo);exit;
include_once($_html_relative_path.'header/check_ip.php');
$adminData = $adminObj->getAdminDataByID($_SESSION['user_id'],$_SESSION['role_id']); //  role  super Admin
$langArr=$langObj->getLanguageList();
//echo $centerlogo['region_logo'];
$reportObj = new reportController();	
$string=$systemLogo;
$logo = explode("../",$string);
//echo "<pre>";print_r($logo[1]);exit;	
$totalCenterTrainer1=0;
$totalCenterStudent1= 0;

$totalBatch1= 0;
$totalTestAttempted1=0;
if($_SESSION['role_id']==3){ 
    $editRight=0;
    $totalCenterDistrict= 0;
	$centers_arr=$centerObj->getCenterByClient($client_id); 
	if($centers_arr!=false){
		$totalCenter=count($centers_arr);
	}
	else{$totalCenter = 0;}
	$region_arr=$centerObj->getRegionDetails();
	
	foreach($region_arr as $rvalue){
	
		
	}
//echo $totalCenterDistrict;exit;
}elseif($_SESSION['role_id']==7){
	$editRight=0;
	$region_arr=$centerObj->getRegionDetailsByUserId($userId);
	$region_arr1=$centerObj->getRegionDetailsByUserId($userId);
	$countryList=$reportObj->getCountryList();
	$cond_arr['region_id'] = $region_arr[0]['region_id'];
	$add_action = $region_arr1[0]['add_action'];
	$edit_action = $region_arr1[0]['edit_action'];
	$centers_arr=$centerObj->getCenterByClient($client_id,$cond_arr); 
	if($centers_arr!=false){
		$totalCenter=count($centers_arr);
		 
	}
	else{$totalCenter = 0;}
}
if($totalCenter>0){

	foreach($centers_arr as $value){
		 $centerId=$value['center_id'];
		 $getSignedUpUserCenter = $centerObj->getSignedUpUserCountByCenter($client_id,$centerId);
		 	// echo "<pre>";print_r($getSignedUpUserCenter);
		 $getBatch=$clientObj->getBatchCount($centerId);
		 $totalBatch1+=$getBatch;
	    // $totalCenterTrainer1+=$getSignedUpUserCenter->totalCenterTeacher;
		 
	     $totalCenterStudent1+=$getSignedUpUserCenter->totalCenterStudent;
		//$totalCenter=count($centerInfo);
	}
}




$profileImgPath=$_html_relative_path."profile_pic/";
$profileImgDefault=$_html_relative_path."images/avatar.jpg";

$userProfilePic = ($user->system_name != '') ? $profileImgPath.$user->system_name : $_html_relative_path."images/avatar.jpg";
$logoImgDefault=$_html_relative_path."images/logo.png";
$regionDetail=$adminObj->getLogoById($region_id);

if(((isset($_SESSION['language']) && $_SESSION['language'] == 'hi') || $_REQUEST['lan'] == 'hi') ){?>
<link rel="stylesheet" type="text/css" href="<?php echo $_html_relative_path; ?>css/lang_font.css?<?php echo date('Y-m-d'); ?>"/>
<?php }?>
<?php if(SHOW_REGION_THEME==1){?>
		<link rel="stylesheet" href="css/theme<?php echo SHOW_REGION_THEME;?>.css"/>
	<?php }?>
<body class="bgDiv">
 <div class="submitPopup" id="loaderDiv" >
   <div class="overlay"></div><div class="loaderImageDiv"></div>
 </div>
  <div class="boxZindexBG"></div>
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
	<div class="headerRight">
		
		<span class="userBgDiv">
		 <?php if($is_language==1){?>
		  <span class="">
			<div class="" style="display: inline-block;padding-right:10px;"> 
				<form method="GET" id="myform">
				 <select class="form-control" name="lan" id="lang" style="height: 22px;padding: 0px 12px;" title="<?php echo $language[$_SESSION['language']]['change_language']; ?>" > 
				 <?php foreach($langArr as $key=>$value){ ?>
				 <option value='<?php echo $value['language_code'];?>' <?php if(isset($_SESSION['language']) && $_SESSION['language'] == $value['language_code']){ echo "selected"; } ?> ><?php echo $value['language_name'];?></option> 
				 <?php } ?>
				 
				 </select> 
				</form>
			</div> 
			</span>
		<?php }?>
        <?php if($is_notification==1){ ?> 
		
		  <a href="#" class="notiDiv dk" style="padding-right: 10px;" title="<?php echo 'view notifications'; ?>" >
            <i class="fa fa-bell"></i>
            <span class="badge badge-sm up bg-danger m-l-n-sm count" style="top: -5px;left: 2px;"></span>
            </a>
			<section class="noti-menu">
            <section class="panel bg-white">
              <header class="panel-heading b-light bg-light">
                <strong><?php echo 'You have'; ?> <span class="count">0</span> <?php echo 'Notifications'; ?></strong>
              </header>
              <div id="latest_notifications" class=" latest_notifications list-group list-group-alt animated">
              </div>
              <footer class="panel-footer text-sm">
               <a href="notification.php" style="color:#fff"><?php echo 'See All The Notifications'; ?>  </a>
              </footer>
            </section>
          </section>
		  
		   <?php }?> 
			<span class="userIcon"> <?php if($adminData->system_name == ''){ ?><img class="userIcon" src="<?php echo $_html_relative_path; ?>images/userIcon.png"/><?php }else{ ?><img class="userIcon" src="<?php echo $profile_img_hosting_url.$adminData->system_name; ?>" style="border:solid thin #111;border-radius:100%"/><?php } ?></span> 
			<span class="user"><?php echo $username; ?></span>
			  <a class="" data-toggle="dropdown"  title="<?php echo $language[$_SESSION['language']]['my_profile']; ?>">
			  <span class="userArroIcon dropdown hidden-xs" id="rightArrowMenu"><img class="userArroIcon" src="<?php echo $_html_relative_path; ?>images/arrowDown.png"/>
			</a> 

			<ul class="dropdown-menu dropdown-menu-right" id="profileDrop" style="left:55px">
			<li><a href="<?php echo $_html_relative_path.$menu_relative; ?>profile.php" title="<?php echo $language[$_SESSION['language']]['my_profile']; ?>"><span class="userIcon">
			<?php if($adminData->system_name == ''){ ?><img class="userIcon" src="<?php echo $_html_relative_path; ?>images/profile.png"/><?php }else{ ?><img class="userIcon" src="<?php echo $profile_img_hosting_url.$adminData->system_name; ?>" style="border:solid thin #111;border-radius:100%"/><?php } ?>
			</span>  <span class="liTxt"> <?php echo $language[$_SESSION['language']]['my_profile']; ?><?php //echo $roleName; ?></span></a> </li>
			<li class="hide"> <a href="feedback.php" title="<?php echo $language[$_SESSION['language']]['feedback']; ?>">
			<span class="userIcon"><img class="menuIcon" src="<?php echo $_html_relative_path; ?>images/feedback.png"/></span>
					<span><?php echo $language[$_SESSION['language']]['feedback']; ?></span>
					</a> </li>
			<li><a class="" href="<?php echo $_html_relative_path; ?>logout.php"  title="<?php echo $language[$_SESSION['language']]['logout']; ?>"><span class="userIcon"><img class="userIcon" src="<?php echo $_html_relative_path; ?>images/logout.png"/></span> <span class="liTxt"> <?php echo $language[$_SESSION['language']]['logout']; ?></span></a> </li>
			</ul>
			</span>
		</span>
		<a id="btn-link" class="btn-link visible-xs"  href="javascript:void(0)" onclick="myMenu();">
		<i class="fa fa-bars"></i>
	    </a>
		
	</div>
		
	</div>
	 <!-- mobile nav -->
	       <nav class="nav-primary navMobile" id="nav-mobile" >
               <ul class="nav">
                   <li id="home1" class="active">
                      <a href="<?php echo $_html_relative_path.$menu_relative; ?>dashboard.php"  title="<?php echo $language[$_SESSION['language']]['logout']; ?>">
                        <img class="menuIcon1" src="<?php echo $_html_relative_path; ?>images/dashboard.png"/>
						<img class="menuIcon1Active hide" src="<?php echo $_html_relative_path; ?>images/dashboard-active.png"/>
                        <span><?php echo $language[$_SESSION['language']]['home']; ?></span>
                      </a>
                   </li>
				
			       <?php if($_SESSION['role_id']==7){?>
					
				
					<li id="cen1">
                      <a href="<?php echo $_html_relative_path.$menu_relative; ?>centerList.php"  title="<?php echo $language[$_SESSION['language']]['states']; ?>">
                        <img class="menuIcon3" src="<?php echo $_html_relative_path; ?>images/feedback.png"/>
						<img class="menuIcon3Active hide"  src="<?php echo $_html_relative_path; ?>images/feedback-active.png"/>				
                        <span><?php echo $language[$_SESSION['language']]['states']; ?></span>
                      </a>
                 </li>
				<?php if($_SESSION['user_id']!='976216'){?>	
				   <li id="cntradmin1">
                      <a href="<?php echo $_html_relative_path.$menu_relative; ?>centerAdminList.php"  title="<?php echo $center_admin; ?>">
                       <img class="menuIcon11" src="<?php echo $_html_relative_path; ?>images/feedback.png"/>
						<img class="menuIcon11Active hide"  src="<?php echo $_html_relative_path; ?>images/feedback-active.png"/>	
                        <span><?php echo $center_admin; ?></span> 
                      </a>
                   </li>
				 <?php }?>
				   <li id="desgn1"  class="">
						  <a href="<?php echo $_html_relative_path.$menu_relative; ?>designationList.php"  >
							<img class="menuIcon17" src="<?php echo $_html_relative_path; ?>images/e_module.png"/>
						   <img class="menuIcon17Active hide" src="<?php echo $_html_relative_path; ?>images/e_module-active.png"/>	
							<span><?php echo $language[$_SESSION['language']]['designations']; ?></span>
						  </a>
					</li> 
				<li id="btch1" class="">
						  <a href="<?php echo $_html_relative_path.$menu_relative; ?>batchList.php"  >
							<img class="menuIcon4" src="<?php echo $_html_relative_path; ?>images/e_module.png"/>
						   <img class="menuIcon4Active hide" src="<?php echo $_html_relative_path; ?>images/e_module-active.png"/>	
							<span><?php echo $language[$_SESSION['language']]['classes']; ?></span>
						  </a>
						</li>
				
				    <li id="tch1">
                      <a href="<?php echo $_html_relative_path.$menu_relative; ?>teacherList.php"  >
                       <img class="menuIcon6" src="<?php echo $_html_relative_path; ?>images/feedback.png"/>
						<img class="menuIcon6Active hide"  src="<?php echo $_html_relative_path; ?>images/feedback-active.png"/>	
                        <span><?php echo $language[$_SESSION['language']]['district_admin']; ?></span>
                      </a>
                   </li>
				
					<li id="std1">
                      <a href="<?php echo $_html_relative_path.$menu_relative; ?>studentList.php"  >
                        <img class="menuIcon8" src="<?php echo $_html_relative_path; ?>images/feedback.png"/>
						<img class="menuIcon8Active hide"  src="<?php echo $_html_relative_path; ?>images/feedback-active.png"/>	
                        <span><?php echo $language[$_SESSION['language']]['learners']; ?></span>
                      </a>
                    </li>
			     <?php if($is_notification==1){?>
				  <li id="notification1" >
                      <a href="<?php echo $_html_relative_path.$menu_relative; ?>notification.php">
                        <img class="menuIcon18" src="<?php echo $_html_relative_path; ?>images/assignment.png"/>
						<img class="menuIcon18Active hide"  src="<?php echo $_html_relative_path; ?>images/assignment-active.png"/>		
                        <span><?php echo $language[$_SESSION['language']]['notifications']; ?></span>
                      </a>
                    </li>
					
					<li id="feedId1">
                      <a href="<?php echo $_html_relative_path.$menu_relative; ?>feedback.php">
                        <img class="menuIcon19" src="<?php echo $_html_relative_path; ?>images/assignment.png"/>
						<img class="menuIcon19Active hide"  src="<?php echo $_html_relative_path; ?>images/assignment-active.png"/>		
                        <span><?php echo $language[$_SESSION['language']]['feedback']; ?> </span>
                      </a>
                    </li>
				  <?php }?>
					<?php if($poll_survey==1){?>
                       <li id="survey1" class="">
						  <a href="<?php echo $_html_relative_path.$menu_relative; ?>manage-survey.php" title="<?php echo $language[$_SESSION['language']]['manage_surveys']; ?>" >
							<img class="menuIcon21" src="<?php echo $_html_relative_path; ?>images/assignment.png"/>
							<img class="menuIcon21Active hide"  src="<?php echo $_html_relative_path; ?>images/assignment-active.png"/>		
							<span><?php echo $language[$_SESSION['language']]['manage_surveys']; ?></span>
						  </a>
						</li>

						<li id="poll1" class="">
						  <a href="<?php echo $_html_relative_path.$menu_relative; ?>manage-poll.php" title="<?php echo $language[$_SESSION['language']]['manage_polls']; ?>" >
							<img class="menuIcon22" src="<?php echo $_html_relative_path; ?>images/assignment.png"/>
							<img class="menuIcon22Active hide"  src="<?php echo $_html_relative_path; ?>images/assignment-active.png"/>		
							<span><?php echo $language[$_SESSION['language']]['manage_polls']; ?></span>
						  </a>
						</li>
						<?php }?>
				<?php }	?>	
				
				
				<?php if($_SESSION['role_id']==3){ ?> 
				
			    <li id="lic1">
                      <a href="license_list.php">
                        <img class="menuIcon2" src="<?php echo $_html_relative_path; ?>images/e_module.png"/>
						<img class="menuIcon2Active hide" src="<?php echo $_html_relative_path; ?>images/e_module-active.png"/>	
                        <span>Licenses</span>
                      </a>
                  </li>
				 <li id="reg1">
                      <a href="<?php echo $_html_relative_path.$menu_relative; ?>regionList.php"  >
                        <img class="menuIcon9" src="<?php echo $_html_relative_path; ?>images/e_module.png"/>
						<img class="menuIcon9Active hide"  src="<?php echo $_html_relative_path; ?>images/e_module-active.png"/>				
                        <span><?php echo $language[$_SESSION['language']]['centres']; ?></span>
                      </a>
					</li>
				
					 <li id="radmin1">
                      <a href="<?php echo $_html_relative_path.$menu_relative; ?>regionAdminList.php"  >
                       <img class="menuIcon10" src="<?php echo $_html_relative_path; ?>images/feedback.png"/>
						<img class="menuIcon10Active hide"  src="<?php echo $_html_relative_path; ?>images/feedback-active.png"/>	
                        <span><?php echo $language[$_SESSION['language']]['centre_admins']; ?></span>
                      </a>
                   </li>
				   <?php }?>
				
				
				  <?php if($region_id==5){?>
				  <li id="rpt1" >
                      <a href="<?php echo $_html_relative_path.$menu_relative; ?>users_report_dseu.php">
                        <img class="menuIcon7" src="<?php echo $_html_relative_path; ?>images/assignment.png"/>
						<img class="menuIcon7Active hide"  src="<?php echo $_html_relative_path; ?>images/assignment-active.png"/>		
                        <span><?php echo $language[$_SESSION['language']]['report']; ?></span>
                      </a>
                  </li>
				  <?php }else{ ?>
					<li id="rpt1" class="">
                      <a href="<?php echo $_html_relative_path.$menu_relative; ?>reports.php">
                        <img class="menuIcon7" src="<?php echo $_html_relative_path; ?>images/assignment.png"/>
						<img class="menuIcon7Active hide"  src="<?php echo $_html_relative_path; ?>images/assignment-active.png"/>		
                        <span><?php echo $language[$_SESSION['language']]['report']; ?></span>
                      </a>
                  </li>
				   <?php }?>
				 <li class="visible-xs" id="profile1"><a href="<?php echo $_html_relative_path.$menu_relative; ?>profile.php">
			   <?php if($adminData->system_name == ''){ ?><img class="userIcon profileIcon" src="<?php echo $_html_relative_path; ?>images/profile.png"/><?php }else{ ?><img class="userIcon profileIcon" src="<?php echo $profile_img_hosting_url.$adminData->system_name; ?>" style="border:solid thin #111;border-radius:100%"/><?php } ?><span><?php echo $language[$_SESSION['language']]['my_profile']; ?> </span></a> </li>
			   <li class="visible-xs"><a class="" href="<?php echo $_html_relative_path; ?>logout.php"><img class="userIcon" src="<?php echo $_html_relative_path; ?>images/logout.png"/> <span ><?php echo $language[$_SESSION['language']]['logout']; ?></span></a> </li>
			   </ul>
            </nav>
			<!-- mobile nav -->
     <section>
       <section class="hbox stretch">
        <!-- .aside -->
        <aside class="bg-dark lter aside-md hidden-print hidden-x" id="nav" >          
          <section class="vbox">
            <section class="top15 w-f scrollable">
              <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0" data-size="5px" data-color="#333333">
                <!-- nav -->
                <nav class="nav-primary" id="nav-menu" >
                  <ul class="nav">
				  <li id="home" class="active">
                     <a href="<?php echo $_html_relative_path.$menu_relative; ?>dashboard.php" title="<?php echo $language[$_SESSION['language']]['overview_of_progress']; ?>">
                        <img class="menuIcon1" src="<?php echo $_html_relative_path; ?>images/dashboard.png"/>
						<img class="menuIcon1Active hide" src="<?php echo $_html_relative_path; ?>images/dashboard-active.png"/>
                        <span><?php echo $language[$_SESSION['language']]['home']; ?></span>
                      </a>
                    </li>
					
					 <?php if($_SESSION['role_id']==7){?>
					
					 <li id="cen">
                      <a href="<?php echo $_html_relative_path.$menu_relative; ?>centerList.php" title="<?php echo $language[$_SESSION['language']]['states'];?>">
                        <img class="menuIcon3" src="<?php echo $_html_relative_path; ?>images/feedback.png"/>
						<img class="menuIcon3Active hide"  src="<?php echo $_html_relative_path; ?>images/feedback-active.png"/>				
                        <span><?php echo $language[$_SESSION['language']]['states']; ?></span>
                      </a>
                    </li>

				<?php if($_SESSION['user_id']!='976216'){?>	
				   <li id="cntradmin">
                      <a href="<?php echo $_html_relative_path.$menu_relative; ?>centerAdminList.php" title="<?php echo $center_admin;?>" >
                       <img class="menuIcon11" src="<?php echo $_html_relative_path; ?>images/feedback.png"/>
						<img class="menuIcon11Active hide"  src="<?php echo $_html_relative_path; ?>images/feedback-active.png"/>	
                        <span><?php echo $center_admin; ?></span>
                      </a>
                   </li>
                 <?php }?>
					<li id="btch"  class="">
						  <a href="<?php echo $_html_relative_path.$menu_relative; ?>batchList.php"  title="<?php echo $language[$_SESSION['language']]['classes'];?>">
							<img class="menuIcon4" src="<?php echo $_html_relative_path; ?>images/e_module.png"/>
						   <img class="menuIcon4Active hide" src="<?php echo $_html_relative_path; ?>images/e_module-active.png"/>	
						      <span><?php echo $language[$_SESSION['language']]['classes']; ?></span>
						  </a>
					</li>
					
				    <li id="tch">
                      <a href="<?php echo $_html_relative_path.$menu_relative; ?>teacherList.php" title="<?php echo $language[$_SESSION['language']]['district_admin'];?>" >
                       <img class="menuIcon6" src="<?php echo $_html_relative_path; ?>images/feedback.png"/>
						<img class="menuIcon6Active hide"  src="<?php echo $_html_relative_path; ?>images/feedback-active.png"/>	
                      <span><?php echo $language[$_SESSION['language']]['district_admins']; ?></span>
                
                      </a>
                   </li>
				

				   <li id="std">
                      <a href="<?php echo $_html_relative_path.$menu_relative; ?>studentList.php" title="<?php echo $language[$_SESSION['language']]['learners'];?>" >
                        <img class="menuIcon8" src="<?php echo $_html_relative_path; ?>images/feedback.png"/>
						<img class="menuIcon8Active hide"  src="<?php echo $_html_relative_path; ?>images/feedback-active.png"/>	
                        <span><?php echo $language[$_SESSION['language']]['learners']; ?></span>
                      </a>
                    </li>
					<?php if($poll_survey==1){?>
                       <li id="survey" class="">
						  <a href="<?php echo $_html_relative_path.$menu_relative; ?>manage-survey.php" title="<?php echo 'Surveys'; ?>" >
							<img class="menuIcon21" src="<?php echo $_html_relative_path; ?>images/assignment.png"/>
							<img class="menuIcon21Active hide"  src="<?php echo $_html_relative_path; ?>images/assignment-active.png"/>		
							<span><?php echo 'Surveys'; ?></span>
						  </a>
						</li>

						<li id="poll" class="">
						  <a href="<?php echo $_html_relative_path.$menu_relative; ?>manage-poll.php" title="<?php echo 'Polls'; ?>" >
							<img class="menuIcon22" src="<?php echo $_html_relative_path; ?>images/assignment.png"/>
							<img class="menuIcon22Active hide"  src="<?php echo $_html_relative_path; ?>images/assignment-active.png"/>		
							<span><?php echo 'Polls'; ?></span>
						  </a>
						</li>
					<?php }?>
					<?php if($is_notification==1){?>
					
						<li id="notification" >
						  <a href="<?php echo $_html_relative_path.$menu_relative; ?>notification.php" title="<?php echo $language[$_SESSION['language']]['notifications'];?>">
							<img class="menuIcon18" src="<?php echo $_html_relative_path; ?>images/assignment.png"/>
							<img class="menuIcon18Active hide"  src="<?php echo $_html_relative_path; ?>images/assignment-active.png"/>		
							<span><?php echo $language[$_SESSION['language']]['notifications']; ?></span>
						  </a>
						</li>
						
						<li id="feedId" >
						  <a href="<?php echo $_html_relative_path.$menu_relative; ?>feedback.php" title="<?php echo $language[$_SESSION['language']]['feedback'];?>" >
							<img class="menuIcon19" src="<?php echo $_html_relative_path; ?>images/assignment.png"/>
							<img class="menuIcon19Active hide"  src="<?php echo $_html_relative_path; ?>images/assignment-active.png"/>		
							<span><?php echo $language[$_SESSION['language']]['feedback']; ?></span>
						  </a>
						</li>
					<?php }?>
					
					<?php }	?>
					
					<?php if($_SESSION['role_id']==3){ ?> 
					<li id="lic">
                      <a href="license_list.php">
                        <img class="menuIcon2" src="<?php echo $_html_relative_path; ?>images/e_module.png"/>
						<img class="menuIcon2Active hide" src="<?php echo $_html_relative_path; ?>images/e_module-active.png"/>	
                        <span>Licenses</span>
                      </a>
                  </li>
					<li id="reg">
                      <a href="<?php echo $_html_relative_path.$menu_relative; ?>regionList.php" title="<?php echo $language[$_SESSION['language']]['centres'];?>"  >
                        <img class="menuIcon9" src="<?php echo $_html_relative_path; ?>images/e_module.png"/>
						<img class="menuIcon9Active hide"  src="<?php echo $_html_relative_path; ?>images/e_module-active.png"/>				
                        <span><?php echo $language[$_SESSION['language']]['centres']; ?></span>
                      </a>
                  </li>
			
				 <li id="radmin">
                      <a href="<?php echo $_html_relative_path.$menu_relative; ?>regionAdminList.php" title="<?php echo $language[$_SESSION['language']]['centre_admins'];?>" >
                       <img class="menuIcon10" src="<?php echo $_html_relative_path; ?>images/feedback.png"/>
						<img class="menuIcon10Active hide"  src="<?php echo $_html_relative_path; ?>images/feedback-active.png"/>	
                        <span><?php echo $language[$_SESSION['language']]['centre_admins']; ?></span>
                      </a>
                   </li>	
				   <li id="prod" >
                      <a href="<?php echo $_html_relative_path.$menu_relative; ?>productList.php"  title="<?php echo $language[$_SESSION['language']]['reports'];?>" >
                        <img class="menuIcon20" src="<?php echo $_html_relative_path; ?>images/assignment.png"/>
						<img class="menuIcon20Active hide"  src="<?php echo $_html_relative_path; ?>images/assignment-active.png"/>		
                        <span>Master Product</span>
                      </a>
                    </li>
				<?php } ?> 
					
				 <?php if($region_id==5){?>
				  <li id="rpt" class="">
                      <a href="<?php echo $_html_relative_path.$menu_relative; ?>users_report_dseu.php"  title="<?php echo $language[$_SESSION['language']]['reports'];?>" >
                        <img class="menuIcon16" src="<?php echo $_html_relative_path; ?>images/assignment.png"/>
						<img class="menuIcon16Active hide"  src="<?php echo $_html_relative_path; ?>images/assignment-active.png"/>		
                        <span><?php echo $language[$_SESSION['language']]['reports']; ?></span>
                      </a>
                    </li>
				  <?php }else { ?>
					<li id="rpt" class="">
                      <a href="<?php echo $_html_relative_path.$menu_relative; ?>reports.php"  title="<?php echo $language[$_SESSION['language']]['reports'];?>" >
                        <img class="menuIcon16" src="<?php echo $_html_relative_path; ?>images/assignment.png"/>
						<img class="menuIcon16Active hide"  src="<?php echo $_html_relative_path; ?>images/assignment-active.png"/>		
                        <span><?php echo $language[$_SESSION['language']]['reports']; ?></span>
                      </a>
                    </li>
				   <?php }?>
				
					</ul>
                </nav>
                <!-- / nav -->
              </div>
            </section>
          </section>
     </aside>
     <!-- /.aside -->
