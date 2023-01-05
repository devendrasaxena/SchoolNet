<?php include_once('../header/adminHeader.php');
$reportObj = new reportController();
$landingObj = new landingController();

if($_SESSION['role_id']==7){
$all_students = $landingObj->getAllUserByRole(2, $_SESSION['region_id']);
$all_teachers = $landingObj->getAllUserByRole(1, $_SESSION['region_id']);
}else{
$all_students_dfpd = $landingObj->getAllUserByRole(2);
$all_teachers = $landingObj->getAllUserByRole(1);
}

$centerObj = new centerController();
$response_result=$centerObj->getRegionAdminCount();
$total_region_admins = $response_result['total'];
?>
<ul class="breadcrumb no-border no-radius b-b b-light">
 <li> <?php echo $language[$_SESSION['language']]['home']; ?></li>
</ul><div class="clear"></div>
<?php if($_SESSION['role_id']==7){?>
<!-- section for region admin -->
 <section class="padder">
  <section class="marginBottom5 serachformDiv">
       <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 pull-left text-left paddLeft0">
		
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-left paddLeft0">
			
			 <label style="padding-top: 5px;"> <?php echo $language[$_SESSION['language']]['state_name']; ?> : </label>  &nbsp;&nbsp;
			  <div class="searchboxCSS search-box col-xs-4 padd0" style="    display: inline-block;">
			
				 <select name="center_id" id="center_id" class="form-control ">
					   
					   <option value="" ctype="" ><?php echo $language[$_SESSION['language']]['select_state']; ?></option>
					   <?php 
					   
					   $center_list_arr_drop_down =$reportObj->getCenterListByClient($client_id,'',$country,$region_id);
						if(count($center_list_arr_drop_down)>0){
						 $optionSelected = ($center_id == 'All') ? "selected" : "";
						  //echo '<option value="All" '.$optionSelected.'>All</option>';
						 foreach($center_list_arr_drop_down  as $key => $value){
								$centerId=$center_list_arr_drop_down[$key]['center_id'];
								$center_type=$center_list_arr_drop_down[$key]['center_type'];
								$center_name=$center_list_arr_drop_down[$key]['name'];
								$optionSelected = ($center_id == $centerId) ? "selected" : "";
								echo '<option   value="'.$centerId.'"  ctype="'.$center_type.'"'.$optionSelected.' count="'.$i++.'" '.$disabled.'>'.$center_name.'</option>';
									
						 }
						}
						

					   ?>
					</select>
				 <div class="result_list"></div>
				</div>
			
			 </div>
			 
			</div>
		 <div class="col-md-8 padd0"></div>
	</section>	
 <section id="section2" class="panel panel-default marginBottom5">

   <header class="panel-heading b-light" style="overflow: auto;">
		<div class="col-md-8 padd0" id="org_div2" style="display: none;" ><?php echo $center; ?> : <span class="font-bold" id="centerName2">General<?php //Generalecho $clientName;?></span>
		</div>
		<div class="col-md-8 padd0" id="summary_div2"><span class="font-bold"><?php echo $language[$_SESSION['language']]['summary']; ?></span>
		</div>
		  
		 </header> 
		<div class="row m-l-none m-r-none bg-light lter">
		<a id="classDiv2" <?php echo $_SESSION['role_id'] !=3 ? 'href="centerList.php"' :''?>  title="<?php echo $language[$_SESSION['language']]['states']; ?>" >
		  <div class="col-sm-6 col-md-4 padder-v b-r b-light text-center">

			<span class="fa-stack fa-2x  m-r-sm vTop">
			  <i class="fa fa-circle fa-stack-2x text-info"></i>
			  <i class="fa fa-columns fa-stack-1x text-white"></i>
			</span><span class="inlineBlock">
			  <span class="clear h3 m-t-xs"><strong id="totalCenterBatch2"><?php echo $totalCenter;?></strong></span>
			  <small class="clear text-muted text-uc" id="centerBatch2"><?php echo $language[$_SESSION['language']]['states']; ?></small>
			  </span>

		  </div> </a> 

		  
		 
		  <a id="teacherDiv2" <?php echo $_SESSION['role_id'] !=3 ? 'href="teacherList.php"' :''?> title="<?php echo $language[$_SESSION['language']]['district_admins']; ?>">
		  <div class="col-sm-6 col-md-4 padder-v  b-r b-light  lt text-center">
		
			<span class="fa-stack fa-2x m-r-sm vTop">
			  <i class="fa fa-circle fa-stack-2x text-danger"></i>
			  <i class="fa fa-users fa-stack-1x text-white"></i>
			</span><span class="inlineBlock">
			  <span class="clear h3 m-t-xs"><strong id="totalCenterTeacher2"><?php echo $all_teachers['total'] > 0 ? $all_teachers['total']:0;?></strong></span>
			  <small class="clear text-muted text-uc"><?php echo $language[$_SESSION['language']]['district_admins']; ?></small>
			  </span>

		  </div>			
		  </a> 
		  
		<a id="learnerDiv2" <?php echo $_SESSION['role_id'] !=3 ? 'href="studentList.php"' :''?> title="<?php echo $language[$_SESSION['language']]['learners']; ?>">
		  <div class="col-sm-6 col-md-4 padder-v  b-r b-light text-center">
		
			<span class="fa-stack fa-2x m-r-sm vTop">
			  <i class="fa fa-circle fa-stack-2x text-success"></i>
			  <i class="fa fa-users fa-stack-1x text-white"></i>
			</span><span class="inlineBlock">
			  <span class="clear h3 m-t-xs"><strong id="totalCenterStudent2"><?php echo $all_students['total'] > 0 ? $all_students['total']:0;?></strong></span>
			  <small class="clear text-muted text-uc"><?php echo $language[$_SESSION['language']]['learners']; ?></small>
			  </span>

		  </div>	
		  </a>
		
		</div>
	</section>
	   <div class="clear"></div>
	   
 <section id="section3" class="panel panel-default marginBottom5" style="display: none;">

   <header class="panel-heading b-light" style="overflow: auto;">
		<div class="col-md-8 padd0" id="org_div3" style="display: none;" ><?php echo $center; ?> : <span class="font-bold" id="centerName3">General<?php //Generalecho $clientName;?></span>
		</div>
		
		  
		 </header> 
		<div class="row m-l-none m-r-none bg-light lter">
		  <a id="districtDiv3" <?php echo $_SESSION['role_id'] !=3 ? 'href="teacherList.php?center_id='.$center_id_dfpd.'"' :''?> title="<?php echo $language[$_SESSION['language']]['district_admins']; ?>">
		  <div class="col-sm-6 col-md-6 padder-v  b-r b-light  lt text-center">
		
			<span class="fa-stack fa-2x m-r-sm vTop">
			  <i class="fa fa-circle fa-stack-2x text-danger"></i>
			  <i class="fa fa-users fa-stack-1x text-white"></i>
			</span><span class="inlineBlock">
			  <span class="clear h3 m-t-xs"><strong id="totalCenterTeacher2"><?php echo $all_teachers['total'] > 0 ? $all_teachers['total']:0;?></strong></span>
			  <small class="clear text-muted text-uc"><?php echo $language[$_SESSION['language']]['district_admins']; ?></small>
			  </span>

		  </div>			
		  </a> 
		<a id="learnerDiv3" <?php echo $_SESSION['role_id'] !=3 ? 'href="studentList.php?center_id='.$center_id_dfpd.'"' :''?> title="<?php echo $language[$_SESSION['language']]['learners']; ?>">
		  <div class="col-sm-6 col-md-6 padder-v  b-r b-light  lt text-center">
		
			<span class="fa-stack fa-2x m-r-sm vTop">
			  <i class="fa fa-circle fa-stack-2x text-success"></i>
			  <i class="fa fa-users fa-stack-1x text-white"></i>
			</span><span class="inlineBlock">
			  <span class="clear h3 m-t-xs"><strong id="totalCenterStudent2"><?php echo $all_students['total'] > 0 ? $all_students['total']:0;?></strong></span>
			  <small class="clear text-muted text-uc"><?php echo $language[$_SESSION['language']]['learners']; ?></small>
			  </span>

		  </div>
		  </a>
		
		</div>
	</section>
	   <div class="clear"></div>

	<section  id="section" class="panel panel-default marginBottom5 " style="display: none;">
   <header class="panel-heading b-light" style="overflow: auto;">
		<div class="col-md-8 padd0" id="org_div" style="display: none;" ><?php echo $center; ?> : <span class="font-bold" id="centerName">General<?php //Generalecho $clientName;?></span>
		</div>
		<div class="col-md-8 padd0" id="summary_div"><span class="font-bold"><?php echo $language[$_SESSION['language']]['summary']; ?></span>
		</div>
		  
		 </header> 
		<div class="row m-l-none m-r-none bg-light lter">
		<a id="classDiv" <?php echo $_SESSION['role_id'] !=3 ? 'href="centerList.php"' :''?>  title="<?php echo $language[$_SESSION['language']]['states']; ?>" >
		  <div class="col-sm-6 col-md-4 padder-v b-r b-light text-center">

			<span class="fa-stack fa-2x  m-r-sm vTop">
			  <i class="fa fa-circle fa-stack-2x text-info"></i>
			  <i class="fa fa-columns fa-stack-1x text-white"></i>
			</span><span class="inlineBlock">
			  <span class="clear h3 m-t-xs"><strong id="totalCenterBatch"><?php echo $totalCenter;?></strong></span>
			  <small class="clear text-muted text-uc" id="centerBatch"><?php echo $language[$_SESSION['language']]['states']; ?></small>
			  </span>

		  </div> </a> 

		  <a id="learnerDiv" <?php echo $_SESSION['role_id'] !=3 ? 'href="studentList.php"' :''?> title="<?php echo $language[$_SESSION['language']]['learners']; ?>">
		  <div class="col-sm-6 col-md-4 padder-v  b-r b-light  lt text-center">
		
			<span class="fa-stack fa-2x m-r-sm vTop">
			  <i class="fa fa-circle fa-stack-2x text-danger"></i>
			  <i class="fa fa-users fa-stack-1x text-white"></i>
			</span><span class="inlineBlock">
			  <span class="clear h3 m-t-xs"><strong id="totalCenterStudent"><?php echo $totalCenterStudent1;?></strong></span>
			  <small class="clear text-muted text-uc"><?php echo $language[$_SESSION['language']]['active']; ?> <?php echo $language[$_SESSION['language']]['learners']; ?></small>
			  </span>

		  </div>
		  </a>
		
		</div>
	</section>
	
</section>
<!-- end of section for center admin -->
<?php }else{?>
<!--  section for super admin -->

 <section class="padder">
  <section class="marginBottom5 serachformDiv">
       <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9 pull-left text-left paddLeft0">
		
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-left paddLeft0">
			
			 <label style="padding-top: 5px;"> <?php echo $language[$_SESSION['language']]['centre_name']; ?> : </label>  &nbsp;&nbsp;
			  <div class="searchboxCSS search-box col-xs-5 padd0" style="    display: inline-block;">
			
				 <select name="region_id" id="region_id" class="form-control ">
					   
					   <option value="" ctype="" ><?php echo $language[$_SESSION['language']]['select_centre']; ?></option>
					   <?php 
			 foreach ($region_arr as $key => $value) {	
			  $regionName= $value['region_name'];
			  
				if( $_REQUEST['region_id']==$value['id']){ $selected ="selected"; }
			  else{ $selected ="";} 
			?>
				<option <?php echo $hide; ?> value="<?php echo $value['id']; ?>" <?php echo $selected; ?>><?php echo $regionName;?></option>	
			  <?php 
			   } ?>
		   </select>
				 <div class="result_list"></div>
				</div>
			
			 </div>
			 
			</div>
		 <div class="col-md-8 padd0"></div>
	</section>	
 <section id="section2" class="panel panel-default marginBottom5">

   <header class="panel-heading b-light" style="overflow: auto;">
		<div class="col-md-8 padd0" id="org_div2" style="display:none" ><?php echo $language[$_SESSION['language']]['centre']; ?> : <span class="font-bold" id="centerName2">General<?php //Generalecho $clientName;?></span>
		</div>
		<div class="col-md-8 padd0" id="summary_div2"><span class="font-bold"><?php echo $language[$_SESSION['language']]['summary_for_all_the_centers']; ?></span>
		</div>
		  
		 </header> 
		<div class="row m-l-none m-r-none bg-light lter">
		<a id="regionadminDiv2" href="regionAdminList.php"  title="<?php echo $language[$_SESSION['language']]['centre_admins']; ?>" >
		  <div class="col-sm-6 col-md-4 padder-v b-r b-light text-center">

			<span class="fa-stack fa-2x  m-r-sm vTop">
			  <i class="fa fa-circle fa-stack-2x text-info"></i>
			  <i class="fa fa-columns fa-stack-1x text-white"></i>
			</span><span class="inlineBlock">
			  <span class="clear h3 m-t-xs"><strong id="totalCenterBatch2"><?php echo $total_region_admins;?></strong></span>
			  <small class="clear text-muted text-uc" id="centerBatch2"><?php echo $language[$_SESSION['language']]['centre_admins']; ?></small>
			  </span>

		  </div> </a>
		
		<a id="classDiv2" <?php echo $_SESSION['role_id'] !=3 ? 'href="centerList.php"' :''?>  title="<?php echo $language[$_SESSION['language']]['states']; ?>" >
		  <div class="col-sm-6 col-md-4 padder-v b-r b-light  lt text-center">

			<span class="fa-stack fa-2x  m-r-sm vTop">
			  <i class="fa fa-circle fa-stack-2x text-info"></i>
			  <i class="fa fa-columns fa-stack-1x text-white"></i>
			</span><span class="inlineBlock">
			  <span class="clear h3 m-t-xs"><strong id="totalCenterCount2"><?php echo $totalCenter;?></strong></span>
			  <small class="clear text-muted text-uc" id="centerBatch2"><?php echo $language[$_SESSION['language']]['states']; ?></small>
			  </span>

		  </div> </a> 

		  <a id="learnerDiv2" <?php echo $_SESSION['role_id'] !=3 ? 'href="studentList.php"' :''?> title="<?php echo $language[$_SESSION['language']]['learners']; ?>">
		  <div class="col-sm-6 col-md-4 padder-v  b-r b-light  text-center">
		
			<span class="fa-stack fa-2x m-r-sm vTop">
			  <i class="fa fa-circle fa-stack-2x text-danger"></i>
			  <i class="fa fa-users fa-stack-1x text-white"></i>
			</span><span class="inlineBlock">
			  <span class="clear h3 m-t-xs"><strong id="totalCenterStudent2"><?php echo $totalCenterStudent1;?></strong></span>
			  <small class="clear text-muted text-uc"><?php echo $language[$_SESSION['language']]['active']; ?> <?php echo $language[$_SESSION['language']]['learners']; ?></small>
			  </span>

		  </div>			
		  </a> 
		  
		
		</div>
	</section>
	   <div class="clear"></div>
	   

</section>
<?php }?>

