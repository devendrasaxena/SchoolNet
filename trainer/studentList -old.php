<?php include_once('../header/trainerHeader.php');


$stdRowsData ='';
/* if(B2C_CENTER!=$centerId){
	if(isset($_POST['batchReportButton'])){
		$batchID = $_POST['batch'];
		$stdRowsData = $adminObj->getUserList(2, $batchID);
	}
}else{
  $batchID = 1;
  $stdRowsData = $adminObj->getUserList(2, $batchID);	
} */
//$stdRowsData = $adminObj->getAllUserDetails(2);///role 2 for student;


$page_param = '';
$batch_id = $status = $title = $student_id = '';
if(isset($_GET['class']) && $_GET['class'] != ''){
	$batch_id = filter_query($_GET['class']);
	$page_param .= "class=$batch_id&";
}

if(isset($_GET['status']) && $_GET['status'] != ''){
	$status = filter_query($_GET['status']);
	$page_param .= "status=$status&";
}
if(isset($_GET['title']) && $_GET['title'] != ''){
	$title = filter_query($_GET['title']);
	$page_param .= "title=$title&";
}



if(isset($_GET['student']) && $_GET['student'] != ''){
	$student_id = filter_query($_GET['student']);
	$page_param .= "student=$student_id&";
	$student_txt ='';
}else if (!empty($_REQUEST['title']) || $_REQUEST['title'] == '0') {
    $student_txt = trim(filter_query($_REQUEST['title']));
	$options['student_txt'] = $student_txt;
	$page_param .= "student_txt=$student_txt&";
}

$assignmentObj = new assignmentController();
$batchInfo = $assignmentObj->getAllClassForTrainer($_SESSION['user_id'], $_SESSION['center_id']);
$_page = empty($_GET['page']) || !is_numeric(filter_query($_GET['page']))? 1 : filter_query($_GET['page']);



if(isset($_REQUEST['limit']))
$_limit = intval($_REQUEST['limit']);
else
 $_limit = PAGINATION_LIMIT;
