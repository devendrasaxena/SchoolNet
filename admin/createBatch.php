<?php
include_once('../header/adminHeader.php');
$adminObj = new centerAdminController();
$assessmentObj = new assessmentController();
$productObj = new productController();

$clientUserId=$assessmentObj->getSuperClientId($user_group_id );
$course_arr=$assessmentObj->getCourseByClientId($clientUserId); 
$productListArr=$productObj->getProductByClientId($client_id);
//echo "<pre>";print_r($productListArr);

//echo "<pre>";print_r($_SESSION['user_group_id']);exit;	

//echo "<pre>";print_r($course_arr);exit;  
$centerDetail=$adminObj->getCenterDetails();
$centerName=$centerDetail[0]['name'];
$center_id=$centerDetail[0]['center_id'];


/* Show edit batch */

if(isset($_GET['bid']) && !empty($_GET['bid']) || isset($_GET['cid']) && !empty($_GET['cid'])){
  $bId = trim( base64_decode($_GET['bid']) );
  $cId = trim( base64_decode($_GET['cid']) );
    if(is_numeric($bId)==true && is_numeric($cId)==true){
		$batchData = $centerObj->getBatchDataByID($bId,$cId);
    }else{
		header('Location: dashboard.php');
		die;
	}
	
  $batchDataDetails1 = $centerObj->getBatchDataByIDDetails($bId,$cId,$product_id=null);
  //echo "<pre>xdgfh";print_r($batchDataDetails);//exit;
  
  $productData1 = array();
  foreach ($batchDataDetails1  as $key => $value) {
   	$productData1[$key] = $value['product_id'];
   } 
  // echo "<pre>";print_r($productData1);
	 
   if($batchData[0]['batch_type']!="None"){
	  $str=$batchData[0]['batch_name'];
	  $batcharr=explode("-",$str);
	  $batchName=$batcharr[0];
	  $sectionCount=$batcharr[1];
   }else{  
	  $batchName=$batchData[0]['batch_name']; 
      $batch_type=$batchData[0]['batch_type']; 	  
   }
  $batchName=$batchData[0]['batch_name'];
  $batchNameArr=explode("-",$batchName);
 
  $batchId=$batchData[0]['batch_id'];  
  $batchCenterId=$batchData[0]['center_id'];
  $learning_mode=$batchData[0]['learning_mode'];
  
  //echo "<pre>";print_r($batchData);exit;
}

if(!empty($_GET['bid'])){
	   $countClass ="";
	  $regClass ="";
	  $errDiv = "";
	  $pageType = $language[$_SESSION['language']]['edit'];
	  $disabled='disabledInput';
  }else{
	  $pageType =$language[$_SESSION['language']]['add'];;
      $countClass ="displayNone";
	  $disabled="";
	 } 

$msg='';	
$err='';	
$succ='';	

if(isset($_SESSION['error']) && $_SESSION['error'] != ""){
	if($_SESSION['error'] == '3'){
		$msg = "$batch is already exist. Please try another.";
	}
	
}

if(isset($_SESSION['error']) && $_SESSION['error'] != ""){

		$msg = $_SESSION['msg'];
		$err=$_SESSION['error'];
		unset($_SESSION['msg']);
		unset($_SESSION['error']);
}
	 
	 
?>
<style>

  .panel-heading a:after {    width: 2%;
    font-family:'FontAwesome';
    content:"\f106";
    float: right;
    color: grey;
	font-size:14px;font-weight:700;position:absolute;    right: 5px;
}
 
.panel-heading a.collapsed:after {
    content:"\f107";
}

</style>
<ul class="breadcrumb no-border no-radius b-b b-light">
 <li> <a href="batchList.php" title="<?php echo $language[$_SESSION['language']]['classes']?>"><i class="fa fa-arrow-left"></i> <?php echo $language[$_SESSION['language']]['classes']; ?></a></li>
