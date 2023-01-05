<?php 
  include('../wiziq/webinar-errorcodes.php');
  include_once('../header/trainerHeader.php');
// echo "-->".$_SESSION['role_id'];
  $tchRowsData = $adminObj->getAllUserDetails(1);///role 1 for trainer
  $tchInfoArr=array();
  //echo "<pre>";print_r($tchRowsData);
   foreach($tchRowsData as $key => $values){
     $tchInfoArr[]= userdetails($values['user_id']);
	}  
  //echo "<pre>";print_r($tchInfoArr);
?>

<ul class="breadcrumb no-border no-radius b-b b-light">
 <li> <a href="webinar.php"><i class="fa fa-arrow-left"></i> Live Classes</a></li>
</ul><div class="clear"></div>
 <section class="padder">
   <div class="row-centered">
	<div class="col-sm-10 col-xs-12 col-centered">
     <section class="marginBottom40">
     <form id="eventForm" action="../wiziq/ClassAPI/cs_addclass.php" method="post" data-validate="parsley" autocomplete="off">
	<div class="row">
          <div class="panel panel-default bdrNone">
            <div class="panel-body padd20">
			 <h3 class="panel-header">Add Live Class</h3>
			 <div class="divider" style='text-align:center;padding-bottom:10px;color:red;'>
				<?php
				if(isset($_REQUEST['err']))
				{
				echo $errorCode[$_REQUEST['err']];
				}
				?>
				</div>
              <div class="stepBg">
                <p class="text-left">
                </p>
              </div>

			   <div class="divider"></div>
			  <div class="row-centered">
			  <?php if(1==1){?>
				    <input type="hidden" name="course" value="<?php echo '1'."~".'1';?>" />
                   <input type="hidden" name="topic" value="<?php echo '1';?>" />
				   <input type="hidden" name="chapter" value="<?php echo '1';?>" />
				  
				  
				 <?php  } else{ }?>
				  
             <div class="col-sm-12 col-xs-12 col-centered">
			    <div class="col-sm-6 col-xs-6 text-left" style="padding-left:0px;">
				   <label class="control-label text-left" for="txtEventTitle"> Event Title <span class="required">*</span> </label>				
					<input class="form-control input-lg" name="txtEventTitle" id="txtEventTitle" type="text" value="" data-required="true"  placeholder="Enter event title" onBlur="trimSpaces(this.id); verifyText(this.value,'eventTitleError',this.id);"/>	<label class="required" id="eventTitleError"></label>			
				  </div> 
				  
				 
			  <div class="col-sm-6 col-xs-6 text-left" style="padding-left:0px;vertical-align:top;">
			  <label class="control-label text-left"  for="txtPresenter"> Presenter <span class="required">*</span></label>				 
				 <select class="form-control input-lg"  name="presenter" id="presenter" type="text" data-required="true" onchange="return teacherBatches(this.value,'txtBatch');">
				 <!--<option value="">Select</option>-->
				   
			   <?php  
			       if(count($tchInfoArr) > 0){ 
		  					$j = 1; 
							foreach($tchInfoArr as $key => $value)
							{
								$teacher_id=$value->user_id;
								$teacher_email=$value->email_id;
								$teacher_name=$value->first_name;
								$lastName=$value->last_name;
								if($lastName!=''){
								 $full_name=$value->first_name.' '.$value->last_name;}
								else{$full_name=$value->first_name;}
						if($_SESSION['user_id']==$teacher_id)
								{
						?>
							
							<option value="<?php echo $teacher_id."^".$full_name;?>"><?php echo $teacher_name
							;?></option>
						<?php
								}
							$j++;
							}
						}
			   
				  /* 
						 if(count(getTeacherList()) > 0){ 
		  					$j = 1; 
							foreach(getTeacherList() as $key => $value)
							{
								$teacher_id=$value->id;
								$teacher_email=$value->teacher_email;
								$teacher_name=$value->teacher_name;
						?>
							<option value="<?php echo $teacher_id."^".$teacher_email;?>"><?php echo $teacher_name;?></option>
						<?php
							$j++;
							}
						} */
						?>
                    </select><label class="required" id="presenterError"></label>	
					</div>
					<div class="clear"></div> 

			<div class="col-sm-12 col-xs-12 text-left" style="padding-left:0px;vertical-align:top;">
			  <label class="control-label text-left"  for="txtBatch"> Class <span class="required">*</span></label>				 
				 <select class="form-control input-lg"  name="txtBatch" id="txtBatch" type="text" data-required="true">
				 <option value="">Select</option>
				  </select><label class="required" id="BatchError"></label>
				 </div>
			
			   <div class="clear"></div> 
			
			  <div class="col-sm-12 col-xs-12 text-left" style="padding-left:0px;vertical-align:top;">
			  <label class="control-label text-left" for="txtCourseDescription">Event Description <span class="required">*</span></label>				 
				<textarea cols="10" rows="3"  class="form-control input-lg textarea" name="description" id="description" type="text" data-required="true" placeholder="Enter event description" onblur="trimSpaces(this.id); verifyText(this.value,'DescriptError',this.id);"></textarea>	<label class="required" id="DescriptError"></label>	
					</div>
			  
			 
			   <div class="clear"></div> 
			 
			  <div class="col-sm-3 col-xs-3 text-left" style="padding-left:0px;vertical-align:top;">
					  <label class="control-label text-left" for="txtDate">
					  Select Date  <span class="required">*</span> </label>	
					  <div id="divDate" class="input-append date">
					  <input class="form-control input-lg" data-date format="dd-mm-yyyy" name="txtDate" id="txtDate" type="text" readonly="true" value=""  placeholder="DD-MM-YYYY" data-required="true"  onBlur=""/>
					  	<span class="calendarBg add-on">
					   <i class="fa fa-calendar"></i>
					  </span></div>
					  	<label class="required" id="dateError"></label>				 
								
					  </div>
				  <div class="col-sm-2 col-xs-2 text-left" style="padding-left:0px;padding-right:0px;vertical-align:top;">
				  <label class="control-label text-left" for="txtSetTime">
				 Set Time <small>(hh:mm)</small> <span class="required">*</span> </label>	
			 <div id="divTime" class="input-group bootstrap-timepicker timepicker">
				    <input class="form-control input-lg" data-format="hh:mm:ss"  name="txtSetTime" id="txtSetTime" type="text" value=""  placeholder="hh:mm:ss" data-required="true"  onBlur="" readonly/>
                      <span class="input-group-addon calendarBg" > <i class="fa fa-clock-o"></i> </span></div>
					  	<label class="required" id="dateError"></label>				 		 
							
				  </div>
				  <div class="col-sm-3 col-xs-3 text-left" style="vertical-align:top;">
				  <label class="control-label text-left" for="txttimeZone">
				  Time Zone<span class="required">*</span> </label>	
			       <select class="form-control" name="timeZone" id="timeZone" data-required="true"  style="padding-left:1px;padding-right:1px;font-size:12px"></select>
					  	<label class="required" id="timeZoneError"></label>				 		 
							
				  </div>
				  <div class="col-sm-2 col-xs-2" style="padding-left:0px;vertical-align:top;">
				  <label class="control-label text-left" for="txtChapter">Duration <small>(Mins)</small> <span class="required">*</span> </label>				 
					<input class="form-control input-lg" name="duration" id="duration" type="text" value="" data-required="true"  data-type="number" onblur="trimSpaces(this.id); durationZero();" maxlength="3" placeholder="00"/><label class="required" id="durError">		 </label>				 
							
				  </div>
				  <div class="col-sm-2 col-xs-2 text-left" style="padding-left:0px;vertical-align:top;">
				  <label class="control-label text-left" for="txtChapter"> 
				  Max Seat <span class="required">*</span> </label>	<input type='hidden' id='isScheduleOnWiziq' name='isScheduleOnWiziq' value='YeS'>			 
					<input class="form-control input-lg" name="textMaxSeat" id="textMaxSeat" type="text" value="" data-required="true"  data-type="number" onblur="maxSeat(this.id,'maxErr');trimSpaces(this.id);" maxlength="3" placeholder="max seat"/><label class="required" id="maxErr">	 </label>		
				  </div>
			  </div>
			 </div>
			   <input name="action" id="action" type="hidden" value="createClass">

		  <input name="user_role_id" id="user_role_id" type="hidden" value="<?php echo $_SESSION['role_id']; ?>">
		  <input name="client_id" id="client_id" type="hidden" value="<?php echo $client_id; ?>">
		  <input name="center_id" id="center_id" type="hidden" value="<?php echo $center_id; ?>">
		  <input name="user_group_id" id="user_group_id" type="hidden" value="<?php echo $user_group_id; ?>">
			</div>
		</div>
         <div class="text-right"> 
			<a  class="btn btn-red confirModal" id="btnBack" href="webinar.php"  data-confirm-title="Confirmation Message" data-confirm-message="Are you sure you want to leave this page?" > <i class="build fa fa-arrow-circle-left"></i> Back</a>&nbsp;&nbsp;
			<button type="submit" name="Submit" class="btn btn-red" id="btnSave" onclick="durationZero(); showLoaderOrNot('eventForm');" ondblclick="durationZero(); showLoaderOrNot('eventForm');"> <i class="build fa fa fa-file-text-o"></i> Save</button>
	    </div>
      </div>   
   </form> 
  </section>
  </div>
  </div>
  </section>
