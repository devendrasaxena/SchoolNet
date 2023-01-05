<?php include_once('../header/trainerHeader.php');
include_once '../controller/productController.php';
$objTR = new trackController();
$proObj = new productController();

$batchId=$_SESSION['batch_id'];
$productId=$_SESSION['product_id'];
$getCourseProductData=$centerObj->getBatchDataByIDDetails($batchId,$center_id,$productId);
$productInfo=$proObj->getProdcutDetailById($productId);
//echo "<pre>";print_r($getCourseProductData); exit; 
$product_name=$productInfo['product_name'];
$master_products_id= $productInfo['master_products_ids'];

$batchCourseStr1 = $getCourseProductData[0]['course'];
$customTopic = $getCourseProductData[0]['topic'];
$customChapter = $getCourseProductData[0]['chapter'];
//$batchCourseArr=explode(',', $batchCourseStr);
$batchCourseStr= str_replace("CRS-","",$batchCourseStr1);
//echo "<pre>";print_r($customTopic );
$clientUserId=$assessmentObj->getSuperClientId($user_group_id);	
$courseType='1';
$courseArr = $adminObj->getCustomCourseList($courseType,$batchCourseStr,$master_products_id);
//$courseArr = $adminObj->getCourseListByLevel($courseType,$clientUserId,$product_standard_id);
//echo "<pre>";print_r($courseArr);
$enableRange=count($courseArr);
$col  = 'sequence_id';
$sort = array();
foreach ($courseArr as $i => $obj) {
	  $sort[$i] = $obj->{$col};
	}
array_multisort($sort, SORT_ASC, $courseArr);
//echo "<pre>";print_r($courseArr);
 if($product_standard_id==10){
	$COURSE_NAME="Class";
	$COURSE_NAMES="Classes";
 }else{
   $COURSE_NAME="Level";
   $COURSE_NAMES="Levels"; 
 }
$courseRangeArr=array();
$courseLevelArr=array();
$courseNameArr=array();
$levelTotalRang=count($courseRangeArr); 
foreach($courseArr as $key=>$val){
   $courseNameArr[$key+1]=$val->name;

   $level_text=str_replace($COURSE_NAME," ",$val->level_text);
 
   //echo "<pre>";print_r($level_text);
   $courseLevelArr[$key+1]=trim($level_text);//$val->level_text;
  //$courseLevelArr[$key]=$val->level_text;
  $courseRangeArr[$key+1]=$val->course_id;
}

if( isset($_REQUEST['cid']) ){
    // $course_id = trim($_GET['cid']);
	$course_id = trim( base64_decode($_GET['cid']) );
}
if( isset($_REQUEST['lid']) ){
   //$levelId = trim($_GET['lid']);
	$levelId = trim( base64_decode($_GET['lid']) );
	$level=$COURSE_NAME." ".$levelId;
}