</ul>
<div class="clear"></div>
 <section class="padder">
   <div class="row-centered">
	<div class="col-sm-10 col-xs-12 col-centered">
	<?php if($err == '3'){?>
		  <div class="alert alert-danger col-sm-12">
			<button type="button" class="close" data-dismiss="alert">x</button>
			<i class="fa fa-ban-circle"></i><?php echo $msg;?> </div>
		  <?php } ?>
     <section class="marginBottom40">
      <form action="ajax/batchFormSubmit.php" id="createBatchForm" name="createBatchForm" class="createbatch" method="post"  data-validate="parsley"  autocomplete="off" onsubmit="return validationBatch();">
	  <div class="row">
       <div class="panel panel-default bdrNone">
        <div class="panel-body padd20">
		<h3 class="panel-header"><?php echo $pageType." ".$language[$_SESSION['language']]['classes']; ?></h3>

		 <input id="batchIdVal" type="hidden" name="batchIdVal" value="<?php echo $bId; ?>"/>
		 <input id="cbatchName" type="hidden" name="cbatchName" value="<?php echo $batchName; ?>"/>
		  <div class="form-group col-sm-4 paddLeft0">

				  <label class="control-label"><?php echo $language[$_SESSION['language']]['state_name']; ?> <span class="required">*</span></label>
				 <select class="form-control input-lg parsley-validated fld_class <?php echo $disabled;?>" name="center_id" id="center_id" data-required="true"  <?php if(count($productListArr)==1){ ?>onchange="showProgramCourseList();" <?php } ?>>
				 <option  value="" ><?php echo $language[$_SESSION['language']]['select_state'];?></option>
				  <?php 
					 foreach ($centers_arr as $key => $value) {	
					   $centerName= $centers_arr[$key]['name'];
					   $centerId= $centers_arr[$key]['center_id']; 
					 
					  $selectedCenter =  (  $centerId == $batchCenterId ) ?  'selected ="selected"' : '';
					 ?>
					<option  value="<?php echo $centerId; ?>" <?php echo $selectedCenter; ?> ><?php echo $centerName;?></option>	
					 <?php }?>
				</select>
				<label class="required" id="errorCenter"> </label>
			 </div>
			
		  <?php if(count($productListArr)>1){ ?>
		   <div class="col-sm-4"> 
		   <label class="control-label">Product List <span class="required">*</span></label> 
			<div class="clear"></div>
			
			<select class="form-control input-lg parsley-validated fld_class" name="product_id[]" id="product_id" data-required="true" size="5" multiple onchange="showProgramCourseList();">
				  <?php 
					 foreach ($productListArr as $value) {	
					   $productName= $value['product_name'];
					   $productId= $value['id']; 
					   
					   $selected=(is_array($productData1) && in_array($value['id'], $productData1))  ? "selected" : "";
					 ?>
					<option  value="<?php echo $productId; ?>" master="<?php echo $value['master_products_ids']; ?>" <?php echo $selected; ?> ><?php echo $productName;?></option>	
					 <?php }?>
				</select>
				<label class="required" id="errorProduct"> </label>
				  </div>
		  <?php }else{?>
		  <div class="col-sm-4"> 
		   <label class="control-label">Product List <span class="required">*</span></label> 
			<div class="clear"></div>
		  <select class="form-control input-lg parsley-validated fld_class" name="product_id[]" id="product_id" data-required="true">
				 <?php 
					 foreach ($productListArr as $value) {	
					   $productName= $value['product_name'];
					   $productId= $value['id']; 
					 $selected=(is_array($productData) && in_array($value['id'], $productData))  ? "selected" : "";
					 ?>
					<option  value="<?php echo $productId; ?>" master="<?php echo $value['master_products_ids']; ?>" <?php echo $selected; ?> ><?php echo $productName;?></option>	
					 <?php }?>
				</select>
				<label class="required" id="errorProduct"> </label>
				  </div>
			  <?php }?>
		    <div class="col-sm-4"> 
		   <label class="control-label"><?php echo $language[$_SESSION['language']]['classes']; ?> <span class="required">*</span></label> 
			<div class="clear"></div>
			
			<input type="text" class="form-control greenBorder" id="batch" name="batch" maxlength = "250"  value="<?php echo $batchName;?>" />
            <label class="required" id="errorBatch"> </label>
		    
		  </div>
			  <div class="col-sm-4 padd0 hide">
                 <label for="lmode" class="control-label">Learning Mode <span class="required">*</span></label>
				 <div class="">
				 <input type="radio" id="lmode1" name="lmode" value="master"  class="testCheckbox lMode"   checked=<?php if($learning_mode == "master") { echo "checked"; }?> /> Master Mode
		           &nbsp;&nbsp;<input type="radio" id="lmode2" name="lmode" value="guided"  class="testCheckbox lMode" <?php if($learning_mode =="guided") { echo "checked"; }?>/> Guided  Mode
                     <label class="required" id="errorLmode">
                </div>
				<div class="miniScorePassDiv" id="miniScorePassDiv" style="display:none"> 
				Minimum score to pass <input type="text" class="form-control" id="miniScorePass" name = "miniScorePass" value="" style="margin-top:10px;width:80px;display:inline-block"/>
				 </div>
             
			 </div>  
		   
	       <?php if(!empty($batchData[0]['batch_type'])){?>

				  <?php if($batchData[0]['batch_type']=="Number"){?>
				    <div class="col-sm-2">
					   <label class="control-label"><?php echo $section; ?> <span class="required required">*</span></label>			 
					   <div class="clear"></div>
						<select class="form-control" id="sectionEdit" name="section" data-required="true">
						
						<option value='<?php echo $sectionCount;?>'><?php echo $sectionCount;?></option>
						<?php //for ($i = 0; $i < 100; $i++) { 
							//$number =$i+1;
							//if($sectionCount==$number){
								//$disabled="disabled";
							//}else{$disabled='';}
							?>
						<!--<option <?php //selectedCheck($sectionCount,$number); ?>value='<?php //echo $number;?>' <?php //echo $disabled;?>><?php// echo $number;?></option>-->
						<?php //} ?>

				     </select>
					  </div>
					   <input type="hidden" id="sectionType" name="sectionType"  value="<?php echo $batchData[0]['batch_type'];?>"/>
		        <?php } if($batchData[0]['batch_type']=="Alphabet"){?>
				  <div class="col-sm-2">
					   <label class="control-label"><?php echo $section; ?> <span class="required">*</span></label>			 
					   <div class="clear"></div>
					 <select class="form-control" id="sectionEdit" name="section" data-required="true">
					 <option value='<?php echo $sectionCount;?>'><?php echo $sectionCount;?></option>
					<?php// foreach (range('A', 'Z') as $char) {//echo $char . "\n";?>
					<!--<option <?php// selectedCheck($sectionCount,$char); ?>value='<?php //echo $char;?>' <?php //echo $disabled;?>><?php //echo $char;?></option>-->
						<?php //} ?>
				   </select>
				    </div>
					 <input type="hidden" id="sectionType" name="sectionType"  value="<?php echo $batchData[0]['batch_type'];?>"/>
					<?php }if($batchData[0]['batch_type']=="None"){?>
						<input type="hidden" id="sectionEdit" name="section"  value=""/>
						 <input type="hidden" id="sectionType" name="sectionType"  value="<?php echo $batchData[0]['batch_type'];?>"/>
					<?php } 
			 }else{
				  if($sectionConfig=='Number') {?>
				    <div class="col-sm-2">
					   <label class="control-label"><?php echo $section; ?> <span class="required">*</span></label>			 
					   <div class="clear"></div>
						<select class="form-control" id="section" name="section" data-required="true">
						<?php for ($i = 0; $i < 100; $i++) { 
							$number =$i+1;
							if($sectionCount==$number){
								//$disabled="disabled";
							}else{$disabled='';}
							?>
						<option <?php selectedCheck($sectionCount,$number); ?>value='<?php echo $number;?>' <?php//echo $disabled;?>><?php echo $number;?></option>
						<?php } ?>
					   </select>
					 </div>
					 <input type="hidden" id="sectionType" name="sectionType"  value="<?php echo $sectionConfig;?>"/>
				   <?php }if($sectionConfig=='Alphabet') {?>
				    <div class="col-sm-2">
					   <label class="control-label"><?php echo $section; ?> <span class="required">*</span></label>			 
					   <div class="clear"></div>
						<select class="form-control" id="section" name="section" data-required="true">
						 <?php foreach (range('A', 'Z') as $char) {//echo $char . "\n";?>
					<option <?php selectedCheck($sectionCount,$char); ?>value='<?php echo $char;?>' ><?php echo $char;?></option>
						<?php } ?>
					   </select>
					 </div>
					 <input type="hidden" id="sectionType" name="sectionType"  value="<?php echo $sectionConfig;?>"/>
				   <?php } if($sectionConfig=="None"){?>
				   <input type="hidden" id="section" name="section"  value=""/>
				    <input type="hidden" id="sectionType" name="sectionType"  value="<?php echo $sectionConfig;?>"/>
			   <?php }?>
			   
					  
		   <?php  }?>
			

		    </div>
			<div class="clear"></div>
		   <div class="">
		   <div class="panel panel-default bdrNone">
        <div class="panel-body padd20">
		 <div id="courseDiv"></div>
		 
		 </div>
		</div>
		   
		   </div>
		
	    </div>
	   </div>
	   <div class="clear"></div>
		   <div class="text-right"> 
			<a href='batchList.php' title="<?php echo $language[$_SESSION['language']]['cancel']; ?>" class="btn btn-primary "><?php echo $language[$_SESSION['language']]['cancel']; ?></a>&nbsp;&nbsp;
			
			<button type="submit" title="<?php echo $language[$_SESSION['language']]['submit']; ?>" class="btn btn-s-md btn-primary" name="createBatch" id="createBatch"  onclick="showLoaderOrNot('createBatchForm');" ondblclick="showLoaderOrNot('createBatchForm');"><?php echo $language[$_SESSION['language']]['submit'];?></button>
	    </div>
     </form>
   </section> 
  </div>
 </div>
