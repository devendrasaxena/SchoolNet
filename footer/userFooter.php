
	</section>
    </section>

	 </section>
	</section>
</section>
 <?php include_once "../alertPopup.php";?>  
 <!-- App -->
  <script src="<?php echo $_html_relative_path; ?>js/app.js"></script>
<!--<script src="<?php echo $_html_relative_path; ?>js/app.plugin.js"></script>-->
<!--JsAlert -->
 <link rel="stylesheet" href="<?php echo $_html_relative_path; ?>js/jsAlert/alertify.core.css?<?php echo date('Y-m-d'); ?>" />
<!--<link rel="stylesheet" href="js/jsAlert/alertify.default.css" id="toggleCSS" />-->
<link rel="stylesheet" href="<?php echo $_html_relative_path; ?>js/jsAlert/alertify.bootstrap.css?<?php echo date('Y-m-d'); ?>" />
  <script src="<?php echo $_html_relative_path; ?>js/jsAlert/alertify.min.js"></script>
  <script src="<?php echo $_html_relative_path; ?>js/slimscroll/jquery.slimscroll.min.js"></script>
   <!-- drag and drop -->
   <!--<script src="<?php //echo $_html_relative_path; ?>js/sortable/jquery-1.4.2.js"></script>-->
  <script src="<?php echo $_html_relative_path; ?>js/sortable/jquery.sortable.js"></script>
   <script src="<?php echo $_html_relative_path; ?>js/jquery.ui.touch-punch.min.js"></script>
    <!-- date Picker --> 
   <script src="<?php echo $_html_relative_path; ?>js/datepicker/bootstrap-datepicker.min.js"></script>
   <link href="<?php echo $_html_relative_path; ?>js/datepicker/bootstrap-datepicker.css"  rel="stylesheet" type="text/css"/>
  <script src="<?php echo $_html_relative_path; ?>js/parsley/parsley.min.js"></script>
  <script src="<?php echo $_html_relative_path; ?>js/parsley/parsley.extend.js"></script>
  <script src="<?php echo $_html_relative_path; ?>js/common.js"></script>
  
 

 </body>
</html>

