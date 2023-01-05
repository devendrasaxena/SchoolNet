<?php
include_once('../../header/lib.php');
include_once('../../header/global_config.php');
error_reporting(E_ALL);
ini_set('display_errors',1);
if(isset($_SESSION['client_id'])){
   $client_id=$_SESSION['client_id'];
 }

?>

	<?php $i = 1;?> 
	<?php 
	$topic_edge_id = trim($_POST['topic_edge_id']);
	$skill_id = trim($_POST['skill_id']);
	$student_id = trim($_POST['student_id']);
	
	$reportObj = new reportController();
	$completionArr =	$reportObj->getTotalAndCompletedLesson($topic_edge_id,$skill_id,$student_id);
	//echo "<pre>";print_r($completionArr);
	$completionArr = json_decode($completionArr);
	$ttlChapter =$completionArr->cnt;
	$ttlComplChapter =$completionArr->cmplt;
	$chapter_list =$completionArr->chapter_list;
	$cmplt_chapter_list =$completionArr->cmplt_chapter_list;
	
	foreach($cmplt_chapter_list as $chapterKey=>$chapterVal):?>

		<?php 
			//$name = $chapterVal->code;
			$chapter_name = stripslashes($reportObj->publishText($chapterVal->code));
			$chapter_description = stripslashes($reportObj->publishText($chapterVal->title));
			$bg_color = $chapterVal->bgColor;
			if($bg_color!=""){
				$bg_color = "Yes";
			}else{
				$bg_color = "No";
			}
			$chapterSkill = $chapterVal->chapterSkill;
			$chapterSkill = $reportObj->getSkillnameById($chapterSkill);
			$quesCount = $chapterVal->quesCount;
			$duration = $chapterVal->duration;
			$thumnailImg = $chapterVal->thumnailImg;
			if($thumnailImg!=""){
				$thumnailImg = "Yes";
			}else{
				$thumnailImg = "No";
			}
			$sequence_no = $chapterVal->sequence_no;
			$objective = stripslashes($reportObj->publishText($chapterVal->objective));
		 ?>

		<tr>

			<td class="col-sm-1 text-cnter"><?php echo $i++;?></td>
			<td class="col-sm-2 text-left"><?php echo $chapter_name;?></td>
			<td class="col-sm-2 text-left"><?php echo $chapter_description;?></td>
			<td class="col-sm-2 text-left"><?php echo $objective;?></td>
			<td class="col-sm-2 text-left"><?php echo $quesCount;?></td>
			<td class="col-sm-2 text-left"><?php echo $thumnailImg;?></td>
			<td class="col-sm-1 text-left"><?php echo $bg_color;?></td>
									

		</tr>
	<?php endforeach;?>
<?php

?>
       