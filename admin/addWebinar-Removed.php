<?php 
  include_once('../header/adminHeader.php');
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
              <div class="stepBg">
                <p class="text-left"> Now start creating a new event 
                  <label><a class="imagehover"><i class="fa fa-exclamation-circle"></i> <span id="helpImg0"></span></a></label>
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
			 <div class="col-sm-4 col-xs-4 text-left" style="padding-left:0px;">
				   <label class="control-label text-left" for="txtEventTitle"> <?php echo $centers; ?> <span class="required">*</span> </label>				
					<select name="center" class="form-control " style="width:200px;">
                    <option value="">Select <?php echo $center; ?></option>
					<?php 
					 foreach ($centers_arr as $key => $value) {	
					  $centerName= $centers_arr[$key]['name'];
					  $centerId= $centers_arr[$key]['center_id']; 
					 // echo B2C_CENTER;
					 // if(B2C_CENTER!=$centerId){?>
						<option <?php echo $hide; ?> value="<?php echo $centerId; ?>"><?php echo $centerName;?></option>	
					  <?php }
					  // } ?>
                   </select>	<label class="required" id="eventTitleError"></label>			
				  </div> 
			    <div class="col-sm-4 col-xs-4 text-left" style="padding-left:0px;">
				   <label class="control-label text-left" for="txtEventTitle"> Event Title <span class="required">*</span> </label>				
					<input class="form-control input-lg" name="txtEventTitle" id="txtEventTitle" type="text" value="" data-required="true"  placeholder="Enter event title" onBlur="trimSpaces(this.id); verifyText(this.value,'eventTitleError',this.id);"/>	<label class="required" id="eventTitleError"></label>			
				  </div> 
				  
				 
			  <div class="col-sm-4 col-xs-4 text-left" style="padding-left:0px;vertical-align:top;">
			  <label class="control-label text-left"  for="txtPresenter"> Presenter <span class="required">*</span></label>				 
				 <select class="form-control input-lg"  name="presenter" id="presenter" type="text" data-required="true">
				 <option value="">Select</option>
				  <?php
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
						}
						?>
                    </select><label class="required" id="presenterError"></label>	
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
				  <div class="col-sm-2 col-xs-2 text-left" style="padding-left:0px;vertical-align:top;">
				  <label class="control-label text-left" for="txtSetTime">
				 Set Time <small>(hh:mm)</small> <span class="required">*</span> </label>	
			 <div id="divTime" class="input-group bootstrap-timepicker timepicker">
				    <input class="form-control input-lg" data-format="hh:mm:ss"  name="txtSetTime" id="txtSetTime" type="text" value=""  placeholder="hh:mm:ss" data-required="true"  onBlur="" readonly/>
                      <span class="input-group-addon calendarBg" > <i class="fa fa-clock-o"></i> </span></div>
					  	<label class="required" id="dateError"></label>				 		 
							
				  </div>
				  <div class="col-sm-3 col-xs-3 text-left" style="padding-left:0px;vertical-align:top;">
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
					<input class="form-control input-lg" name="textMaxSeat" id="textMaxSeat" type="text" value="" data-required="true"  data-type="number" onblur="trimSpaces(this.id);" maxlength="3" placeholder="max seat"/><label class="required" id="durError">	 </label>		
				  </div>
			  </div>
			 </div>
			   <input name="action" id="action" type="hidden" value="createClass">
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
<?php include '../footer/adminFooter.php'; ?>
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
	startDate: new Date(),
    //endDate:'-10y',//new Date(),
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
</script>