$objPage = new Pagination($_page, $_limit);
$stdRowsData = $assignmentObj->getAllUserDetails(2, $objPage->_db_start, $_limit, $batch_id, $status, $student_txt, $student_id);
$_total_rscords = $assignmentObj->getTotalUserDetails(2, $batch_id, $status, $student_txt, $student_id);
$objPage->_total = $_total_rscords['total'];
?>
<div class="breadcrumbBgNone breadcrumbPadder">
	<div class="col-md-6 col-sm-6 text-left"><?php echo $language[$_SESSION['language']]['learners']; ?></div>
	<div class="col-md-6 col-sm-6 text-right"></div>
 </div>
 <div class="clear"></div>
 <section class="padder">
   
	 <form id="serachform" name="serachform"  class="form-horizontal form-centerReg" data-validate="parsley" action="studentList.php" >
			<section class="marginBottom5" style="height:80px;">
			
				 
				<div class="col-xs-3 col-sm-3 col-md-3 paddLeft0">
					<select class="form-control parsley-validated" id="class" name="class" >
					  <option value=""><?php echo $language[$_SESSION['language']]['select_class']; ?></option>
					  <?php  foreach($batchInfo as $key => $value){ 
						$sel = ($batch_id == $value['batch_id']) ? "selected" : "";
						?>
					  <option value="<?php echo $value['batch_id']; ?>" <?php echo $sel; ?> ><?php echo $value['batch_name']; ?></option>
					  <?php } ?>
					</select>
				  </div>
				  <div class="col-xs-3 col-sm-3 col-md-3 text-left paddLeft0">
				     <select id="status" name="status" onchange="selectStatus(this);" class="form-control " >
					 
					    <option value="" >  <?php echo $language[$_SESSION['language']]['select'].' '.$language[$_SESSION['language']]['status']; ?></option>
						<option  value="1" <?php if($status=='1'){echo 'selected';} ?>><?php echo $language[$_SESSION['language']]['active']; ?></option>	
						<option <?php if($status=='0'){ echo 'selected';} ?> value="0"><?php echo $language[$_SESSION['language']]['inactive']; ?></option>
					</select>
				  </div>
				 <div class="col-xs-3 col-sm-3 col-md-3 text-left paddLeft0" >
			 <div class="search-box pull-right" style="position: absolute;background: #fff; z-index: 999;">
				<input name="title"  id="title"  type="text" autocomplete="off" placeholder=" <?php echo $language[$_SESSION['language']]['search'].' '.$language[$_SESSION['language']]['name']; ?>..." class="form-control  parsley-validated" <?php if((isset($_REQUEST['title']) && $_REQUEST['title']!="")){?> value="<?php echo filter_query($_REQUEST['title']);?>" <?php }?>/>
				<input name="student"  id="student_hidden"  type="hidden" <?php if((isset($_REQUEST['student']) && $_REQUEST['student']!="")){?> value="<?php echo filter_query($_REQUEST['student']);?>" <?php }?>/>
				
		      <div class="result_list" style="width: 200px"></div>
		   </div>
			</div>	
			 <div class="col-xs-3 col-sm-3 col-md-3 pull-right text-right paddRight0">
				<button type="submit" class="btn btn-red" id="btnSave" style="margin-top:0px" title=" <?php echo $language[$_SESSION['language']]['search'].' '.$language[$_SESSION['language']]['learners']; ?>"> <?php echo $language[$_SESSION['language']]['search']; ?></button>
				<a class="btn btn-sm btn-red btnwidth40" href="studentList.php" name="refresh" title=" <?php echo $language[$_SESSION['language']]['refresh']; ?>" style="margin-top:0px"> <i class="fa fa-refresh"></i></a>
			</div>
   </section>  

				 <label><?php echo $language[$_SESSION['language']]['show_records']; ?> </label> <select name="limit" onchange="this.form.submit()">
			 	<option value="10" <?php echo $_limit==10? 'selected':'' ?> >10 </option>
			 	<option value="25"<?php echo $_limit==25? 'selected':'' ?> >25 </option>
			 	<option value="50" <?php echo $_limit==50? 'selected':'' ?> >50 </option>
			 	<option value="100" <?php echo $_limit==100? 'selected':'' ?> >100 </option>
			 </select>
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
			  <th class="col-sm-3"><?php echo $language[$_SESSION['language']]['first_name']; ?></th>
			  <th class="col-sm-3"><?php echo $language[$_SESSION['language']]['last_name']; ?></th>
			  <th class="col-sm-3 text-left"><?php echo $language[$_SESSION['language']]['login_id']; ?></th>
			  <th class="col-sm-2 text-left"><?php echo $language[$_SESSION['language']]['status']; ?></th>
			  <th class="col-sm-1 text-left"><?php echo $language[$_SESSION['language']]['action']; ?></th>
			  </tr>
			</thead>
		   <tbody>
			<?php
			  foreach($stdRowsData as $key => $values){
				   //$stdInfo= userdetails($values['user_id']);
				   if($values['is_active']==1){
					   $status= $language[$_SESSION['language']]['active'];
					   $activeClass="style='color:Green'";
					 }else{
						 $status=$language[$_SESSION['language']]['inactive'];;
						 $activeClass="style='color:Red'";
					  }

					  $uid = base64_encode($values['user_id']);

				   ?>
				<tr class="col-sm-12 padd0" uid="<?php echo $values['user_id'];?>">
				  <td class="col-sm-3"><?php echo $values['first_name']; ?></td>
				  <td class="col-sm-3"><?php echo $values['last_name']; ?></td> 
				  <td class="col-sm-3 text-left"><?php echo $values['email_id']; ?></td>
				  <td class="col-sm-2 text-left" <?php echo $activeClass;?>>
				  <?php echo $status; ?>
				  </td>
				  <td class="col-sm-1 text-left">
				  <a title="<?php echo $language[$_SESSION['language']]['view_details']; ?>" href="learner-detail.php?uid=<?php echo $uid?>"><?php echo $language[$_SESSION['language']]['view_details']; ?> </a>
				  </td>
				 </tr>
			  <?php } ?>
			  <tr>
			  <td colspan="12" class="text-center col-sm-12">
			  <?php echo $objPage->createLinks($page_param,5,'pagination');?>
			  </td>
			  </tr>
			    </tbody>
		      </table>
			   </div>
			 <?php }else{ 
                 if(empty($stdRowsData) && (!empty($_REQUEST['class']) || !empty($_REQUEST['status']))){?>
                 <div class="col-sm-12 noRecord text-center">Records is not available.</div>
				<?php }else{ ?>
                  <div class="col-sm-12 noRecord text-center"> Please
				  <?php if(B2C_CENTER!=$centerId){?>select <?php echo $batch;?> <?php }else{?>
				"Add <?php echo $student; ?>"
				
			<?php }?> to get <?php echo $student;?> list.</div>
                 <?php } } ?>
            
	 </div>
</section>
</section>
<?php include_once('../footer/trainerFooter.php');?>
<script>

$('#class').change(function(){
	$('.search-box input').val('');
})

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
$(document).ready(function(){
    $('.search-box input[type="text"]').on("keyup input", function(){
        /* Get input value on change */
        var inputVal = $(this).val();
		$('#student_hidden').val('');
		
		var batch_id = $('#class option:selected').val();
		var status = $('#status option:selected').val();		
        var resultDropdown = $(this).siblings(".result_list");
		if(inputVal.length && inputVal.length>0){
            $.post("ajax/search_by_keyword.php?action=search_student", {uname: inputVal,batch_id: batch_id,status: status}).done(function(data){ 
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
</script> 