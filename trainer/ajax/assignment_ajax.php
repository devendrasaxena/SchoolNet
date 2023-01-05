<?php
error_reporting(E_ALL);
ini_set('display_errors','1');
include_once('../../header/lib.php');

$assignmentObj = new assignmentController();
$adminObj = new centerAdminController();
$assessmentObj = new assessmentController();
 
if(isset($_GET['action']) && $_GET['action'] == 'getlevel'){
	$option = '<option value="">Select Level</option>';
	if(isset($_POST['batch_id']) && $_POST['batch_id'] != '' && 
		isset($_POST['trainer_id']) && $_POST['trainer_id'] != '' &&
		isset($_POST['center_id']) && $_POST['center_id'] != ''){
		if($_POST['trainer_id'] == $_SESSION['user_id']){
			$level = $assignmentObj->getProductConfigurationByClassAndTrainer($_POST['batch_id'], $_POST['center_id'], 'Level');
			$enabled_level = $level[0]['is_enabled'];
			$level_arary = $enabled_level == 0 ? $enabled_level : explode(', ', $enabled_level);
			
			$courseType='0';
			$courseArr = $adminObj->getCourseListByLevel($courseType,2);


			$col  = 'level_text';
			$sort = array();
			foreach ($courseArr as $i => $obj) {
				  $sort[$i] = $obj->{$col};
				}
			array_multisort($sort, SORT_ASC, $courseArr);
			
			
			foreach($courseArr as $key=>$val){
				$option .= '<option value="'.$val->course_id.'">'.'Level - '.($key+1).'</option>';
			}
		} 
	}
	echo $option;

}

if(isset($_GET['action']) && $_GET['action'] == 'gettopic'){
	$option = '<option value="">Select Module</option>';
	
	if(isset($_POST['batch_id']) && $_POST['batch_id'] != '' && 
		isset($_POST['level_id']) && $_POST['level_id'] != '' &&
		isset($_POST['trainer_id']) && $_POST['trainer_id'] != '' &&
		isset($_POST['center_id']) && $_POST['center_id'] != ''){
		if($_POST['trainer_id'] == $_SESSION['user_id']){
			$topic = $assignmentObj->getProductConfigurationByClassAndTrainer($_POST['batch_id'], $_POST['center_id'], 'Module');
			$enabled_topics = $topic[0]['is_enabled'];
			$topic_arary = $enabled_topics == 0 ? $enabled_topics : explode(', ', $enabled_topics);

			$topic_arr = $assessmentObj->getTopicOrAssessmentByCourseId($_POST['level_id'],$customTopic);

			if(is_array($topic_arary)){

				foreach($topic_arr as $key=>$val){
					if($val->assessment_type == ''){
						if(in_array($val->edge_id, $topic_arary)){
							$option .= '<option value="'.$val->edge_id.'">'.$val->name.'</option>';
						}
					}
				}
			} else {
				foreach($topic_arr as $key=>$val){
					if($val->assessment_type == ''){
						$option .= '<option value="'.$val->edge_id.'">'.$val->name.'</option>';
					}
				}
			}

		} 
	}
	echo $option ;

}
if(isset($_GET['action']) && $_GET['action'] == 'getchapter'){
	$option = '<option value="">Select Chapter</option>';
	
	if(isset($_POST['batch_id']) && $_POST['batch_id'] != '' && 
		isset($_POST['topic_id']) && $_POST['topic_id'] != '' &&
		isset($_POST['trainer_id']) && $_POST['trainer_id'] != '' &&
		isset($_POST['center_id']) && $_POST['center_id'] != ''){
		if($_POST['trainer_id'] == $_SESSION['user_id']){
			$chapter = $assignmentObj->getProductConfigurationByClassAndTrainer($_POST['batch_id'], $_POST['center_id'],'Chapter');
			$enabled_chapters = $chapter[0]['is_enabled'];
			
			$chapter_arary = $enabled_chapters == 0 ? $enabled_chapters : explode(', ', $enabled_chapters);

			$chapter_arr = $assessmentObj->getChapterByTopicEdgeId($_POST['topic_id'],$customChapter);
			
			if(count($chapter_arr) > 0){
				if(is_array($chapter_arary)){
					foreach($chapter_arr as $key=>$val){
						if(in_array($val->edge_id, $chapter_arary)){
							$option .= '<option value="'.$val->edge_id.'">'.$val->name.'</option>';
						}
					}
				} else {
					foreach($chapter_arr as $key=>$val){
						$option .= '<option value="'.$val->edge_id.'">'.$val->name.'</option>';
					}
				}
			}

		}
	}
	echo $option;

}

?>