</section>
<?php include_once('../footer/adminFooter.php');?>
<script>

$(document).ready(function(){
var sectorWise = "<?php  echo $sectionConfig; ?>";

  if(sectorWise=='Number'){
		for (i = 0; i < 100; i++) {
			var number =i+1;
			var opt ="<option value='"+number+"'>"+number+"</option>";
		  $("#section").append(opt);
		}
     }
  if(sectorWise=='Alphabet'){
		for (i = 0; i < 26; i++) {
			var number =i+1;
			var aplha =(i+10).toString(36);
			var opt ="<option value='"+aplha+"'>"+aplha+"</option>";
		  	$("#section").append(opt);
		}
     }
	if(sectorWise=='None'){
		  $("#section").val('');
     } 
	 
   $('.lMode').click(function(){
		$("#miniScorePass").val('');
		 //alert($(this).val());
		if ($(this).val()=='guided'){
		   $("#miniScorePassDiv").show();
		}else{
			$("#miniScorePassDiv").hide();
			$("#miniScorePass").val('0');
		} 
	 });	
	 
});



function checkBatchExistFn(id,errid,mode){
	$("#"+errid).text("");
	 $("#errorCenter").text("");	
	var centerId=$("#center_id option:selected" ).val();
	if(centerId==''){
	  $("#errorCenter").text("Please select <?php echo $center; ?>");	
	}else{
		var cValue=$("#"+id).val();
		cValue=cValue.trim();
		var dataString = {batch:cValue,centerId:centerId};
	  if(cValue!=''){
		$.ajax({
			type: "POST",
			url: "ajax/checkBatchExist.php",
			data: dataString,
			cache: false,
			success: function(result){
				console.log(result);
				if(result==1){
					$("#createBatch").addClass("disabled");
					if(mode=='Add'){
						$("#"+id).val(' ');
					}else{
						
					}
				   $("#"+errid).text("<?php echo $batch; ?> is already exist. Please try another.");
					cValue=cValue.trim();
				  // alertPopup("<?php echo $batch; ?> is already exist. Please try another.");
				   return false;
				}else{
					$("#createBatch").removeClass("disabled");
				}
				
			}
		});
	  }
	} 
}