<!-- end of section for super admin -->
<?php include_once('../footer/adminFooter.php');?>



<?php if($_SESSION['role_id']==7){?>
<script>
var region_id = '<?php echo $_SESSION["role_id"]?>';console.log(region_id);
$('#center_id').change(function() {
	  var eValue = $(this).val();
	  var ctype = $('#center_id  option:selected').attr('ctype');
	 
	 //$("#totalCenterTrainer").html('');
	 //$("#totalCenterStudent").html('');
	 if(eValue!='' && ctype==1){
		 showLoader();
			$.ajax({
			  type: 'POST',
			  url: "ajax/getCenterData.php",
			data: {centerId:eValue,customerId:<?php echo $customer_id;?>},
			  dataType: "text",
			  success: function(res) { 
			 // console.log(res);
			 var data = JSON.parse(res);
			 //console.log(data);
			  $("#section2").hide();
			  $("#section3").hide();
			  $("#section").show();
			  $("#summary_div").hide();
			  $("#org_div").show();
			  $("#centerName").html(data['cDetail']["name"]);
			   $("#totalTrainer").html(data['cDetail']["trainer_limit"]);
		    //  $("#totalStudent").html(data['cDetail']["student_limit"]);
			   $("#totalCenterTrainer").html(data['center_detail']["totalCenterTeacher"]);
			   $("#totalCenterStudent").html(data['center_detail']["totalCenterStudent"]);
			    if(data['batch_count']>0){
			   $("#totalCenterBatch").html(data['batch_count']-1);
			   }else{
				   $("#totalCenterBatch").html(data['batch_count']);
			   }
			   $("#centerBatch").html('<?php echo $batches; ?>');
			   <?php if($_SESSION['role_id'] !=3){?>
				$("#classDiv").attr('href','batchList.php?center_id='+eValue);
				$("#classDiv").attr('title','<?php echo $language[$_SESSION['language']]['classes']; ?>');
				
				$("#districtDiv").attr('href','teacherList.php?center_id='+eValue);
				$("#learnerDiv").attr('href','studentList.php?center_id='+eValue);
			
			   <?php }?>
			   //showGraph(arr1);
			   hideLoader();
			   $("#divAdmin").hide();	 
			   $("#divCustomer").show();
			  }
		  });
	 }else if(eValue!='' && ctype==0){
		 showLoader();
			$.ajax({
			  type: 'POST',
			  url: "ajax/getCenterData.php",
			data: {centerId:eValue,customerId:<?php echo $customer_id;?>},
			  dataType: "text",
			  success: function(res) { 
			  console.log(res);
			 var data = JSON.parse(res);
			 //console.log(data);
			  $("#section2").hide();
			  $("#section").hide();
			  $("#section3").show();
			  $("#summary_div3").hide();
			  $("#org_div3").show();
			  $("#centerName3").html(data['cDetail']["name"]);
			   $("#totalTrainer").html(data['cDetail']["trainer_limit"]);
			   //$("#totalStudent").html(data['cDetail']["student_limit"]);
			   
			  $("#totalCenterTrainer2").html(data['center_detail']["totalCenterTeacher"]);
//console.log(data['center_detail']["totalCenterStudent"]);
			   $("#totalCenterStudent2").html(data['center_detail']["totalCenterStudent"]);
			    //$("#totlaLicenseIssued").html(data['license_detail']["totalIssued"]);
			   if(data['batch_count']>0){
			   $("#totalCenterBatch").html(data['batch_count']-1);
			   }else{
				   $("#totalCenterBatch").html(data['batch_count']);
			   }
			   $("#centerBatch").html('<?php echo $batches; ?>');
			   <?php if($_SESSION['role_id'] !=3){?>
				$("#classDiv").attr('href','batchList.php?center_id='+eValue);
				$("#classDiv").attr('title','<?php echo $language[$_SESSION['language']]['classes']; ?>');
				
				$("#districtDiv3").attr('href','teacherList.php?center_id='+eValue);
				$("#learnerDiv3").attr('href','studentList.php?center_id='+eValue);
			
			   <?php }?>
			   //showGraph(arr1);
			   hideLoader();
			   $("#divAdmin").hide();	 
			   $("#divCustomer").show();
			  }
		  });
		 
	 }else{
		 showLoader();
		 $("#section2").show();
		 $("#section").hide();
		 $("#section3").hide();
		 $("#org_div").hide();
		$("#summary_div").show();
		$("#centerName").html();
	   
	   $("#totalCenterTrainer").html('<?php echo $totalCenterTrainer1;?>');
	   $("#totalCenterStudent").html('<?php echo $totalCenterStudent1;?>');
	   $("#totalCenterBatch").html('<?php echo $totalCenter; ?>');
	   $("#centerBatch").html('<?php echo $centers; ?>');
	    <?php if($_SESSION['role_id'] !=3){?>
	      $("#classDiv").attr('href','centerList.php');
		  $("#classDiv").attr('title','<?php echo $language[$_SESSION["language"]]["states"]; ?>');
				
		 <?php }?>		
	  // showGraph(arr);
	   $("#divCustomer").hide();	 
	   $("#divAdmin").show();	 
	     hideLoader();
	 }
});

</script>
<?php }else{?>

<script>
var region_id = '<?php echo $_SESSION["role_id"]?>';console.log(region_id);
$('#region_id').change(function() {
	  var eValue = $(this).val();
	  var ctype = $('#region_id  option:selected').attr('ctype');
	 
	 //$("#totalCenterTrainer").html('');
	 //$("#totalCenterStudent").html('');
	 if(eValue!=''){
		 showLoader();
			$.ajax({
			  type: 'POST',
			  url: "ajax/getRegionData.php",
			data: {region_id:eValue,customerId:<?php echo $customer_id;?>},
			  dataType: "text",
			  success: function(res) { 
			  console.log(res);
			 var data = JSON.parse(res);
			 console.log(data);
			 
			  $("#summary_div2").hide();
			  $("#org_div2").show();
			  $("#centerName2").html(data['region_name']);
			   
			   $("#totalCenterTrainer2").html(data['center_detail']["totalCenterTeacher"]);//districts
			   $("#totalCenterStudent2").html(data['center_detail']["totalCenterStudent"]);
			   $("#totalCenterBatch2").html(data['total_region_admins']);
			   
			  
			   $("#totalCenterCount2").html(data['total_centers']);
			 $("#regionadminDiv2").attr('href','regionAdminList.php?region_id='+eValue);
			   //showGraph(arr1);
			   hideLoader();
			   $("#divAdmin").hide();	 
			   $("#divCustomer").show();
			   
			   
				
			  }
		  });
		 
	 }else{
		 showLoader();
		
		 $("#org_div2").hide();
		$("#summary_div2").show();
		$("#centerName").html();
	   
	   $("#totalCenterTrainer2").html('<?php echo $totalCenterTrainer1;?>');
	   $("#totalCenterStudent2").html('<?php echo $totalCenterStudent1;?>');
	   $("#totalTestAttempted").html('<?php echo $totalTestAttempted1;?>');
	   $("#totalCenterBatch2").html('<?php echo $total_region_admins; ?>');
	   $("#totalCenterCount2").html('<?php echo $totalCenter; ?>');
	   $("#centerBatch").html('<?php echo $centers; ?>');
	  	 $("#regionadminDiv2").attr('href','regionAdminList.php');
	  // showGraph(arr);
	   $("#divCustomer").hide();	 
	   $("#divAdmin").show();	 
	     hideLoader();
	 }
});

</script>

<?php }?>