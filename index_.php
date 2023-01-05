<?php
include_once 'header/loginHeader.php';

$msg='';
$err='';
$msgPop='';
if(isset($_POST['login_email']) && $_POST['login_email'] != "" && $_POST['login_password']){

	$username = strip_tags($_POST['login_email']);
	$password = strip_tags($_POST['login_password']);
	//echo "<pre>";print_r($_POST);exit;
	$status = userLogin($username,$password,$client_name);
	//echo "<pre>";print_r($status);exit;
	if($status->user_id != ""){
		/* if($status->is_active==0){
			$_SESSION['error']=3;//Your account deactivated
			header('location:admin.php');
			exit;
		}else{ */
			
			$_SESSION['user_id'] = $status->user_id;
			$_SESSION['role_id'] = $status->roleId;
			if($_SESSION['role_id']==2){
				$_SESSION['token'] = $status->token;
				$_SESSION['package_code'] = $status->package_code;
			} 
			$_SESSION['user_group_id'] = $status->user_group_id;
			$user_dtl = userdetails($status->user_id);
			$center_id = centerDetails($status->user_id);
			$_SESSION['username'] = $user_dtl->first_name;
			$_SESSION['client_id'] = $user_dtl->client_id;
			$_SESSION['center_id'] = $center_id->center_id;
			$firstTimeLogin = $user_dtl->firstTime_login;
			$configClientId=$customer_id;
			//echo $user_dtl->client_id."<pre>";echo $configClientId;
		//echo "<pre>";print_r($user_dtl);exit;
			if($configClientId == $user_dtl->client_id){// super Admin
				//echo $configClientId ."==".$user_dtl->client_id;exit;
					if($status->roleId == 3){// super Admin
						//header('location:admin/webinar.php');
						header('location:admin/dashboard.php');
						exit;
					}else if($status->roleId ==7){// region Admin
						$centerObj = new centerController();
						$region_arr=$centerObj->getRegionDetailsByUserId($status->user_id);
						$_SESSION['region_id'] = $region_arr[0]['region_id'];
						header('location:admin/dashboard.php');
						exit;
					}else if($status->roleId==4){// center Admin
						header('location:centerAdmin/dashboard.php');
						exit;
					}else if($status->roleId==1 ){// 1  teacher
						header('location:trainer/dashboard.php');
						exit;
					}else if($status->roleId==2){// 2 Student
					     $expiry_date1 = strtotime($status->expiry_date);
						$cTime1 = strtotime($status->cTime);
						if($expiry_date1!='' && $expiry_date1>0){
							  $diffInSeconds = $cTime1 - $expiry_date1;
							  if($diffInSeconds>0){
									//$_SESSION['error']=5;
									header('location:user/dashboard.php');
									exit();
								
							}else{
								
									   //echo "-->".$firstTimeLogin;
									   if($firstTimeLogin==""){
										  header('location:user/dashboard.php');
										  exit;
										}else{
										   //header('location:welcome.php');
										   $_SESSION['default'] = 0;
											  $firstVisitArr=json_decode($firstTime_login);
											if($firstVisitArr[1]->tag=='dashboard' && $firstVisitArr[1]->visit=='1') {
											   $_SESSION['headTitle']="Resume";
											}else{
												  $_SESSION['headTitle']="Start";
											}

											header("Location:user/dashboard.php");
											exit;

						         } 
							}
						}else{
							
								 if($firstTimeLogin==""){
								  header('location:user/dashboard.php');
								  exit;
								}else{
								   //header('location:welcome.php');
								   $_SESSION['default'] = 0;
									  $firstVisitArr=json_decode($firstTime_login);
									if($firstVisitArr[1]->tag=='dashboard' && $firstVisitArr[1]->visit=='1') {
									   $_SESSION['headTitle']="Resume";
									}else{
										  $_SESSION['headTitle']="Start";
									}

									header("Location:user/dashboard.php");
									exit;
							
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
					
				}	
				
		//}////close active user
		
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
 <img src="images/login_bg.png" class="relative bgDivImg bgLoginDivImg" alt="" />
	  <div class="login_mainDiv">
		<div class="relative">
		<div class="midDiv">
		<div class="loginDiv">

	  <form class="login-form" id="loginForm" action="" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="return  loginValidation('login_email','login_password')">
	
		 <p class="heading logon-heading relative">Log in to your <?php echo APP_NAME;?> account</p>
		 <div class="loginBox relative">
		<div class="loginRegBox loginBox relative">
		  <div class="formMainDiv">
		   <div class="form-Div">
			  <label class="label">Email</label>
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
		  </div>
		  </div> <div class="clear"></div>
		   <div class="text-center relative taketestDiv" style="display:none">
		   <a class="taketest text-right underLine" href="" tabindex="4">Sign up</a>  | <a class="taketest text-right underLine" href="" tabindex="5">Forgot password?</a></div>
		    <div class="clear"></div></div>
		<img src="images/group-2-copy.svg" class="responsive login_iconImg" alt=""/>
		 <img src="images/login_left.png" class="responsive login_leftImg"alt=""/>
		<img src="images/login_right.png" class="responsive login_rightImg" alt=""/>
		</form>	
		</div>
	 </div>
  </div>	
	 <div class="clear"></div>	

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
