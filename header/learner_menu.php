<?php

$proObj = new productController();
$productPath='learning_module.php';
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
include_once($_html_relative_path.'header/check_ip.php');
//echo "<pre>";print_r($userInfo);exit;
$studentData = $adminObj->getUserDataByID($_SESSION['user_id'], $loginid,2); // student role 2 used function in dashoard
$userBatchInfo = $adminObj->getUserBatch($userId);
$langArr=$langObj->getLanguageList();


$batch_id=$userBatchInfo[0]['batch_id'];
$_SESSION['batch_id']=$batch_id;
$batchData = $centerObj->getBatchDataByID($batch_id,$center_id);

$batchMandatoryData1='';
$batchMandatoryData=implode(",",$batchMandatoryData1);//$batchData[0]['mandatory'];

if(isset($_SESSION['product_id']) && $_SESSION['product_id']!=''){
	$product_id=$_SESSION['product_id'];
	
	$getProductInfo=$proObj->getProdcutDetailById($_SESSION['product_id']);
	$product_client_id=$getProductInfo['client_id'];
	$product_standard_id=$getProductInfo['id'];//$getProductInfo['standard_id'];
	
	if($product_id==10){
		$COURSE_NAME='Class';
		$COURSE_NAMES='Class';
	}else{
	   $COURSE_NAME='Level';
	   $COURSE_NAMES='Level';	
	}

}else{
	$product_client_id='';
	$current_product_id='';
	$product_id='';
	$product_standard_id='';
}

$batchAllProductMapDetails = $centerObj->getBatchDataByIDDetails($batch_id,$center_id,$product_id1=null);
//echo "<pre>";print_r($batchAllProductMapDetails);//exit;

  $getAllProductData = array();
  foreach ($batchAllProductMapDetails  as $key => $value) {
   	$getAllProductData[] = $value['product_id'];
   }

$getAssignProductInfo=$centerObj->getBatchDataByIDDetails($batch_id,$center_id,$product_id);
 //echo "<pre>";print_r($getAssignProductInfo);//exit;
$getCourseProductData=array();

if(count($getAssignProductInfo)>0){
	foreach ($getAssignProductInfo as $key => $val) {
	  $product_id=$val['product_id'];
	  $getCourseProductData=$val;
	}
}else{
	$getCourseProductData=$proObj->getProductByClientId($client_id);
	
}	

if($userBatchInfo){
  $totalUserBatch=$userBatchInfo;
} 
if($roleId==2){
	$uToken=getUserRefreshToken($email_id,$userId);
	//echo "<pre>";print_r($uToken);exit;
	$master_mode=0;//$uToken->master_mode;
	if($master_mode==0){
		$topicPass=0;
	}else{
		$topicPass=13;
	}
	$quiz_passing_percentage=0;//$uToken->quiz_passing_percentage;
	$review_passing_percentage=0;//$uToken->review_passing_percentage;
	$level_passing_score=0;//$uToken->level_passing_score;	
	
    $userToken=$uToken->token;	
	$package_code = isset($_SESSION['package_code']) ? $_SESSION['package_code'] : null;

	$checkCourseLevel = checkCourseLevelVisitByUserToken($userToken);

	$user_start_level=$checkCourseLevel->user_start_level;
	$user_current_level=$user_current_level=(showCurrentlevel==1)? user_current_level:$checkCourseLevel->user_current_level;


	$getRange=$user_current_level; 
	$level='Module '.$getRange;
	$levelJump=$level; 
	//echo "<pre>";print_r($checkCourseLevel); 	
			
    $clientUserId=$assessmentObj->getSuperClientId($user_group_id);	
    $courseType='0';
   $productInfo=$proObj->getProdcutDetailById($product_standard_id);
   //echo "<pre>";print_r($product_standard_id); 
	$product_name=$productInfo['product_name'];
    $master_products_id= $productInfo['master_products_ids'];
	 // echo "<pre>";print_r($master_products_id); exit;
	$batchCourseStr1 = $getCourseProductData['course'];
	$customTopic = $getCourseProductData['topic'];
	$customChapter = $getCourseProductData['chapter'];
	$batchCourseArr=explode(',', $batchCourseStr);
	 //echo "<pre>";print_r($batchCourseArr); 
	$batchCourseStr= str_replace("CRS-","",$batchCourseStr1);
	$courseArr = $adminObj->getCustomCourseList($courseType,$batchCourseStr,$master_products_id);
      //echo "<pre>";print_r($courseArr); 
	 $enableRange=count($courseArr);
		$col  = 'sequence_id';
		$sort = array();
		foreach ($courseArr as $i => $obj) {
			  $sort[$i] = $obj->{$col};
			}
		array_multisort($sort, SORT_ASC, $courseArr);
		//echo "<pre>";print_r($courseArr);//exit;
		$courseRangeArr=array();
		$courseLevelArr=array();
		$courseNameArr=array();
		$levelTotalRang=count($courseRangeArr); 
		foreach($courseArr as $key=>$val){
		  $courseNameArr[$key+1]=$val->name;

	      $level_text=str_replace($COURSE_NAME," ",$val->level_text);
	 
	    //echo "<pre>";print_r($level_text);
	     $courseLevelArr[$key+1]=trim($level_text);//$val->level_text;
	   //$courseLevelArr[$key]=$val->level_text;
	    $courseRangeArr[$key+1]=$val->course_id;
	}	
	//echo "<pre>";print_r($courseRangeArr);
}

