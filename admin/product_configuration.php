<?php 
include_once('../header/adminHeader.php');
$adminObj = new centerAdminController();
$assessmentObj = new assessmentController();

$clientUserId=$assessmentObj->getSuperClientId($user_group_id );
$course_arr=$assessmentObj->getCourseByClientId($clientUserId); 

//echo "<pre>";print_r($_SESSION['user_group_id']);exit;	
$courseType='0';
$courseArr = $adminObj->getCourseListByLevel($courseType,$clientUserId);
//echo "<pre>";print_r($courseArr);//exit;
$col  = 'level_text';
$sort = array();
foreach ($courseArr as $i => $obj) {
	  $sort[$i] = $obj->{$col};
	}
array_multisort($sort, SORT_ASC, $courseArr);
//echo "<pre>";print_r($getRange);
$enableRange=count($courseArr);
//echo "<pre>";print_r($courseArr);
$courseRangeArr=array();
foreach($courseArr as $key=>$val){
  $courseRangeArr[$val->level_text]=$val->course_id;;
}
//echo "<pre>";print_r($course_arr);exit;  
$centerDetail=$adminObj->getCenterDetails();
$centerName=$centerDetail[0]['name'];
$center_id=$centerDetail[0]['center_id'];
//$battery_arr =$commonObj->getProduct();

/* Show edit batch */
if(isset($_GET['bid']) && !empty($_GET['bid']) || isset($_GET['cid']) && !empty($_GET['cid'])){
  $bId = trim( base64_decode($_GET['bid']) );
  $cId = trim( base64_decode($_GET['cid']) );
  $batchData = $centerObj->getBatchDataByID($bId,$cId); 
  $batchId=$batchData[0]['batch_id'];  
  $batchCenterId=$batchData[0]['center_id'];
  $arrAssignCourse= $centerObj->getBatchCourseMapList($batchId,$cId);
 
  //echo "<pre>";print_r($batchData);exit;
}

if(!empty($_GET['bid'])){
	//  $countClass ="displayNone";
	  $regClass ="";
	  $errDiv = "";
	  $pageType ="Edit";
	  $disabled='disabledInput';
  }else{
	  $pageType ="Add";
      //$countClass ="";
	  $disabled="";
	 }
$codeArr=array();
 foreach($courseRangeArr as $key=>$val){
  //$code='CRS-'.$val;
    $code=$val;
  $codeArr[]=$code;
 }
 $codelevel=implode(',',$codeArr);
 //print_r($codelevel);exit;	 
	 
	 
?>
<style>

  .panel-heading a:after {    width: 2%;
    font-family:'FontAwesome';
    content:"\f106";
    float: right;
    color: grey;
	font-size:14px;font-weight:700;position:absolute;    right: 0px;
}
 
.panel-heading a.collapsed:after {
    content:"\f107";
}

</style>
<ul class="breadcrumb no-border no-radius b-b b-light">
 <li> <a href="batchList.php"><i class="fa fa-arrow-left"></i> Configurtion<?php //echo $batch; ?> </a></li>
