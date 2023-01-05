<?php include_once('../header/trainerHeader.php');

$assignmentObj = new assignmentController();
$stdRowsData ='';
///role 2 for student;

$assessmentObj = new assessmentController();
$batchInfo = $assignmentObj->getAllClassForTrainer($_SESSION['user_id'], $_SESSION['center_id']);

$page_param = '';

$batch_id = $status = $title = $product_id='';
if(isset($_GET['class']) && $_GET['class'] != ''){
	$batch_id = $_GET['class'];
	$page_param .= "class=$batch_id&";
}
if(isset($_GET['product_id']) && $_GET['product_id'] != ''){
	$product_id = $_GET['product_id'];
	$page_param .= "class=$product_id&";
}
if(isset($_GET['status']) && $_GET['status'] != ''){
	$status = $_GET['status'];
	$page_param .= "status=$status&";
}

if(isset($_GET['title']) && $_GET['title'] != ''){
	$title = $_GET['title'];
	$page_param .= "title=$title&";
}

$_page = empty($_GET['page']) || !is_numeric($_GET['page'])? 1 : $_GET['page'];



$_limit = PAGINATION_LIMIT;
$objPage = new Pagination($_page, $_limit);

$stdRowsData = $assignmentObj->getAssignmentsByTeacher($_SESSION['user_id'], $objPage->_db_start, $_limit,$center_id,$batch_id,$product_id,$status,$title);

$_total_rscords = $assignmentObj->getTotalAssignmentsByTeacher($_SESSION['user_id'],$batch_id,$product_id,$status,$title);
$objPage->_total = $_total_rscords['total'];
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
   
?>
<div class="breadcrumbBgNone breadcrumbPadder">
	<div class="col-md-6 col-sm-6 text-left"><?php echo $language[$_SESSION['language']]['assignments']; ?></div>
	<div class="col-md-6 col-sm-6 text-right"><span class="pull-right"><a href='createAssignment.php' class="btn btn-primary marginTop0"><?php echo $language[$_SESSION['language']]['add_assignment']; ?></a> 