$profileImgPath=$_html_relative_path."profile_pic/";
$profileImgDefault=$_html_relative_path."images/avatar.jpg";
  //echo "<pre>";print_r($courseRangeArr);
//$courseDetials= $adminObj->getBatchCourseMapList($batch_id);
$regionDetail=$adminObj->getLogoById($region_id);

$cTime=getTime();
$menuHide=0;
if($expiry_date!=''){
  $expiry_date1 = strtotime($expiry_date);
  $cTime1 = strtotime($cTime);
  $diffInSeconds = $cTime1 - $expiry_date1;
	  if($diffInSeconds>0){
		 $menuHide=1;
		$_SESSION['diffInSeconds']=$diffInSeconds;
		 header('location:../logout.php');
         die(); 
		 
	}
}

if($is_active==0){ 
 
	 header('location:../logout.php');
      die();	 
 
}
$exam_date = strtotime($userInfo->exam_date);
$cTime1 = strtotime(date('Y-m-d 00:00:00'));
if($exam_date!=''){
	include_once('../controller/placementController.php'); 
    $placementObj = new placementController();
	$testTrackInfo = $placementObj->placementTracking($userToken,0);
	//echo "<pre>";print_r($testTrackInfo);
   $diffInSecondsExam = $cTime1 - $exam_date;
   $is_examDate=($diffInSecondsExam>=0 && $testTrackInfo['attempted_post']=='no')?'1':'0';
}else{
  $is_examDate='0';
}

?>
<?php if(SHOW_REGION_THEME==1){?>
	<link rel="stylesheet" href="<?php echo $_html_relative_path; ?>css/theme<?php echo $region_id;?>.css"/>
<?php }?>
<div class="boxZindexBG"></div>
<body class="bgDiv">
<div class="submitPopup" id="loaderDiv" >
   <div class="overlay"></div><div class="loaderImageDiv"></div>