if(isset($_GET['cid'])!='' && isset($_GET['lid'])!='' ){
   $topicArr=$assessmentObj->getTopicOrAssessmentByCourseId($course_id ,$customTopic=null);
    $col  = 'sequence_id';
	$sort = array();
	foreach ($topicArr as $i => $obj) {
			  $sort[$i] = $obj->{$col};
	}
	array_multisort($sort, SORT_ASC, $topicArr);

   $totalTopic=count($topicArr); 
   $levelId = trim( base64_decode($_GET['lid']) );
   $visitLevel= trim( base64_decode($_GET['lid']) );
   /* $arrVisit1 = array();
   $arrVisit1['token'] = $userToken;
   $arrVisit1['visiting_level'] = $levelId;
   $visitLevelArr =  $objTR->setVisitLevel($arrVisit1);  */

}else{
	
	if($courseRangeArr!=''){

	   $arrVisit = array();
	   $arrVisit['token'] = $userToken;
	   $arrVisit['product_id'] = $productId;
	   $arrVisit['course_id'] = $course_id;
	   $getVisitLevelArr =  $objTR->getVisitLevel($arrVisit);
	   
		  
		 if($getVisitLevelArr==0){
		    $course_id=$courseRangeArr[$getRange];
			$visitLevel=$getRange;//current
			
		 } else{
			if(array_key_exists($getVisitLevelArr,$courseRangeArr)){
			  //echo "Key exists!";
				$course_id=$courseRangeArr[$getVisitLevelArr]; 
				$visitLevel=$getVisitLevelArr;
			  }else{
			   //echo "Key does not exist!";
				$course_id=$courseRangeArr[$getRange];
				$visitLevel=$getRange;//current  
			  }
			
	    } 
		//$course_id=$courseRangeArr[$getRange];
		//$visitLevel=$getRange;//current
	  $topicArr=$assessmentObj->getTopicOrAssessmentByCourseId($course_id,$customTopic);
	  $totalTopic=count($topicArr);	
	  //$level=$COURSE_NAME." ".$getRange;
	  //$levelId=$getRange;
	 //echo "<pre>";print_r($topicArr);	
	} else{
		
		
	} 
}//echo "<pre>";print_r($courseRangeArr);	
//echo $levelId;//exit;	
//echo $getRange;exit;	
//$totalTopic=0;
$course_code="CRS-".$course_id; 
?>
  <section class="scrollable padderNone">
	<div class="moduleHeader relative">
	
	<div class="levelDiv"  id="levelDiv"> 
		  <div class="levels" style="padding-top:5px;"><span><?php echo $product_name;//$COURSE_NAMES ?></span></div>
		   <div class="levelsRange">
			<ul>
			<?php 
			$i=1;
		   // echo "<pre>";print_r($courseRangeArr);
			foreach($courseRangeArr as $key=>$levelValue){
				 if($courseLevelArr[$key]<10){
				   $courseCount="0".$courseLevelArr[$key];
				   $crsCount=$key;
				  }else{
					 $courseCount=$courseLevelArr[$key];
					 $crsCount=$key;				 
				  } 
				   $courseName=$courseNameArr[$key];
				 $link=$levelValue;
			    if($getRange==$key){
					$active="active"; 
				 }else{
					$active="";  
				 }
				if($key==$visitLevel){
					$activeShow="show";			
				}else{
				  $activeShow="";
				 
				}	
				 
			 ?>
			  <li id="lpath<?php echo $i;?>" path="<?php echo $lpath; ?>" class="<?php echo $hideColor." ".$activeShow.' '.$active;?>" link="<?php echo $link;?>"
			  ><a href="learning_module.php?cid='<?php echo base64_encode($link).'&lid='.base64_encode($key); ?>'" <?php echo $disable; ?>><?php echo $courseName;?></a>
			</li>
			<!-- <li id="lpath<?php echo $i;?>" path="<?php echo $lpath; ?>" onclick="visitLevel(this.id,'<?php echo $link; ?>','<?php echo $key; ?>');"  class="<?php echo $hideColor." ".$activeShow.' '.$active;?>" link="<?php echo $link;?>"
			  ><a href="javascript:void(0)" <?php //echo $disable; ?>><?php //echo $courseCount;?></a></li>-->
			<?php $i++;
			} ?>
			</ul>
		   </div>
		
		 </div>
		 
		 
	</div>  
		<div class="clear"></div>
		<div class="moduleRightBg"><div class="moduleRightMidBg">&nbsp;</div></div>
	<div class="clear"></div>
	<div style="padding-right: 210px;display:none" class="pull-right"><a class="" href="dashboard.php">Return to Dashboard</a></div>
	<div class="clear"></div> 
  <div class="padder20 top0">	
  
  
	 <div class="clear"></div> 
  <div class="allTopicDiv">
   <?php 
