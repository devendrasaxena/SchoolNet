<?php
//echo "<pre>";print_r($_SESSION);exit;
$centerObj = new centerController(); 
$adminObj = new centerAdminController();
$proObj = new productController();
$productPath='learning_module.php';

$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$client_id = isset($_SESSION['client_id']) ? $_SESSION['client_id'] : null;
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
$center_id = $userInfo->center_id;
$centerId = $userInfo->center_id;
$loginid= $userInfo->loginid;
$is_active= $userInfo->is_active;
$center_description= $userInfo->center_description;
$center_status= $userInfo->center_status;
$region_id= $userInfo->region;
$user_ip_address= $userInfo->ip_address;
//echo "<pre>";print_r($userInfo);exit;
include_once($_html_relative_path.'header/check_ip.php');
$langArr=$langObj->getLanguageList();

$centerDetails=$adminObj->getCenterDetails();
$centerName=$centerDetails[0]['name'];
$totalBatch=0;	
$teacherData = $adminObj->getUserDataByID($userId,$loginid, 1);
//$batchData= $teacherData->batch_id;
$batchData = $adminObj->getBatchDetailByUserID($userId,1);
$batchDataArr=array();
$batchIdArr=array();
$stdRowsData=array();
foreach($batchData as $key=>$bValue){
	$batch_id=$bValue['batch_id'];
	$batchIdArr[] = $bValue['batch_id'];
	$batchDataArr[] = $adminObj->getBatchNameByID($batch_id);
	$stdRowsData[] =$adminObj->getUserList(2, $batch_id);
	$batchMandatoryData=implode(",",$batchMandatoryData1);//$batchData[0]['mandatory'];

 //echo "<pre>";print_r($batchData1);
}
//echo "<pre>";print_r($stdRowsData); 
 $totalStudent=0;
if(count($stdRowsData)>0){
	 $totalStudent1=0;
		foreach($stdRowsData as $key=>$bValue){
			$totalStudent1+=count($bValue);
	 }
	 $totalStudent+=$totalStudent1;
 }else{
	 $totalStudent=0;
 }
//echo "<pre>";print_r($totalStudent); 
if(isset($_SESSION['batch_id']) && $_SESSION['batch_id']!=''){
	$batchId1=$_SESSION['batch_id'];
	$batchData2 = $centerObj->getBatchDataByID($batchId1,$center_id);
	$batchId= $batchData2[0]['batch_id'];
	$_SESSION['batch_id']=$batchId;
	$batch_id=$batchId;
}else{
   $batchId= $batchData[0]['batch_id'];
   $_SESSION['batch_id']=$batchId;
   $batch_id=$batchId;
}	
//echo "<pre>";print_r($batch_id);	
	
if(count($batchDataArr[0])>0){
  $totalBatch=count($batchDataArr[0]);
 }else{
  $totalBatch=0;
}
//echo "-->".$userId;
$teacherBatchCount=$adminObj->getTeacherBatchCount($userId);



if(isset($_SESSION['product_id']) && $_SESSION['product_id']!=''){
	$product_id=$_SESSION['product_id'];
	if($product_id==10){
		$COURSE_NAME='Class';
		$COURSE_NAMES='Class';
	}else{
	   $COURSE_NAME='Level';
	   $COURSE_NAMES='Level';	
	}
	$getProductInfo=$centerObj->getBatchDataByIDDetails($batch_id,$center_id,$product_id);
	
	
}else{
	
	$getProductInfo='';
	$product_id='';

}

 //echo "<pre>"; print_r($getProductInfo); //die;
//$getCourseProductData=array();
$getProductData=array();
if(count($getProductInfo)>0){
	foreach ($getProductInfo as $key => $val) {
	  $product_id=$val['product_id'];
	 // $getCourseProductData=$val;
	  $getProductData[]=$proObj->getProdcutDetailById($product_id);
	}
}else{
	//$getCourseProductData=$proObj->getProductByClientId($client_id);
	$getProductData[]='';
}	
   $uToken=getUserRefreshToken($email_id,$userId);	
   $userToken=$uToken->token;	
	$package_code = isset($_SESSION['package_code']) ? $_SESSION['package_code'] : null;

	$checkCourseLevel = checkCourseLevelVisitByUserToken($userToken);

	$user_start_level=$checkCourseLevel->user_start_level;
	$user_current_level=$user_current_level=(showCurrentlevel==1)? user_current_level:$checkCourseLevel->user_current_level;


	$getRange=$user_current_level; 
	$level='Module '.$getRange;
	$levelJump=$level; 
	
$profileImgPath=$_html_relative_path."profile_pic/";
$profileImgDefault=$_html_relative_path."images/avatar.jpg";
//$profileImgPath=$profile_img_hosting_url; //emp server path
$regionDetail=$adminObj->getLogoById($region_id);