</div>

  <section class="vbox">
  
	
	<div class="header relative">
	 <div class="logo">
		<?php if($regionDetail!='' && $regionDetail['is_app_logo_show']==1){?> 
		<a href="dashboard.php"><img src="<?php echo $_html_relative_path.applogo; ?>" class="logoImg"/></a>
		<?php }elseif($regionDetail!='' && $regionDetail['is_app_logo_show']==0){}else{?> 
		 <a href="dashboard.php"> <img src="<?php echo $_html_relative_path.applogo; ?>" class="logoImg"/></a>
        <?php }?> 
		<?php if($regionDetail!='' && $regionDetail['is_region_logo_show']==1){?> 
		<a href="dashboard.php"><img src="<?php echo $_html_relative_path."/images/region/".$regionDetail['region_logo']; ?>" class="logoImg"/></a>
		<?php }?>
		<?php if($is_secondary_logo==1){?>
		  <img class="logoImg2" src="<?php echo $_html_relative_path.SECONDARY_LOGO; ?>" />
		<?php }?>
		</div>
	   
		<div class="headerRight">
		
		<span class="langDiv" style="display:none">
			<a href="javascript:void(0)"><span class="lang">English</span>		
			<span class="userArroIcon"><img class="userArroIcon" src="<?php echo $_html_relative_path; ?>images/arrowDown.png"/></span></a>
		</span>
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
			<span class="userIcon"> <?php if($studentData->system_name == ''){ ?><img class="userIcon" src="<?php echo $_html_relative_path; ?>images/userIcon.png"/><?php }else{ ?><img class="userIcon" src="<?php echo $profile_img_hosting_url.$studentData->system_name; ?>" style="border:solid thin #111;border-radius:100%"/><?php } ?></span> 
			<span class="user"><?php echo $username; ?></span>
			<a class="" data-toggle="dropdown" title="<?php echo $language[$_SESSION['language']]['my_profile']?>">
			 <span class="userArroIcon dropdown hidden-xs" id="rightArrowMenu"><img class="userArroIcon" src="<?php echo $_html_relative_path; ?>images/arrowDown.png"/>
			</a> 
			
			<ul class="dropdown-menu dropdown-menu-right" id="profileDrop" style="left:55px">

			<li><a href="profile.php" title="<?php echo $language[$_SESSION['language']]['my_profile']?>">
			<span class="userIcon"> <?php if($studentData->system_name == ''){ ?>
			<img class="userIcon " src="<?php echo $_html_relative_path; ?>images/profile.png"/>
			<?php }else{ ?>
			<img class="userIcon" src="<?php echo $profile_img_hosting_url.$studentData->system_name; ?>" style="border:solid thin #111;border-radius:100%"/><?php } ?></span>  <span class="liTxt"><?php echo $language[$_SESSION['language']]['my_profile']; ?> <?php //echo $roleName; ?></span></a> </li>
			<li id="feedback1" class="hide">
			   <a href="feedback.php">
				<span class="userIcon"> <img class="userIcon menuIcon17" src="<?php echo $_html_relative_path; ?>images/feedback.png"/>
				<img class="userIcon menuIcon17Active hide"  src="<?php echo $_html_relative_path; ?>images/feedback-active.png"/>	</span>						
				 <span class="liTxt"><?php echo $language[$_SESSION['language']]['feedback']; ?></span>
				</a>
			</li>
			<li><a class="" href="../logout.php" title="<?php echo $language[$_SESSION['language']]['logout']?>"><span class="userIcon"><img class="userIcon" src="<?php echo $_html_relative_path; ?>images/logout.png"/></span> <span class="liTxt"> <?php echo $language[$_SESSION['language']]['logout']; ?></span></a> </li>
			 
			</ul>
			
			<?php 
			if(count($getAllProductData)>1){?>
			<span class="switchDiv dropdown" style="padding: 0px 5px 0px 25px;">
			<a href="javascript:void(0)"  data-toggle="dropdown" title="Course List">
			<span class="user"><?php echo 'Courses'; ?></span>
			<span class="userArroIcon dropdownSwitch"><img class="userArroIcon" src="<?php echo $_html_relative_path; ?>images/arrowDown.png"/>
			<!--<i class="fa fa-ellipsis-h"></i></span>-->
			</a>
			<ul class="dropdown-menu dropdown-menu-right switchDrop" id="switchDrop">
			<h6> Recently Used</h6>
			 <?php $i=1;
			      $col  = 'id';
				 $sort = array();
				 foreach ($getAllProductData as $i => $obj) {
					  $sort[$i] = $obj->{$col};
					}
				 array_multisort($sort, SORT_ASC, $getAllProductData);
				 foreach($getAllProductData as $key=>$val ){ 
				   $productInfo1=$proObj->getProdcutDetailById($val);
				
				 ?>
				<li style="min-height: 60px;"><h4><div style="float:left;width:70%;"><?php echo $productInfo1['product_name'];?></div> <?php if($product_id!=$productInfo1['id']){?>
					<div style="float:right;width:30%;">  <a style="margin-top:0px;" class="text-right"id="pro<?php echo $i;?>" href="javascript:void(0)" path="<?php echo $productPath;?>" onclick="visitProduct('<?php echo $i;?>','<?php echo $batch_id;?>','<?php echo $productInfo1['id']?>','<?php echo $productPath;?>','<?php echo $productInfo1['code'];?>')">Go to <?php //echo $val['product_name'];?> <i class="fa fa-arrow-right"></i></a>
				 </div><?php }?></h4>
				<?php //if($productInfo1['product_desc']!=''){?><div class="pDesc" style="display:none"><p><?php //echo $productInfo1['product_desc']; ?></p></div><?php //}?>
				</li>
				
			 <?php $i++;}?>
			</ul>
		   </span>
             <script>
			 function visitProduct(id,bid,pid,path,package_code){
				 if(pid!==''){
				  var data = {action: 'set_visitproduct',product_id:pid,batch_id:bid,package_code:package_code};
					$.ajax({url: '../set_visit_product_ajax.php', type: 'post', dataType: 'json', data: data, async: false,
					   success : function(data){
						  console.log(data.status)
					  if(data.status==1){
							
							 window.location.href='<?php echo $productPath;?>'; 
						
						  $("#loaderDiv").hide();
					  }else{
						console.log(data.status)
					  }
					},
						error: function () {}
					});
				 }

			}

			</script>
        <?php }
		?>
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
				  <?php if($menuHide==0){?>
                    <li id="das1">
                      <a href="dashboard.php" title="<?php echo $language[$_SESSION['language']]['overview_of_progress'];   ?>">
                        <img class="menuIcon12" src="<?php echo $_html_relative_path; ?>images/dashboard.png"/>
						<img class="menuIcon12Active hide" src="<?php echo $_html_relative_path; ?>images/dashboard-active.png"/>
                        <span><?php echo $language[$_SESSION['language']]['dashboard']; ?></span>
                      </a>
                    </li>
                   <li id="lmodule1">
                     <a href="learning_module.php<?php //echo $lpath;?>" title="<?php echo $language[$_SESSION['language']]['view_content_in_the_form_of_various_modules'];   ?>">  
				   <img class="menuIcon13" src="<?php echo $_html_relative_path; ?>images/e_module.png"/>
					<img class="menuIcon13Active hide" src="<?php echo $_html_relative_path; ?>images/e_module-active.png"/>				   
						<span><?php echo $language[$_SESSION['language']]['learning_modules']; ?></span>
                          </a>
                    </li>
			<?php if($is_assignment==1){?>
				<li id="assignments">
				 <a href="assignments.php">  
						<img class="menuIcon5" src="<?php echo $_html_relative_path; ?>images/assignment.png"/>
					<img class="menuIcon5Active hide"  src="<?php echo $_html_relative_path; ?>images/assignment-active.png"/><span> Assignments</span></a>
		        </li>
			<?php }?>
			 <?php if($region_id!=5){?>
			 <li id="perform1" class="hide" style="display:none">
              <a href="my-performance.php">  
				<img class="menuIcon14" src="<?php echo $_html_relative_path; ?>images/perform.png"/>
				<img class="menuIcon14Active hide"  src="<?php echo $_html_relative_path; ?>images/perform-active.png"/>							
						<span> My Performance</span>
                </a>
              </li>
			<?php } ?>
			  <?php if($region_id==5){?>
			   <!--<li id="mycerty1"> 
			
              <a href="my_certificates.php"   title="<?php echo $language[$_SESSION['language']]['view_module_score_and_download_certificate_for_completed_modules'];   ?>">  
				<img class="menuIcon15" src="<?php echo $_html_relative_path; ?>images/assignment.png"/>
				<img class="menuIcon15Active hide"  src="<?php echo $_html_relative_path; ?>images/assignment-active.png"/>							
						<span><?php echo $language[$_SESSION['language']]['my_certificates']; ?></span>
                </a>
              </li>-->
			<?php } ?>
			
			 <?php if($is_notification==1){?>
			<li id="notification1" >
			   <a href="notification.php" title="<?php echo $language[$_SESSION['language']]['view_notifications_sent_by_admin'];   ?>">
				<img class="menuIcon16" src="<?php echo $_html_relative_path; ?>images/feedback.png"/>
				<img class="menuIcon16Active hide"  src="<?php echo $_html_relative_path; ?>images/feedback-active.png"/>							
				<span> <?php echo $language[$_SESSION['language']]['notifications']; ?></span>
				</a>
			 
			  </li>
			<li id="feedback1" >
			   <a href="feedback.php" title="<?php echo $language[$_SESSION['language']]['feedback'];   ?>">
				<img class="menuIcon17" src="<?php echo $_html_relative_path; ?>images/feedback.png"/>
				<img class="menuIcon17Active hide"  src="<?php echo $_html_relative_path; ?>images/feedback-active.png"/>							
				<span><?php echo $language[$_SESSION['language']]['feedback']; ?></span>
				</a>
			</li>
			 <?php }?>
			 <?php if($poll_survey==1){?>
			<li id="survey1"  class="<?php echo $is_notification_show;?>">
				   <a href="survey.php" title="<?php echo $language[$_SESSION['language']]['surveys']; ?>">
					<img class="menuIcon21" src="<?php echo $_html_relative_path; ?>images/feedback.png"/>
					<img class="menuIcon21Active hide"  src="<?php echo $_html_relative_path; ?>images/feedback-active.png"/>							
					<span><?php echo $language[$_SESSION['language']]['surveys']; ?> </span>
					</a>
				 
				  </li>

				  <li id="poll1"  class="<?php echo $is_notification_show;?>">
				   <a href="poll.php" title="<?php echo $language[$_SESSION['language']]['polls']; ?>">
					<img class="menuIcon22" src="<?php echo $_html_relative_path; ?>images/feedback.png"/>
					<img class="menuIcon22Active hide"  src="<?php echo $_html_relative_path; ?>images/feedback-active.png"/>							
					<span> <?php echo $language[$_SESSION['language']]['polls']; ?>  </span>
					</a>
				 
				  </li>
			<?php }?>  
			<?php if(SHOW_POST_EXAM==1  && $is_examDate==1){ ?>
			   <li id="post1">
				   <a href="exam.php" title="<?php echo 'Post Test'; ?>">
					<img class="menuIcon18" src="<?php echo $_html_relative_path; ?>images/feedback.png"/>
					<img class="menuIcon18Active hide"  src="<?php echo $_html_relative_path; ?>images/feedback-active.png"/>							
					<span><?php echo 'Post Test'; ?></span>
					</a>
				</li>
			<?php } ?>
			<?php if(SHOW_RESOUCRES==1){ ?>
			<li id="reso1">
			   <a href="resourceLink.php" title="<?php echo 'Resources'; ?>">
				<img class="menuIcon19" src="<?php echo $_html_relative_path; ?>images/feedback.png"/>
				<img class="menuIcon19Active hide"  src="<?php echo $_html_relative_path; ?>images/feedback-active.png"/>							
				<span><?php echo 'Resources'; ?></span>
				</a>
			</li>
			<?php } ?>
			<?php if($is_live_class==1){?>
			  <li id="sess1">
              <a href="live_session.php">  
				<img class="menuIcon4"src="<?php echo $_html_relative_path; ?>images/live-session.png"/>
				<img class="menuIcon4Active hide"  src="<?php echo $_html_relative_path; ?>images/live-session-active.png"/>							
						<span> Live Session</span>
                </a>
              </li>
			<?php }?>
			
		<?php }?>
			<?php if($menuHide==1){?>
			  <li  class="visible-xs"> <a href="isActive.php"><img class="userIcon" src="<?php echo $_html_relative_path; ?>images/dashboard.png"/><span><?php echo $language[$_SESSION['language']]['dashboard']; ?></span></a>  </li>
			<?php }?>			  
			
			   <li class="visible-xs" id="profile1">
			   <a href="profile.php">
			   <?php if($studentData->system_name == ''){ ?><img class="userIcon profileIcon" src="<?php echo $_html_relative_path; ?>images/profile.png"/><?php }else{ ?><img class="userIcon profileIcon" src="<?php echo $profile_img_hosting_url.$studentData->system_name; ?>" style="border:solid thin #111;border-radius:100%"/><?php } ?><span> <?php echo $language[$_SESSION['language']]['my_profile']; ?> </span></a> </li>
			   
			   <li class="visible-xs"><a class="" href="<?php echo $_html_relative_path; ?>logout.php"><img class="userIcon" src="<?php echo $_html_relative_path; ?>images/logout.png"/> <span ><?php echo $language[$_SESSION['language']]['logout']; ?> </span></a> </li>
			   
                  </ul>
                </nav>
		  <!-- mobile nav -->
	 <section>
      <section class="hbox stretch">
	  <?php if($menuHide==0){?>
        <!-- .aside -->
        <aside class="bg-dark lter aside-md hidden-print hidden-x" id="nav" >          
          <section class="vbox">
            
            <section class="top15 w-f scrollable">
              <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0" data-size="5px" data-color="#333333">
                
                <!-- nav -->
                <nav class="nav-primary" id="nav-menu" >
                  <ul class="nav">
                    <li id="das">
                      <a href="dashboard.php" title="<?php echo $language[$_SESSION['language']]['overview_of_progress'];   ?>">
                        <img class="menuIcon12" src="<?php echo $_html_relative_path; ?>images/dashboard.png"/>
						<img class="menuIcon12Active hide" src="<?php echo $_html_relative_path; ?>images/dashboard-active.png"/>
                        <span><?php echo $language[$_SESSION['language']]['dashboard']; ?></span>
                      </a>
                    </li>
                  <li id="lmodule" >
				   <a href="learning_module.php"  title="<?php echo $language[$_SESSION['language']]['view_content_in_the_form_of_various_modules'];   ?>">  
				   <img class="menuIcon13" src="<?php echo $_html_relative_path; ?>images/e_module.png"/>
					<img class="menuIcon13Active hide" src="<?php echo $_html_relative_path; ?>images/e_module-active.png"/>				   
						<span><?php echo $language[$_SESSION['language']]['learning_modules']; ?></span>
                     </a>
                </li>
				<?php if($is_assignment==1){?>
				<li id="assignments">
		
				  <a href="assignments.php">  
					<img class="menuIcon5"src="<?php echo $_html_relative_path; ?>images/assignment.png"/>
					<img class="menuIcon5Active hide"  src="<?php echo $_html_relative_path; ?>images/assignment-active.png"/>							
							<span> Assignments</span>
					</a>
				 </li>
				<?php }?>
			<li id="perform" class="hide" style="display:none">
			
              <a href="my-performance.php">  
				<img class="menuIcon14" src="<?php echo $_html_relative_path; ?>images/perform.png"/>
				<img class="menuIcon14Active hide"  src="<?php echo $_html_relative_path; ?>images/perform-active.png"/>							
						<span> My Performance</span>
                </a>
              </li>
			  	
			  
				
				   <?php if(SHOW_POST_EXAM==1 && $is_examDate==1){ ?>
				   <li id="post">
					   <a href="exam.php" title="<?php echo 'Post Test'; ?>">
						<img class="menuIcon18" src="<?php echo $_html_relative_path; ?>images/feedback.png"/>
						<img class="menuIcon18Active hide"  src="<?php echo $_html_relative_path; ?>images/feedback-active.png"/>							
						<span><?php echo 'Post Test'; ?></span>
						</a>
					</li>
				<?php } ?>
			  <?php if(SHOW_RESOUCRES==1){ ?>
				   <li id="reso">
					   <a href="resourceLink.php" title="<?php echo 'Resources'; ?>">
						<img class="menuIcon19" src="<?php echo $_html_relative_path; ?>images/feedback.png"/>
						<img class="menuIcon19Active hide"  src="<?php echo $_html_relative_path; ?>images/feedback-active.png"/>							
						<span><?php echo 'Resources'; ?></span>
						</a>
					</li>
			  <?php } ?>

			<?php if($region_id==5){ ?>
			  <!--<li id="mycerty" style="">
			
              <a href="my_certificates.php" title="<?php echo $language[$_SESSION['language']]['view_module_score_and_download_certificate_for_completed_modules'];   ?>">  
				<img class="menuIcon15" src="<?php echo $_html_relative_path; ?>images/assignment.png"/>
				<img class="menuIcon15Active hide"  src="<?php echo $_html_relative_path; ?>images/assignment-active.png"/>							
				<span><?php echo $language[$_SESSION['language']]['my_certificates']; ?></span>
                </a>
              </li>-->

			  <?php} else{?>
			   <li id="mycerty" style="display:none">
			
              <a href="my_certificates.php" title="<?php echo $language[$_SESSION['language']]['view_module_score_and_download_certificate_for_completed_modules'];   ?>">  
				<img class="menuIcon15" src="<?php echo $_html_relative_path; ?>images/assignment.png"/>
				<img class="menuIcon15Active hide"  src="<?php echo $_html_relative_path; ?>images/assignment-active.png"/>							
				<span><?php echo $language[$_SESSION['language']]['my_certificates']; ?></span>
                </a>
              </li>
			  <?php }?>
				<?php if($is_notification==1){?>
				<li id="notification">
				   <a href="notification.php" title="<?php echo $language[$_SESSION['language']]['view_notifications_sent_by_admin'];   ?>">
					<img class="menuIcon16" src="<?php echo $_html_relative_path; ?>images/feedback.png"/>
					<img class="menuIcon16Active hide"  src="<?php echo $_html_relative_path; ?>images/feedback-active.png"/>							
					<span> <?php echo $language[$_SESSION['language']]['notifications']; ?></span>
					</a>
				 
				  </li>
				<li id="feedback">
				   <a href="feedback.php">
					<img class="menuIcon17" src="<?php echo $_html_relative_path; ?>images/feedback.png"/>
					<img class="menuIcon17Active hide"  src="<?php echo $_html_relative_path; ?>images/feedback-active.png"/>							
					<span><?php echo $language[$_SESSION['language']]['feedback']; ?></span>
					</a>
				  </li>
				<?php }?>  
				<?php if($poll_survey==1){?>
			    <li id="survey"  class="<?php echo $is_notification_show;?>">
				   <a href="survey.php" title="<?php echo $language[$_SESSION['language']]['surveys']; ?>">
					<img class="menuIcon21" src="<?php echo $_html_relative_path; ?>images/feedback.png"/>
					<img class="menuIcon21Active hide"  src="<?php echo $_html_relative_path; ?>images/feedback-active.png"/>							
					<span><?php echo $language[$_SESSION['language']]['surveys']; ?> </span>
					</a>
				 
				  </li>

				  <li id="poll"  class="<?php echo $is_notification_show;?>">
				   <a href="poll.php" title="<?php echo $language[$_SESSION['language']]['polls']; ?>">
					<img class="menuIcon22" src="<?php echo $_html_relative_path; ?>images/feedback.png"/>
					<img class="menuIcon22Active hide"  src="<?php echo $_html_relative_path; ?>images/feedback-active.png"/>							
					<span> <?php echo $language[$_SESSION['language']]['polls']; ?>  </span>
					</a>
				 
				  </li>
			<?php }?>  
			  <!--<li class="nav-item">
				<a class="nav-link" href='notification_module.php'><img src="images/notification.svg">Notifications</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="feedback_module.php" >
				<img src="images/feedback.svg">Feedback</a>
				
							   
			  </li>-->
			  	<?php if($is_live_class==1){?>
				<li id="sess">
				  <a href="live_session.php">  
					<img class="menuIcon4"src="<?php echo $_html_relative_path; ?>images/live-session.png"/>
					<img class="menuIcon4Active hide"  src="<?php echo $_html_relative_path; ?>images/live-session-active.png"/>							
							<span> Live Session</span>
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
	  <?php }?>
 