var levelArr=[];
var moduleArr=[];
var chapterArr=[];

 
	function showProgramCourseList(){
		var bid ="<?php echo $_GET['bid']?>"; 
		var cid ="<?php echo $_GET['cid']?>"; 
		if(bid!='' && cid!=''){
			var center_id=cid;
		}else{
			var center_id=$("#center_id option:selected").val();
			if(center_id==''){
				alert("Please Select centre");
				return false;
			}
		}
		var len = $('#product_id option:selected').length;
		 var cValue = [];
		$("#product_id option:selected").each(function () {
			cValue.push($(this).attr("master"));
		})
	 if(len>0  && center_id!=''){
		   $.post("ajax/getCourseList.php", {action:'courseAction',bid:bid,cid:center_id,product_id: cValue,levelEditArr:levelArr,moduleEditArr:moduleArr,chapterEditArr:chapterArr,client_id:<?php echo $client_id ?>}).done(function(data){
				// Display the returned data in browser
				//console.log(data)
				$("#courseDiv").html(data);
				
			}); 
		} else{
					
		}	
	}
			

$(document).ready(function(){
	var bid ="<?php echo $_GET['bid']?>"; 
	var cid ="<?php echo $_GET['cid']?>"; 
	if(bid!='' && cid!=''){
	  showProgramCourseList();	
	}
 
});

// Validation for select level
function validationBatch(){
 $("#errorLevel").html("");
	if ($('input:checkbox').filter(':checked').length < 1){
        $("#errorLevel").html("Select at least one level/module/lesson ");
	   return false;
	 }

}


</script>