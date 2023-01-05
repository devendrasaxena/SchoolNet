<?php
include_once 'header/loginHeader.php';

$msg='';
$err='';
$msgPop='';
if(isset($_POST['login_email']) && $_POST['login_email'] != "" && $_POST['login_password']){

	
	$username = strip_tags($_POST['login_email']);
	$password = strip_tags($_POST['login_password']);
	
	$status = userLogin($username,$password,$client_name);
	//echo "<pre>";print_r($status);exit;

	if($status->user_id != ""){
		
			$_SESSION['user_id'] = $status->user_id;
			$_SESSION['role_id'] = $status->roleId;
			if($_SESSION['role_id']==2){
				$_SESSION['token'] = $status->token;
			} 
		
			$_SESSION['user_group_id'] = $status->user_group_id;
			$user_dtl = userdetails($status->user_id);
			$center_arr = centerDetails($status->user_id);
			$_SESSION['username'] = $user_dtl->first_name;
			$_SESSION['client_id'] = $user_dtl->client_id;
			$_SESSION['center_id'] = $center_arr->center_id;
			$_SESSION['region_id'] = $center_arr->region;
			$firstTimeLogin = $user_dtl->firstTime_login;
			$configClientId=$customer_id;
 
			//if($configClientId == $user_dtl->client_id){
				//echo $client_reg_id ."==".$user_dtl->region;//exit;
				
					if($status->roleId == 3){// super Admin
						//header('location:admin/webinar.php');
						header('location:admin/dashboard.php');
						exit;
					}else if(client_reg_id==$user_dtl->region){

					if($status->roleId ==7){// region Admin
					    if($status->is_active==0){//Your account deactivated
						  $_SESSION['isActive']=$status->is_active;
						  header('location:isActive.php');
						  exit;
						}else{
							$centerObj = new centerController();
							$region_arr=$centerObj->getRegionDetailsByUserId($status->user_id);
							$_SESSION['region_id'] = $region_arr[0]['region_id'];
							header('location:admin/dashboard.php');
							exit;
						}
					}else if($status->roleId==4){// center Admin

							   if($status->is_active==0){//Your account deactivated
							  	 $_SESSION['isActive']=$status->is_active;
								  header('location:isActive.php');
								  exit;
							    //}elseif($user_dtl->release_status==0){//release from center account deactivated
									//$_SESSION['isActive']=0;
								  //header('location:isActive.php');
								   // exit;
								}elseif($user_dtl->center_status==0){//center account deactivated
									$_SESSION['isActive']=0;
								    header('location:isActive.php');
								    exit;
								}else{
									header('location:centerAdmin/dashboard.php');
									exit;
								}
					}else if($status->roleId==1 ){// 1 teacher
								$_SESSION['user_from']=$user_from;
							  if($status->is_active==0){//Your account deactivated
							      $_SESSION['isActive']=$status->is_active;
								  header('location:isActive.php');
								  exit;
								/*}elseif($user_dtl->release_status==0){//release from center user deactivated
									$_SESSION['isActive']=0;
								    header('location:isActive.php');
								    exit;*/
								}elseif($user_dtl->center_status==0){//center is deactivated
									$_SESSION['isActive']=0;
								    header('location:isActive.php');
								    exit;
									
								}else{
									header('location:trainer/dashboard.php');
									exit;
								}
						}else if($status->roleId==2){// 2 Student
						
							   $region_detail = regionDetails($user_dtl->region);
								if($region_detail->is_placement_test==1){
									$show_url='welcome.php';
								}else{
									$show_url='product.php';
								}
								//echo "dd<pre>";print_r($show_url);exit;

						     $_SESSION['user_from']=$user_from;
								if($status->is_active==0){//Your account deactivated
									   header('location:isActive.php');
									   exit;
								}elseif($user_dtl->center_status==0){//center account deactivated
										$_SESSION['isCenterActive']=0;
										header('location:isActive.php');
										exit;
								}else{
										
									  if($user_from=='b2c'){
										
										header('location:product.php');
										exit; 
										
										
									}else{
						
										 $expiry_date1 = strtotime($status->expiry_date);
										 $cTime1 = strtotime($status->cTime);
										if($expiry_date1!=''){
											  $diffInSeconds = $cTime1 - $expiry_date1;
											  if($diffInSeconds>0){
													//$_SESSION['error']=5;
													$_SESSION['diffInSeconds']=$diffInSeconds;
													header('location:isActive.php');
													exit();
												
											}else{
													   $_SESSION['default'] = 0;
														  $firstVisitArr=json_decode($firstTime_login);
														if($firstVisitArr[1]->tag=='dashboard' && $firstVisitArr[1]->visit=='1') {
														   $_SESSION['headTitle']="Resume";
														}else{
															  $_SESSION['headTitle']="Start";
														}
														header("Location:".$show_url);
														exit;
											}
										}else{
										
												
												   //header('location:welcome.php');
												   $_SESSION['default'] = 0;
													  $firstVisitArr=json_decode($firstTime_login);
													if($firstVisitArr[1]->tag=='dashboard' && $firstVisitArr[1]->visit=='1') {
													   $_SESSION['headTitle']="Resume";
													}else{
														  $_SESSION['headTitle']="Start";
													}

													header("Location:".$show_url);
													exit;

										}
						       }
					    }
					}else{
						$_SESSION['error']=4;
						header('location:index.php');
						exit;
						//$hide="displayNone";
					}
	            }else{
					$_SESSION['error']=1;
					header('location:index.php');
					exit;
					//$hide="displayNone";
			    }
	}else{
		$_SESSION['error']=2;
		 header('location:index.php');
		exit;
		
		
	}
}

