<?php
include_once('../../header/lib.php');
include_once('../../header/global_config.php');
$centerObj = new centerController(); 
$adminObj = new centerAdminController();
$assessmentObj = new assessmentController();
$productObj = new productController();


$productId=$_REQUEST['product_id'];
$client_id=$_REQUEST['client_id'];		
$bid=$_REQUEST['bid'];
$cid=$_REQUEST['cid'];	
$clientUserId='';	
$levelEditArr1=$_REQUEST['levelEditArr'];
$productArr=array();
if(isset($productId) && $productId!=''){
    $product_standard_id = implode(",", $productId);
	foreach($productId as $key=>$val){
		 $productArrData=$productObj->getProdcutIdByMasterId($client_id,$val);  
		 $productArr[]=$productArrData['id'];
	}
	$courseType='';
	$courseArr = $adminObj->getCourseListByLevel($courseType,$clientUserId,$product_standard_id);
 //echo "<pre>";print_r($courseArr);exit;
$col  = 'product_id';
$sort = array();
foreach ($courseArr as $i => $obj) {
	  $sort[$i] = $obj->{$col};
	}
array_multisort($sort, SORT_ASC, $courseArr);
  foreach ($courseArr as $value) {
	  $course_type = $value->course_type;
	  if($course_type==1){
		$courseArrILT[]=$value;  
	  }else{
		$courseArrWBT[]=$value;
	  }
	}

$enableRange=count($courseArrWBT);
//echo "<pre>";print_r($courseArr);
$courseRangeArrWBT=array();
$courseNameArrWBT=array();
$courseProductArrWBT=array();
foreach($courseArrWBT as $key=>$val){
  $courseNameArrWBT[$key]=$val->name;
  $courseRangeArrWBT[$key]=$val->course_id;
  $productArr1=$productObj->getProdcutIdByMasterId($client_id,$val->product_id);
  $courseProductArrWBT[$key]=$productArr1['id'];
}
$courseProductArrWBT1=array_unique($courseProductArrWBT);
//echo "<pre>";print_r($courseProductArrWBT);
$enableRange=count($courseArrILT);
//echo "<pre>";print_r($courseArr);
$courseRangeArrILT=array();
$courseNameArrILT=array();
$courseProductArrILT=array();

foreach($courseArrILT as $key=>$val){
  $courseNameArrILT[$key]=$val->name;
  $courseRangeArrILT[$key]=$val->course_id;
  $productArr2=$productObj->getProdcutIdByMasterId($client_id,$val->product_id);
  $courseProductArrILT[$key]=$productArr2['id'];
}
$courseProductArrILT1=array_unique($courseProductArrILT);
//echo "<pre>";print_r($courseProductArrILT); 
if(isset($bid) && !empty($bid) || isset($cid) && !empty($cid)){
  $bId = base64_decode($bid);
  $cId = base64_decode($cid);
  $product_id = implode(",", $productArr);
  $batchDataDetails = $centerObj->getBatchDataByIDDetails($bId,$cId,$product_id);
  
$batchProductArr1 = array();
$batchCourseArr1 = array(); 
$batchTopicArr1 = array();  
$batchChapterArr1 = array();
  foreach ($batchDataDetails  as $key => $value) {
	$batchProductArr1[] = $value['product_id'];
   	$batchCourseArr1[] = $value['course'];
	$batchTopicArr1[] = $value['topic'];
	$batchChapterArr1[] = $value['chapter'];
   }
$batchProductArr = array();    
$batchCourseArr = array(); 
$batchTopicArr = array();  
$batchChapterArr = array();
    foreach ($batchProductArr1  as $key => $value) {
   	 $batchProductArr[$key] = $value;
    } 
    foreach ($batchCourseArr1  as $key => $value) {
   	 $batchCourseArr[$key] = explode(',', $value);
    } 
   foreach ($batchTopicArr1  as $key => $value) {
	$batchTopicArr[$key] = explode(',', $value);
   }
   foreach ($batchChapterArr1  as $key => $value) {
	$batchChapterArr[$key] = explode(',', $value);
   }
   
 
   if(count($batchProductArr)>1){
	    $courseCount=count($batchCourseArr);
		$topicCount=count($batchTopicArr);
		$chapterCount=count($batchChapterArr); 
  // echo "<pre>xdgfh";print_r($batchCourseArr);//exit; 
	 $batchCourseArr2=array(); 
	 $batchTopicArr2=array();  
	 $batchChapterArr2=array();   
	 foreach ($batchProductArr  as $key => $value) {
		 $batchCourseArr2 = array_merge($batchCourseArr2,$batchCourseArr[$key]);
		 $batchTopicArr2 = array_merge($batchTopicArr2,$batchTopicArr[$key]);
		 $batchChapterArr2 = array_merge($batchChapterArr2,$batchChapterArr[$key]);
	  } 
	 // echo "<pre>if";print_r($batchCourseArr2);//exit;  
	   $checkedlevel1 = ($courseCount>1) ? $batchCourseArr2:$batchCourseArr[0];
       $checkedtopic1 = ($topicCount>1) ? $batchTopicArr2:$batchTopicArr[0];
       $checkedchapter1 = ($chapterCount>1)? $batchChapterArr2:$batchChapterArr[0];
    // echo "<pre>if";print_r($checkedlevel1);//exit;  
   }else{
	  // echo "<pre>else";print_r($batchProductArr);//exit;  
	  $checkedlevel1 = $batchCourseArr[0];
      $checkedtopic1 = $batchTopicArr[0];
      $checkedchapter1 = $batchChapterArr[0];  
   }

}
?>
<h3 class="panel-header"><?php echo $language[$_SESSION['language']]['product_configuration'];?></h3>
		 <div class="col-sm-12">
		 <label class="required showErr" id="errorLevel"></label></div>
		
 <div class="col-sm-6">
		   <h6>ILT Course</h6>	<div class="clear"></div>
		  
		     <div class="panel-group" id="accordion">
			 <?php  if(count($courseRangeArrILT ) > 0 && !empty($courseRangeArrILT)){?>  
			 	<input type="hidden" name="levc" value="<?php echo count($courseRangeArrILT); ?>" />
			 <?php  
				 $codeArr=array();
				 $modc = $chapc = 0;
				foreach($courseRangeArrILT as $key=>$val1){
					$code1='CRS-'.$val1;
					$courseKey=$key;
					$code=$val1;
					$codeArr[]=$code;
					if(isset($bid) && !empty($bid) || isset($cid) && !empty($cid)){
						
						$selected=(is_array($checkedlevel1) && in_array($code1, $checkedlevel1))  ? "checked" : ( $checkedlevel1 == 0 ? "checked":"");
					}
					else{$selected = "checked";}
					?>  
		<div class="panel panel-default parent">
      <div class="panel-heading" style="background-color: #f5f5f5;padding: 0px;height: 22px;position: relative;"> <div class="col-md-1 col-sm-1 displayInline">
      	<?php  	$topic_arr = $assessmentObj->getTopicOrAssessmentByCourseId($code,$customTopic=null);  $modc += count($topic_arr);?>
      	<input type="checkbox" total-child="<?php echo count($topic_arr)?>" name="level<?php echo $courseProductArrILT[$courseKey];?>[]"  <?php echo $selected;?> value="<?php echo $code1;?>" id="chklvl<?php echo $code;?>" onchange="checkAll('chklvl<?php echo $code;?>','level<?php echo $code;?>','','level');"/>  </div> <a  title="<?php echo $language[$_SESSION['language']]['mandatory_modules'];?>" data-toggle="collapse" data-parent="#accordion" href="#level<?php echo $code;?>" open="true" onclick="" class="collapsed">
      <div class="col-md-11 col-sm-11 displayInline"> 
   <?php echo $courseNameArrILT[$key];//$test.' - '.$key;?> </div>
      </a>
      </div>
      <div id="level<?php echo $code;?>" class="panel-collapse collapse" >
	  <div class="panel-body">
	  <div class="col-sm-12">
	  	<div class="clear"></div>
	  <div class="panel-group" id="accordion1">
	  <div class="panel panel-default">
        
		  <?php //echo "<pre>";print_r($topic_arr);
		 if(count($topic_arr)>0){

			 foreach($topic_arr  as $key => $value){
					
					$tree_node_id = $value->tree_node_id;
					
					$name = $value->name;
					$edge_id = $value->edge_id;
			        $assessment_type = $value->assessment_type;
					$is_survey = $value->is_survey;
					$topic_type = $value->topic_type;
					if(isset($bid) && !empty($bid) || isset($cid) && !empty($cid)){
					
						$optionSelected=(is_array($checkedtopic1) && in_array($edge_id, $checkedtopic1))  ? "checked" : ( $checkedtopic == 0 ? "checked":"");
						
					//$optionSelected=(in_array($code, $checkedtopic1)) ? "checked" : "";
				    }else{
				    	$optionSelected = "checked";
						
				    }
					/* $optionSelected = ($valSelected == $edge_id) ? "checked" : "checked";*/
					?>
				<div class="col-md-12 col-sm-12 displayInline padd0">
				 <div class="panel-heading " style="background-color: #f5f5f5;padding: 0px;height: 22px;margin-bottom:5px;position: relative;"> <div class="col-md-1 col-sm-1 displayInline">
				 	<?php $chapter_arr = $assessmentObj->getChapterByTopicEdgeId($edge_id,$customeChapter=null); $chapc += count($chapter_arr); ?>
				 	<input type="checkbox" total-child="<?php echo count($chapter_arr)?>" name="module<?php echo $courseProductArrILT[$courseKey];?>[]" <?php echo $optionSelected;?> value="<?php echo $edge_id;?>" tree_node_id="<?php echo $tree_node_id;?>" id="chktpc<?php echo $edge_id;?>" onchange="checkAll('chktpc<?php echo $edge_id;?>','topic<?php echo $edge_id;?>', 'chklvl<?php echo $code;?>','topic');" class="chklvl<?php echo $code;?>"/>
				 </div> <a data-toggle="collapse" data-parent="#accordion1" href="#topic<?php echo $edge_id;?>" class="collapsed">
					<div class="col-md-10 col-sm-10 displayInline"><?php echo $name;?></div>
				  </a>  
				</div>
				  <div id="topic<?php echo $edge_id;?>" class="panel-collapse collapse" >
				  <div class="panel-body">
					
					 <?php //echo "<pre>";print_r($chapter_arr);exit;
					if(count($chapter_arr)>0){

						foreach($chapter_arr  as $key => $value1){
						
						$ch_tree_node_id = $value1->tree_node_id;
						
						$chname = $value1->name;
						$chdescription = $value1->description;
						$chthumnailImg = $value1->thumnailImg;
						$chskill = $value1->chapterSkill;
						$ch_edge_id = $value1->edge_id;
						
						if(isset($bid) && !empty($bid) || isset($cid) && !empty($cid)){
						
							$optionSelected1=(is_array($checkedchapter1) && in_array($ch_edge_id, $checkedchapter1))  ? "checked" : ( $checkedchapter1 == 0 ? "checked":"");	
							//$optionSelected1=(in_array($code, $checkedchapter1)) ? "checked" : "";
						}else{
							$optionSelected1 = "checked";
						}
                    /* $optionSelected1 = ($valSelected == $ch_edge_id) ? "checked" : "checked";*/
                 ?>
					<div class="col-sm-6"><div class="chBox skill<?php echo $chskill;?>" tree_node_id="<?php echo $ch_tree_node_id;?>"  skill="<?php echo $chskill;?>">
					<div class="col-md-1 col-sm-1 displayInline"><input onchange="checkAll('chkchp<?php echo $ch_edge_id;?>', 'chapter<?php echo $ch_edge_id;?>', 'chktpc<?php echo $edge_id;?>','chapter')" type="checkbox" name="chapter<?php echo $courseProductArrILT[$courseKey];?>[]" class="chktpc<?php echo $edge_id;?>" <?php echo $optionSelected1;?> value="<?php echo $ch_edge_id;?>" id="chkchp<?php echo $ch_edge_id;?>"/></div><div class="col-md-10 col-sm-10 displayInline">
					<div class="chBoxDiv"><div class="chthumbnail pull-left skill<?php echo $chskill;?>"><img src="<?php echo $thumnail_Img_url.$chthumnailImg;
					?>"/></div><div class="title"><?php echo $chname;?></div><div class="description"><?php echo $chdescription;
					?></div></div></div></div></div>
						
				<?php }
			 }else{?>
				<div class="col-sm-12">Not Available</div>
			<?php }?>
			 
					  </div>	
					</div>		
			    </div>
			
				
			<?php 	}
				 }else{
					echo 'Not Available';
				}
			?>  </div> </div></div></div>
		  </div>
		</div><?php  }?>
		
		<input type="hidden" name="modc" value="<?php echo $modc; ?>" />
		<input type="hidden" name="chapc" value="<?php echo $chapc; ?>" />
		
		<?php }?> </div>
			  
		  </div>
		
		  <div class="col-sm-6">   <h6>WBT Course</h6>	<div class="clear"></div>
		  
		     <div class="panel-group" id="accordion10">
			 <?php  if(count($courseRangeArrWBT ) > 0 && !empty($courseRangeArrWBT)){?>  
			 	<input type="hidden" name="levc" value="<?php echo count($courseRangeArrWBT); ?>" />
			 <?php  
				 $codeArr=array();
				 $modc = $chapc = 0;
				foreach($courseRangeArrWBT as $key=>$val2){
					$code1='CRS-'.$val2;
					$courseKey=$key;	  
					$code=$val2;
					$codeArr[]=$code;
					
					if(isset($bid) && !empty($bid) || isset($cid) && !empty($cid)){
						
						$selected=(is_array($checkedlevel1) && in_array($code1, $checkedlevel1))  ? "checked" : ( $checkedlevel1 == 0 ? "checked":"");
					}
					else{$selected = "checked";}
					?>  
		<div class="panel panel-default parent">
      <div class="panel-heading" style="background-color: #f5f5f5;padding: 0px;height: 22px;position: relative;"> <div class="col-md-1 col-sm-1 displayInline">
      	<?php  	$topic_arr = $assessmentObj->getTopicOrAssessmentByCourseId($code,$customTopic=null);  $modc += count($topic_arr);?>
      	<input type="checkbox" total-child="<?php echo count($topic_arr)?>" name="level<?php echo $courseProductArrWBT[$courseKey];?>[]"  <?php echo $selected;?> value="<?php echo $code1;?>" id="chklvl<?php echo $code;?>" onchange="checkAll('chklvl<?php echo $code;?>','level<?php echo $code;?>','','level');"/>  </div> <a  title="<?php echo $language[$_SESSION['language']]['mandatory_modules'];?>" data-toggle="collapse" data-parent="#accordion" href="#level<?php echo $code;?>" open="true" onclick="" class="collapsed">
      <div class="col-md-11 col-sm-11 displayInline"> 
   <?php echo $courseNameArrWBT[$key];//$test.' - '.$key;?> </div>
      </a>
      </div>
      <div id="level<?php echo $code;?>" class="panel-collapse collapse" >
	  <div class="panel-body">
	  <div class="col-sm-12">
	  	<div class="clear"></div>
	  <div class="panel-group" id="accordion11">
	  <div class="panel panel-default">
        
		  <?php //echo "<pre>";print_r($topic_arr);
		 if(count($topic_arr)>0){

			 foreach($topic_arr  as $key => $value){
					
					$tree_node_id = $value->tree_node_id;
					
					$name = $value->name;
					$edge_id = $value->edge_id;
			        $assessment_type = $value->assessment_type;
					$is_survey = $value->is_survey;
					$topic_type = $value->topic_type;
					if(isset($bid) && !empty($bid) || isset($cid) && !empty($cid)){
						
					
						$optionSelected=(is_array($checkedtopic1) && in_array($edge_id, $checkedtopic1))  ? "checked" : ( $checkedtopic == 0 ? "checked":"");
						
					//$optionSelected=(in_array($code, $checkedtopic1)) ? "checked" : "";
				    }else{
				    	$optionSelected = "checked";
						
				    }
					/* $optionSelected = ($valSelected == $edge_id) ? "checked" : "checked";*/
					?>
				<div class="col-md-12 col-sm-12 displayInline padd0">
				 <div class="panel-heading " style="background-color: #f5f5f5;padding: 0px;height: 22px;margin-bottom:5px;position: relative;"> <div class="col-md-1 col-sm-1 displayInline">
				 	<?php $chapter_arr = $assessmentObj->getChapterByTopicEdgeId($edge_id,$customeChapter=null); $chapc += count($chapter_arr); ?>
				 	<input type="checkbox" total-child="<?php echo count($chapter_arr)?>" name="module<?php echo $courseProductArrWBT[$courseKey];?>[]" <?php echo $optionSelected;?> value="<?php echo $edge_id;?>" tree_node_id="<?php echo $tree_node_id;?>" id="chktpc<?php echo $edge_id;?>" onchange="checkAll('chktpc<?php echo $edge_id;?>','topic<?php echo $edge_id;?>', 'chklvl<?php echo $code;?>','topic');" class="chklvl<?php echo $code;?>"/>
				 </div> <a data-toggle="collapse" data-parent="#accordion1" href="#topic<?php echo $edge_id;?>" class="collapsed">
					<div class="col-md-10 col-sm-10 displayInline"><?php echo $name;?></div>
				  </a>  
				</div>
				  <div id="topic<?php echo $edge_id;?>" class="panel-collapse collapse" >
				  <div class="panel-body">
					
					 <?php //echo "<pre>";print_r($chapter_arr);exit;
					if(count($chapter_arr)>0){

						foreach($chapter_arr  as $key => $value1){
						
						$ch_tree_node_id = $value1->tree_node_id;
						
						$chname = $value1->name;
						$chdescription = $value1->description;
						$chthumnailImg = $value1->thumnailImg;
						$chskill = $value1->chapterSkill;
						$ch_edge_id = $value1->edge_id;
						
						if(isset($bid) && !empty($bid) || isset($cid) && !empty($cid)){
						
						
							$optionSelected1=(is_array($checkedchapter1) && in_array($ch_edge_id, $checkedchapter1))  ? "checked" : ( $checkedchapter1 == 0 ? "checked":"");	
							//$optionSelected1=(in_array($code, $checkedchapter1)) ? "checked" : "";
						}else{
							$optionSelected1 = "checked";
						}
                    /* $optionSelected1 = ($valSelected == $ch_edge_id) ? "checked" : "checked";*/
                 ?>
					<div class="col-sm-6"><div class="chBox skill<?php echo $chskill;?>" tree_node_id="<?php echo $ch_tree_node_id;?>"  skill="<?php echo $chskill;?>">
					<div class="col-md-1 col-sm-1 displayInline"><input onchange="checkAll('chkchp<?php echo $ch_edge_id;?>', 'chapter<?php echo $ch_edge_id;?>', 'chktpc<?php echo $edge_id;?>','chapter')" type="checkbox" name="chapter<?php echo $courseProductArrWBT[$courseKey];?>[]" class="chktpc<?php echo $edge_id;?>" <?php echo $optionSelected1;?> value="<?php echo $ch_edge_id;?>" id="chkchp<?php echo $ch_edge_id;?>"/></div><div class="col-md-10 col-sm-10 displayInline">
					<div class="chBoxDiv"><div class="chthumbnail pull-left skill<?php echo $chskill;?>"><img src="<?php echo $thumnail_Img_url.$chthumnailImg;
					?>"/></div><div class="title"><?php echo $chname;?></div><div class="description"><?php echo $chdescription;
					?></div></div></div></div></div>
						
				<?php }
			 }else{?>
				<div class="col-sm-12">Not Available</div>
			<?php }?>
			 
					  </div>	
					</div>		
			    </div>
			
	
			<?php 	}
				 }else{
					echo 'Not Available';
				}
			?>  </div> </div></div></div>
		  </div>
		</div><?php  }?>
		
		<input type="hidden" name="modc" value="<?php echo $modc; ?>" />
		<input type="hidden" name="chapc" value="<?php echo $chapc; ?>" />
		
		<?php }?> </div>
			  
		  </div>
				
		
<?php }?>
<script>

function checkAll(cid,lid, parent_node,type) {
		let val= $("#"+cid).val();
		let total_child_uncheckd = 0;
		let total_children = parseInt($('#'+parent_node).attr('total-child'));
		if ($("#"+cid).is(':checked')) {
			//alert("checked") 
			$("#"+cid).prop('checked', true);
			$('#'+lid+' input').prop('checked', true);
			
		}else {
			
			 $("#"+cid).prop('checked', false);
			
			//alert("unchecked")
			$('#'+lid+' input').prop('checked', false);
		}
		if(parent_node!=''){
			$('.'+parent_node).each(function(index, value){
				
				console.log(!$(value).prop('checked'));
				if(!$(value).prop('checked')){
					total_child_uncheckd++;
				}	
			});
		}

		if(total_child_uncheckd == total_children){
			$("#"+parent_node).prop('checked', false);
			
		} else {
			$("#"+parent_node).prop('checked', true);
		}


	}
</script>