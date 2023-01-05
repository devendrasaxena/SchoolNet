<?php 
   include_once('../header/trainerHeader.php');
   $assessmentObj = new assessmentController();
   $assignmentObj = new assignmentController();
	$batches ='';
   $batches = $assignmentObj->getAllClassForTrainer($_SESSION['user_id'], $_SESSION['center_id']);
	$exts = '[\''.implode('\',\'', $assignmentObj->getAllowedExtensions()).'\']';
   $notAllowedExts = '[\''.implode('\',\'', $assignmentObj->getNotAllowedExtensions()).'\']';
	
   $msg='';	
   $err='';	
   $succ='';	
   
   if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
   	if($_SESSION['error'] == '1'){
   		$msg = "Assignment not saved. Please try again.";
   	}
   	if($_SESSION['error'] == '2'){
   		$msg = "Assignment is already exist. Please try another.";
   	}
   }
   
   if(isset($_SESSION['succ']) && $_SESSION['succ'] != ""){
   	
   	if($_SESSION['succ'] == '1'){
   		$msg = "Assignment created successfully.";
   	}
   	if($_SESSION['succ'] == '2'){
   		$msg = "Assignment updated successfully.";
   	}
   }
   if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
   	
   		$msg = $_SESSION['msg'];
   		$err=$_SESSION['error'];
   		unset($_SESSION['msg']);
   		unset($_SESSION['error']);
   	
   }
   if(isset($_SESSION['succ']) && $_SESSION['succ'] != ""){
   	
   		//$msg = $_SESSION['msg'];
   		$succ = $_SESSION['succ'];
   	    unset($_SESSION['msg']);
   	    unset($_SESSION['succ']);
   }
   
   if(isset($_GET['aid'])){
     $aid = trim(base64_decode($_GET['aid']) );
     $assignmentData= $assignmentObj->getAssignmentById($aid);

	 if($assignmentData['assignment_start_date']!='' && $assignmentData['assignment_start_date']!='0000-00-00 00:00:00'){
		$assignment_start_date=date('d-m-Y',strtotime($assignmentData['assignment_start_date']));
	 }else{ 
     $assignment_start_date=''; 
	 }

   if($assignmentData['assignment_end_date']!='' && $assignmentData['assignment_end_date']!='0000-00-00 00:00:00'){
    $assignment_end_date=date('d-m-Y',strtotime($assignmentData['assignment_end_date']));
   }else{ 
     $assignment_end_date=''; 
   }
	     

       $disabledInput="disabledInput-";
      
	// echo "<pre>"; print_r($assignmentData);
   }else{
      $disabledInput=""; 
	    $assignment_start_date=''; 
      $assignment_end_date=''; 
   }
 
   ?>
<ul class="breadcrumb no-border no-radius b-b b-light" title="<?php echo $language[$_SESSION['language']]['assignments']; ?>">
   <li> <a href="assignments.php"><i class="fa fa-arrow-left"></i> <?php echo $language[$_SESSION['language']]['assignments']; ?></a></li>