</span>	</div>
 </div>
 <div class="clear"></div>
 <section class="padder">
   
	 <form id="serachform" name="serachform"  class="form-horizontal form-centerReg" data-validate="parsley" action="assignments.php" >
			<section class="marginBottom5" style="height:80px;">
		
				 
				<div class="col-xs-3 col-sm-3 col-md-3 paddLeft0">
					<select class="form-control parsley-validated" id="class" name="class" onchange="selectBatch(this.value);" >
					  <option value=""><?php echo $language[$_SESSION['language']]['select_class']; ?></option>
					  <?php  foreach($batchInfo as $key => $value){ 
						$sel = ($batch_id == $value['batch_id']) ? "selected" : "";
						?>
					  <option value="<?php echo $value['batch_id']; ?>" <?php echo $sel; ?> ><?php echo $value['batch_name']; ?></option>
					  <?php } ?>
					</select>
				  </div>
				  <!--<div class="col-xs-3 col-sm-3 col-md-3 paddLeft0">
					<select class="form-control parsley-validated" id="product_id" name="product_id" >
					  <option value="">Select Product</option>
					</select>
				  </div>
				  <div class="col-xs-2 col-sm-2 col-md-2 text-left paddLeft0">
				     <select id="status" name="status" onchange="selectStatus(this);" class="form-control " >
					    <option value="">Select Status</option>
						<option <?php echo $status == 'active' ? 'selected="selected"':''?> value="active">Active</option>	
						<option <?php echo $status == 'inactive' ? 'selected="selected"':''?> value="inactive">Inactive</option>
					</select>
				  </div>
				 <div class="col-xs-3 col-sm-3 col-md-3 text-left paddLeft0" >
			 <div class="search-box pull-right" style="position: absolute;background: #fff; z-index: 999;">
				<input name="title"  id="title"  type="text" autocomplete="off" placeholder="Search title..." class="form-control  parsley-validated" <?php if((isset($_REQUEST['title']) && $_REQUEST['title']!="")){?> value="<?php echo $_REQUEST['title'];?>" <?php }?>/>
				
		      <div class="result_list" style="width: 200px"></div>
		   </div>
			</div>	-->
			 <div class="col-xs-2 col-sm-2 col-md-2 pull-right text-right paddRight0">
				<button type="submit" class="btn btn-red" id="btnSave" style="margin-top:0px"> <?php echo $language[$_SESSION['language']]['search']; ?></button>
			 </div>
   </section>  
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
  </form>
   <div class="clear"></div>	 

  <section class="panel panel-default">
   <div class="panel-body">
		  <?php
			if( count($stdRowsData) > 0 && !empty($stdRowsData)) {?>
			<div class="table-responsive">
			<table class="table table-border dataTable table-fixed">
		   <thead  class="fixedHeader">
			  <tr class="col-sm-12 padd0">
			  <th class="col-sm-4 text-left"><?php echo $language[$_SESSION['language']]['title']; ?></th>
			  <!--<th class="col-sm-2 text-left">Course</th>
			  <th class="col-sm-2 text-left">Topic</th>
			  <th class="col-sm-2 text-left">Chapter</th>-->
			  <th class="col-sm-2 text-center"><?php echo $language[$_SESSION['language']]['date']; ?></th>
			  <th class="col-sm-1 text-center"><?php echo $language[$_SESSION['language']]['status']; ?></th>
			  <th class="col-sm-1 text-center"><?php echo $language[$_SESSION['language']]['response']; ?></th>
			  <th class="col-sm-1 text-center"><?php echo $language[$_SESSION['language']]['evaluated']; ?></th>
			  <th class="col-sm-3 text-center"><?php echo $language[$_SESSION['language']]['action']; ?></th>
			  </tr>
			</thead>
		   <tbody>
			<?php

			  foreach($stdRowsData as $key => $stdData){
				   if($stdData['status']==1){
					   $status="Active";
					   $activeClass="style='color:Green'";
					 }else{
						 $status="Inactive";
						 $activeClass="style='color:Red'";
					  }

					  /* Level Detail */
					   $courseDataArr = $assignmentObj->getProductConfigurationByClassAndTrainer($stdData['batch_code'],$_SESSION['center_id'], $_POST['product_id'],'course');

					   $batchCourseStr= str_replace("CRS-","",$courseDataArr[0]['course']);
			
						$courseType='0';
						$courseArr = $adminObj->getCustomCourseList($courseType,$batchCourseStr,'');
					   

	                      $col  = 'sequence_id';
	                      $sort = array();
	                      foreach ($courseArr as $i => $obj) {
	                           $sort[$i] = $obj->{$col};
	                         }
	                      array_multisort($sort, SORT_ASC, $courseArr); 
	                      $course_name = '';

	                      foreach ($courseArr as $key => $course) {
	                      	if($course->course_id == $stdData['course_code']){
	                      		$course_name = $course->name;
	                      	}
	                      }
                    


					  /* Topic Detail */
					  $topic = $assignmentObj->getTopicOrAssessmentByCourseId($stdData['course_code'], $stdData['topic_edge_id']);
					  
                      
                      /* Chapter detail */

                      $chapter_arr = $assessmentObj->getChapterByTopicEdgeId($stdData['topic_edge_id'],$stdData['chapter_edge_id']);
                      foreach($chapter_arr as $key=>$val){
                           if($val->edge_id == $stdData['chapter_edge_id']){
                           	$chapter = $val->name;
                           }
                        }

                        $isResponsed = $assignmentObj->getAllResponsesById($stdData['id'],$stdData['product_id'],$isEvaluated='');
                        $total_response = 0;
                        if($isResponsed){
							$disabledEdit='disabled';
                        	$total_response = count($isResponsed);
                        }else{
							$disabledEdit='';
						}


                     	$evaluated_total = 0;
                     	$_evaluated = $assignmentObj->getEvolutionByAssignmentId($stdData['id'],$stdData['product_id']);
                     	if($_evaluated){
                     		$evaluated_total = count($_evaluated);
							//$disabledEdit='disabled';
                     	}else{
							//$disabledEdit='';
						}
                      ?>
				<tr class="col-sm-12 padd0" ass_id="<?php echo $stdData['id'];?>" prod="<?php echo $stdData['product_id'];?>">
				  <td class="col-sm-4 text-left"><?php echo $stdData['assignment_name']; ?></td>
				  <!--<td class="col-sm-2 text-left"><?php echo $course_name; ?></td>
				  <td class="col-sm-2 text-left"><?php echo $topic->name; ?></td>
				  <td class="col-sm-2 text-left"><?php echo $chapter; ?></td>-->
				  <td class="col-sm-2 text-center"><?php echo date('d-m-Y', strtotime($stdData['created_date'])); ?></td>
				  <td class="col-sm-1 text-center" <?php echo $activeClass;?>><?php echo $status; ?></td>
				  <td class="col-sm-1 text-center"><?php echo $total_response?></td>
				  <td class="col-sm-1 text-center"><?php echo $evaluated_total; ?></td>
				  <td class="col-sm-3 text-center">
				      <a   class="<?php echo $disabledEdit; ?>" class="edit" href="<?php echo "createAssignment.php?aid=".base64_encode($stdData['id']); ?>"> <i class="fa fa-edit"></i> Edit<?php //echo $language[$_SESSION['language']]['edit']; ?></a> | 
					  <?php if($isResponsed){?> 
					  	<a class="text-primary" href="<?php echo 'evaluateStudentResponse.php?aid='.base64_encode($stdData['id']).'&prod='.base64_encode($stdData['product_id']);?>" ><?php echo $total_response>$evaluated_total? 'Evaluate' : 'View' ?></a>
					  	<?php } else { ?>
					  		Evaluate<?php //echo $language[$_SESSION['language']]['evaluate']; ?>
					  	<?php } ?>
					</td>
				 </tr>
			  <?php } ?>
			  <tr><td colspan="12" class="text-center col-sm-12"><?php echo $objPage->createLinks($page_param,5,'pagination');?></td></tr>
			    </tbody>
		      </table>
			   </div>
			 <?php }else{ 
                  if(empty($stdRowsData) && !empty($_POST['batch']) ){?>
                 <div class="col-sm-12 noRecord text-center"><?php echo $language[$_SESSION['language']]['records_not_available.']; ?></div>
				<?php }else{ ?>
                  <div class="col-sm-12 noRecord text-center"> Please
				  <?php if(B2C_CENTER!=$centerId){?>select <?php echo $batch;?> <?php }else{?>
				"Add <?php echo $assignment; ?>"
				
			<?php }?> to get <?php echo $assignment;?> list.</div>
                 <?php } } ?>
            
	 </div>