</ul>
<div class="clear"></div>
 <section class="padder">
   <div class="row-centered">
	<div class="col-sm-10 col-xs-12 col-centered">
     <section class="marginBottom40">
      <form action="ajax/batchTopicChapterSubmit.php" id="mapBatchForm" class="createbatch" method="post"  data-validate="parsley"  autocomplete="off" >
	  <div class="row">
       <div class="panel panel-default bdrNone">
        <div class="panel-body padd20">
		<h3 class="panel-header">Manage Tasks</h3>

			<?php  if(count($courseRangeArr ) > 0 && !empty($courseRangeArr)){?>  
			   <div class="col-sm-6"> 
		    <label class="control-label">Select <?php echo $test;?> <span class="required">&nbsp;</span></label>
					<div class="clear"></div>
					<select id="course" name="course[]" class="form-control mdb-select colorful-select dropdown-primary md-form" searchable="Search here.."  onchange="showModule(this);" multiple>
					<!--<option value="<?php echo $codelevel;?>" selected><?php //echo $test.' 1 to 10';?></option>-->
					  <?php  
					    $codeArr=array();
					    foreach($courseRangeArr as $key=>$val){
						  //$code='CRS-'.$val;
						   $code=$val;
					      $codeArr[]=$code;
					  
					?>
					<option value="<?php echo $code;?>" <?php echo $selected;?>><?php echo $test.' - '.$key;?></option>
					  <?php    }?>
					</select>
					<div class="col-sm-12"><label class="required showErr" id="testError"></label></div>
		  </div> 
		    <?php }?>
		     <div class="col-sm-6"> 
		    <label class="control-label">Select <?php echo TOPIC_NAME;?> <span class="required">&nbsp;</span></label>
					<div class="clear"></div>
					<select id="topic" name="topic[]" class="form-control mdb-select colorful-select dropdown-primary md-form" searchable="Search here.."   onchange="showChapter(this.id);" multiple>
					</select>
		  </div>   <div class="clear"></div>
		  <div class="col-sm-12" id="chapterShow"> 
		  </div>
		     <div class="clear"></div>
			 
		<div class="panel-group" id="accordion">
			 <?php  if(count($courseRangeArr ) > 0 && !empty($courseRangeArr)){?>  
			 <?php  
				 $codeArr=array();
				foreach($courseRangeArr as $key=>$val1){
						  //$code='CRS-'.$val;
					$code=$val1;
					$codeArr[]=$code;
					$selected=($valSelected == $code) ? "checked" : "checked";
					
					?>  
		<div class="panel panel-default parent">
      <div class="panel-heading" style="padding: 0px;height: 22px;position: relative;"> <div class="col-md-1 col-sm-1 displayInline"><input type="checkbox" name="level[]"  <?php echo $selected;?> value="<?php echo $code;?>" id="chklvl<?php echo $code;?>" onchange="checkAll('chklvl<?php echo $code;?>','level<?php echo $code;?>');"/>  </div> <a data-toggle="collapse" data-parent="#accordion" href="#level<?php echo $code;?>" onclick="" class="collapsed">
      <div class="col-md-11 col-sm-11 displayInline"> 
   <?php echo $test.' - '.$key;?> </div>
      </a>
      </div>
      <div id="level<?php echo $code;?>" class="panel-collapse collapse" >
	  <div class="panel-body">
	  <div class="col-sm-12">
	  <div class="panel-group" id="accordion1">
	  <div class="panel panel-default">
        <?php  
		$topic_arr = $assessmentObj->getTopicOrAssessmentByCourseId($code);
		  //echo "<pre>";print_r($topic_arr);
		 if(count($topic_arr)>0){

			 foreach($topic_arr  as $key => $value){
					
					$tree_node_id = $value->tree_node_id;
					
					$name = $value->name;
					$edge_id = $value->edge_id;
			        $assessment_type = $value->assessment_type;
					$is_survey = $value->is_survey;
					$topic_type = $value->topic_type;
					$optionSelected = ($valSelected == $edge_id) ? "checked" : "checked";
					?>
			
				 <div class="panel-heading" style="padding: 0px;height: 22px;margin-bottom:5px;position: relative;"> <div class="col-md-1 col-sm-1 displayInline"><input type="checkbox" name="module[]" <?php echo $optionSelected;?> value="<?php echo $edge_id;?>" tree_node_id="<?php echo $tree_node_id;?>" id="chktpc<?php echo $edge_id;?>" onchange="checkAll('chktpc<?php echo $edge_id;?>','topic<?php echo $edge_id;?>');"/></div> <a data-toggle="collapse" data-parent="#accordion1" href="#topic<?php echo $edge_id;?>" class="collapsed">
			   <div class="col-md-11 col-sm-11 displayInline"><?php echo $name;?></div>
				  </a> 
				</div>
				  <div id="topic<?php echo $edge_id;?>" class="panel-collapse collapse" >
				  <div class="panel-body">
					<?php $chapter_arr = $assessmentObj->getChapterByTopicEdgeId($edge_id);
					 //echo "<pre>";print_r($chapter_arr);exit;
					 if(count($chapter_arr)>0){

					foreach($chapter_arr  as $key => $value1){
					
					$ch_tree_node_id = $value1->tree_node_id;
					
					$chname = $value1->name;
					$chdescription = $value1->description;
					$chthumnailImg = $value1->thumnailImg;
					$chskill = $value1->chapterSkill;
					$ch_edge_id = $value1->edge_id;
			
                    $optionSelected1 = ($valSelected == $ch_edge_id) ? "checked" : "checked";
                 ?>
					<div class="col-sm-6"><div class="chBox skill<?php echo $chskill;?>" tree_node_id="<?php echo $ch_tree_node_id;?>"  skill="<?php echo $chskill;?>">
					<div class="col-md-1 col-sm-1 displayInline"><input type="checkbox" name="chapter[]" <?php echo $optionSelected1;?> value="<?php echo $ch_edge_id;?>" id="chkchp<?php echo $ch_edge_id;?>"/></div><div class="col-md-10 col-sm-10 displayInline">
					<div class="chBoxDiv"><div class="title"><?php echo $chname;?></div><div class="description"><?php echo $chdescription;
					?></div></div><div class="chthumbnail pull-right skill<?php echo $chskill;?>"><img src="<?php echo $thumnail_Img_url.$chthumnailImg;
					?>"/></div></div></div></div>
						
			<?php }
		 }else{?>
			<div class="col-sm-12">Not Available</div>
		<?php }?>
	     
				  </div>	
				  </div>		
		<?php 	}
			 }else{
				echo 'Not Available';
			}
		?>  </div> </div></div></div>
      </div>
	</div><?php  }?> 
	<?php }?> </div>
		  
		 </div>
	    </div>
	   </div>
	   <div class="clear"></div>
		   <div class="text-right"> 
			<a href='product_configuration.php' class="btn btn-primary ">Cancel</a>&nbsp;&nbsp;
			 <input id="centerIdVal" type="hidden" name="centerIdVal" value="<?php echo $cId; ?>"/>
			 <input id="batchIdVal" type="hidden" name="batchIdVal" value="<?php echo $bId; ?>"/>
			<button type="submit" class="btn btn-s-md btn-primary" name="mapBatch" id="mapBatch">Submit</button>
	    </div>
     </form>
   </section> 
  </div>
 </div>