<?php
   if(strpos($_SERVER['REQUEST_URI'], 'learning_module.php') !== false || strpos($_SERVER['REQUEST_URI'], 'module.php') !== false || strpos($_SERVER['REQUEST_URI'], 'component.php') !== false || strpos($_SERVER['REQUEST_URI'], 'concept.php') || strpos($_SERVER['REQUEST_URI'], 'quiz.php') !== false || strpos($_SERVER['REQUEST_URI'], 'vocab-practice.php') || strpos($_SERVER['REQUEST_URI'], 'role-play.php')!== false || strpos($_SERVER['REQUEST_URI'], 'speech-role-play.php')!== false || strpos($_SERVER['REQUEST_URI'], 'skill_quiz.php')!== false || strpos($_SERVER['REQUEST_URI'], 'game.php')!== false){ ?>
	<script type="text/javascript"> 
	 $("#lmodule1").addClass('active');
	  $("#lmodule").addClass('active');
	  $("#home1").removeClass('active');
	  $("#home").removeClass('active');
	  $(".menuIcon13Active").removeClass('hide');
	  $(".menuIcon13").addClass('hide');
	</script>
	<?php } else if(strpos($_SERVER['REQUEST_URI'], 'assignmentList.php') !== false || strpos($_SERVER['REQUEST_URI'], 'attemptAssignment.php') !== false ) { ?>
	<script type="text/javascript">
	  $("#assignments1").addClass('active');
	  $("#assignments").addClass('active');
	  $("#home1").removeClass('active');
	  $("#home").removeClass('active');
	  $(".menuIcon5Active").removeClass('hide');
	  $(".menuIcon5").addClass('hide');
	</script>
	<?php } else if(strpos($_SERVER['REQUEST_URI'], 'my-performance.php') !== false || strpos($_SERVER['REQUEST_URI'], 'skill-performance.php') !== false || strpos($_SERVER['REQUEST_URI'], 'quiz-performance.php') !== false) { ?>
	<script type="text/javascript">
	   $("#perform1").addClass('active');
	  $("#perform").addClass('active');
	  $("#home1").removeClass('active');
	  $("#home").removeClass('active');
	  $(".menuIcon14Active").removeClass('hide');
	  $(".menuIcon14").addClass('hide');
	</script>
	<?php } else if(strpos($_SERVER['REQUEST_URI'], 'live_session.php') !== false ) { ?>
	<script type="text/javascript">
	  $("#sess1").addClass('active');
	   $("#sess").addClass('active');
	   $("#home1").removeClass('active');
	  $("#home").removeClass('active');
	  $(".menuIcon4Active").removeClass('hide');
	  $(".menuIcon4").addClass('hide');
	</script>
	<?php } else if(strpos($_SERVER['REQUEST_URI'], 'my_certificates.php') !== false ) { ?>
	<script type="text/javascript">
	  $("#mycerty1").addClass('active');
	   $("#mycerty").addClass('active');
	   $("#home1").removeClass('active');
	  $("#home").removeClass('active');
	  $(".menuIcon15Active").removeClass('hide');
	  $(".menuIcon15").addClass('hide');
	</script>
	<?php } else if(strpos($_SERVER['REQUEST_URI'], 'support.php') !== false ) { ?>
	<script type="text/javascript">
	  $("#feed1").addClass('active');
	   $("#feed").addClass('active');
	  $("#home").removeClass('active');
	  $(".menuIcon5Active").removeClass('hide');
	  $(".menuIcon5").addClass('hide'); 
	</script>
	<?php } else if(strpos($_SERVER['REQUEST_URI'], 'notification.php') !== false ) { ?>
	<script type="text/javascript">
	  $("#notification1").addClass('active');
	  $("#notification").addClass('active');
	  $("#home1").removeClass('active');
	  $("#home").removeClass('active');
	  $(".menuIcon16Active").removeClass('hide');
	  $(".menuIcon16").addClass('hide');
 
	</script>
   <?php } else if(strpos($_SERVER['REQUEST_URI'], 'feedback.php') !== false ) { ?>
	<script type="text/javascript">
	  $("#feedId").addClass('active');
	  $("#feedId1").addClass('active');
	  $("#home1").removeClass('active');
	  $("#home").removeClass('active');
	  $(".menuIcon19Active").removeClass('hide');
	  $(".menuIcon19").addClass('hide');
	</script>
	<?php } else if(strpos($_SERVER['REQUEST_URI'], 'resourceLink.php') !== false ) { ?>
	<script type="text/javascript">
	  $("#reso1").addClass('active');
	  $("#reso").addClass('active');
	  $("#home1").removeClass('active');
	  $("#home").removeClass('active');
	  $(".menuIcon19Active").removeClass('hide');
	  $(".menuIcon19").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'survey.php') !== false){?>
	<script>
	$("#survey").addClass('active');
	$("#survey1").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon21Active").removeClass('hide');
	$(".menuIcon21").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'poll.php') !== false){?>
	<script>
	$("#poll").addClass('active');
	$("#poll1").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon22Active").removeClass('hide');
	$(".menuIcon22").addClass('hide');
	</script>
	<?php } else if(strpos($_SERVER['REQUEST_URI'], 'exam.php') !== false ) { ?>
	<script type="text/javascript">
	  $("#post").addClass('active');
	  $("#post1").addClass('active');
	  $("#home1").removeClass('active');
	  $("#home").removeClass('active');
	  $(".menuIcon18Active").removeClass('hide');
	  $(".menuIcon18").addClass('hide');
	</script>
	<?php } else if(strpos($_SERVER['REQUEST_URI'], 'profile.php') !== false ) { ?>
	<script type="text/javascript">
	  $("#profile1").addClass('active');
	  $("#home1").removeClass('active');
	  $("#home").removeClass('active');
	</script>
	
	<?php } else{?>
	<script type="text/javascript">
	$("#das").addClass('active');
	$("#das1").addClass('active');
	$("#home1").removeClass('active');
	$("#home").removeClass('active');
	$(".menuIcon12Active").removeClass('hide');
	$(".menuIcon12").addClass('hide');
	</script>
<?php } ?>
<script>
console.log("user:"+<?php echo $_SESSION['user_id'];?>);
function resizeWindow(){
	  var winHeight =$(window).height();
	  var docHeight =$(document).height();
		if(docHeight > 607){
		
	  }
	  if(docHeight<608){
		//alert(winHeight+"R Default")
		
	 }
	 if(winHeight<608){
		//alert(winHeight+"R Default")
		
	 }	 
	 
 }

resizeWindow();
$( window ).resize(function() {
  resizeWindow();
});
$(document).ready(function(){

 resizeWindow();
  $("#preLoaderPage").delay(0).fadeOut();
  $("#loaderDiv").delay(0).fadeOut();
});
window.onresize = function (event) {
  resizeWindow;
 
}
function showFeeddback(){
  $("#feedbackDeskModal").modal({backdrop: "static"});
	var feedback_url = <?php echo json_encode($_FEEDBACK_FORM_COMPLETE_URL);?>;console.log(feedback_url);
	$ ("#feedback-frame").attr('src', feedback_url+ '?_=' + new Date().getTime());
	$("iframe").load(function() {
		 var innerDiv = $("#feedback-frame").find("formClass").attr("id");
		 if(typeof innerDiv!=="undefined"){console.log(innerDiv)}
		  $("#preInnerLoader").hide();
		   //$("iframe").css({"height":"460px", "width":"100%"});
		   /* var path = $("#feedbackForm").attr('action');
		   alert(path);
		   if(feedback_url!==path){
			 alert("something wrong")
			}*/
	   });
			

 
}
function hideFeeddback(){
	  $("#feedbackDeskModal").modal('hide');
			$(".myCourseMenu").show();
            window.location.href = "dashboard.php";
			 $("#preInnerLoader").hide();

}
function showChat(cId){
 var x = document.getElementById(cId);
  /* if (x.style.display === "inline") {
	$("#mioId2").show();
	$("#mioId1").hide();
  } else {
	$("mioId1").show();
	$("mioId2").hide();
  } */
  	$("#"+cId).toggle();
	$("#mioId1").toggleClass('show hide');
	$("#mioId2").toggleClass('hide show');
}


</script>
	
<?php if($is_active==0){?>

<script>
//$("#status-msg").modal({backdrop: "static"});
</script>
<?php }?>