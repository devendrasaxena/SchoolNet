<?php
include_once('../../header/lib.php');

if($_POST['action']=="topic_show"){
	   $assessmentObj = new assessmentController();
	   $optionSelected = "";
	   $courseArr = json_decode($_POST['course']);
	 //some php operation
	 foreach($courseArr  as $key => $courseId){
	   $topic_arr = $assessmentObj->getTopicOrAssessmentByCourseId($courseId);
		//  echo "<pre>";print_r($topic_arr);
		 if(count($topic_arr)>0){

			 foreach($topic_arr  as $key => $value){
					
					$tree_node_id = $value->tree_node_id;
					
					$name = $value->name;
					$edge_id = $value->edge_id;
			
					$optionSelected = ($valSelected == $edge_id) ? "selected" : "";
					echo '<option  value="'.$edge_id.'" '.$optionSelected.' tree_node_id="'.$tree_node_id.'">'.$name.'</option>';
						
			 }
		 }else{
			echo '<option value="">Not Available</option>';
		}
	 }

}

if($_POST['action']=="chapter_show"){
	   $assessmentObj = new assessmentController();
	   $optionSelected = "";
	   $topicArr = json_decode($_POST['topic']);
	 //some php operation
	 foreach($topicArr  as $key => $topicEdge){
	   $chapter_arr = $assessmentObj->getChapterByTopicEdgeId($topicEdge);
		 //echo "<pre>";print_r($chapter_arr);exit;
		 if(count($chapter_arr)>0){

			 foreach($chapter_arr  as $key => $value){
					
					$tree_node_id = $value->tree_node_id;
					
					$name = $value->name;
					$description = $value->description;
					$thumnailImg = $value->thumnailImg;
					$skill = $value->chapterSkill;
					$edge_id = $value->edge_id;
			
                    $optionSelected = ($valSelected == $edge_id) ? "checked" : "checked";

					echo '<div class="col-sm-6"><div class="chBox skill'.$skill.'" tree_node_id="'.$tree_node_id.'"  skill="'.$skill.'">
					<div class="col-md-1 col-sm-1 displayInline"><input type="checkbox" name="'.$name.'" '.$optionSelected.' value="'.$edge_id.'"/></div><div class="col-md-10 col-sm-10 displayInline"><div class="chBoxDiv"><div class="title">'.$name.'</div><div class="description">'.$description.'</div></div><div class="chthumbnail pull-right skill'.$skill.'"><img src="'.$thumnail_Img_url.$thumnailImg
					.'"/></div></div></div></div>';
						
			 }
		 }else{
			echo '<div class="col-sm-12">Not Available</div>';
		}
	 }

}
		
?>