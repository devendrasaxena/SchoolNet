<?php
include_once('header/lib.php');
//echo "<pre>";print_r($_SESSION);exit;
include_once 'header/header.php';
 if(!isset($checkScore->score)){	
	if(isset($_POST['demo_login']) && $_POST['demo_login'] != ""){//only demo default

		 $_SESSION['default'] = $_POST['demo_login'];
		 header('location:score.php');
		 exit();

	}
	if(isset($_POST['test_login']) && $_POST['test_login'] != ""){//real test check

		  $_SESSION['default'] = 0;
		  $_SESSION['headTitle']="Start";
		  $userToken=$uToken->token;	
		  $package_code = isset($_SESSION['package_code']) ? $_SESSION['package_code'] : null;
		  $checkScore = checkLTIScoreByUserToken($userToken);
		 if(empty($checkScore->score)){	
			 header('location:lti_test.php');
			 exit();
		 }else{
			 $_SESSION['score']=$checkScore->score;
			 $_SESSION['user_start_level']=$checkScore->user_start_level;
			 $_SESSION['user_current_level']=$checkScore->user_current_level;
			 $_SESSION['user_current_description']=$checkScore->user_current_description;
			 $_SESSION['user_current_mapto']=$checkScore->user_current_mapto;	 
			 header("Location:score.php");
			 exit;
	 
		 }		 
	}
}else{	
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

 
?><section class="scrollable scrollableZindex">
<div class="bgImg bgLoginImg">
 <img src="images/login_bg.png"  class="relative bgDivImg bgLoginDivImg" alt="">
	  <div class="mainDiv login_mainDiv">
		<div class="relative">
		<div class="midDiv">

         <div class="wel-heading relative" >
		
		 <form class="login-form" id="loginForm" action="" method="post" enctype="multipart/form-data" autocomplete="off">
		 <input type="hidden" id="demo_login" name="demo_login" value="1"  autocomplete="off" /> 
		 <p class="welcome-heading relative">  	
		 <button type="submit" style="cursor: none;
    background: transparent;
    border: none;" onclick="startLogin()">Let's get started!</button></p></form>
		 <p class="sub-heading relative">Take a quick test to know your current level</p>
		 </div>
		 <div class="loginRegBox relative welcomeBoxBg">
		<div class="welcomeBox relative">
		  <div class="formMainDiv">
		  <div class="icon_text">
			  <div class="timeDiv">
				<span class="oval_iconDiv"><img src="images/oval.svg" class="oval"/></span>
				<span class="time">30 minutes</span>
				</div>
				<div class="clear"></div>
			  <div class="adaptsDiv">
				<span class="adapts_iconDiv"><img src="images/adapts_icon.svg" class="adapts_icon"/></span>
				<span class="adapts">Adapts based on your answers</span>
			 </div>
			 <div class="clear"></div>
			  <div class="headsetDiv">
				<span class="headset_iconDiv"><img src="images/headset_icon.svg" class="headset_icon"/></span>
				<span class="headset">Headset required</span>
			 </div>
		  
		  </div>
		  <div class="clear"></div>
		  <div class="btnDiv">
			 <div class="text-center">
			 <form class="login-form" id="ltiForm" action="" method="post" enctype="multipart/form-data" autocomplete="off">
			    <input type="hidden" id="test_login" name="test_login" value="0"  autocomplete="off" /> 
			   <button type="submit" class="btn" tabindex="1">Take the test</button>
                </form>
			  </div>
			</div>
		  </div>
		  </div>
		  <div class="text-center relative taketestDiv"><img src="images/baseline-help.svg"
     class="baseline-help" onclick="testInst();"><a class="taketest" href="javascript:void(0)" tabindex="2" onclick="testInst();">Why should I take the test ?</a></div>
	 <img src="images/welcome_icon.svg" class="responsive welcome_iconImg" alt="" style="right: 0px;
    bottom: 45%;"/>
	 </div>
		
		 <img src="images/welcome_left.png" class="responsive welcome_leftImg" alt=""/>
		
     </div>
  </div>
  </section>
<?php include_once 'footer/footer.php';?>
<!-- instruction Test popup-->						 
<div id="showInstPopup" class="modal fade showPopup" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content" style="border-radius: 25px;">
		 <div class="modal-header">
			<button type="button" class="close closeIcon" data-dismiss="modal">&times;</button>
			
		  </div>
			<div class="modal-body">
			
		  <div class="text-left paddTop20 fontSize18" >
            <h3 class="header">Why should I take this test?</h3>
		     <ul>
			 <li>	Instant, accurate results to help learners get mapped to their current English proficiency level</li>

 <li>Evaluation across writing, listening, reading, grammar and vocabulary </li>

 <li>Accurate GSE score (10-90) mapped to CEFR</li>

 <li>Uses integrated skill testing – dual skills such as listening & writing, listening and speaking tested together to reflect real life circumstances </li>

			 </ul>
		  
			</div>
			
		</div>
	 </div>
  </div>
</div>	


<script>
$(document).ready(function () {
	$('.header').css("box-shadow"," 0 0 10px 0 #e5e7ee");	
});


function testInst(){
 $('#showInstPopup').modal({
    backdrop: 'static',
    keyboard: true, 
    show: true
 });	
		
}
/* ============== Vertically center Bootstrap 3 modals so they aren't always stuck at the top ============= */
function centerModal() {
    $(this).css('display', 'block');
    var $dialog = $(this).find(".modal-dialog");
    var offset = ($(window).height() - $dialog.height()) / 2;
    // Center modal vertically in window
    $dialog.css("margin-top", offset);
}
$('.modal').on('show.bs.modal', centerModal);

$(window).on("resize", function () {
    $('.modal:visible').each(centerModal);
});

</script>
