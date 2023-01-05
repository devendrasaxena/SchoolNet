<?php include_once('../header/adminHeader.php');
$stdRowsData = getTestReport(2,'1B');	
?>

 <div class="breadcrumbBgNone breadcrumbPadder">
	<div class="col-md-6 col-sm-6 text-left">
	  <h3>Reports</h3>
	 <p>Select the <span class="textLower"><?php echo $center; ?></span> to view and download reports</p>
	</div>
		<div class="col-md-6 col-sm-6 text-right"></div>
 </div>
 <div class="clear"></div>
 
 <section class="padder reportList"> 
 <ul class="nav nav-tabs" style="margin-bottom:40px;">
    <li class="textUpper"><a class="active" data-toggle="tab" href="#enReport">Enrollment Reports</a></li>
    <li class="textUpper"><a data-toggle="tab" href="#levelReport">Level Reports</a></li>
    <li class="textUpper"><a data-toggle="tab" href="#levelAssReport">Level Assessment Reports</a></li>
    <li class="textUpper"><a data-toggle="tab" href="#activityReport">Activity Report</a></li>
  </ul>
	  <div class="tab-content">
	  
      <div id="enReport" class="tab-pane fade in active">
	     <?php  if(count($centers_arr) > 0){?>
    <section class="marginBottom5" style="height:80px;">
		  <div class="col-md-3 text-left paddLeft0">
			 <form role="form" method = "POST"  class="form-horizontal form-centerReg" data-validate="parsley" >
				 <select name="center" class="form-control " style="width:200px;">
                    <option value="">Select <?php echo $center; ?></option>
					<?php 
					 foreach ($centers_arr as $key => $value) {	
					  $centerName= $centers_arr[$key]['name'];
					  $centerId= $centers_arr[$key]['center_id']; 
					 // echo B2C_CENTER;
					 // if(B2C_CENTER!=$centerId){?>
						<option <?php echo $hide; ?> value="<?php echo $centerId; ?>"><?php echo $centerName;?></option>	
					  <?php //}
					   } ?>
                   </select>
				</form>
			 </div>
			 <div class="col-md-2 text-left">
			 </div>
			 <div class="col-md-5">
			 <form role="form" method = "POST"  class="search form-horizontal form-centerReg" data-validate="parsley" >
			 <div class="col-sm-12 pull-right"  style="width: 60%;">
				 <input name="searchEnReport" class="form-control" placeholder="Search Learners"/>
                  <button type="submit"><i class="fa fa-search"></i></button>  
				</form>
				</div>
			</div>
			  <div class="col-md-2 paddRight0">
			    <button type="button" class="printBtn btn btn-s-md btn-primary margin0">Download .xls</button>
			  </div>
	</section>	
   <div class="clear"></div>	
   <?php  }?>
  
       <section class="panel panel-default">
	    <div class="panel-body">
	     <div class="table-responsive">
		  <table class="table table-border dataTable table-fixed">
		    <thead  class="fixedHeader">
			  <tr class="col-sm-12 padd0">
			  <th class="col-sm-2 text-left textUpper">Name</th>
			   <th class="col-sm-2 text-left textUpper">Inrollment Date</th>
			   <th class="col-sm-2 text-left textUpper">First Login Date</th>
			   <th class="col-sm-2 text-left textUpper">GSE Score</th>
			   <th class="col-sm-2 text-left textUpper">Level Mapped</th>
			   <th class="col-sm-2 text-left textUpper">Current Level</th>
			 </tr>
			</thead>
		   <tbody>			
				<tr class="col-sm-12 padd0" >
				   <td class="col-sm-2 text-left textUpper"><?php ?></td>
				   <td class="col-sm-2 text-left textUpper"><?php ?></td>
				   <td class="col-sm-2 text-left textUpper"><?php ?></td>
				   <td class="col-sm-2 text-left textUpper"><?php ?></td>
				   <td class="col-sm-2 text-left textUpper"><?php ?></td>
				   <td class="col-sm-2 text-left textUpper"><?php ?></td>
				</tr>
			</tbody>
		    </table>
			</div>
		   </div>
		 </section>
	   </div>
	   
	   <div id="levelReport" class="tab-pane fade  in">
	      <?php  if(count($centers_arr) > 0){?>
    <section class="marginBottom5" style="height:80px;">
		 <div class="col-md-3 text-left paddLeft0">
			 <form role="form" method = "POST"  class="form-horizontal form-centerReg" data-validate="parsley" >
				 <select name="center" class="form-control " style="width:200px;">
                    <option value="">Select <?php echo $center; ?></option>
					<?php 
					 foreach ($centers_arr as $key => $value) {	
					  $centerName= $centers_arr[$key]['name'];
					  $centerId= $centers_arr[$key]['center_id']; 
					 // echo B2C_CENTER;
					  //if(B2C_CENTER!=$centerId){?>
						<option <?php echo $hide; ?> value="<?php echo $centerId; ?>"><?php echo $centerName;?></option>	
					  <?php //}
					   } ?>
                   </select>
				</form>
			 </div>
			 <div class="col-md-2 text-left">
			 <form role="form" method = "POST"  id="levelForm1" class="form-horizontal form-centerReg" data-validate="parsley" >
			 <?php  if(count($courseRangeArr ) > 0 && !empty($courseRangeArr)){?>  
			 
					<select id="course1" name="course" class="form-control " style="width:200px;">  
					 <option value="">Select Level</option>
					  <?php  foreach($courseRangeArr as $key=>$val){
						  $code='CRS-'.$val;
						//$testList=$assessmentObj->getTopicOrAssessmentByCourseId($val['course_id']);
						$selected=(in_array($code,$arrAssignCourse))?'selected':''; 
						  ?>
					  <option value="<?php echo $code;?>" <?php echo $selected;?>><?php echo 'Level - '.$key;?></option>
					  <?php }?>
					</select>
		      </form>
		    <?php }?>
			</div> 
			
			<div class="col-md-5">
			 <form role="form" method = "POST"  class="search form-horizontal form-centerReg" data-validate="parsley" >
			 <div class="col-sm-12 pull-right"  style="width: 60%;">
				 <input name="searchEnReport" class="form-control" placeholder="Search Learners"/>
                  <button type="submit"><i class="fa fa-search"></i></button>  
				</form>
				</div>
			</div>
			  <div class="col-md-2 paddRight0">
			    <button type="button" class="printBtn btn btn-s-md btn-primary margin0">Download .xls</button>
			  </div>
	</section>	
   <div class="clear"></div>	
   <?php  }?>
  
		<section class="panel panel-default">
	     <div class="panel-body">
	      <div class="table-responsive">
		  <table class="table table-border dataTable table-fixed">
		    <thead  class="fixedHeader">
			  <tr class="col-sm-12 padd0">
			   <th class="col-sm-2 text-left textUpper">Name</th>
			   <th class="col-sm-2 text-left textUpper">Email</th>
			   <th class="col-sm-2 text-left textUpper">Module Completed</th>
			   <th class="col-sm-2 text-left textUpper">Time Spent</th>
			   <th class="col-sm-2 text-left textUpper">Task Attempted</th>
			   <th class="col-sm-2 text-left textUpper">Total Attempts</th>
			 </tr>
			</thead>
		   <tbody>			
				<tr class="col-sm-12 padd0" >
				   <td class="col-sm-2 text-left textUpper"><?php ?></td>
				   <td class="col-sm-2 text-left textUpper"><?php ?></td>
				   <td class="col-sm-2 text-left textUpper"><?php ?></td>
				   <td class="col-sm-2 text-left textUpper"><?php ?></td>
				   <td class="col-sm-2 text-left textUpper"><?php ?></td>
				   <td class="col-sm-2 text-left textUpper"><?php ?></td>
				</tr>
			</tbody>
		    </table>
			</div>
		   </div>
		 </section>
	   </div>
	   
	   <div id="levelAssReport" class="tab-pane fade  in">
	      <?php  if(count($centers_arr) > 0){?>
    <section class="marginBottom5" style="height:80px;">
		  <div class="col-md-3 text-left paddLeft0">
			 <form role="form" method = "POST"  class="form-horizontal form-centerReg" data-validate="parsley" >
				 <select name="center" class="form-control " style="width:200px;">
                    <option value="">Select <?php echo $center; ?></option>
					<?php 
					 foreach ($centers_arr as $key => $value) {	
					  $centerName= $centers_arr[$key]['name'];
					  $centerId= $centers_arr[$key]['center_id']; 
					 // echo B2C_CENTER;
					 // if(B2C_CENTER!=$centerId){?>
						<option <?php echo $hide; ?> value="<?php echo $centerId; ?>"><?php echo $centerName;?></option>	
					  <?php //}
					   } ?>
                   </select>
				</form>
			 </div>
			  <div class="col-md-2 text-left">
			 <form role="form" method = "POST"  id="levelForm2" class="form-horizontal form-centerReg" data-validate="parsley" >
			 <?php  if(count($courseRangeArr ) > 0 && !empty($courseRangeArr)){?>  
			 
					<select id="course1" name="course" class="form-control " style="width:200px;">  
					 <option value="">Select Level</option>
					  <?php  foreach($courseRangeArr as $key=>$val){
						  $code='CRS-'.$val;
						//$testList=$assessmentObj->getTopicOrAssessmentByCourseId($val['course_id']);
						$selected=(in_array($code,$arrAssignCourse))?'selected':''; 
						  ?>
					  <option value="<?php echo $code;?>" <?php echo $selected;?>><?php echo 'Level - '.$key;?></option>
					  <?php }?>
					</select>
		      </form>
		    <?php }?>
			</div> 
			
			<div class="col-md-5">
			 <form role="form" method = "POST"  class="search form-horizontal form-centerReg" data-validate="parsley" >
			 <div class="col-sm-12 pull-right"  style="width: 60%;">
				 <input name="searchEnReport" class="form-control" placeholder="Search Learners"/>
                  <button type="submit"><i class="fa fa-search"></i></button>  
				</form>
				</div>
			</div>
			  <div class="col-md-2 paddRight0">
			    <button type="button" class="printBtn btn btn-s-md btn-primary margin0">Download .xls</button>
			  </div>
	</section>	
   <div class="clear"></div>	
   <?php  }?>
  
		<section class="panel panel-default">
	     <div class="panel-body">
	      <div class="table-responsive">
		  <table class="table table-border dataTable table-fixed">		  	
		    <thead  class="fixedHeader">
			  <tr class="col-sm-12 padd0">
			   <th class="col-sm-3 text-left textUpper">Name</th>
			   <th class="col-sm-3 text-left textUpper">Email</th>
			   <th class="col-sm-3 text-left textUpper">Quiz 1 Score</th>
			   <th class="col-sm-3 text-left textUpper">Quiz 2 Score</th>
			 </tr>
			</thead>
		   <tbody>			
				<tr class="col-sm-12 padd0" >
				   <td class="col-sm-3 text-left textUpper"><?php ?></td>
				   <td class="col-sm-3 text-left textUpper"><?php ?></td>
				   <td class="col-sm-3 text-left textUpper"><?php ?></td>
				   <td class="col-sm-3 text-left textUpper"><?php ?></td>
				</tr>
			</tbody>
		    </table>
			</div>
		   </div>
		 </section>
	   </div>
	    <div id="activityReport" class="tab-pane fade in">
		   <?php  if(count($centers_arr) > 0){?>
    <section class="marginBottom5" style="height:80px;">
		 <div class="col-md-3 text-left paddLeft0">
			 <form role="form" method = "POST"  class="form-horizontal form-centerReg" data-validate="parsley" >
				 <select name="center" class="form-control " style="width:200px;">
                    <option value="">Select <?php echo $center; ?></option>
					<?php 
					 foreach ($centers_arr as $key => $value) {	
					  $centerName= $centers_arr[$key]['name'];
					  $centerId= $centers_arr[$key]['center_id']; 
					 // echo B2C_CENTER;
					  //if(B2C_CENTER!=$centerId){?>
						<option <?php echo $hide; ?> value="<?php echo $centerId; ?>"><?php echo $centerName;?></option>	
					  <?php //}
					   } ?>
                   </select>
				</form>
			 </div>
			<div class="col-md-2 text-left">
			 </div>
			<div class="col-md-5">
			 <form role="form" method = "POST"  class="search form-horizontal form-centerReg" data-validate="parsley" >
			 <div class="col-sm-12 pull-right"  style="width: 60%;">
				 <input name="searchEnReport" class="form-control" placeholder="Search Learners"/>
                  <button type="submit"><i class="fa fa-search"></i></button>  
				</form>
				</div>
			</div>
			  <div class="col-md-2 paddRight0">
			    <button type="button" class="printBtn btn btn-s-md btn-primary margin0">Download .xls</button>
			  </div>
	</section>	
   <div class="clear"></div>	
   <?php  }?>
  
		<section class="panel panel-default">
	     <div class="panel-body">
	      <div class="table-responsive">
		  <table class="table table-border dataTable table-fixed">		
		    <thead  class="fixedHeader">
			   <th class="col-sm-2 text-left textUpper">Name</th>
			   <th class="col-sm-2 text-left textUpper">Level Mapped</th>
			   <th class="col-sm-2 text-left textUpper">Module Completed</th>
			   <th class="col-sm-3 text-left textUpper">Task Completed</th>
			   <th class="col-sm-3 text-left textUpper">Time Spent</th>
			</thead>
		   <tbody>			
				<tr class="col-sm-12 padd0">
				   <td class="col-sm-2 text-left textUpper"><?php ?></td>
				   <td class="col-sm-2 text-left textUpper"><?php ?></td>
				   <td class="col-sm-2 text-left textUpper"><?php ?></td>
				   <td class="col-sm-3 text-left textUpper"><?php ?></td>
				   <td class="col-sm-3 text-left textUpper"><?php ?></td>
				</tr>
			</tbody>
		    </table>
			</div>
		   </div>
		 </section>
	   </div>	   
	  </div>
</section>

<?php include_once('../footer/adminFooter.php'); ?>
<script>
</script>