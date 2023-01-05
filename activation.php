<?php
include_once 'header/loginHeader.php';
$regObj = new registrationController();
$msg='';
$msg1='';
$err='';
$err1='';
$succ='';
$succ1='';
$reg_status = 0;
$regArr=$_SESSION['REGISTRATION']['FIELDS'];
//unset($_SESSION['REGISTRATION1']);
if(isset($_SESSION['REGISTRATION1']['error']) && $_SESSION['REGISTRATION1']['error'] != ''){
	if($_SESSION['REGISTRATION1']['error'] == '1'){
    $msg= $_SESSION['REGISTRATION1']['ERR']['MSG'];
	}
	if($_SESSION['REGISTRATION1']['error'] == '2'){
    $msg =  $_SESSION['REGISTRATION1']['ERR']['MSG'];
	}
}
if(isset($_SESSION['REGISTRATION1']['success']) && $_SESSION['REGISTRATION1']['success'] != ''){
	if($_SESSION['REGISTRATION1']['success'] == '1'){
    $msg= 'OTP has been resend to your email address';
	}
	
}
if(isset($regArr['success']) && $regArr['success'] != ''){
	if($regArr['success'] == '1'){
    $msg1= 'OTP has been sent to your email address';
	}
	
}

if(isset($_SESSION['REGISTRATION1']['success']) && $_SESSION['REGISTRATION1']['success'] != ""){
         
		 $succ=$_SESSION['REGISTRATION1']['success'];
		unset($_SESSION['REGISTRATION1']['success']);
	
}
if(isset($_SESSION['REGISTRATION1']['error']) && $_SESSION['REGISTRATION1']['error'] != ""){
		$err=$_SESSION['REGISTRATION1']['error'];
		unset($_SESSION['REGISTRATION1']['error']);
	
}
if(isset($regArr['error']) && $regArr['error'] != ""){
		$err1=$regArr['error'];
		unset($regArr['error']);
		unset($_SESSION['REGISTRATION']['error']);
	
}
if(isset($regArr['success']) && $regArr['success'] != ""){
         
		 $succ1=$regArr['success'];
		 unset($regArr['success']);
		 unset($_SESSION['REGISTRATION']['success']);
		 
	
}

if( isset($_SESSION['REGISTRATION1']['ERR']['MSG']) ){
    $msg = trim($_SESSION['REGISTRATION1']['ERR']['MSG']);
    $reg_status = 0;
	//unset($_SESSION['reg_status']);
	unset($_SESSION['REGISTRATION1']['ERR']['MSG']);
}
if( isset($_SESSION['REGISTRATION']['expires_on']) ){
    $expires_on=$_SESSION['REGISTRATION']['expires_on'];
}
if( isset($_SESSION['REGISTRATION1']['expires_on']) ){
    $expires_on=$_SESSION['REGISTRATION1']['expires_on'];
}


if(isset($_POST['resend_otp']) && $_POST['resend_otp'] != ""){
	  $arr1 = $regObj->regGenerateOTP($regArr);
		  if($arr1['status'] == 1){
			  $_SESSION['REGISTRATION1'] = array();
			  $_SESSION['REGISTRATION1']['expires_on']=$arr1['expires_on'];
			  $expires_on=$arr1['expires_on'];
			  $_SESSION['REGISTRATION1']['success']=1;
			   header('location:activation.php');
			   exit();

		  }else{  
		        $_SESSION['REGISTRATION1'] = array();
				$_SESSION['REGISTRATION1']['ERR']['MSG'] = $arr1['msg'];
				$_SESSION['REGISTRATION1']['error']=1;
				header('Location:activation.php');
				die;
		  } 
	
}

if(isset($_POST['reg_otp']) && $_POST['reg_otp'] != ""){
	 $user_otp = $_POST['reg_otp'];
	 $is_otp_based=1;
     try{	
	     $arr2 = $regObj->regVerifyOTP($user_otp,$regArr);
		  if($arr2['status'] == 1){
			$is_email_verified=1;
			 $arr = $regObj->register($is_email_verified,$is_otp_based,$regArr);
			$_SESSION['REGISTRATION'] = array();
			$_SESSION['REGISTRATION']['ERR'] = array();
			$_SESSION['REGISTRATION']['SUCCESS'] = array();
			  if($arr['status'] == 0){
				    $_SESSION['REGISTRATION']['FIELDS'] = $regArr;
					$_SESSION['REGISTRATION']['ERR']['MSG'] = $arr['msg'];
					$_SESSION['REGISTRATION']['reg_status']=0;
					header('Location:signup.php');
					die;
				}else if($arr['status'] == 1){ //success start
					$_SESSION['REGISTRATION1']['FIELDS'] = $regArr;
					$_SESSION['REGISTRATION']['SUCCESS']['MSG'] = $arr['msg'];
					$username=$regArr['reg_email'];
					$password=$regArr['reg_password'];
					$_SESSION['reg_email']=$username;
					$_SESSION['reg_password']=$password;
					$_SESSION['reg_status']=1;
					$_SESSION['user_id']=$arr['user_id'];
					$_SESSION['token']=$arr['token'];
					header('location:do-registration_login.php');
					exit();//success end
				 }else{
					$regArr['error']=1;
					header('location:signup.php');
					exit();
			 }  
		  } else if($arr2['status'] == 0){
			   echo $arr2['msg'];
				$_SESSION['REGISTRATION1'] = array();
				$_SESSION['REGISTRATION1']['ERR']['MSG'] = $arr2['msg'];
				$_SESSION['REGISTRATION1']['error']=1;

				header('Location:activation.php');
				exit();
		  }
		  else{
			    $_SESSION['REGISTRATION1'] = array();
				$_SESSION['REGISTRATION1']['ERR']['MSG'] = $arr2['msg'];
				$_SESSION['REGISTRATION1']['error']=2;
				header("location:activation.php");
				exit;
		  }
		
	   }//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
	} 

 }  