</section>
<?php include_once('../footer/adminFooter.php');?>
<script>
var centerId='<?php echo $cId; ?>';
$(document).ready(function(){
	
})
function checkAll(cid,lid) {
	
    if ($("#"+cid).is(':checked')) {
		//alert("checked") 
		$("#"+cid).prop('checked', true);
        $('#'+lid+' input').prop('checked', true);
    }else {
         $("#"+cid).prop('checked', false);
		//alert("unchecked")
        $('#'+lid+' input').prop('checked', false);
    }
}
	

function showModule(e){
	 $("#testError").text("");	
	 if( $('#course option:selected').length > 0){
        //build an array of selected values
        var selectednumbers = [];
        $('#course :selected').each(function(i, selected) {
            selectednumbers[i] = $(selected).val();
        });
	
		var dataString =  {action: "topic_show",'course':JSON.stringify(selectednumbers)};
		$.ajax({
			
			type: "POST",
			url: "ajax/showTopicByCourse.php",
			data: dataString,
			cache: false,
			success: function(result){
				//console.log(result);
				$("#topic").html(result);
	
			}
		});
	} 
}

function showChapter(cid){
	 $("#testError").text("");	
	 if( $('#topic option:selected').length > 0){
        //build an array of selected values
        var selectednumbers = [];
        $('#topic :selected').each(function(i, selected) {
            selectednumbers[i] = $(selected).val();
        });
	
		var dataString =  {action: "chapter_show",'topic':JSON.stringify(selectednumbers)};
		$.ajax({
			type: "POST",
			url: "ajax/showTopicByCourse.php",
			data: dataString,
			cache: false,
			success: function(result){
				//console.log(result);
				$("#chapterShow").html(result);
	
			}
		});
	} 
}
</script>
