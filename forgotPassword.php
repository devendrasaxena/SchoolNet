<?php
include_once 'header/loginHeader.php';
if(client_reg_id==5){
	header('dseu_forgotPassword.php');
	exit;	
}

$msg='';
$err='';
$succ='';

if(isset($_SESSION['error']) && $_SESSION['error'] != ''){
	if($_SESSION['error'] == '1'){
    $msg= '<label class="required showErr error login_msg_err" id="login_msg_err" >Invalid email address or problem in server</label>';
	}
	if($_SESSION['error'] == '2'){
    $msg = '<label class="required showErr error login_msg_err" id="login_msg_err" >Invalid user</label>';
	}
}
if(isset($_SESSION['success']) && $_SESSION['success'] != ''){
	if($_SESSION['success'] == '1'){
    $msg= '<label class="text-success showErr error login_msg_err" id="login_msg_err" >Your password has been sent to your email address</label>';
	}
	
}
if(isset($_SESSION['success']) && $_SESSION['success'] != ""){
         
		 $succ=$_SESSION['success'];
		unset($_SESSION['success']);
	
}
if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
		$err=$_SESSION['error'];
		unset($_SESSION['error']);
	
}

if(isset($_POST['login_email']) && $_POST['login_email'] != ""){

	$username = $_POST['login_email'];
	//echo "<pre>";print_r($_POST);exit;
     try{	
	
	     $password = getPassword($username);
		 $email=$username;  
		 include_once('forgot_password_mailer.php');
		
	   }//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		} 
	//echo "<pre>";print_r($status);exit;
	if($status == "ok"){
		 $_SESSION['success']=1;
		header("location:forgotPassword.php");
		exit;
	}elseif($status == "failed"){
		 $_SESSION['error']=1;
		header("location:forgotPassword.php");
		exit;
	}elseif($status == "inValidE"){
		 $_SESSION['error']=2;
		header("location:forgotPassword.php");
		exit;
	} 
	
 }
?> 

<div class="bgImg bgLoginImg">
 <img src="images/login_bg.png" class="relative bgDivImg bgLoginDivImg" alt=""/>
	  <div class="login_mainDiv">
		<div class="relative">
		<div class="midDiv">
		 <div class="loginDiv">

	  <form class="login-form" id="loginForm" action="" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="return loginValidation('login_email');" >
	
		 <p class="heading logon-heading relative">Forgot password </p>
		
		 <div class="loginBox relative">
		<div class="loginRegBox loginBox relative">
		
		  <div class="formMainDiv">
		   <p><!--Password will sent on your email-->
		   <?php if($err=='1'){?>
		  <div class="alert alert-danger col-sm-12">
			<button type="button" class="close" data-dismiss="alert" style="margin-top: -5px;">x</button>
			<i class="fa fa-ban-circle"></i><?php echo $msg;?> </div>
		  <?php } ?>
		   
		 <?php if($succ=='1'){?>
		  <div class="alert alert-success col-sm-12">
			<button type="button" class="close" data-dismiss="alert" style="margin-top: -5px;">x</button>
			<i class="fa fa-ban-circle"></i><?php echo $msg;?></div>
		  <?php } ?></p>
		   <div class="form-Div">
			  <label class="label">Email</label>
			  <div class="inputDiv">
				<input type="text" id="login_email" name="login_email" class="inputText" value="" tabindex="1" autocomplete="off" placeholder="Enter your email address" />
			  </div>
		  </div>
			<div class="clear"></div>
		  <div class="btnDiv">
			 <div class="btnBg">
			   <button type="submit" class="btn" tabindex="2">Submit</button>
			  </div>
			</div>
		  </div>
		  </div> <div class="clear"></div>
		   <div class="text-center relative taketestDiv">
		    <a class="taketest text-right underLine" href="index.php" tabindex="3">Login</a> 
		  <!--|  <a class="taketest text-right underLine" href="signup.php" tabindex="4">Sign up</a>-->
		  </div>
		    <div class="clear"></div></div>
		<img src="images/group-2-copy.svg" class="responsive login_iconImg" alt=""/>
		 <img src="images/login_left.png" class="responsive login_leftImg" alt=""/>
		<img src="images/login_right.png" class="responsive login_rightImg" alt=""/>
		</form>	
		</div>
	 </div>
  </div>	
	
<?php include_once 'footer/loginFooter.php';?>
<script>
	function loginValidation(email){
	   var errMsg='';   
      $("#"+email).next('.showErr').remove();
	 var setFlag = 0;
	if($("#"+email).val() == ""){
		 $("#"+email).after('<img src="images/error-icon.png" class="showErr errorImg" /> <label class="required showErr error" id="login_email_err"> Please enter a valid email address</label>');
		 $("#"+email).addClass("errorClassbdr");
		 setFlag = 1;
		 $("#"+email).focus();
		return false;
	}

	if($("#"+email).val() != ""){
			var emailValue=$("#"+email).val();
			var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			if(!regex.test(emailValue)) {
				 $("#"+email).after('<img src="images/error-icon.png" class="showErr errorImg" /> <label class="required showErr error" id="login_email_err">Please enter a valid email address</label>');
				 $("#"+email).addClass("errorClassbdr");
				setFlag = 1;
				$("#"+email).focus();
			   return false;
			}
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
	   // console.log(dInput);
		$(".showErr").html('');
		$(".login_msg_err").hide('');
		$(".showErr").removeClass("errorClassbdr");
		$(".inputText").removeClass("errorClassbdr");
		$('.showErr').remove();
	});
	$(".inputText").keypress(function() {
		$(".showErr").html('');
		$(".login_msg_err").hide('');
		$(".inputText").removeClass("errorClassbdr");
		$('.showErr').remove();
	});

}); 
</script>  