if($is_active==0){ 
 
	 header('location:../logout.php');
      die();	 
 
}
?>
<?php if(SHOW_REGION_THEME==1){?>
		<link rel="stylesheet" href="<?php echo $_html_relative_path; ?>css/theme<?php echo $region_id;?>.css"/>
	<?php }?>

</head>
<body class="bgDiv">
 <div class="submitPopup" id="loaderDiv" >
   <div class="overlay"></div><div class="loaderImageDiv"></div>
 </div>
  <div class="boxZindexBG"></div>
  <section class="vbox">
  <div class="header relative">
   <div class="logo">
		<?php if($regionDetail!='' && $regionDetail['is_app_logo_show']==1){?> 
		<a href="dashboard.php"><img src="<?php echo $_html_relative_path.applogo; ?>" class="logoImg"/></a>
		<?php }elseif($regionDetail!='' && $regionDetail['is_app_logo_show']==0){}else{?> 
		  <a href="dashboard.php"><img src="<?php echo $_html_relative_path.applogo; ?>" class="logoImg"/></a>
        <?php }?> 
		<?php if($regionDetail!='' && $regionDetail['is_region_logo_show']==1){?> 
		<a href="dashboard.php"><img src="<?php echo $_html_relative_path."/images/region/".$regionDetail['region_logo']; ?>" class="logoImg"/></a>
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
			<span class="userIcon"> <?php if($teacherData->system_name == ''){ ?><img class="userIcon" src="<?php echo $_html_relative_path; ?>images/userIcon.png"/><?php }else{ ?><img class="userIcon" src="<?php echo $profile_img_hosting_url.$teacherData->system_name; ?>" style="border:solid thin #111;border-radius:100%"/><?php } ?></span> 
			<span class="user"><?php echo $username; ?></span>
			<a class="" data-toggle="dropdown"   title="<?php echo $language[$_SESSION['language']]['my_profile']; ?>">
			 <span class="userArroIcon dropdown hidden-xs" id="rightArrowMenu"><img class="userArroIcon" src="<?php echo $_html_relative_path; ?>images/arrowDown.png"/>
			</a> 
			
			<ul class="dropdown-menu dropdown-menu-right" id="profileDrop" style="left:55px">
			<li><a href="<?php echo $_html_relative_path.$menu_relative; ?>profile.php"  title="<?php echo $language[$_SESSION['language']]['my_profile']?>"><span class="userIcon"><?php if($teacherData->system_name == ''){ ?><img class="userIcon" src="<?php echo $_html_relative_path; ?>images/profile.png"/><?php }else{ ?><img class="userIcon" src="<?php echo $profile_img_hosting_url.$teacherData->system_name; ?>" style="border:solid thin #111;border-radius:100%"/><?php } ?></span>  <span class="liTxt"><?php echo $language[$_SESSION['language']]['my_profile']; ?> <?php //echo $roleName; ?></span></a> </li>
			<li class="hide"> <a href="feedback.php" title="<?php echo $language[$_SESSION['language']]['feedback'];  ?>">
			<span class="userIcon"><img class="menuIcon" src="<?php echo $_html_relative_path; ?>images/feedback.png"/></span>
					<span><?php echo $language[$_SESSION['language']]['feedback']; ?></span>
					</a> </li>
			 <li><a class="" href="<?php echo $_html_relative_path; ?>logout.php" title="<?php echo $language[$_SESSION['language']]['logout'];?>"><span class="userIcon"><img class="userIcon" src="<?php echo $_html_relative_path; ?>images/logout.png"/></span> <span class="liTxt"> <?php echo $language[$_SESSION['language']]['logout']; ?></span></a> </li>
			</ul>

			
		<?php  if(strpos($_SERVER['REQUEST_URI'], 'learning_module.php') !== false || strpos($_SERVER['REQUEST_URI'], 'module.php') !== false){
			if(count($getProductData)>1){?>
			<span class="switchDiv dropdown" style="padding: 0px 5px 0px 25px;">
			<a href="javascript:void(0)"  data-toggle="dropdown">
			<span class="userArroIcon dropdownSwitch"><!--<img class="userArroIcon" src="<?php echo $_html_relative_path; ?>images/arrowDown.png"/>-->
			<i class="fa fa-ellipsis-h"></i></span></a>
			<ul class="dropdown-menu dropdown-menu-right switchDrop" id="switchDrop">
			<h6> Recently Used</h6>
			 <?php $i=1;
				 foreach($getProductData as $key=>$val ){ 
				
				 ?>
				<li><h4><?php echo $val['product_name'];?></h4>
				<?php if($val['product_desc']!=''){?><div class="pDesc"><p><?php echo $val['product_desc']; ?></p></div><?php }?>
				 <?php if($product_id!=$val['id']){?>
					 <a class="text-right"id="pro<?php echo $i;?>" href="javascript:void(0)" path="<?php echo $productPath;?>" onclick="visitProduct('<?php echo $i;?>','<?php echo $val['id']?>','<?php echo $productPath;?>')">Go to <?php //echo $val['product_name'];?> <i class="fa fa-arrow-right"></i></a></li>
				 <?php }?>
				
			 <?php $i++;}?>
			</ul>
		   </span>
             <script>
			 function visitProduct(id,pid,path){
				 if(pid!==''){
				  var data = {action: 'set_visitproduct',product_id:pid};
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
		}?>
			</span>
		</span>
		<a id="btn-link" class="btn-link visible-xs"  href="javascript:void(0)" onclick="myMenu();">
		<i class="fa fa-bars"></i>
	    </a>
		</div>
		<div class="clear"></div>
	
		
	</div>
	 <!-- mobile nav -->
	       <nav class="nav-primary navMobile" id="nav-mobile" >
               <ul class="nav">
			     <div class="headingNav"><?php echo $language[$_SESSION['language']]['admin_view']; ?></div>
                   <li id="home1" class="active">
                     <a href="<?php echo $_html_relative_path.$menu_relative; ?>dashboard.php" >
                        <img class="menuIcon1" src="<?php echo $_html_relative_path; ?>images/dashboard.png"/>
						<img class="menuIcon1Active hide" src="<?php echo $_html_relative_path; ?>images/dashboard-active.png"/>
                        <span><?php echo $language[$_SESSION['language']]['home']; ?></span>
                      </a>
                    </li>
					<li id="lmodule1">
                     <a href="product.php<?php //echo $lpath;?>">  
				     <img class="menuIcon2" src="<?php echo $_html_relative_path; ?>images/e_module.png"/>
					 <img class="menuIcon2Active hide" src="<?php echo $_html_relative_path; ?>images/e_module-active.png"/>				   
						<span><?php echo $language[$_SESSION['language']]['classes']; ?></span>
                          </a>
                    </li>
					<li id="std1">
                      <a href="<?php echo $_html_relative_path.$menu_relative; ?>studentList.php"  >
                        <img class="menuIcon5" src="<?php echo $_html_relative_path; ?>images/feedback.png"/>
						<img class="menuIcon5Active hide"  src="<?php echo $_html_relative_path; ?>images/feedback-active.png"/>	
                        <span><?php echo $language[$_SESSION['language']]['learners']; ?></span>
                      </a>
                    </li>
					<?php if($is_assignment==1){?>
					
					  <li id="asm1">
						  <a href="assignments.php">
							<img class="menuIcon8" src="<?php echo $_html_relative_path; ?>images/assignment.png"/>
										<img class="menuIcon8Active hide"  src="<?php echo $_html_relative_path; ?>images/assignment-active.png"/>		
							<span>Assignments</span>
						  </a>
                      </li> 
					 <?php }?>
					 <?php if($region_id==5){?>
					<li id="rpt1" >
                      <a href="users_report_dseu.php">
                        <img class="menuIcon6" src="<?php echo $_html_relative_path; ?>images/assignment.png"/>
						<img class="menuIcon6Active hide"  src="<?php echo $_html_relative_path; ?>images/assignment-active.png"/>		
                        <span>Reports</span>
                      </a>
                    </li>
					<?php }?>
					<?php if($is_live_class==1){?>
				    <li id="webinar1">
                      <a href="webinar-bbb.php">
                       <img class="menuIcon7" src="<?php echo $_html_relative_path; ?>images/live-session.png"/>
						<img class="menuIcon7Active hide"  src="<?php echo $_html_relative_path; ?>images/live-session-active.png"/>	
                        <span>Live Classes</span>
                      </a>
                    </li>
					<?php }?>
					<?php if($is_notification==1){?>
					<li id="notification1" class="">
					   <a href="<?php echo $_html_relative_path.$menu_relative; ?>notification.php">
						<img class="menuIcon16" src="<?php echo $_html_relative_path; ?>images/feedback.png"/>
						<img class="menuIcon16Active hide"  src="<?php echo $_html_relative_path; ?>images/feedback-active.png"/>							
						<span> <?php echo $language[$_SESSION['language']]['notifications']; ?></span>
						</a>
					 
					  </li>
					<li id="feedback1" class="">
					   <a href="<?php echo $_html_relative_path.$menu_relative; ?>feedback.php">
						<img class="menuIcon17" src="<?php echo $_html_relative_path; ?>images/feedback.png"/>
						<img class="menuIcon17Active hide"  src="<?php echo $_html_relative_path; ?>images/feedback-active.png"/>							
						<span><?php echo $language[$_SESSION['language']]['feedback']; ?></span>
						</a>
					  </li>
					<?php }?>
			  <li class="visible-xs" id="profile1">
			  <a href="<?php echo $_html_relative_path.$menu_relative; ?>profile.php">
			  <?php if($teacherData->system_name == ''){ ?><img class="userIcon profileIcon" src="<?php echo $_html_relative_path; ?>images/profile.png"/><?php }else{ ?><img class="userIcon profileIcon" src="<?php echo $profile_img_hosting_url.$teacherData->system_name; ?>" style="border:solid thin #111;border-radius:100%"/><?php } ?> <span> <?php echo $language[$_SESSION['language']]['my_profile']; ?>  </span></a> </li>
			  <li class="visible-xs"><a class="" href="<?php echo $_html_relative_path; ?>logout.php"><img class="userIcon" src="<?php echo $_html_relative_path; ?>images/logout.png"/> <span > <?php echo $language[$_SESSION['language']]['logout']; ?></span></a> </li>
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
                     <a href="<?php echo $_html_relative_path.$menu_relative; ?>dashboard.php" title="<?php echo $language[$_SESSION['language']]['overview_of_progress'];   ?>">
                        <img class="menuIcon1" src="<?php echo $_html_relative_path; ?>images/dashboard.png"/>
						<img class="menuIcon1Active hide" src="<?php echo $_html_relative_path; ?>images/dashboard-active.png"/>
                        <span><?php echo $language[$_SESSION['language']]['home']; ?></span>
                      </a>
                    </li>
					<li id="lmodule">
                     <a href="product.php<?php //echo $lpath;?>">  
				     <img class="menuIcon2" src="<?php echo $_html_relative_path; ?>images/e_module.png"/>
					 <img class="menuIcon2Active hide" src="<?php echo $_html_relative_path; ?>images/e_module-active.png"/>				   
						<span><?php echo $language[$_SESSION['language']]['classes']; ?></span>
                          </a>
                    </li>
					<li id="std">
                      <a href="<?php echo $_html_relative_path.$menu_relative; ?>studentList.php"  title="<?php echo $language[$_SESSION['language']]['learners'];   ?>">
                        <img class="menuIcon5" src="<?php echo $_html_relative_path; ?>images/feedback.png"/>
						<img class="menuIcon5Active hide"  src="<?php echo $_html_relative_path; ?>images/feedback-active.png"/>	
                        <span><?php echo $language[$_SESSION['language']]['learners']; ?></span>
                      </a>
                    </li>
					<?php if($is_notification==1){?>
					<li id="notification" class="">
					   <a href="<?php echo $_html_relative_path.$menu_relative; ?>notification.php" title="<?php echo $language[$_SESSION['language']]['notifications'];   ?>">
						<img class="menuIcon16" src="<?php echo $_html_relative_path; ?>images/feedback.png"/>
						<img class="menuIcon16Active hide"  src="<?php echo $_html_relative_path; ?>images/feedback-active.png"/>							
						<span> <?php echo $language[$_SESSION['language']]['notifications']; ?></span>
						</a>
					 
					  </li>
					
					  <li id="feedback" class="">
                      <a href="<?php echo $_html_relative_path.$menu_relative; ?>feedback.php" title="<?php echo $language[$_SESSION['language']]['feedback'];   ?>">
                        <img class="menuIcon17" src="<?php echo $_html_relative_path; ?>images/assignment.png"/>
						<img class="menuIcon17Active hide"  src="<?php echo $_html_relative_path; ?>images/assignment-active.png"/>		
                        <span><?php echo $language[$_SESSION['language']]['feedback']; ?> </span>
                      </a>
                    </li>
					<?php }?>
					<?php if($is_assignment==1){?>
					<li id="asm">
                      <a href="assignments.php">
                        <img class="menuIcon8" src="<?php echo $_html_relative_path; ?>images/assignment.png"/>
						            <img class="menuIcon8Active hide"  src="<?php echo $_html_relative_path; ?>images/assignment-active.png"/>		
                        <span>Assignments</span>
                      </a>
                    </li>
					<?php }?>
					<?php if($region_id==5){?>
					<li id="rpt" >
                      <a href="users_report_dseu.php">
                        <img class="menuIcon6" src="<?php echo $_html_relative_path; ?>images/assignment.png"/>
						<img class="menuIcon6Active hide"  src="<?php echo $_html_relative_path; ?>images/assignment-active.png"/>		
                        <span>Reports</span>
                      </a>
                    </li>
					<?php }?>
				<?php if($is_live_class==1){?>
				<li id="webinar">
                      <a href="webinar-bbb.php">
                       <img class="menuIcon7" src="<?php echo $_html_relative_path; ?>images/live-session.png"/>
						<img class="menuIcon7Active hide"  src="<?php echo $_html_relative_path; ?>images/live-session-active.png"/>	
                        <span>Live Classes</span>
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