</section>
</section>
<?php include_once('../footer/trainerFooter.php');?>
<script>

var stdProWin;
function stdProgress(userId){ 
	var winWd=screen.width;
	var winHt=screen.height;
	//var winWd=1012//900;
	//var winHt=607//500;
	//var winLeft = (screen.width - winWd) / 2;
	//var winTop = (screen.height - winHt) / 3;
	var settings='width='+winWd+',height='+winHt+',toolbar=no,menubar=no,resizable=yes,statusbar=no,scrollbars=yes,location=no,fullscreen=yes,directories=no';
	//var settings='left='+winLeft+',top='+winTop+',width='+winWd+',height='+winHt+',toolbar=no,menubar=no,resizable=no,statusbar=no,scrollbars=yes,location=no,fullscreen=yes,directories=no';
	var fpath="studentProgress.php?userId="+userId;
	stdProWin=window.open(fpath, "verify", "fullscreen=yes, scrollbars=auto,toolbar=no,menubar=no,resizable=no,statusbar=no,location=no,directories=no,width="+screen.availWidth+",height="+screen.availHeight);
	//assWin=window.open(fpath,'verify',settings);
	
	openFullscreen();
	stdProWin.focus();
}
</script> 

<script type="text/javascript">
$('#class').change(function(){
	$('.search-box input').val('');
})
$(document).ready(function(){
    $('.search-box input[type="text"]').on("keyup input", function(){
        /* Get input value on change */
        var inputVal = $(this).val();
		$('#student_hidden').val('');
		
		var batch_id = $('#class option:selected').val();
		var status = $('#status option:selected').val();		
        var resultDropdown = $(this).siblings(".result_list");
		if(inputVal.length && inputVal.length>0){
            $.post("ajax/search_by_keyword.php?action=search_assignment", {uname: inputVal,batch_id: batch_id,product_id: product_id,status: status}).done(function(data){ 
                // Display the returned data in browser
				resultDropdown.addClass("resultserchDiv");
                resultDropdown.html(data);
            });
        } else{
			resultDropdown.removeClass("resultserchDiv");
            resultDropdown.empty();
        }
	  
    });
    
    // Set search input value on click of result_list item
    $(document).on("click", ".result_list option", function(){
		
        $(this).parents(".search-box").find('input[type="hidden"]').val($(this).val());
        $(this).parents(".search-box").find('input[type="text"]').val($(this).text());
		$(this).parent(".result_list").removeClass("resultserchDiv");
        $(this).parent(".result_list").empty();
		
    });
});

 function selectBatch(batch_id){
	  <?php  if(isset($_GET['product_id'])){?>
         var product_id_selected = '<?php echo $_GET['product_id'];?>';
	  <?php }else{?>
		  var product_id_selected = '';
	  <?php }?>
	  
   	 if(batch_id!=''){
   		 showLoader();
   			$.ajax({
				type: 'POST',
				url: "ajax/assignment_ajax.php?action=getProduct",
				data: {batch_id:batch_id,trainer_id:<?php echo $_SESSION['user_id'];?>,center_id:<?php echo $_SESSION['center_id'];?>,product_id_selected:product_id_selected},
				
				success: function(res) { 
				   	$('#product_id').html(res);
				    hideLoader();
				   
				}
   		  });
   	
      }
   }
  <?php  if(isset($_GET['class'])){?>
	let batch_id = '<?php echo $_GET['class'];?>';
    selectBatch(batch_id);
	<?php  }?>  
</script> 