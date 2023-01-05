<?php 
include_once('../header/trainerHeader.php');
$centerObj = new centerController();
$reportObj = new reportController();
$country_list_arr=$reportObj->getCountryList();

$msg='';	
$err='';	
$succ='';	

if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
	if($_SESSION['error'] == '1'){
		$msg = "$student not saved. Please try again.";
	}
}
if(isset($_SESSION['succ']) && $_SESSION['succ'] != ""){
	
	if($_SESSION['succ'] == '1'){
		$msg = "$student created successfully.";
	}
	if($_SESSION['succ'] == '2'){
		$msg = "$student updated successfully.";
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
//echo $_SESSION['center_id'];

$options = array();
$options['client_id'] = $client_id;
$options['center_id'] = $center_id;
$options['role_id'] = 2;

$dir = "";
$order = (isset($_GET['sort']) && $_GET['sort'] !="") ? filter_query($_GET['sort']) : 'u1.created_date';
$dir = (isset($_GET['dir']) && $_GET['dir'] !="") ? filter_query($_GET['dir']) : 'ASC';

switch(strtoupper($dir)){
	case 'DESC': 
		$dir = 'ASC'; 
		break;
	case 'ASC': 
		$dir = 'DESC'; 
		break;
	default: 
		$dir = 'DESC'; 
		break;
}


$page_param='';

$page_param .= "sort=".filter_query($_GET['sort'])."&dir=".filter_query($_GET['dir'])."&";



$batch_id='';
$student_id='';
$status='';
$batchStringData=implode(",",$batchIdArr);

//$batchIdArr;
if (!empty($_REQUEST['batch_id'])) {
    $batch_id = trim(filter_query($_REQUEST['batch_id']));
	$options['batch_id'] = $batch_id;
	$page_param .= "batch_id=$batch_id&";
}else{
$options['batch_id'] = $batchStringData;
	$page_param .= "batch_id=$batch_id&";	
}

if (!empty($_REQUEST['student'])) {
    $student_id = trim(filter_query($_REQUEST['student']));
	$options['student_id'] = $student_id;
	$page_param .= "student_id=$student_id&";
}else if (!empty($_REQUEST['student_txt']) || $_REQUEST['student_txt'] == '0') {
    $student_txt = trim(filter_query($_REQUEST['student_txt']));
	$options['student_txt'] = $student_txt;
	$page_param .= "student_txt=$student_txt&";
}
if (!empty($_REQUEST['status']) || $_REQUEST['status'] == '0') {
    $status = trim(filter_query($_REQUEST['status']));
	$options['status'] = $status;
	$page_param .= "status=$status&";
}




$_page = empty($_GET['page']) || !is_numeric($_GET['page'])? 1 : filter_query($_GET['page']); 

//$_limit = 20;
if(isset($_REQUEST['limit']))
$_limit = intval($_REQUEST['limit']);
else
 $_limit = PAGINATION_LIMIT;
$objPage = new Pagination($_page, $_limit);

if( isset($_REQUEST['report_type']) && $_REQUEST['report_type'] == 'export' ){

	$response_result= $centerObj->getUsersByBatchCenterAndCountry($options,$objPage->_db_start, '',$order,$dir);

}else{
	$response_result= $centerObj->getUsersByBatchCenterAndCountry($options,$objPage->_db_start, $_limit,$order,$dir);
}

$objPage->_total = $response_result['total'];
$users_arr = $response_result['result'];



?>
<div class="breadcrumbBgNone breadcrumbPadder">
	<div class="col-md-6 col-sm-6 text-left"> <?php echo $language[$_SESSION['language']]['learners']; ?></div>
		<div class="col-md-6 col-sm-6 text-right paddRight0">
		<?php if($create_learner==1){?>
		<span class="pull-right">
			<a href='createStudent.php' title=" <?php echo $language[$_SESSION['language']]['add_learner']; ?>" class="btn btn-primary marginTop0"> <?php echo $language[$_SESSION['language']]['add_learner']; ?></a> 
			<a href='bulkStudentUpload.php' class="btn btn-primary marginTop0" title="<?php echo $language[$_SESSION['language']]['bulk_upload_learners']; ?>"><?php echo $language[$_SESSION['language']]['bulk_upload_learners']; ?></a>
		</span>	
		<?php }?>
</div>
 </div>
 <div class="clear"></div>
 <section class="padder">

 
	 <form id="serachform" name="serachform"  method = "POST"  class="form-horizontal form-centerReg" data-validate="parsley" action="studentList.php" >
			<section class="marginBottom5 serachformDiv">
	
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pull-left text-left paddLeft0">
              <div class="col-xs-3 col-sm-3 col-md-3 text-left paddLeft0">
					<select class="form-control parsley-validated" id="batch_id" name="batch_id" >
						<?php  $optiondisabled = ($batch_id) ? "disabled" : ""; ?>
                
					  <option value="" <?php  echo $optiondisabled ;?>><?php echo $language[$_SESSION['language']]['select_class']; ?></option>
					  <?php 
					   $optionSelected = ($batch_id == 'All') ? "selected" : "";
                       echo '<option value="All" '.$optionSelected.'>All</option>';
					  foreach($batchData as $key => $value){ 
					  	$batchNameData=$adminObj->getBatchNameByID($value['batch_id']);
		
					
						$sel = ($batch_id == $value['batch_id']) ? "selected" : "";
						?>
					  <option value="<?php echo $value['batch_id']; ?>" <?php echo $sel; ?> ><?php echo $batchNameData[0]['batch_name'];?></option>
					  <?php } ?>
					</select>
				  </div>
				

					<div class="col-xs-3 col-sm-3 col-md-3 text-left paddLeft0 paddRight0">
				     <select id="status" name="status" onchange="selectStatus(this);" class="form-control " >
							<option value=""><?php echo $language[$_SESSION['language']]['status']; ?></option>
							<option  value="1" <?php if($status=='1'){echo 'selected';} ?>><?php echo $language[$_SESSION['language']]['active']; ?></option>	
						<option <?php if($status=='0'){ echo 'selected';} ?> value="0"><?php echo $language[$_SESSION['language']]['inactive']; ?></option>
						</select>
				  </div>
			
		    <div class="col-xs-4 col-sm-4 col-md-4  text-left relative" >
			   <div class="searchboxCSS search-box col-xs-10 padd0 pull-right">
				<input name="student_txt"  id="student_txt"  type="text" autocomplete="off" placeholder="<?php echo $language[$_SESSION['language']]['search'].' '.$language[$_SESSION['language']]['learners'].' '.$language[$_SESSION['language']]['name_or_email']; ?>..." class="form-control  parsley-validated" <?php if((isset($_REQUEST['student_txt']) && $_REQUEST['student_txt']!="")){?> value="<?php echo filter_query($_REQUEST['student_txt']);?>" <?php }?>/>
				<input name="student"  id="student_hidden"  type="hidden" class="form-control  parsley-validated"  <?php if((isset($_REQUEST['student']) && $_REQUEST['student']!="") && (isset($_REQUEST['student_txt']) && $_REQUEST['student_txt']!="")){?> value="<?php echo filter_query($_REQUEST['student']);?>" <?php }?>/>
				<div class="result_list"></div>
		        </div>
			 </div>	
			  
			 <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 pull-right text-right padd0">
				<button type="submit" name="Submit" class="btn btn-red" id="btnSave" style="margin-top:0px" title=" <?php echo $language[$_SESSION['language']]['search']; ?> <?php echo $language[$_SESSION['language']]['learners']; ?>"> <?php echo $language[$_SESSION['language']]['search']; ?></button>
				<a class="btn btn-sm btn-red btnwidth40" href="studentList.php" name="refresh" title=" <?php echo $language[$_SESSION['language']]['refresh']; ?>" style="margin-top:0px"> <i class="fa fa-refresh"></i></a>
			 </div>
		</div>
		<div class="clear" style="margin-top:10px;">&nbsp;</div>
				 <label><?php echo $language[$_SESSION['language']]['show_records']; ?> </label> <select name="limit" onchange="this.form.submit()">
			 	<option value="10" <?php echo $_limit==10? 'selected':'' ?> >10 </option>
			 	<option value="25"<?php echo $_limit==25? 'selected':'' ?> >25 </option>
			 	<option value="50" <?php echo $_limit==50? 'selected':'' ?> >50 </option>
			 	<option value="100" <?php echo $_limit==100? 'selected':'' ?> >100 </option>
			 </select>
			 
	 <?php if($succ=='1'){?>
      <div class="alert alert-success col-sm-12">
        <button type="button" class="close" data-dismiss="alert">x</button>
        <i class="fa fa-ban-circle"></i><?php echo $msg;?></div>
      <?php } ?>
	<?php if($succ=='2'){?>
      <div class="alert alert-success col-sm-12">
        <button type="button" class="close" data-dismiss="alert">x</button>
        <i class="fa fa-ban-circle"></i> <?php echo $msg;?> </div>
      <?php } ?>
	    <?php if($err == '1'){?>
		  <div class="alert alert-danger col-sm-12">
			<button type="button" class="close" data-dismiss="alert">x</button>
			<i class="fa fa-ban-circle"></i><?php echo $msg;?> </div>
		  <?php } ?>		 
   </section>  
  </form>
   <div class="clear"></div>	

  <section class="panel panel-default">
  
  
   <div class="panel-body">
		  <?php if($objPage->_total>0){	$no = ($_page - 1) * $_limit + 1;?>
			<div class="table-responsive">
			<table class="table table-border dataTable table-fixed">
		   <thead  class="fixedHeader">
			  <tr class="col-sm-12 padd0">
			  <th class="col-sm-4"><a href="studentList.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo filter_query($_REQUEST['student_txt']); ?>&course_id=<?php echo $course_id; ?>&sort=first_name&dir=<?php echo $dir; ?>" class="th-sortable"><?php echo $language[$_SESSION['language']]['name']; ?>
					<span class="th-sort"> 
					<?php 
					if(isset($_GET['sort']) && $_GET['sort'] == 'first_name' && $_GET['dir']=='ASC'){ ?>
						<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
					<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'first_name' && $_GET['dir']=='DESC'){ ?>
						<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
					<?php }else{ ?> 
						<i class='fa fa-sort'></i>
					<?php } ?>
					</span></a></th>
			 
			  <th class="col-sm-2 text-left hide"><a href="studentList.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo filter_query($_REQUEST['student_txt']); ?>&course_id=<?php echo $course_id; ?>&sort=last_name&dir=<?php echo $dir; ?>" class="th-sortable"><?php echo $language[$_SESSION['language']]['last_name']; ?>
					<span class="th-sort"> 
					<?php 
					if(isset($_GET['sort']) && $_GET['sort'] == 'last_name' && $_GET['dir']=='ASC'){ ?>
						<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
					<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'last_name' && $_GET['dir']=='DESC'){ ?>
						<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
					<?php }else{ ?> 
						<i class='fa fa-sort'></i>
					<?php } ?>
					</span></a></th>
			 <th class="col-sm-3 text-left"><a href="studentList.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo filter_query($_REQUEST['student_txt']); ?>&course_id=<?php echo $course_id; ?>&sort=email_id&dir=<?php echo $dir; ?>" class="th-sortable"><?php echo $language[$_SESSION['language']]['login_id']; ?>
					<span class="th-sort"> 
					<?php 
					if(isset($_GET['sort']) && $_GET['sort'] == 'email_id' && $_GET['dir']=='ASC'){ ?>
						<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
					<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'email_id' && $_GET['dir']=='DESC'){ ?>
						<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
					<?php }else{ ?> 
						<i class='fa fa-sort'></i>
					<?php } ?>
					</span></a></th>
			  <th class="col-sm-2 text-left"><a href="studentList.php?country=<?php echo $country; ?>&center_id=<?php echo $center_id; ?>&batch_id=<?php echo $batch_id; ?>&student=<?php echo $student_id; ?>&student_txt=<?php echo filter_query($_REQUEST['student_txt']); ?>&course_id=<?php echo $course_id; ?>&sort=phone&dir=<?php echo $dir; ?>" class="th-sortable"><?php echo $language[$_SESSION['language']]['mobile']; ?>
					<span class="th-sort"> 
					<?php 
					if(isset($_GET['sort']) && $_GET['sort'] == 'phone' && $_GET['dir']=='ASC'){ ?>
						<i class='fa fa-sort'></i><i class='fa fa-sort-down fa-active'></i>
					<?php }else if(isset($_GET['sort']) && $_GET['sort'] == 'phone' && $_GET['dir']=='DESC'){ ?>
						<i class='fa fa-sort-up fa-active'></i><i class='fa fa-sort'></i></i>
					<?php }else{ ?> 
						<i class='fa fa-sort'></i>
					<?php } ?>
					</span></a></th>
			  <th class="col-sm-2 text-center"><?php echo $language[$_SESSION['language']]['status']; ?></th>
			   </tr>
			</thead>
		   <tbody>
			<?php
			   foreach($users_arr  as $key => $value){
				$first_name=$value->first_name;
				$last_name=$value->last_name;
				  

				$uid = base64_encode($value->user_id);
				   if($value->is_active==1){
					   $status="Active";
					   $activeClass="style='color:Green'";
					 }else{
						 $status="Inactive";
						 $activeClass="style='color:Red'";
					  }
				  if($region_id=='5'){
					 $email_id=$value->loginid;
				  }else{
					 $email_id= $value->email_id;
				  }
				  //echo "<pre>";print_r($stdInfo);
				   ?>
				<tr class="col-sm-12 padd0" uid="<?php echo $value->user_id;?>">
				  <td class="col-sm-4"><?php echo $value->first_name.' '.$value->last_name; ?></td>
				  <td class="col-sm-2 text-left hide"><?php echo $value->last_name; ?></td>
				<td class="col-sm-3 text-left"><?php echo $email_id; ?></td>
				  <td class="col-sm-2 text-left"><?php echo $value->phone; ?></td>
				   <td class="col-sm-2 text-center" <?php echo $activeClass;?>><?php echo $status; ?></td>
				  </tr>
				
			  <?php } ?>
			   <tr><td colspan="12" class="text-center col-sm-12"><?php echo $objPage->createLinks($page_param,5,'pagination');?></td></tr>
			    </tbody>
		      </table>
			   </div>
			 <?php }else{ 
                  if(empty($users_arr) && !empty($_POST['batch']) ){?>
                 <div class="col-sm-12 noRecord text-center"><?php echo $language[$_SESSION['language']]['records_not_available.']; ?></div>
				<?php }else{ ?>
                  <div class="col-sm-12 noRecord text-center"> 
				  <?php if(B2C_CENTER!=$centerId){?><?php echo $language[$_SESSION['language']]['records_not_available.']; ?>	<?php echo $language[$_SESSION['language']]['add_learner']; ?> <?php }else{?><?php echo $language[$_SESSION['language']]['records_not_available.']; ?>
				<?php echo $language[$_SESSION['language']]['add_learner']; ?>
				
			<?php }?></div>
                 <?php } } ?>
            
	 </div>
</section>
</section>

<?php include_once('../footer/trainerFooter.php');?>
<style>
.th-sortable .th-sort {
    float: none;
    position: relative;margin-left:2px;
}
</style>

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

function updateStudentStatusFn(filePath,userId,uStatus){
      var stype;	
		if(uStatus==1){
			stype='Deactivate';
		}else if(uStatus==0){
			stype='Activate'
		}
		//var r1 = confirmPopup("Are you sure to "+stype+" this user ?");
		//alert(r1)
		var r = confirm("Are you sure to "+stype+" this user ?");
		if (r == true) {
			if(uStatus==1){
				status=0;
			}else if(uStatus==0){
				status=1;
			}
			var selectBatch=$("#fld_batch").val();
			console.log(selectBatch)
			//alert("You've clicked Ok");
			$.post(filePath, {userId: userId,uStatus:status}, 
			function(data){ 
				if(data == 'nO'){ 
					console.log('Error');
				}else{ 
				    window.location.href= window.location.href; 
				  /* if(selectBatch==batchID){
					$("#batchReportButton").trigger( "click" );
					
				 }else{
					//window.location.href= window.location.href; 
				 } */
				} 
			});
		} else {
		  //alert("You've clicked Cancel");
		}

}

</script>
<script type="text/javascript">
$(document).ready(function(){
    $('.search-box input[type="text"]').on("keyup input", function(){
        /* Get input value on change */
        var inputVal = $(this).val();
		$('#student_hidden').val('');
		var batch_id = $('#batch_id option:selected').val();
		if(batch_id.length && batch_id.length>0){
			batch_id=batch_id;
		}else{
			batch_id=<?php echo $batchStringData;?>
		}
		var center_id = $('#center_id option:selected').val();
		var country = $('#country option:selected').val();
		var status = $('#status').val();
        var resultDropdown = $(this).siblings(".result_list");
		if(inputVal.length && inputVal.length>0){
            $.post("ajax/search_student.php", {uname: inputVal,batch_id: batch_id,center_id: <?php echo $center_id;?>,country: country,status: status}).done(function(data){ 
                // Display the returned data in browser
                resultDropdown.html(data);
                resultDropdown.css({"border":"1px solid #ccc","border-top":"0px"});
            });
        } else{
            resultDropdown.empty();
        }
	  
    });
    
    // Set search input value on click of result_list item
    $(document).on("click", ".result_list option", function(){
		
        $(this).parents(".search-box").find('input[type="hidden"]').val($(this).val());
        $(this).parents(".search-box").find('input[type="text"]').val($(this).text());
        $(this).parent(".result_list").empty();
		$(this).parent(".result_list").css({"border":"none"});
		$(this).parent(".result_list").css({"padding":"0px","width":"0px","height":"0px","overflow-y":"none","overflow-x":"hidden"});
		 $(this).parents(".search-box").css({"padding":"0px","width":"0px","height":"0px","overflow-y":"none","overflow-x":"hidden"});
    });
});
</script> 