$master_mode=1;
      if($totalTopic>0){
	     //echo "<pre>";print_r($topicArr);//exit;	
            $i=1; 
			
            if($visitLevel==$getRange){
				//echo " equal";
				//$master_mode=1;//1 mean unlock and 0 mean lock and empty mean default fn
				if($master_mode==1){
				   $visit=1;//1 mean visit yes and 0 mean not visit
				}else{
					$visit=0;//1 mean visit yes and 0 mean not visit
				}
			}
			if($visitLevel<$getRange){
				//echo " less";
				if($master_mode==1){
				   $visit=1;//1 mean visit yes and 0 mean not visit
				}else{
					$visit=1;//1 mean visit yes and 0 mean not visit
				}
			}
			if($visitLevel>$getRange){
				//echo " greater";
				if($master_mode==1){
				   $visit=1;//1 mean visit yes and 0 mean not visit
				}else{
					$visit=0;//1 mean visit yes and 0 mean not visit
				}
			}
		
		$moduleCount = 0;	
		$nomoduleCount = 0;
		
		$edgeIdArr = array();
		$sequenceArr=array();
		$topicAssEdgePath=array();
		$topicAssTreeNode=array();
		$allRemidationTopicArr=array();
		foreach($topicArr as $key=>$val){	
		    $topic_label=$val->topic_label;
			$assessment_type=$val->assessment_type;
			$is_survey=$val->is_survey;
			$sequence_id=$val->sequence_id;
		    $topic_type=$val->topic_type;
			if(($assessment_type=='' && $topic_type==1) || $assessment_type=='mid'){
			  $topicAssEdgePath[$sequence_id]=$val->edge_id;
			  $topicAssTreeNode[$sequence_id]=$val->tree_node_id;
			  $sequenceArr[$sequence_id]=$val->edge_id;
			  $allRemidationTopicArr[$sequence_id]=$val;
			}
			if($assessment_type=='' && $topic_type==2){
			  $allRemidationTopicArr[$sequence_id]=$val;
			}
			$arrTopic = array();
			$arrTopic['topic_edge_id'] = $val->edge_id;
			$arrTopic['userToken'] = $userToken;
			$arrTopic['package_code'] = $package_code;
			$course_code="CRS-".$course_id;
			$arrTopic['course_code'] = $course_code;
			$arrTopic['batch_id'] = $batch_id;
			//print_r($arrTopic);
			//$res = $objTR->topicCompletion($arrTopic);
			//print_r($res);
			
			$edgeIdArr[] = $val->edge_id;
			
			
		}		
		
		
		//echo "<pre>";print_r($topicArr);//exit; 

		$openKey=1;
		$completeTopicPerArr=array();
		$nextUnclock=1;
		foreach($topicArr as $key=>$val){
			$topic_label=$val->topic_label;
			$assessment_type=$val->assessment_type;
			$is_survey=$val->is_survey;
			$sequence_id=$val->sequence_id;
		    $topic_type=$val->topic_type;
			
			$arrTopic = array();
			$arrTopic['edge_id'] = $val->edge_id;//$edgeIdArr;
			$arrTopic['userToken'] = $userToken;
			$arrTopic['package_code'] = $package_code;
			$course_code="CRS-".$course_id;
			$arrTopic['course_code'] = $course_code;
			$arrTopic['batch_id'] = $batch_id;
			//echo "<pre>";print_r($arrTopic);
			$completeTopicArr =  $objTR->getCompletion($arrTopic);	
			//echo "<pre>";print_r($completeTopicArr);//exit; 
		
		   if(($assessment_type=='' && $topic_type==1) || $assessment_type=='mid'){
			  $completeTopicStatus = $completeTopicArr['completion_status'];
			 // $completeTopicPer = !empty($completeTopicArr[$val->edge_id]['complete_per'])?$completeTopicArr[$val->edge_id]['complete_per']:0;
			 // $completeTopicPerArr[$val->edge_id]=$completeTopicPer;
			  // $completeTopicStatus='';
			 //  $completeTopicPer='';
			   if($completeTopicStatus=="na"){
					$completeTopicPer=0;
				}else if($completeTopicStatus=="nc"){
					$completeTopicPer=50;
				}else if($completeTopicStatus=="c"){
					$completeTopicPer=100;
				} else {
					$completeTopicPer=0;
				} 
			}
           if($assessment_type=='' && $topic_type==1){
				//$topic_label=1;
				$topicName=$val->name;
				$tree_node_id=$val->tree_node_id;
				$topic_edge_id=$val->edge_id;
              	//$moduleCount += count($topic_label);
				$moduleCount ++;
				if($moduleCount<10){
					$topicCount="0".$moduleCount;
				  }else{
					$topicCount=$moduleCount;  
				}
				
				if($topic_label<10){
					$topic_label="0".$topic_label;
				  }else{
					$topic_label=$topic_label;  
				}
				$thumnailImg=$val->thumnailImg;
				if($thumnailImg!=''){
					$topicImg=$thumnail_Img_url.$thumnailImg; 
				}else{
					$topicImg=$_html_relative_path."images/defaultTopic.png";	
				}
				
				
				if($master_mode==1){
						if($visit==1 && $i==1){
							$module_url = "module.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$topic_edge_id."&nid=".$tree_node_id."&tcount=".$moduleCount; 
							$disable="";
							$active="active";
							$lockImg="";		
						}
						if($visit==1 && $i!=1){
							$module_url = "module.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$topic_edge_id."&nid=".$tree_node_id."&tcount=".$moduleCount; 
							$disable="";
							$active="";
							$lockImg="";		
						}
						if($visit==0 && $i==1){
							$module_url = "module.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$topic_edge_id."&nid=".$tree_node_id."&tcount=".$moduleCount; 
							$disable="";
							$active="active";
							$lockImg="";		
						}
						if($visit==0 && $i!=1){
							$module_url = "module.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$topic_edge_id."&nid=".$tree_node_id."&tcount=".$moduleCount; 
							$disable="";
							$active="";
							$lockImg="";		
						}
								
					 }
					 else if($master_mode==0){//lock fn
					    // echo "lock";
						
						if($visitLevel==$getRange){
						
							$firstTopic = current(array_values($topicAssEdgePath));
							$indexFirst = array_search($firstTopic, $topicAssEdgePath);
							if($indexFirst==$i || $nextUnclock==0){
							 	$module_url = "module.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$topic_edge_id."&nid=".$tree_node_id."&tcount=".$moduleCount; 
								$disable="";
								$active="active";
								$lockImg="";

							}else{
								
								 $module_url = ""; 
									$disable="style='cursor:default'";
									$active="";
									$lockImg='<img class="imgLock" src="'.$_html_relative_path.'images/lock.png"/>';
							} 
				
				         }else if($visitLevel<$getRange){
								$module_url = "module.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$topic_edge_id."&nid=".$tree_node_id."&tcount=".$moduleCount; 
								$disable="";
								$active="active";
								$lockImg="";		
						 }else{
								$module_url = ""; 
								$disable="style='cursor:default'";
								$active="";
								$lockImg='<img class="imgLock" src="'.$_html_relative_path.'images/lock.png"/>';			
							}
					  if($completeTopicPer>=$topicPass){
							$nextUnclock=0;
						}else{
							$nextUnclock=1;
						}
						
				   }else{
						if(($visit==1 && $i==1) && ($visitLevel<=$getRange)){
							$module_url = "module.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$topic_edge_id."&nid=".$tree_node_id."&tcount=".$moduleCount; 
							$disable="";
							$active="active";
							$lockImg="";		
						}else{
							$module_url = ""; 
							$disable="style='cursor:default'";
							$active="";
							$lockImg='<img class="imgLock" src="'.$_html_relative_path.'images/lock.png"/>';			
						}
					
				}

				if($topic_type==2){
					$hideRemi="style='display:none'";
				}
				else{
					$hideRemi=" ";
				}
				
				//$completeTopicStatus=12;
				//$totalChapter=10;
				//$completeTopicPer=10;
		  ?>
			<div class="topicHead <?php //echo $active;?>" nextTopic="<?php echo $openKey;?>" count="<?php echo $moduleCount;?>" <?php echo $hideRemi;?>>
			<a id="module<?php echo $i;?>" path="<?php echo $module_url; ?>" onclick="chapterLink(this.id,'<?php echo $topic_edge_id; ?>');" href="javascript:void(0)"  <?php echo $disable;?>>
			<?php if($completeTopicPer==100){?><img class="imgComplete" src="<?php echo $_html_relative_path; ?>images/complete.png"/><?php }?>
			 <div class="topicImg"><img class="imgTopic" src="<?php echo $topicImg; ?>"/>
			 <?php echo $lockImg;?>
			 </div>
			 <div class="clear"></div>
				<div class="headLeft">
				<div class="title"><?php echo $topicName//echo truncateString($topicName,topicName); ?></div>
				<div class="topicCount"><?php echo $topicCount; ?></div>
			 </div></a>
			<div class="clear"></div>
             <div class="progressDiv" completeTopicStatus="<?php echo $completeTopicStatus;?>" id="progressDiv<?php echo $i;?>" count="<?php echo $completeTopicPer;?>">
			   <div class="empty"></div>
			 <?php if($completeTopicPer==0 && $completeTopicPer==''){?>
			    <div class="scoreFill" style="width:<?php echo '0%';?>" ></div>
				<?php }else{  ?>
				<div class="scoreFill" style="width:<?php echo $completeTopicPer.'%';?>" ></div>
			<?php }?>
			
			</div>
			 <div class="clear"></div>
			  <?php if($completeTopicPer==0 && $completeTopicPer==''){?>
			     <div class="perDiv"  id="perDiv<?php echo $i;?>"><?php echo '0%'?></div>
				<?php }else{  ?>
				 <div class="perDiv"  id="perDiv<?php echo $i;?>"><?php echo $completeTopicPer.'%';?></div>
			<?php }?>
			 
		
			</div>
			
			<?php  }else if($assessment_type=='mid'){
				
			    $package_code=$_SESSION['package_code'];
				$course_code="CRS-".$course_id;
				$arr = array();
				$arr['token'] = $userToken;
				$arr['package_code'] = $package_code;
				$arr['course_code'] = $course_code;
				//$testPerformanceObj=  $objTR->trackTestPerformance($arr);
				
				$topic_label=0;
				$nomoduleCount += count($topic_label);	
				$topicName=$val->name;
				$tree_node_id=$val->tree_node_id;
				$topic_edge_id=$val->edge_id;	
				$assKey =array_search($topic_edge_id, $topicAssEdgePath);
				$keyCount=$assKey+1;
				$thumnailImg=$val->thumnailImg;
				
			if($is_survey==2){//is_survey mean quiz
			//echo $master_mode;
			if($thumnailImg!=''){
						$topicImg=$thumnail_Img_url.$thumnailImg; 
					}else{
						 $topicImg=$_html_relative_path."images/quizDefault.png";	
						
					}
				if($master_mode==1){//unlock fn
					// echo "unlock";
				       if($visit==1 && $i==1){
							$module_url = "skill_quiz.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$topic_edge_id."&nid=".$tree_node_id."&tcount=".$sequence_id; 
							$disable="";
							$active="active";
							$lockImg="";		
						}
						if($visit==1 && $i!=1){
							$module_url = "skill_quiz.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$topic_edge_id."&nid=".$tree_node_id."&tcount=".$sequence_id; 
							$disable="";
							$active="";
							$lockImg="";		
						}
						if($visit==0 && $i==1){
							$module_url = "skill_quiz.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$topic_edge_id."&nid=".$tree_node_id."&tcount=".$sequence_id; 
							$disable="";
							$active="active";
							$lockImg="";		
						}
						if($visit==0 && $i!=1){
							$module_url = "skill_quiz.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$topic_edge_id."&nid=".$tree_node_id."&tcount=".$sequence_id;  
							$disable="";
							$active="";
							$lockImg="";		
						}
								
					}else if($master_mode==0){//lock fn
					    // echo "lock";
						
						if($visitLevel==$getRange){
							//echo "<pre>";print_r($completeTopicPerArr);
							$firstTopic = current(array_values($topicAssEdgePath));
							$indexFirst = array_search($firstTopic, $topicAssEdgePath);
							//if($indexFirst==$i || $nextUnclock==0){
							if($nextUnclock==0){
								echo "gh";
							   $module_url = "skill_quiz.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$topic_edge_id."&nid=".$tree_node_id."&tcount=".$sequence_id; 
							}else{
								$module_url = ""; 
							    $disable="style='cursor:default'";
							    $active="";
							    $lockImg='<img class="imgLock" src="'.$_html_relative_path.'images/lock.png"/>';
								
							}
							
					   }else if($visitLevel<$getRange){
						  $module_url = "skill_quiz.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$topic_edge_id."&nid=".$tree_node_id."&tcount=".$sequence_id;
								$disable="";
								$active="active";
								$lockImg="";		
					    }else{
								$module_url = ""; 
								$disable="style='cursor:default'";
								$active="";
								$lockImg='<img class="imgLock" src="'.$_html_relative_path.'images/lock.png"/>';			
							}
						
					 if($completeTopicPer>=$quiz_passing_percentage){
							$nextUnclock=0;
						}else{
							$nextUnclock=1;
						}
						 
				 }else{
					 if(($visit==1 && $i==1) && ($visitLevel<=$getRange)){
							$module_url = "skill_quiz.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$topic_edge_id."&nid=".$tree_node_id."&tcount=".$sequence_id;
							$disable="";
							$active="active";
							$lockImg="";		
						}else{
							$module_url = ""; 
							$disable="style='cursor:default'";
							$active="";
							$lockImg='<img class="imgLock" src="'.$_html_relative_path.'images/lock.png"/>';			
						}

					
				 }
			  $topicHeadDefault='topicHeadDefault';
			  
			  
			  ?>
			<div class="topicHead <?php echo $topicHeadDefault;?>" <?php echo $hide;?> count="<?php echo $nomoduleCount;?>">
			  <a id="nomodule<?php echo $nomoduleCount.$i;?>" path=""   href="<?php echo $module_url;?>"  <?php echo $disable;?>>
			  <?php if($completeTopicPer==100){?><img class="imgComplete" src="<?php echo $_html_relative_path; ?>images/complete.png"/><?php }?>
			 <div class="topicImg">
				<div class="title"><?php echo $topicName//echo truncateString($topicName,topicName); ?></div>
			 <?php echo $lockImg;?>
			 </div>
			 <div class="clear"></div>
				<div class="midContent">
				
				Get ready for a timed Quiz to see how you are doing so far
			 </div>
			 <img class="imgRight" src="<?php echo $topicImg;?>"/>
			 </a>
			<div class="clear"></div>
            
		
			</div>	
					
			<?php 
			  
			     $quizAcoreArr=array();
				 $quiz_spend_time=array();
				 $quizRemiSkillScoreArr=array();
				 $allskilltimespArr=0;
				 $showRemediationArr=array();
				 /* foreach($testPerformanceObj as $key1=>$val1){
					
					$qCount=$val1->qCount;
					$totalCrrct=$val1->totalCrrct;
					$per_complete = round(($totalCrrct / $qCount) * 100);
					$ttlTimeSp=$val1->ttlTimeSp;
					$avg_time_sp=$val1->avg_time_sp;
					
					//echo $avg_time_sp;
					if($ttlTimeSp>0){
						$total_time_sp1=$ttlTimeSp;
						$hours = floor($total_time_sp1 / 3600);
						$total_time_sp1 -= $hours * 3600;
					    $minutes = floor($total_time_sp1 / 60);
					    $total_time_sp1 -= $minutes * 60;
					    $h = (!empty($hours))?'<span style="font-size:12px"> hr</span> ':'';
					    $min = (!empty($minutes))?'<span style="font-size:12px"> mins</span> ':'';
					    $sec=(!empty($total_time_sp1))?'<span style="font-size:12px"> sec</span>':'';
					    $hours = ($hours>0)?$hours:'';
					    $minutes = ($minutes)?$minutes:'';
					    $seconds=($total_time_sp1>0)?$total_time_sp1:'0';
					    $totaltimeSpent = $hours.$h.$minutes.$min.$seconds.$sec;
					 }else{
						  $totaltimeSpent ="0 <span style='font-size:12px'> sec</span>"; 
					 }
					  $remediation_edge_id=$val1->remediation_edge_id;
				      $skill=$val1->skill;
					  $skill_ArrSet1=array();
					  $skill_timeSpendArr1=array();
					  $all_skill_time_sp=0;
					  foreach($skill as $key=>$skillObjVal){
							$skill_ArrSet=array();
							$skill_timeSpendArr=array();

							$skill_id=$skillObjVal['skill_id'];
							$skill_name=$skillObjVal['skill_name'];
							  if($skillObjVal['skill_name']!='Undefined'){
								$all_skill_time_sp+=$total_time_sp;
								$attempt_no=$skillObjVal['attempt_no'];
								$per_skill = round(($total_correct / $attempted_question) * 100);
								$skill_ArrSet[]=$per_skill;
								$skill_ArrSet1[]=$skill_ArrSet;
								$skill_timeSpendArr[]=$total_time_sp;
								$skill_timeSpendArr1[]=$skill_timeSpendArr;
							  }
					  }
					  //echo  $all_skill_time_sp;
					  if($topic_edge_id==$val1->edge_id){
						 $quizScoreArr[$topic_edge_id]=$per_complete;
						 $quiz_spend_time[$topic_edge_id]=$ttlTimeSp;
						 $quizRemiSkillScoreArr[$remediation_edge_id]=$skill_ArrSet1;
						 $allskilltimespArr[$remediation_edge_id]= $skill_timeSpendArr1;
						 $showRemediationArr[$remediation_edge_id]=$val1->showRemediation;
					   }
					
				 }  */
				//echo $showRemediationArr[];
			 $remidationEdgeId= $objCon->getRemidationMapAss($topic_edge_id);
			$remidation_Edge_Id=$remidationEdgeId['topic_tree_node_id'];
             //echo "<pre>";print_r($remidation_Edge_Id);				
         // echo "<pre>";print_r($showRemediationArr[$remidation_Edge_Id]);
		  if($showRemediationArr[$remidation_Edge_Id]==1){
					foreach($quizRemiSkillScoreArr[$remidation_Edge_Id] as $key=>$skillScoreVal){
					 //echo "<pre>";print_r($skillScoreVal);
					 if($skillScoreVal[$key]<60){
						 $showRemidation="style='display:inline-block'";
						
					 }else{
						 $showRemidation="style='display:none'";
						
					 }
				 }
				  
			// if($allskilltimespArr[$remidation_Edge_Id]>0){  

			 //$getRemidationScoreArr = $objCon->getRemidationScore($remidation_Edge_Id,$user_id);
			
			 $topicHeadDefaultRem='topicHeadDefault100';
			  //echo "<pre>";print_r($allRemidationTopicArr);
			
			   foreach($allRemidationTopicArr as $remval){
				 if($remidation_Edge_Id==$remval->edge_id){
					 $tree_node_id_remidation=$remval->tree_node_id;
				     $remidationEdge_id=$remval->edge_id;
					 $topicRemName=$remval->name;
					 $topicRemDescription=$remval->description;
					 $rem_thumnailImg=$remval->thumnailImg;
					 $module_url = "remidation.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$remidationEdge_id."&nid=".$tree_node_id_remidation;
					?>
					<div class="topicHead <?php echo $topicHeadDefaultRem;?>" <?php echo $showRemidation;?> count="<?php echo $nomoduleCount;?>"> 
					  <a id="nomodule<?php echo $nomoduleCount.$i;?>" path="" href="<?php echo $module_url;?>"  <?php echo $disable;?>>
					 <div class="topicImg">
					  <div class="topicImgBg"><img class="imgTopc" src="<?php echo $_html_relative_path; ?>images/remidation.png" style="width:60px;"/>  </div>
					 <div class="title">
					 <span class="leftText"><?php echo $topicRemName//echo truncateString($topicName,topicName); ?><br/> <p class="subTitle"><?php echo displayText($topicRemDescription); ?></p> </span>
					 	<span  class="rightText pull-right"><button id="btnStart" type="button" class="btn btn-blue btnSubmit btnStartAss" style="margin-top:10px;width:90px;display:none">Start</button></span>
					 </div>
				
					 </div>
						</a>
					<div class="clear"></div>
					
				
					</div>	
			<?php 	 }

			      } 
				
			 }else{
			
		 } 
			?>
			<?php //}
			}
			//echo "<pre>";print_r($topicArr);//exit; 
             if($is_survey==3){//is_survey mean review
			     if($thumnailImg!=''){
						$topicImg=$thumnail_Img_url.$thumnailImg; 
					}else{
						 $topicImg=$_html_relative_path."images/review.png";	
						
					}
					if($master_mode==1){//unlock fn
					// echo "unlock";
				       if($visit==1 && $i==1){
							$module_url = "skill_quiz.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$topic_edge_id."&nid=".$tree_node_id."&tcount=".$sequence_id; 
							$disable="";
							$active="active";
							$lockImg="";		
						}
						if($visit==1 && $i!=1){
							$module_url = "skill_quiz.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$topic_edge_id."&nid=".$tree_node_id."&tcount=".$sequence_id; 
							$disable="";
							$active="";
							$lockImg="";		
						}
						if($visit==0 && $i==1){
							$module_url = "skill_quiz.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$topic_edge_id."&nid=".$tree_node_id."&tcount=".$sequence_id; 
							$disable="";
							$active="active";
							$lockImg="";		
						}
						if($visit==0 && $i!=1){
							$module_url = "skill_quiz.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$topic_edge_id."&nid=".$tree_node_id."&tcount=".$sequence_id;  
							$disable="";
							$active="";
							$lockImg="";		
						}
								
					}else if($master_mode==0){//lock fn
					    // echo "lock";
						
						if($visitLevel==$getRange){
							//echo "<pre>";print_r($completeTopicPerArr);
							$firstTopic = current(array_values($topicAssEdgePath));
							$indexFirst = array_search($firstTopic, $topicAssEdgePath);
							//if($indexFirst==$i || $nextUnclock==0){
							if($nextUnclock==0){
							   $module_url = "skill_quiz.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$topic_edge_id."&nid=".$tree_node_id."&tcount=".$sequence_id; 
							}else{
								$module_url = ""; 
							    $disable="style='cursor:default'";
							    $active="";
							    $lockImg='<img class="imgLock" src="'.$_html_relative_path.'images/lock.png"/>';
								
							}
							
					   }else if($visitLevel<$getRange){
						  $module_url = "skill_quiz.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$topic_edge_id."&nid=".$tree_node_id."&tcount=".$sequence_id;
								$disable="";
								$active="active";
								$lockImg="";		
					    }else{
								$module_url = ""; 
								$disable="style='cursor:default'";
								$active="";
								$lockImg='<img class="imgLock" src="'.$_html_relative_path.'images/lock.png"/>';			
							}
						
					 if($completeTopicPer>=$quiz_passing_percentage){
							$nextUnclock=0;
						}else{
							$nextUnclock=1;
						}
						 
				 }else{
					 if(($visit==1 && $i==1) && ($visitLevel<=$getRange)){
							$module_url = "skill_quiz.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$topic_edge_id."&nid=".$tree_node_id."&tcount=".$sequence_id;
							$disable="";
							$active="active";
							$lockImg="";		
						}else{
							$module_url = ""; 
							$disable="style='cursor:default'";
							$active="";
							$lockImg='<img class="imgLock" src="'.$_html_relative_path.'images/lock.png"/>';			
						}

					
				 }
					$topicHeadDefault='topicHeadDefault100';
					//$hide="style='display:none;'";
				?>
			<div class="topicHead <?php echo $topicHeadDefault;?>" <?php echo $hide;?> count="<?php echo $nomoduleCount;?>"> <?php if($completeTopicPer==100){?><img class="imgComplete" style="top:43%" src="<?php echo $_html_relative_path; ?>images/complete.png"/><?php }?>
			  <a id="nomodule<?php echo $nomoduleCount.$i;?>" path="" href="<?php echo $module_url;?>"  <?php echo $disable;?>>
			 <div class="topicImg">
			  <img class="imgTopc" src="<?php echo $topicImg; ?>" style="width:60px;"/>  
			 <div class="title"><span class="leftText"><?php echo $topicName//echo truncateString($topicName,topicName); ?></span><span  class="rightText pull-right"><button id="btnStart" type="button" class="btn btn-blue btnSubmit btnStartAss" style="margin-top:10px;width:90px;display:none">Start</button></span><?php echo $lockImg;?>
			 </div>
			
			 </div>
			 <div class="clear"></div>
				</a>
			<div class="clear"></div>
            
		
			</div>	
			<?php } if($is_survey==4){
				      if($thumnailImg!=''){
						$topicImg=$thumnail_Img_url.$thumnailImg; 
					}else{
						 $topicImg=$_html_relative_path."images/review.png";	
						
					}
					if($master_mode==1){//unlock fn
					// echo "unlock";
				       if($visit==1 && $i==1){
							$module_url = "skill_quiz.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$topic_edge_id."&nid=".$tree_node_id."&tcount=".$sequence_id; 
							$disable="";
							$active="active";
							$lockImg="";		
						}
						if($visit==1 && $i!=1){
							$module_url = "skill_quiz.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$topic_edge_id."&nid=".$tree_node_id."&tcount=".$sequence_id; 
							$disable="";
							$active="";
							$lockImg="";		
						}
						if($visit==0 && $i==1){
							$module_url = "skill_quiz.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$topic_edge_id."&nid=".$tree_node_id."&tcount=".$sequence_id; 
							$disable="";
							$active="active";
							$lockImg="";		
						}
						if($visit==0 && $i!=1){
							$module_url = "skill_quiz.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$topic_edge_id."&nid=".$tree_node_id."&tcount=".$sequence_id;  
							$disable="";
							$active="";
							$lockImg="";		
						}
								
					}else if($master_mode==0){//lock fn
					    // echo "lock";
						
						if($visitLevel==$getRange){
							//echo "<pre>";print_r($completeTopicPerArr);
							$firstTopic = current(array_values($topicAssEdgePath));
							$indexFirst = array_search($firstTopic, $topicAssEdgePath);
							//if($indexFirst==$i || $nextUnclock==0){
							if($nextUnclock==0){
							   $module_url = "skill_quiz.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$topic_edge_id."&nid=".$tree_node_id."&tcount=".$sequence_id; 
							}else{
								$module_url = ""; 
							    $disable="style='cursor:default'";
							    $active="";
							    $lockImg='<img class="imgLock" src="'.$_html_relative_path.'images/lock.png"/>';
								
							}
							
					   }else if($visitLevel<$getRange){
						  $module_url = "skill_quiz.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$topic_edge_id."&nid=".$tree_node_id."&tcount=".$sequence_id;
								$disable="";
								$active="active";
								$lockImg="";		
					    }else{
								$module_url = ""; 
								$disable="style='cursor:default'";
								$active="";
								$lockImg='<img class="imgLock" src="'.$_html_relative_path.'images/lock.png"/>';			
							}
						
					 if($completeTopicPer>=$quiz_passing_percentage){
							$nextUnclock=0;
						}else{
							$nextUnclock=1;
						}
						 
				 }else{
					 if(($visit==1 && $i==1) && ($visitLevel<=$getRange)){
							$module_url = "skill_quiz.php?cid=".$course_id."&lid=".$visitLevel."&tEdge_id=".$topic_edge_id."&nid=".$tree_node_id."&tcount=".$sequence_id;
							$disable="";
							$active="active";
							$lockImg="";		
						}else{
							$module_url = ""; 
							$disable="style='cursor:default'";
							$active="";
							$lockImg='<img class="imgLock" src="'.$_html_relative_path.'images/lock.png"/>';			
						}

					
				 }
				//is_survey mean level test	
				  $hide="";
				  $topicHeadDefault='topicHeadDefault100';?>
				
			<div class="topicHead <?php echo $topicHeadDefault;?>" <?php echo $hide;?> count="<?php echo $nomoduleCount;?>"><?php if($completeTopicPer==100){?><img class="imgComplete" style="top:43%" src="<?php echo $_html_relative_path; ?>images/complete.png"/><?php }?>
			  <a id="nomodule<?php echo $nomoduleCount.$i;?>"  path=""   href="<?php echo $module_url;?>"  <?php echo $disable;?>>
			 <div class="topicImg">
			 <img class="imgTopc" src="<?php echo $topicImg; ?>" style="width:60px;"/> 
			 <div class="title"><span class="leftText"><?php echo $topicName//echo truncateString($topicName,topicName); ?></span><span  class="rightText pull-right"><button id="btnStart" type="button" class="btn btn-blue btnSubmit btnStartAss" style="margin-top:10px;width:90px;display:none">Start</button></span><?php echo $lockImg;?></div>
			
			 </div>
			 <div class="clear"></div>
				</a>
			<div class="clear"></div>
            
		
			</div>	
				
		 <?php	}?>
				
					
			<?php } $i++; } 
			}else{?>
   <div class="topic">Module is not available</div>
   
   <?php }?>
	</div>
 </div>