</ul>
<div class="clear"></div>
<section class="padder">
   <div class="row-centered">
      <div class="col-sm-10 col-xs-12 col-centered">
         <section class="marginBottom40">
            </br>
            <?php if($errDiv!=''){?>
            <div class="alert alert-danger col-sm-12">
               <i class="fa fa-ban-circle"></i><?php echo $errDiv;?> 
            </div>
            <?php } ?>
            <?php if($succ=='1'){?>
            <div class="alert alert-success col-sm-12">
               <button type="button" class="close" data-dismiss="alert">x</button>
               <i class="fa fa-ban-circle"></i><?php echo $msg;?>
            </div>
            <?php } ?>
            <?php if($succ=='2'){?>
            <div class="alert alert-success col-sm-12">
               <button type="button" class="close" data-dismiss="alert">x</button>
               <i class="fa fa-ban-circle"></i> <?php echo $msg;?> 
            </div>
            <?php } ?>
            <?php if($err == '1'){?>
            <div class="alert alert-danger col-sm-12">
               <button type="button" class="close" data-dismiss="alert">x</button>
               <i class="fa fa-ban-circle"></i><?php echo $msg;?> 
            </div>
            <?php } ?>
            <?php if($err == '2'){?>
            <div class="alert alert-danger col-sm-12">
               <button type="button" class="close" data-dismiss="alert">x</button>
               <i class="fa fa-ban-circle"></i><?php echo $msg;?> 
            </div>
            <?php } ?>  
            <form action="ajax/assignmentFormSubmit.php" id="createStudentForm" class="createStudentForm" method="post"  data-validate="parsley" autocomplete="off" enctype="multipart/form-data">
               <div class="row">
                  <div class="panel panel-default bdrNone">
                     <div class="panel-body padd20">
                        <h3 class="panel-header"><?php echo $language[$_SESSION['language']]['assignment']; ?></h3>
                        <div>
                           <div class="form-group col-sm-12">
	                           
                              <div class="form-group col-sm-12">
                                 <label class="control-label"><?php echo $language[$_SESSION['language']]['assignment']; ?> <?php echo $language[$_SESSION['language']]['title']; ?> <span class="required">*</span></label>
                                 <input type="text" name="title" id="title" title="<?php echo $language[$_SESSION['language']]['assignment']; ?> <?php echo $language[$_SESSION['language']]['title']; ?>" placeholder="<?php echo $language[$_SESSION['language']]['assignment']; ?> <?php echo $language[$_SESSION['language']]['title']; ?>" class="form-control input-lg"  data-required="true" value="<?php echo $assignmentData['assignment_name']; ?>" maxlength = "30" autocomplete="off"/>
                              </div>
                              <div class="form-group col-sm-4 hide">
                                 <label class="control-label"><?php echo $language[$_SESSION['language']]['status']; ?> </label>
                                 <select title="<?php echo $language[$_SESSION['language']]['status']; ?>" class="form-control input-lg parsley-validated fld_class <?php echo (($status==0)?'':'disabledInput');?>" name="status">
                                    <!--<option value="">Select Status</option>-->
                                    <option <?php echo $assignmentData['status'] == '1' ? 'selected="selected"':''?> value="1" selected><?php echo $language[$_SESSION['language']]['active']; ?></option>
                                    <option <?php echo $assignmentData['status'] == '0' ? 'selected="selected"':''?> value="0"><?php echo $language[$_SESSION['language']]['inactive']; ?></option>
                                 </select>
                              </div>
                             
                              <div class="col-sm-6"> 
							           <label class="control-label"><?php echo $language[$_SESSION['language']]['assignment_start_date']; ?>  <span class="required text-red">*</span></label>			 
      										<div id="divDate1" class="input-append date">
      										  <input  data-date-format="dd-mm-yyyy" name="assignment_start_date" value="<?php echo $assignment_start_date; ?>"  id="activationDateFrom" title="<?php echo $language[$_SESSION['language']]['assignment_start_date']; ?>" placeholder="DD-MM-YYYY" class="form-control" readonly="true" autocomplete="off" required />
      										  	<span class="calendarBg add-on">
      										   <i class="fa fa-calendar"></i>
      										  </span></div>   
      									
      							  </div>
								  
								 <div class="col-sm-6"> 
							           <label class="control-label"><?php echo $language[$_SESSION['language']]['assignment_end_date']; ?>  <span class="required text-red">*</span></label>			 
      										<div id="divDate2" class="input-append date">
      										  <input  data-date-format="dd-mm-yyyy" name="submission_date" value="<?php echo $assignment_end_date; ?>"  id="activationDateTo" title="<?php echo $language[$_SESSION['language']]['assignment_end_date']; ?>" placeholder="DD-MM-YYYY" class="form-control" readonly="true" autocomplete="off" required />
      										  	<span class="calendarBg add-on">
      										   <i class="fa fa-calendar"></i>
      										  </span></div>   
      									
      							  </div>
                             
                             
                              <div class="clear"></div>
                             <br>

                              <div class="form-group col-sm-12">
                                 <label class="control-label"><?php echo $language[$_SESSION['language']]['assignment']; ?> <?php echo $language[$_SESSION['language']]['description']; ?> <span class="required text-red">*</span></label>

                                 <textarea name="description" id="description" title="<?php echo $language[$_SESSION['language']]['assignment']; ?> <?php echo $language[$_SESSION['language']]['description']; ?>" placeholder="<?php echo $language[$_SESSION['language']]['assignment']; ?> <?php echo $language[$_SESSION['language']]['description']; ?>..." class="form-control input-lg "  autocomplete="off" rows="5" data-required="true"><?php echo $assignmentData['assignment_desc']; ?></textarea>
                                 
                              </div>


                              <div class="clear"></div>
                              
                              <div class="form-group col-sm-6">
                                 <label class="control-label"><?php echo $language[$_SESSION['language']]['assignment']; ?> <?php echo $language[$_SESSION['language']]['file']; ?> </label>
                                 
								 <?php if($assignmentData['assignment_file']!=''){?>
								    <input type="file" name="assignment_file" id="assignment_file" placeholder="First Name" class="form-control input-lg " value="<?php echo $assignmentData['assignment_file']; ?>"/>
                                     <input type="hidden" name="old_file" id="old_file"   value="<?php echo $response['response_file']; ?>"/>
                          
									<?php echo $assignmentData['assignment_file']; ?>
								  <?php }else{?>
									  <input type="file" name="assignment_file" id="assignment_file" placeholder="First Name" class="form-control input-lg "/>
                               
								 <?php }?>
                              </div>
                             
                               <div class="form-group col-sm-6">
	                              <label class="control-label"><?php echo $language[$_SESSION['language']]['classes']; ?>  <span class="required">*</span></label>
	                              <select title="<?php echo $language[$_SESSION['language']]['select_class']; ?>" class="form-control input-lg parsley-validated fld_class <?php echo $disabledInput;?>" name="batch_id[]" id="batch_id" multiple="multiple" data-required="true" onchange="selectBatch(this.value);" >
	                                 <?php 
									 $batch_codes = explode(',',$assignmentData['batch_code']);
									 foreach ($batches as $key => $batch) {
									 ?>
	                                 <option style="width:100%" <?php echo in_array($batch['batch_id'], $batch_codes) ? 'selected="selected"':''?>  value="<?php echo $batch['batch_id']; ?>"><?php echo $batch['batch_name'];?></option>
	                                 <?php }?>
	                              </select>
								  <p><?php echo $language[$_SESSION['language']]['multi_class']; ?></p>
	                           </div>
                             
                              
                              </fieldset>
							 
                           </div>
                           <div class="clear"></div>
                        </div>
                     </div>
                  </div>
                  <div class="clear"></div>
                  
                  <div class="text-right"> 
                     <a href='assignments.php' class="btn btn-primary  hide" title="<?php echo $language[$_SESSION['language']]['cancel']; ?>"><?php echo $language[$_SESSION['language']]['cancel']; ?></a>&nbsp;&nbsp;
                     <input id="assignment_id" type="hidden" name="assignment_id" value="<?php echo $assignmentData['id']; ?>"/>
                     <input id="user_id" type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>"/>
                     <input id="center_id" type="hidden" name="center_id" value="<?php echo $center_id; ?>"/>
                     
					 <input id="client_id" type="hidden" name="client_id" value="<?php echo $client_id; ?>"/>
                     <input type="hidden" id="cpFlag" value="0" />
                     <input type="hidden" id="ass_type"  name="ass_type" value="Teacher" />
                     
					 <button type="submit" name="createAssignment" value='createAssignment' title="<?php echo $language[$_SESSION['language']]['submit']; ?>" class="btn btn-s-md btn-primary  pre-loader btnSave"><?php echo $language[$_SESSION['language']]['submit']; ?></button>
                  </div>
               </div>
            </form>
         </section>
      </div>
   </div>
