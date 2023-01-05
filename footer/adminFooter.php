 <!--Start Copy right Footer -->
		</section>
    </section>
</section>
	 </section>
	</section>
</section>
<?php include_once ("../alertPopup.php"); ?>  
  <!-- App -->
  <script src="<?php echo $_html_relative_path; ?>js/app.js"></script>
<!--<script src="<?php echo $_html_relative_path; ?>js/app.plugin.js"></script>-->
<!--JsAlert -->
 <link rel="stylesheet" href="<?php echo $_html_relative_path; ?>js/jsAlert/alertify.core.css?<?php echo date('Y-m-d'); ?>" />
<!--<link rel="stylesheet" href="js/jsAlert/alertify.default.css" id="toggleCSS" />-->
<link rel="stylesheet" href="<?php echo $_html_relative_path; ?>js/jsAlert/alertify.bootstrap.css?<?php echo date('Y-m-d'); ?>" />
  <script src="<?php echo $_html_relative_path; ?>js/jsAlert/alertify.min.js"></script>
  <!-- chart --> 
  <script src="<?php echo $_html_relative_path; ?>js/slimscroll/jquery.slimscroll.min.js"></script>
   
    <!-- date Picker --> 
	<script src="<?php echo $_html_relative_path; ?>js/datepicker/bootstrap-datepicker.min.js"></script>
	<link href="<?php echo $_html_relative_path; ?>js/datepicker/bootstrap-datepicker.css"  rel="stylesheet" type="text/css"/>
  <script src="<?php echo $_html_relative_path; ?>js/parsley/parsley.min.js"></script>
  <script src="<?php echo $_html_relative_path; ?>js/parsley/parsley.extend.js"></script>
  <script src="<?php echo $_html_relative_path; ?>js/common.js"></script>

 </body>