if(isset($_SESSION['error']) && $_SESSION['error'] != " "){
	if($_SESSION['error'] == '1'){
      $msg= '<img src="images/error-icon.png" class="showErr errorImg" /> <label class="required showErr error login_msg_err" id="login_msg_err" >Username is not exist.</label>';
	}
	 if($_SESSION['error'] == '2'){
      $msg= '<img src="images/error-icon.png" class="showErr errorImg" /> <label class="required showErr error login_msg_err" id="login_msg_err" >Invalid Username or Password</label>';
	}
	if($_SESSION['error'] == '3'){
      $msg= '<img src="images/error-icon.png" class="showErr errorImg" /> <label class="required showErr error login_msg_err" id="login_msg_err" >Your account deactivated</label>';
	}
	 if($_SESSION['error']== '4'){
      $msg= '<img src="images/error-icon.png" class="showErr errorImg" /> <label class="required showErr error login_msg_err" id="login_msg_err" >Something is wrong. Please try again.</label>';
	}
	 if($_SESSION['error']== '5'){
      $msgPop= 1;
	}
	 
}

if(isset($_SESSION['error']) && $_SESSION['error'] != " "){
		$err=$_SESSION['error'];
		unset($_SESSION['error']);
	
}

?> 

<div class="bgImg bgLoginImg">
 
	  <div class="login_mainDiv">
		<div class="relative">
		<div class="midDiv row">
			<div class="col-sm-4  col-xs-6"><img src="images/theme5/leftTop.png" class="lefttopImg"/></div>
			<div class="col-sm-8  col-xs-6"><img src="images/theme5/midTop.png" class="midtopImg"/></div>
		</div>
		<div class="midDiv row">
		<div class="col-sm-2 xs-hidden"></div>
		<div class="col-sm-4  col-xs-4 text-left">
			<div class="col-sm-12 text-center"><img src="images/theme5/leftMidImg1.png" class="leftMidImg1"/></div>
			<div class="clear"></div>
			<div class="col-sm-12 text-center"> <img src="images/theme5/leftMidImg2.png" class="leftMidImg2"/></div>
		</div>
		<div class="col-sm-4  col-xs-8 text-left">
		     <div class="loginDiv">

				  <form class="login-form" id="loginForm" action="" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="return  loginValidation('login_email','login_password')">

					<div class="loginRegBox loginBox relative">
					  <p class="heading logon-heading relative">Log in to your <br><?php echo APP_NAME;?> account</p>
					  <div class="formMainDiv">
					   <div class="form-Div">
						  <label class="label">Roll No / Email</label>
						  <div class="inputDiv">
							<input type="text" id="login_email" name="login_email" class="inputText" value="" tabindex="1" autocomplete="off" autofocus />
						  </div>
					  </div>
						<div class="form-Div">
						  <label class="label">Password</label>
						  <div class="inputDiv">
							<input type="password" id="login_password" name="login_password" class="inputText"  value="" tabindex="2" autocomplete="off" /> 
							<!--<span class="reveal"><i class="fa fa-eye-slash"></i></span>-->
							 <span class="serverMsg" id="serverMsg"><?php if($err!= ''){ echo $msg;  } ?></span>
							
							
					  </div>
					  </div> <div class="clear"></div>
					  <div class="btnDiv">
						 <div class="btnBg">
						   <button type="submit" class="btn" tabindex="3">Login</button>
						  </div>
						</div>
						 <div class="clear"></div>
						 <div class="text-center relative taketestDiv">
					   <?php if(SELF_REGISTER==1){ $hide=''; }else{ $hide='hide';  }?>
					   <a class="taketest text-right underLine <?php echo $hide;?>" href="signup.php" tabindex="4">Sign up |</a>   <a class="taketest text-right underLine <?php echo $hide;?>" href="dseu_forgotPassword.php" tabindex="5">Forgot password?</a>
					   </div>
						<div class="clear"></div>
					  </div>
					  </div> 

					</form>	
				</div>
			</div>	
			<div class="col-sm-2 col-xs-1 vMidlle text-center"><img src="images/theme5/rightMidImg.png" class="rightMidImg"/></div>
	 <div class="clear"></div>	
  
	</div>
	 	
  </div>	
  <div class="bgFooter"></div>