</section>


<?php include_once('../footer/trainerFooter.php');?>
<!-- date Picker --> 
<script src="<?php echo $_html_relative_path; ?>js/datepicker/bootstrap-datepicker.js?<?php echo date('Y-m-d'); ?>"></script>
<link rel="stylesheet" href="<?php echo $_html_relative_path; ?>js/datepicker/bootstrap-datepicker.css?<?php echo date('Y-m-d'); ?>"/>
<link rel="stylesheet" href="<?php echo $_html_relative_path; ?>js/datepicker/boostrap-timepicker.css?<?php echo date('Y-m-d'); ?>" type="text/css" media="screen"/>
<script type="text/javascript" src="<?php echo $_html_relative_path; ?>js/datepicker/bootstrap-timepicker.js?<?php echo date('Y-m-d'); ?>"></script>

<script>
   var countryData='<?php echo $countryData; ?>';
   var type='<?php echo $studentData->type; ?>';
   var defaultProfilePath='<?php echo $profileImgDefault; ?>';
   var expiry_date='<?php echo $expiryDate; ?>';
   


 $(function () {
  var d1= $("#divDate1").datepicker({ 
    autoclose: true, 
    todayHighlight: true,
    format: 'dd-mm-yyyy',
		//startDate: '1-1-1950',
	 //startDate: '+1d'
       startDate: new Date(),
  });
 var d2 = $("#divDate2").datepicker({ 
    autoclose: true, 
    todayHighlight: true,
    format: 'dd-mm-yyyy',
		//startDate: '1-1-1950',
	 //startDate: '+1d'
       startDate: new Date(),
  });

 $("#divDate1").change(()=>{
  console.log('d111111111111111=>',d1.startDate);
 });
});

   window.setTimeout(function() {
       $(".alertHide").fadeTo(500, 0).slideUp(500, function(){
           $(this).remove(); 
       });
   }, 4000);
   
  $("#assignment_file").on('change', function(){
          
      var validExtensions = <?php echo $exts?>; //array of valid extensions
      var notAllExtensions = <?php echo $notAllowedExts?>; //array of valid extensions
      var fileName = this.files[0].name;
      var fileNameArray = fileName.split('.');
      for(var i=0;i<notAllExtensions.length;i++){
         if($.inArray(notAllExtensions[i], fileNameArray) == 1){
            this.type = '';
              this.type = 'file';
             // $('#'+viewId).attr('src',"");  
              alertPopup("Only these file types are accepted : "+validExtensions.join(', '));
              //$('#'+viewId).attr('src',defaultProfilePath); 
             return false;
         }
      }
    
      var fileNameExt = fileName.substr(fileName.lastIndexOf('.') + 1);
       if ($.inArray(fileNameExt, validExtensions) == -1) {
           this.type = '';
           this.type = 'file';
          // $('#'+viewId).attr('src',"");  
           alertPopup("Only these file types are accepted : "+validExtensions.join(', '));
           //$('#'+viewId).attr('src',defaultProfilePath); 
          return false;
          
       } 
   });
   
  
   function selectBatch(batch_id){
	    var product_id_selected = '<?php echo $assignmentData["product_id"];?>';
	     $('#product_id').html('');
		 $('#level_id').html('');
		 $('#topic_id').html('');
		 $('#chapter_id').html('');
   	   if(batch_id!=''){
   		  showLoader();
   			$.ajax({
				type: 'POST',
				url: "ajax/assignment_ajax.php?action=getProduct",
				data: {batch_id:batch_id,trainer_id:<?php echo $_SESSION['user_id'];?>,center_id:<?php echo $_SESSION['center_id'];?>,product_id_selected :product_id_selected},
				
				success: function(res) { 
				   	$('#product_id').html(res);
				    <?php  if(isset($_GET['aid'])){?>

					 let product_id = $('#product_id option:selected').val();
					 selectProduct(product_id);
				
				    <?php  }?>
				    hideLoader();
				   
				}
   		  });
   	
      }
	  
   }