</html>
   <?php if(strpos($_SERVER['REQUEST_URI'], 'createCustomer.php') !== false){?>
	<script>
	$("#cus").addClass('active');
	$("#cus1").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon1Active").removeClass('hide');
	$(".menuIcon1").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'centerList.php') !== false || strpos($_SERVER['REQUEST_URI'], 'createCenter.php') !== false || strpos($_SERVER['REQUEST_URI'], 'registerSuccess.php') !== false){?>
	<script>
	$("#cen").addClass('active');
	$("#cen1").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon3Active").removeClass('hide');
	$(".menuIcon3").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'companyList.php') !== false || strpos($_SERVER['REQUEST_URI'], 'createCompany.php') !== false){?>
	<script>
	$("#com").addClass('active');
	$("#com1").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon1Active").removeClass('hide');
	$(".menuIcon1").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'licenses.php') !== false || strpos($_SERVER['REQUEST_URI'], 'requestLicense.php') !== false || strpos($_SERVER['REQUEST_URI'], 'assignLicense.php') !== false || strpos($_SERVER['REQUEST_URI'], 'license_list.php') !== false){?>
	<script>
	$("#lic").addClass('active');
	$("#lic1").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon2Active").removeClass('hide');
	$(".menuIcon2").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'reports.php') !== false || strpos($_SERVER['REQUEST_URI'], 'users_report_dseu.php') !== false || strpos($_SERVER['REQUEST_URI'], 'reports_dseu.php') !== false  || strpos($_SERVER['REQUEST_URI'], 'learners_report.php') !== false || strpos($_SERVER['REQUEST_URI'], 'trainers_report.php') !== false || strpos($_SERVER['REQUEST_URI'], 'learning_objective_report.php') !== false || strpos($_SERVER['REQUEST_URI'], 'lesson_report.php') !== false || strpos($_SERVER['REQUEST_URI'], 'skill_report.php') !== false || strpos($_SERVER['REQUEST_URI'], 'time_spent_report.php') !== false || strpos($_SERVER['REQUEST_URI'], 'users_report.php') !== false || strpos($_SERVER['REQUEST_URI'], 'statewise-users.php') !== false){?>
	<script>
	$("#rpt").addClass('active');
	$("#rpt1").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon16Active").removeClass('hide');
	$(".menuIcon16").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'productList.php') !== false ){?>
	<script>
	$("#pro").addClass('active');
	$("#pro1").addClass('active');
	$("#home").addClass('active');
	$("#home1").addClass('active');
	$(".menuIcon7Active").removeClass('hide');
	$(".menuIcon7").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'webinar.php') !== false || strpos($_SERVER['REQUEST_URI'], 'addWebinar.php') !== false){?>
	<script>
	$("#webinar").addClass('active');
	$("#webinar1").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon7Active").removeClass('hide');
	$(".menuIcon7").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'designationList.php') !== false || strpos($_SERVER['REQUEST_URI'], 'createDesignation.php') !== false ){?>
	<script>
	$("#desgn").addClass('active');
	$("#desgn1").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon17Active").removeClass('hide');
	$(".menuIcon17").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'batchList.php') !== false || strpos($_SERVER['REQUEST_URI'], 'createBatch.php') !== false ){?>
	<script>
	$("#btch").addClass('active');
	$("#btch1").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon4Active").removeClass('hide');
	$(".menuIcon4").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'districtList.php') !== false || strpos($_SERVER['REQUEST_URI'], 'createDistrict.php') !== false ){?>
	<script>
	$("#dst").addClass('active');
	$("#dst1").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon5Active").removeClass('hide');
	$(".menuIcon5").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'teacherList.php') !== false || strpos($_SERVER['REQUEST_URI'], 'createTeacher.php') !== false){?>
	<script>
	$("#tch").addClass('active');
	$("#tch1").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon6Active").removeClass('hide');
	$(".menuIcon6").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'tehsilList.php') !== false || strpos($_SERVER['REQUEST_URI'], 'createTehsil.php') !== false){?>
	<script>
	$("#teh").addClass('active');
	$("#teh1").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon7Active").removeClass('hide');
	$(".menuIcon7").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'studentList.php') !== false || strpos($_SERVER['REQUEST_URI'], 'createStudent.php') !== false || strpos($_SERVER['REQUEST_URI'], 'bulkStudentUpload.php') !== false || strpos($_SERVER['REQUEST_URI'], 'learner-detail.php') !== false || strpos($_SERVER['REQUEST_URI'], 'bulkAttendanceUpload.php') !== false || strpos($_SERVER['REQUEST_URI'], 'bulkprepostAssessment.php') !== false){?>
	<script>
	$("#std").addClass('active');
	$("#std1").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon8Active").removeClass('hide');
	$(".menuIcon8").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'user_access_list.php') !== false || strpos($_SERVER['REQUEST_URI'], 'generateAccessCode.php') !== false ){?>
	<script>
	$("#accCode").addClass('active');
	$("#accCode1").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon8Active").removeClass('hide');
	$(".menuIcon8").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'regionList.php') !== false || strpos($_SERVER['REQUEST_URI'], 'createRegion.php') !== false){?>
	<script>
	$("#reg").addClass('active');
	$("#reg1").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon9Active").removeClass('hide');
	$(".menuIcon9").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'regionAdminList.php') !== false || strpos($_SERVER['REQUEST_URI'], 'createRegionAdmin.php') !== false){?>
	<script>
	$("#radmin").addClass('active');
	$("#radmin1").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon10Active").removeClass('hide');
	$(".menuIcon10").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'centerAdminList.php') !== false || strpos($_SERVER['REQUEST_URI'], 'createCenterAdmin.php') !== false){?>
	<script>
	$("#cntradmin").addClass('active');
	$("#cntradmin1").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon11Active").removeClass('hide');
	$(".menuIcon11").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'notification.php') !== false){?>
	<script>
	$("#notification").addClass('active');
	$("#notification1").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon18Active").removeClass('hide');
	$(".menuIcon18").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'feedback.php') !== false ){?>
	<script>
	$("#feedId").addClass('active');
	$("#feedId1").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon19Active").removeClass('hide');
	$(".menuIcon19").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'manage-survey.php') !== false || strpos($_SERVER['REQUEST_URI'], 'survey_edit.php') !== false  || strpos($_SERVER['REQUEST_URI'], 'survey_charts.php') !== false ){?>
	<script>
	$("#survey").addClass('active');
	$("#survey1").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon21Active").removeClass('hide');
	$(".menuIcon21").addClass('hide');
	</script>
	<?php }else if(strpos($_SERVER['REQUEST_URI'], 'manage-poll.php') !== false || strpos($_SERVER['REQUEST_URI'], 'poll_edit.php') !== false  || strpos($_SERVER['REQUEST_URI'], 'poll_charts.php') !== false ){?>
	<script>
	$("#poll").addClass('active');
	$("#poll1").addClass('active');
	$("#home").removeClass('active');
	$("#home1").removeClass('active');
	$(".menuIcon22Active").removeClass('hide');
	$(".menuIcon22").addClass('hide');
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
 