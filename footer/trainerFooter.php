 <!--Start Copy right Footer -->
		</section>
    </section>
</section>
	 </section>
	</section>
</section>
<?php include_once "../alertPopup.php"; ?>  
   <!-- App -->
  <script src="<?php echo $_html_relative_path; ?>js/app.js"></script>
<!--<script src="<?php echo $_html_relative_path; ?>js/app.plugin.js"></script>-->
  <script src="<?php echo $_html_relative_path; ?>js/jsAlert/alertify.min.js"></script> 
  <script src="<?php echo $_html_relative_path; ?>js/slimscroll/jquery.slimscroll.min.js"></script>
    
  <script src="<?php echo $_html_relative_path; ?>js/parsley/parsley.min.js"></script>
  <script src="<?php echo $_html_relative_path; ?>js/parsley/parsley.extend.js"></script>
  <script src="<?php echo $_html_relative_path; ?>js/common.js"></script>

 </body>
</html>
   
   <?php if(strpos($_SERVER['REQUEST_URI'], 'batchList.php') !== false || strpos($_SERVER['REQUEST_URI'], 'createBatch.php') !== false ){?>
	<script>
	$("#btch").addClass('active');
	$("#btch1").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon3Active").removeClass('hide');
	$(".menuIcon3").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'product.php') !== false || strpos($_SERVER['REQUEST_URI'], 'learning_module.php') !== false || strpos($_SERVER['REQUEST_URI'], 'module.php') !== false){?>
	<script>
	$("#lmodule").addClass('active');
	$("#lmodule").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon2Active").removeClass('hide');
	$(".menuIcon2").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'studentList.php') !== false || strpos($_SERVER['REQUEST_URI'], 'createStudent.php') !== false || strpos($_SERVER['REQUEST_URI'], 'bulkStudentUpload.php') !== false || strpos($_SERVER['REQUEST_URI'], 'learner-detail.php') !== false){?>
	<script>
	$("#std").addClass('active');
	$("#std1").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon5Active").removeClass('hide');
	$(".menuIcon5").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'assignments.php') !== false || strpos($_SERVER['REQUEST_URI'], 'createAssignment.php') !== false || strpos($_SERVER['REQUEST_URI'], 'evaluateStudentResponse.php') !== false){?>
	
	<script>
	$("#asm1").addClass('active');
	$("#asm").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon8Active").removeClass('hide');
	$(".menuIcon8").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'licenses.php') !== false || strpos($_SERVER['REQUEST_URI'], 'requestLicense.php') !== false){?>
	<script>
	$("#lic").addClass('active');
	$("#lic1").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon2Active").removeClass('hide');
	$(".menuIcon2").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'profile.php') !== false ){?>
	<script>
	$("#macc").addClass('active');
	$("#macc1").addClass('active');
	$("#home").removeClass('active');
	$(".menuIcon1Active").removeClass('hide');
	$(".menuIcon1").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'reports.php') !== false || strpos($_SERVER['REQUEST_URI'], 'users_report_dseu.php') !== false ||  strpos($_SERVER['REQUEST_URI'], 'learning_objective_report.php') !== false ){?>
	<script>
	$("#rpt").addClass('active');
	$("#rpt1").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon6Active").removeClass('hide');
	$(".menuIcon6").addClass('hide');
	</script><?php }else if(strpos($_SERVER['REQUEST_URI'], 'tehsilList.php') !== false || strpos($_SERVER['REQUEST_URI'], 'createTehsil.php') !== false){?><script>
	$("#teh").addClass('active');
	$("#teh1").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon7Active").removeClass('hide');
	$(".menuIcon7").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'webinar.php') !== false || strpos($_SERVER['REQUEST_URI'], 'addWebinar.php') !== false || strpos($_SERVER['REQUEST_URI'], 'editWebinar.php') !== false){?>
	<script>
	$("#webinar").addClass('active');
	$("#webinar1").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon7Active").removeClass('hide');
	$(".menuIcon7").addClass('hide');
	</script>
	<?php } else if(strpos($_SERVER['REQUEST_URI'], 'notification.php') !== false ) { ?>
	<script type="text/javascript">
	  $("#notification1").addClass('active');
	   $("#notification").addClass('active');
	  $("#home").removeClass('active');
	  $(".menuIcon16Active").removeClass('hide');
	  $(".menuIcon16").addClass('hide');
	</script>
   <?php } else if(strpos($_SERVER['REQUEST_URI'], 'feedback.php') !== false ) { ?>
	<script type="text/javascript">
	  $("#feedback1").addClass('active');
	   $("#feedback").addClass('active');
	  $("#home").removeClass('active');
	  $(".menuIcon17Active").removeClass('hide');
	  $(".menuIcon17").addClass('hide');
	</script>
	<?php } else if(strpos($_SERVER['REQUEST_URI'], 'profile.php') !== false ) { ?>
	<script type="text/javascript">
	  $("#profile1").addClass('active');
	  $("#home").removeClass('active');
	</script>
	<?php }else{ ?>
	<script>
	$("#home").addClass('active');
	$("#home1").addClass('active');
	$(".menuIcon1Active").removeClass('hide');
	$(".menuIcon1").addClass('hide');
	</script>
 <?php }?>