</section>
<?php include_once('../footer/trainerFooter.php');?>
<script>
var course_code = <?php echo json_encode($course_code);?>; 
var getVisitLevel = <?php echo json_encode($getVisitLevelArr);?>; 
var topic_edge_id;
function showHideLevel(wId,lId,cnt){
	if(cnt=='1'){
	 $("#"+wId).hide();
	 $(".showDown").hide();
	 $("#"+lId).show();
	 $(".showUp").show();
	}else{
		$("#"+lId).hide();
	   $(".showUp").hide();
	   $("#"+wId).show();
	   $(".showDown").show();
	}

}

 function visitLevel(id,cid,level){
	 if(cid!=='' && level!=''){
	  var data = {action: 'set_visitlevel',code:cid, level:level};
        $.ajax({url: 'ajax/set_visit_level_ajax.php', type: 'post', dataType: 'json', data: data, async: false,
           success : function(data){
			  console.log(data.status)
		  if(data.status==1){
			  location.reload();
			  
			  $("#loaderDiv").hide();
		  }else{
			console.log(data.status)
		  }
		},
            error: function () {}
        });
	 }

}

 function chapterLink(id,tEdgeId){
	var chapterPath=$("#"+id).attr('path');
    //console.log(chapterPath);
	if(chapterPath!==''){
		topic_edge_id =tEdgeId;
	  // sendBookmarkTrackingData(); 
	   window.location.href=chapterPath;	
		
	}
	
   
 }
  $( window ).on('beforeunload', function() {
        try{
           //  $("#loaderDiv").show();
			  
        }
        catch(e){

        }

    }); 
 
  function sendBookmarkTrackingData(){
	   $("#loaderDiv").show();
	  console.log("bookmark Course");
       var data = {action: 'set_bookmark',course_code:course_code, topic_edge_id:topic_edge_id, chapter_edge_id:'',component_edge_id: '',other: ''};
        $.ajax({url: 'ajax/bookmark-ajax.php', type: 'post', dataType: 'json', data: data, async: false,
           success : function(data){
		  if(data.status==1){
			  //console.log(data.res)
			  $("#loaderDiv").hide();
		  }else{
			console.log(data.res)
		  }
		},
            error: function () {}
        });
 } 
 
 $(document).ready(function(){
  //topic_edge_id ='';
 // sendBookmarkTrackingData();
});
 function setBatchList(){
	 var bid = $('#batch_id option:selected').val();
	 if(bid!==''){
	  var data = {action: 'set_batch',batch_id:bid};
		$.ajax({url: '../set_visit_product_ajax.php', type: 'post', dataType: 'json', data: data, async: false,
		   success : function(data){
			  console.log(data.status)
		  if(data.status==1){
			  location.reload();
			  
			  $("#loaderDiv").hide();
		  }else{
			console.log(data.status)
		  }
		},
			error: function () {}
		});
	 }

}

  
/*  var boxDivWidth=$(".allTopicDiv").css(width);
boxDivWidth=boxDivWidth-15;
boxDivWidth=boxDivWidth/3;
$(".topicHead").css("width",boxDivWidth+"%"); */
</script>