?> 

<div class="bgImg bgLoginImg">
 <img src="images/login_bg.png" class="relative bgDivImg bgLoginDivImg" alt=""/>
	  <div class="login_mainDiv">
		<div class="relative">
		<div class="midDiv">
		 <div class="loginDiv">

	  <form class="login-form" id="loginForm" action="" method="post" enctype="multipart/form-data" autocomplete="off">
	
		 <p class="heading logon-heading relative">Verification</p>
		
		 <div class="loginBox relative">
		<div class="loginRegBox loginBox relative">
		
		  <div class="formMainDiv">
		   <p><!--Otp will sent on your email-->
		   <?php if($err=='1'){?>
		  <div class="alert alert-danger col-sm-12">
			<button type="button" class="close" data-dismiss="alert" style="margin-top: -5px;">x</button>
			<i class="fa fa-ban-circle"></i><?php echo $msg;?> </div>
		  <?php } ?>
		    <?php if($err1=='1'){?>
		  <div class="alert alert-danger col-sm-12">
			<button type="button" class="close" data-dismiss="alert" style="margin-top: -5px;">x</button>
			<i class="fa fa-ban-circle"></i><?php echo $msg1;?> </div>
		  <?php } ?>
		 <?php if($succ=='1'){?>
		  <div class="alert alert-success col-sm-12">
			<button type="button" class="close" data-dismiss="alert" style="margin-top: -5px;">x</button>
			<i class="fa fa-ban-circle"></i><?php echo $msg;?></div>
		  <?php } ?>
		  <?php if($succ1=='1'){?>
		  <div class="alert alert-success col-sm-12">
			<button type="button" class="close" data-dismiss="alert" style="margin-top: -5px;">x</button>
			<i class="fa fa-ban-circle"></i><?php echo $msg1;?></div>
		  <?php } ?></p>
		  <p class="text-center">We have 6 digit code to your email<br>
		  <?php echo $regArr['reg_email']; ?></p>
		   <div class="form-Div">
			  <label class="label"></label>
			  <div class="inputDiv">
				<input type="text" id="reg_otp" name="reg_otp" class="inputText" value="" tabindex="1" autocomplete="off" placeholder="Enter the OTP" maxlength="6" />
			  </div>
		  </div>
		  <div class="clear"></div>
		   <p class="timeDiv">Didn't receive the OTP? You can resend OTP in <span id="timer"></span></p>
		   <p class="resendDiv hide">
		   	<input type="hidden" id="resend_otp" name="resend_otp" value="" />
			 <input type="hidden" name="registration_form" value="1">	  
		   <button type="submit" class="btn" value="resendBtn" onclick="reSendOtp();">Resend OTP</button></p>
			<div class="clear"></div>
		  <div class="btnDiv">
			 <div class="btnBg">
			   <button type="submit" class="btn" tabindex="2">Verify</button>
			  </div>
			</div>
		  </div>
		  </div> <div class="clear"></div>
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

var timerOn = true;

function timer(remaining) {
  var m = Math.floor(remaining / 60);
  var s = remaining % 60;
  
  m = m < 10 ? '0' + m : m;
  s = s < 10 ? '0' + s : s;
  document.getElementById('timer').innerHTML = m + ':' + s;
  remaining -= 1;
  
  if(remaining >= 0 && timerOn) {
    setTimeout(function() {
        timer(remaining);
    }, 1000);
    return;
  }

  if(!timerOn) {
    // Do validate stuff here
    return;
  }
  $(".resendDiv").show();
  $(".timeDiv").hide();
  $("#resend_otp").val('resend');
  
  // Do timeout stuff here
  //alert('Timeout for otp');
}
timer(<?php echo $expires_on;?>);
$(document).ready(function(){
	$("#resend_otp").val('');
	$(".resendDiv").hide();
    $(".timeDiv").show();
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
 
(function (global) {

	if(typeof (global) === "undefined")
	{
		throw new Error("window is undefined");
	}

    var _hash = "!";
    var noBackPlease = function () {
        global.location.href += "#";

		// making sure we have the fruit available for juice....
		// 50 milliseconds for just once do not cost much (^__^)
        global.setTimeout(function () {
            global.location.href += "!";
        }, 50);
    };
	
	// Earlier we had setInerval here....
    global.onhashchange = function () {
        if (global.location.hash !== _hash) {
            global.location.hash = _hash;
        }
    };

    global.onload = function () {
        
		noBackPlease();

		// disables backspace on page except on input fields and textarea..
		document.body.onkeydown = function (e) {
            var elm = e.target.nodeName.toLowerCase();
            if (e.which === 8 && (elm !== 'input' && elm  !== 'textarea')) {
                e.preventDefault();
            }
            // stopping event bubbling up the DOM tree..
            e.stopPropagation();
        };
		
    };

})(window); 
 
</script>  