<?php include '../footer/centerAdminFooter.php'; ?>
<!-- time zone -->
<script src="<?php echo $_html_relative_path; ?>js/timezone/timezones.full.js?<?php echo date('Y-m-d'); ?>"></script>
<!-- date Picker --> 
<script src="<?php echo $_html_relative_path; ?>js/datepicker/bootstrap-datepicker.js?<?php echo date('Y-m-d'); ?>"></script>
<link rel="stylesheet" href="<?php echo $_html_relative_path; ?>js/datepicker/bootstrap-datepicker.css?<?php echo date('Y-m-d'); ?>"/>
<link rel="stylesheet" href="<?php echo $_html_relative_path; ?>js/datepicker/boostrap-timepicker.css?<?php echo date('Y-m-d'); ?>" type="text/css" media="screen"/>
<script type="text/javascript" src="<?php echo $_html_relative_path; ?>js/datepicker/bootstrap-timepicker.js?<?php echo date('Y-m-d'); ?>"></script>
<script type="text/javascript">
$('#timeZone').timezones();

$(function () {
  $("#divDate").datepicker({ 
    autoclose: true, 
    todayHighlight: true,
    format: 'dd-mm-yyyy',
		//startDate: '1-1-1950',
	 //startDate: '+1d'
       startDate: new Date(),
  });
});

$(document).ready(function () {
  $('#txtSetTime').timepicker({
    defaultTime: 'value',
    format: 'hh:mm:ss',
    minuteStep: 1,
    disableFocus: true,
    template: 'dropdown',
    showMeridian: false,
    use24hours: true
  });
});

$(document).ready(function(){
teacherBatches(document.getElementById('presenter').value, 'txtBatch');
});

function maxSeat(id,errid){
	$("#"+errid).text("");
	var cValue=$("#"+id).val();
  if(cValue><?php echo session_max_seat; ?>){
	 $("#"+id).val('');
	 $("#"+errid).text("Max seat should not be greater then 10");  
	return false;
  }
}

function teacherBatches(teacherID, targetID){

	//alert(teacherID);
	if(teacherID != ''){
       // showLoader();
        var hide_loader_val = 1;

		$.post('ajax/getBatchTeachers.php', {teacherID: teacherID}, function(data){ 
		
            $('#'+targetID).html(data);
			
            if( hide_loader_val == 1){
                hideLoader();
            }
        });
		
	
	}
}
</script>