function selectProduct(product_id){
	$('#level_id').html('');
	$('#topic_id').html('');
	$('#chapter_id').html('');
	 var course_id_selected = '<?php echo $assignmentData["course_code"];?>';
	 let batch_id = $('#batch_id option:selected').val();
   	 if(batch_id!='' && product_id!=''){
   		 showLoader();
   			$.ajax({
				type: 'POST',
				url: "ajax/assignment_ajax.php?action=getCourse",
				data: {batch_id:batch_id,product_id:product_id,trainer_id:<?php echo $_SESSION['user_id'];?>,center_id:<?php echo $_SESSION['center_id'];?>,course_id_selected :course_id_selected },
				
				success: function(res) { 
				console.log(res)
				   	$('#level_id').html(res);
				     <?php  if(isset($_GET['aid'])){?>

					  let level_id = $('#level_id option:selected').val();
					  selectLevel(level_id);
				
				    <?php  }?>
				    hideLoader();
				   
				}
   		  });
   	
      }
	  
	  
   }
   function selectLevel(level_id){
	   $('#topic_id').html('');
	  $('#chapter_id').html('');
	var topic_id = '<?php echo $assignmentData["topic_edge_id"];?>';
	
   	let batch_id = $('#batch_id option:selected').val();
	let product_id = $('#product_id option:selected').val();
   	 if(level_id!='' && batch_id!=''&& product_id!=''){
   		 showLoader();
   			$.ajax({
				type: 'POST',
				url: "ajax/assignment_ajax.php?action=getTopic",
				data: {batch_id:batch_id,product_id:product_id,level_id:level_id,trainer_id:<?php echo $_SESSION['user_id'];?>,center_id:<?php echo $_SESSION['center_id'];?>,topic_id:topic_id},
				
				success: function(res) { 
				   	$('#topic_id').html(res);
					 <?php  if(isset($_GET['aid'])){?>

					  let topic_id = $('#topic_id option:selected').val();
					  selectTopic(topic_id);
				
				    <?php  }?>
				    hideLoader();
				   
				}
   		  });
   	
      }
	  
   }
   function selectTopic(topic_id){
	
	  $('#chapter_id').html('');
	   var chapter_id = '<?php echo $assignmentData["chapter_edge_id"];?>';
	
	  let batch_id = $('#batch_id option:selected').val();
	  let product_id = $('#product_id option:selected').val();
	  let level_id = $('#level_id option:selected').val();
   	  if(topic_id!='' && level_id!='' && batch_id!=''&& product_id!=''){
   	 
   		 showLoader();
   			$.ajax({
				type: 'POST',
				url: "ajax/assignment_ajax.php?action=getChapter",
				data: {batch_id:batch_id,product_id:product_id,topic_id:topic_id,trainer_id:<?php echo $_SESSION['user_id'];?>,center_id:<?php echo $_SESSION['center_id'];?>,chapter_id:chapter_id},
				success: function(res) { 
				   	$('#chapter_id').html(res);
				    hideLoader();
				   
				}
   		  });
   	
      }
   }
   
  
		    $(document).ready(function() {

      

        $(".search-export").click(function() {
            var formValid = checkForm();
            if (formValid == false) {
                return false;
            } else {
                event.preventDefault();
                var search_text = $.trim($("#search_text").val());
                var assignment_start_date = $("#assignment_start_date").val();
                var assignment_end_date = $("#assignment_end_date").val();
                var cust_id = '';
                if ($("#cust_id").length) {
                    cust_id = $("#cust_id").val();
                }
                var url = 'center_status_new.php?search_text=' + search_text + '&assignment_start_date=' + assignment_start_date + '&assignment_end_date=' + assignment_end_date + '&cust_id=' + cust_id;
                if ($(this).hasClass('export-report')) {
                    url += '&report_type=export';
                    hideLoader();
                }
                location.href = url;

            }
        });

    });
   
<?php  if(isset($_GET['aid'])){?>
let batch_id = $('#batch_id option:selected').val();
selectBatch(batch_id);
/*let product_id = $('#product_id option:selected').val();
let level_id = $('#level_id option:selected').val();
let topic_id = $('#topic_id option:selected').val();
let chapter_id = $('#chapter_id option:selected').val();
 
 selectProduct(product_id);
 selectLevel(level_id)*/
<?php  }?>


</script>