<?php include_once 'footer/loginFooter.php';?>
<script>
var msg =<?php echo json_encode($msg);?>;
var msgPop =<?php echo json_encode($msgPop);?>;

	if(msgPop!=""){
	 alertPopup("Your trial period has been expired, please contact Pearson support.");
	}

function loginValidation(email,pass){
	   var errMsg='';
      $("#"+email).next('.showErr').remove();
	  $("#"+pass).next('.showErr').remove();
	 var setFlag = 0;
	if($("#"+email).val() == ""){
		 $("#"+email).after('<img src="images/error-icon.png" class="showErr errorImg" /> <label class="required showErr error" id="login_email_err"> Please enter a valid email address</label>');
		 $("#"+email).addClass("errorClassbdr");
		 setFlag = 1;
		 $("#"+email).focus();
		return false;
	}
	if($("#"+pass).val() == ""){
		 $("#"+pass).after('<label class="required showErr error" id="login_pass_err">Please enter a password</label>');
		$("#"+pass).addClass("errorClassbdr");
			setFlag = 1;
			$("#"+pass).focus();
			return false;
		}
	if($("#"+email).val() != ""){
			/* var emailValue=$("#"+email).val();
			var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			if(!regex.test(emailValue)) {
				 $("#"+email).after('<img src="images/error-icon.png" class="showErr errorImg" /> <label class="required showErr error" id="login_email_err">Please enter a valid email address</label>');
				 $("#"+email).addClass("errorClassbdr");
				setFlag = 1;
				$("#"+email).focus();
			   return false;
			} */
	 }

  if(setFlag == 1){
	  //alert("no")
    return false;
  }else{
	  //alert("yes")
	return true;
  }	 
}
$(document).ready(function(){
	$(".inputText").blur(function() {
		if(msg!=""){
			 $("#serverMsg").html('');
			 $(".serverMsg").hide('');
		}

	   // console.log(dInput);
		$(".showErr").html('');
		$(".login_msg_err").hide('');
		$(".showErr").removeClass("errorClassbdr");
		$(".inputText").removeClass("errorClassbdr");
		
		$('.showErr').remove();
	});
	$(".inputText").keypress(function() {
		if(msg!=""){
		  $("#serverMsg").html('');
		  $(".serverMsg").hide('');
		}
		$(".showErr").html('');
		$(".login_msg_err").hide('');
		$(".inputText").removeClass("errorClassbdr");
		$('.showErr').remove();
	});

}); 
</script>  
