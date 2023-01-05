<?php
/* unset($_SESSION['user_id']);
unset($_SESSION['role_id']);
unset($_SESSION['username']); */
include_once 'header/loginHeader.php';

header("location:dseu_login.php");//Not show forget password
		exit;
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

 
	  <div class="login_mainDiv">
		<div class="relative">
		<div class="midDiv row">
			<div class="col-sm-4"><img src="images/theme5/leftTop.png" class="lefttopImg"/></div>
			<div class="col-sm-8"><img src="images/theme5/midTop.png" class="midtopImg"/></div>
		</div>
		<div class="midDiv row">
		<div class="col-sm-2"></div>
		<div class="col-sm-4 text-left">
			<div class="col-sm-12 text-center"><img src="images/theme5/leftMidImg1.png" class="leftMidImg1"/></div>
			<div class="clear"></div>
			<div class="col-sm-12 text-center"> <img src="images/theme5/leftMidImg2.png" class="leftMidImg2"/></div>
		</div>
		<div class="col-sm-4">
		     <div class="loginDiv">
	      <form class="login-form" id="loginForm" action="" method="post" enctype="multipart/form-data" autocomplete="off" onsubmit="return loginValidation('login_email');" >
	
		
		 <div class="loginBox relative">
		   <div class="loginRegBox loginBox relative">
			 <p class="heading logon-heading relative">Forgot password </p>
	
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
			 <div class="clear"></div>
		   <div class="text-center relative taketestDiv">
		    <a class="taketest text-right underLine" href="index.php" tabindex="3">Login</a> 
		  <!--|  <a class="taketest text-right underLine" href="signup.php" tabindex="4">Sign up</a>-->
		  </div>
		    <div class="clear"></div>
		  </div>
		  </div> 
		 
		</div>
		
		</form>	
		</div>
			</div>	
			<div class="col-sm-2 vMidlle text-center"><img src="images/theme5/rightMidImg.png" class="rightMidImg"/></div>
	 <div class="clear"></div>	
    
	</div>
	 	
  </div>		
	<div class="bgFooter"></div>
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
