<?php

class placementController {
    
    public $dbConn;
    public function __construct() {

        
		$this->dbConn = DBConnection::createConn();
		
    }
	
	public function getCourseEdgeIdByCourseId($course_id){
		$con = createConnection();
		$stmt = $con->prepare("SELECT gmt.edge_id FROM generic_mpre_tree gmt
								JOIN course c ON c.tree_node_id = gmt.tree_node_id
								WHERE  c.course_id=?");
		$stmt->bind_param("i",$course_id);
		$stmt->execute();
		$stmt->bind_result($edge_id);
		$stmt->fetch();
		$stmt->close();
		return $edge_id;
	
	}
	
	
   public function getTopicOrAssessmentByCourseId1($course_id,$customTopic){

		$con = createConnection();
		$course_edge_id = $this->getCourseEdgeIdByCourseId($course_id);

		$topicArr = array();
		$stmt = $con->prepare("SELECT `cm`.`tree_node_id`,`cm`.`name`,`cm`.`description`,`cm`.`duration`,`cm`.`instimage`,`cm`.`isQuesRand`,`cm`.`isAnsRand`,`cm`.`timeleft_warn`,`cm`.`assessment_type`,`gmt`.`edge_id`,`tnd`.`tree_node_category_id`,`sequence_id`,`topic_label`,`thumnailImg` FROM `generic_mpre_tree` AS `gmt`
			JOIN `tree_node_def` AS `tnd` ON `tnd`.`tree_node_id`=`gmt`.`tree_node_id`
			JOIN `cap_module` AS `cm` ON `cm`.`tree_node_id`=`gmt`.`tree_node_id`
			WHERE `gmt`.`is_active`=1 AND `tree_node_super_root`=? AND `tnd`.`tree_node_category_id`=5 AND `cm`.`assessment_type`='pre'");
		$stmt->bind_param("i",$course_edge_id);
		$stmt->execute();
		$stmt->bind_result($tree_node_id,$name,$description,$duration,$assInstFile,$isQuesRand,$isAnsRand,$timeleft_warn,$assessment_type,$edge_id,$tree_node_category_id,$sequence_id,$topic_label,$thumnailImg);
		$stmt->execute();
		while($stmt->fetch()) {
			$topic1 = new stdClass();
			$topic1->tree_node_id = $tree_node_id;
			$topic1->name = $name;
			$topic1->description = $description;
			$topic1->edge_id = $edge_id;
			$topic1->topic_label = $topic_label;
			$topic1->thumnailImg = $thumnailImg;
			$topic1->assessment_type = $assessment_type;

			array_push($topicArr,$topic1);
		}
		$stmt->close();
		
		  $topicArr2 = array();
		$whr.='WHERE gmt.is_active=1 AND cm.is_topic_active="1" AND tree_node_super_root=? AND tnd.tree_node_category_id IN(3,5)';
		 if($customTopic!=""){
			$whr.=' AND gmt.edge_id IN('.$customTopic.')'; 
		} 
		$whr.=' AND (cm.assessment_type="mid" OR cm.assessment_type IS NULL) ORDER BY sequence_id';

		$sql="SELECT `cm`.`tree_node_id`,`cm`.`name`,`cm`.`description`,`cm`.`duration`,`cm`.`instimage`,`cm`.`isQuesRand`,`cm`.`isAnsRand`,`cm`.`timeleft_warn`,`cm`.`assessment_type`,`gmt`.`edge_id`,`tnd`.`tree_node_category_id`,`sequence_id`,`topic_label`,`thumnailImg`,`is_survey`,`no_of_attempt`,`ttl_ques_to_show` ,`passing_score`,`no_of_skill_ques` ,`topic_type` FROM `generic_mpre_tree` AS `gmt`
			JOIN `tree_node_def` AS `tnd` ON `tnd`.`tree_node_id`=`gmt`.`tree_node_id`
			JOIN `cap_module` AS `cm` ON `cm`.`tree_node_id`=`gmt`.`tree_node_id`
			$whr";	 		 
			//echo "<pre>";print_r($sql);
          /*  $sql="SELECT `cm`.`tree_node_id`,`cm`.`name`,`cm`.`description`,`cm`.`duration`,`cm`.`instimage`,`cm`.`isQuesRand`,`cm`.`isAnsRand`,`cm`.`timeleft_warn`,`cm`.`assessment_type`,`gmt`.`edge_id`,`tnd`.`tree_node_category_id`,`sequence_id`,`topic_label`,`thumnailImg`,`is_survey`,`no_of_attempt`,`ttl_ques_to_show` ,`passing_score`,`no_of_skill_ques` ,`topic_type` FROM `generic_mpre_tree` AS `gmt`
			JOIN `tree_node_def` AS `tnd` ON `tnd`.`tree_node_id`=`gmt`.`tree_node_id`
			JOIN `cap_module` AS `cm` ON `cm`.`tree_node_id`=`gmt`.`tree_node_id`
			WHERE `gmt`.`is_active`=1 AND cm.`is_topic_active`='1' AND `tree_node_super_root`=? AND `tnd`.`tree_node_category_id` IN(3,5) AND (`cm`.`assessment_type`='mid' OR `cm`.`assessment_type` IS NULL) ORDER BY `sequence_id`";	 */		 
			//echo "<pre>";print_r($sql);	exit;
		$stmt = $con->prepare($sql);
		$stmt->bind_param("i",$course_edge_id);
		$stmt->execute();
		$stmt->bind_result($tree_node_id,$name,$description,$duration,$assInstFile,$isQuesRand,$isAnsRand,$timeleft_warn,$assessment_type,$edge_id,$tree_node_category_id,$sequence_id,$topic_label,$thumnailImg,$is_survey,$no_of_attempt,$ttl_ques_to_show,$passing_score,$no_of_skill_ques,$topic_type);
		$stmt->execute();
		while($stmt->fetch()) {
			$topic2 = new stdClass();
			$topic2->tree_node_id = $tree_node_id;
			$topic2->name = $name;
			$topic2->description = $description;
			$topic2->assInstFile = $assInstFile;
			$topic2->isQuesRand = $isQuesRand;
			$topic2->isAnsRand = $isAnsRand;
			$topic2->edge_id = $edge_id;
			$topic2->duration = $duration;
			$topic2->sequence_id = $sequence_id;
			$topic2->topic_label = $topic_label;
			$topic2->thumnailImg = $thumnailImg;
			$topic2->is_survey = $is_survey;
			$topic2->no_of_attempt = $no_of_attempt;
			$topic2->ttl_ques_to_show = $ttl_ques_to_show;
			$topic2->passing_score = $passing_score;
			$topic2->no_of_skill_ques = $no_of_skill_ques;
			$topic2->topic_type = $topic_type;
			
			$topic2->assessment_type = $assessment_type;

			array_push($topicArr,$topic2);
		}
		$stmt->close();
		
		$topicArr3 = array();
		$stmt = $con->prepare("SELECT `cm`.`tree_node_id`,`cm`.`name`,`cm`.`description`,`cm`.`duration`,`cm`.`instimage`,`cm`.`isQuesRand`,`cm`.`isAnsRand`,`cm`.`timeleft_warn`,`cm`.`assessment_type`,`gmt`.`edge_id`,`tnd`.`tree_node_category_id`,`sequence_id` ,`topic_label`,`thumnailImg` FROM `generic_mpre_tree` AS `gmt`
			JOIN `tree_node_def` AS `tnd` ON `tnd`.`tree_node_id`=`gmt`.`tree_node_id`
			JOIN `cap_module` AS `cm` ON `cm`.`tree_node_id`=`gmt`.`tree_node_id`
			WHERE `gmt`.`is_active`=1 AND `tree_node_super_root`=? AND `tnd`.`tree_node_category_id`=5 AND `cm`.`assessment_type`='post'");
		$stmt->bind_param("i",$course_edge_id);
		$stmt->execute();
		$stmt->bind_result($tree_node_id,$name,$description,$duration,$assInstFile,$isQuesRand,$isAnsRand,$timeleft_warn,$assessment_type,$edge_id,$tree_node_category_id,$sequence_id,$topic_label,$thumnailImg);
		$stmt->execute();
		while($stmt->fetch()) {
			$topic3 = new stdClass();
			$topic3->tree_node_id = $tree_node_id;
			$topic3->name = $name;
			$topic3->description = $description;
			$topic3->edge_id = $edge_id;
			$topic3->topic_label = $topic_label;
			$topic3->thumnailImg = $thumnailImg;
			$topic3->assessment_type = $assessment_type;
			array_push($topicArr,$topic3);
		}
		return $topicArr;
	}
	
		
		public function getChapterByTopicEdgeId($topic_edge_id,$customChapter){
			$con = createConnection();
			$topicArr = array();

			$whr.='WHERE gmt.is_active=1 AND tree_node_parent=?';
			 if($customChapter!=""){
				$whr.=' AND gmt.edge_id IN('.$customChapter.')'; 
			} 
			$whr.=' AND tnd.tree_node_category_id=2 ORDER BY sequence_no';
			$sql="SELECT `gmt`.`edge_id`,`cm`.`tree_node_id`,`cm`.`code`,`cm`.`title`,`cm`.`chapterSkill`,`cm`.`quesCount`,`cm`.`duration`,`cm`.`thumnailImg` ,`cm`.`sequence_no` 
			    FROM `generic_mpre_tree` AS `gmt`
				JOIN `tree_node_def` AS `tnd` ON `tnd`.`tree_node_id`=`gmt`.`tree_node_id`
				JOIN `session_node` AS `cm` ON `cm`.`tree_node_id`=`gmt`.`tree_node_id`
				$whr";
			/* $sql="SELECT `gmt`.`edge_id`,`cm`.`tree_node_id`,`cm`.`code`,`cm`.`title`,`cm`.`chapterSkill`,`cm`.`quesCount`,`cm`.`duration`,`cm`.`thumnailImg` ,`cm`.`sequence_no` 
			    FROM `generic_mpre_tree` AS `gmt`
				JOIN `tree_node_def` AS `tnd` ON `tnd`.`tree_node_id`=`gmt`.`tree_node_id`
				JOIN `session_node` AS `cm` ON `cm`.`tree_node_id`=`gmt`.`tree_node_id`
				WHERE `gmt`.`is_active`=1 AND `tree_node_parent`=? AND `tnd`.`tree_node_category_id`=2 ORDER BY `sequence_no`"); */
			 //echo "<pre>";print_r($sql);exit;
			$stmt = $con->prepare($sql);
				
			$stmt->bind_param("i",$topic_edge_id);
			$stmt->execute();
			$stmt->bind_result($edge_id,$tree_node_id,$code,$title,$chapterSkill,$quesCount,$duration,$thumnailImg,$sequence_no);
			while($stmt->fetch()) {
				$topic = new stdClass();
				$topic->edge_id = $edge_id;
				$topic->tree_node_id = $tree_node_id;
				$topic->name = $code;
				$topic->description = $title;
				$topic->chapterSkill = $chapterSkill;
				$topic->quesCount = $quesCount;
				$topic->duration = $duration;
				$topic->thumnailImg = $thumnailImg;
				$topic->sequence_no = $sequence_no;
				array_push($topicArr,$topic);
			}
			 return $topicArr;
			
		  }	
		  
	public function getScenarioByChapterId($tree_node_id){
		$con = createConnection();
		$topicArr = array();
		
		
		$stmt = $con->prepare("SELECT `component_id`,`component_edge_id`,`parent_edge_id`,`scenario_type`,`scenario_subtype`,`scenario_name`,`scenario_description`,`scenario_duration` ,`scenario_image`,`thumbnailImg`,`component_description`,`sequence_no` FROM `tbl_component` WHERE `scenario_type`='Activity' AND `parent_edge_id`=? AND `status`=1");
		$stmt->bind_param("i",$tree_node_id);
		$stmt->execute();
		$stmt->bind_result($component_id,$component_edge_id,$parent_edge_id,$scenario_type,$scenario_subtype,$scenario_name,$scenario_description,$scenario_duration,$scenario_image,$thumbnailImg,$component_description,$sequence_no);		
		while($stmt->fetch()) {
			$topic1 = new stdClass();
			$topic1->component_id = $component_id;
			$topic1->component_edge_id = $component_edge_id;
			$topic1->parent_edge_id = $parent_edge_id;
			$topic1->scenario_type = $scenario_type;
			$topic1->scenario_subtype = $scenario_subtype;
			$topic1->scenario_name = $scenario_name;
			$topic1->scenario_description = $scenario_description;
			$topic1->scenario_duration = $scenario_duration;
			$topic1->scenario_image = $scenario_image;
			$topic1->thumbnailImg = $thumbnailImg;
			$topic1->component_description = $component_description;
			$topic1->sequence_no = $sequence_no;
			array_push($topicArr,$topic1);
		}
		$stmt->close();

		$stmt = $con->prepare("SELECT `component_id`,`component_edge_id`,`parent_edge_id`,`scenario_type`,
	     `scenario_subtype`,`scenario_name`,`scenario_description`,`scenario_duration`
	     ,`scenario_image`, `thumbnailImg`,`component_description`,`sequence_no` FROM `tbl_component` WHERE `scenario_subtype`='Role-play' AND `parent_edge_id`=? AND `status`=1");
		$stmt->bind_param("i",$tree_node_id);
		$stmt->execute();
		$stmt->bind_result($component_id,$component_edge_id,$parent_edge_id,$scenario_type,$scenario_subtype,$scenario_name,$scenario_description,$scenario_duration,$scenario_image,$thumbnailImg,$component_description,$sequence_no);		
		while($stmt->fetch()) {
			$topic2 = new stdClass();
			$topic2->component_id = $component_id;
			$topic2->component_edge_id = $component_edge_id;
			$topic2->parent_edge_id = $parent_edge_id;
			$topic2->scenario_type = $scenario_type;
			$topic2->scenario_subtype = $scenario_subtype;
			$topic2->scenario_name = $scenario_name;
			$topic2->scenario_description = $scenario_description;
			$topic2->scenario_duration = $scenario_duration;
			$topic2->scenario_image = $scenario_image;
			$topic2->thumbnailImg = $thumbnailImg;
			$topic2->component_description = $component_description;
			$topic2->sequence_no = $sequence_no;
			array_push($topicArr,$topic2);
		}
		$stmt->close();
		
		$stmt = $con->prepare("SELECT `component_id`,`component_edge_id`,`parent_edge_id`,`scenario_type`,`scenario_subtype`,`scenario_name`,`scenario_description`,`scenario_duration` ,`scenario_image`,`timeleft_warn`,`isQuesRand`,`isAnsRand`,`is_show_feedback`,`thumbnailImg`,`component_description`,`sequence_no` FROM `tbl_component` WHERE `scenario_subtype`='Quiz' AND `parent_edge_id`=? AND `status`=1");
		$stmt->bind_param("i",$tree_node_id);
		$stmt->execute();
		$stmt->bind_result($component_id,$component_edge_id,$parent_edge_id,$scenario_type,$scenario_subtype,$scenario_name,$scenario_description,$scenario_duration,$scenario_image,$timeleft_warn,$isQuesRand,$isAnsRand,$is_show_feedback,$thumbnailImg,$component_description,$sequence_no);
		$stmt->execute();
		while($stmt->fetch()) {
			$topic3 = new stdClass();
			$topic3->component_id = $component_id;
			$topic3->component_edge_id = $component_edge_id;
			$topic3->parent_edge_id = $parent_edge_id;
			$topic3->scenario_type = $scenario_type;
			$topic3->scenario_subtype = $scenario_subtype;
			$topic3->scenario_name = $scenario_name;
			$topic3->scenario_description = $scenario_description;
			$topic3->scenario_duration = $scenario_duration;
			$topic3->scenario_image = $scenario_image;
			$topic3->timeleft_warn=$timeleft_warn;
			$topic3->isQuesRand=$isQuesRand;
			$topic3->isAnsRand=$isAnsRand;
			$topic3->is_show_feedback=$is_show_feedback;
			$topic3->thumbnailImg = $thumbnailImg;
			$topic3->component_description = $component_description;
			$topic3->sequence_no = $sequence_no;
			array_push($topicArr,$topic3);
		}
		$stmt->close();
		
		$stmt = $con->prepare("SELECT `component_id`,`component_edge_id`,`parent_edge_id`,`scenario_type`,`scenario_subtype`,`scenario_name`,`scenario_description`,`scenario_duration` ,`scenario_image`,`thumbnailImg`,`component_description`,`sequence_no` FROM `tbl_component` WHERE `scenario_subtype`='Conversation Practice' AND `parent_edge_id`=? AND `status`=1");
		$stmt->bind_param("i",$tree_node_id);
		$stmt->execute();
		$stmt->bind_result($component_id,$component_edge_id,$parent_edge_id,$scenario_type,$scenario_subtype,$scenario_name,$scenario_description,$scenario_duration,$scenario_image,$thumbnailImg,$component_description,$sequence_no);
		$stmt->execute();
		while($stmt->fetch()) {
			$topic4 = new stdClass();
			$topic4->component_id = $component_id;
			$topic4->component_edge_id = $component_edge_id;
			$topic4->parent_edge_id = $parent_edge_id;
			$topic4->scenario_type = $scenario_type;
			$topic4->scenario_subtype = $scenario_subtype;
			$topic4->scenario_name = $scenario_name;
			$topic4->scenario_description = $scenario_description;
			$topic4->scenario_duration = $scenario_duration;
			$topic4->scenario_image = $scenario_image;
			$topic4->thumbnailImg = $thumbnailImg;
			$topic4->component_description = $component_description;
			$topic4->sequence_no = $sequence_no;
			array_push($topicArr,$topic4);
		}
		$stmt->close();
		
        $stmt = $con->prepare("SELECT `component_id`,`component_edge_id`,`parent_edge_id`,`scenario_type`,`scenario_subtype`,`scenario_name`,`scenario_description`,`scenario_duration`,`scenario_image`,`thumbnailImg`,`component_description`,`sequence_no` FROM `tbl_component` WHERE `scenario_type`='Concept' AND `parent_edge_id`=? AND `status`=1");
		$stmt->bind_param("i",$tree_node_id);
		$stmt->execute();
		$stmt->bind_result($component_id,$component_edge_id,$parent_edge_id,$scenario_type,$scenario_subtype,$scenario_name,$scenario_description,$scenario_duration,$scenario_image,$thumbnailImg,$component_description,$sequence_no);	
		while($stmt->fetch()) {
			$topic5 = new stdClass();
			$topic5->component_id = $component_id;
			$topic5->component_edge_id = $component_edge_id;
			$topic5->parent_edge_id = $parent_edge_id;
			$topic5->scenario_type = $scenario_type;
			$topic5->scenario_subtype = $scenario_subtype;
			$topic5->scenario_name = $scenario_name;
			$topic5->scenario_description = $scenario_description;
			$topic5->scenario_duration = $scenario_duration;
			$topic5->scenario_image = $scenario_image;
			$topic5->thumbnailImg = $thumbnailImg;
			$topic5->component_description = $component_description;
			$topic5->sequence_no = $sequence_no;
			array_push($topicArr,$topic5);
		}
		$stmt->close();

		
		$stmt = $con->prepare("SELECT `component_id`,`component_edge_id`,`parent_edge_id`,`scenario_type`,`scenario_subtype`,`scenario_name`,`scenario_description`,`scenario_duration` ,`scenario_image`,`thumbnailImg`,`component_description`,`sequence_no`,`interactive_html` FROM `tbl_component` WHERE `scenario_subtype`='Game' AND `parent_edge_id`=? AND `status`=1");
		$stmt->bind_param("i",$tree_node_id);
		$stmt->execute();
		$stmt->bind_result($component_id,$component_edge_id,$parent_edge_id,$scenario_type,$scenario_subtype,$scenario_name,$scenario_description,$scenario_duration,$scenario_image,$thumbnailImg,$component_description,$sequence_no,$interactive_html);
		$stmt->execute();		
		while($stmt->fetch()) {
			$topic6 = new stdClass();
			$topic6->component_id = $component_id;
			$topic6->component_edge_id = $component_edge_id;
			$topic6->parent_edge_id = $parent_edge_id;
			$topic6->scenario_type = $scenario_type;
			$topic6->scenario_subtype = $scenario_subtype;
			$topic6->scenario_name = $scenario_name;
			$topic6->scenario_description =$scenario_description;//htmlspecialchars($scenario_description);
			$topic6->scenario_duration = $scenario_duration;
			$topic6->scenario_image = $scenario_image;
			$topic6->thumbnailImg = $thumbnailImg;
			$topic6->component_description = $component_description;
			$topic6->sequence_no = $sequence_no;
			$topic6->interactive_html = $interactive_html;
			
			array_push($topicArr,$topic6);
		}
		$stmt->close();
		
		$stmt = $con->prepare("SELECT `component_id`,`component_edge_id`,`parent_edge_id`,`scenario_type`,`scenario_subtype`,`scenario_name`,`scenario_description`,`scenario_duration`,`scenario_image`, wordCount, instruction, para, json,scale,`thumbnailImg`,`component_description`,`sequence_no` FROM `tbl_component` WHERE `scenario_subtype`='SpeedReading' AND `parent_edge_id`=? AND `status`=1");
		$stmt->bind_param("i",$tree_node_id);
		$stmt->execute();
		$stmt->bind_result($component_id,$component_edge_id,$parent_edge_id,$scenario_type,$scenario_subtype,$scenario_name,$scenario_description,$scenario_duration, $scenario_image, $wc, $ins, $para, $json,$scale,$thumbnailImg,$component_description,$sequence_no);
		$stmt->execute();		
		while($stmt->fetch()) {
			$topic7 = new stdClass();
			$topic7->component_id = $component_id;
			$topic7->component_edge_id = $component_edge_id;
			$topic7->parent_edge_id = $parent_edge_id;
			$topic7->scenario_type = $scenario_type;
			$topic7->scenario_subtype = $scenario_subtype;
			$topic7->scenario_name = $scenario_name;
			$topic7->scenario_description = $scenario_description;
			$topic7->scenario_duration = $scenario_duration;
			$topic7->scenario_image = $scenario_image;
			$topic7->thumbnailImg = $thumbnailImg;
			$topic7->component_description = $component_description;
			$topic7->sequence_no = $sequence_no;
			
			$topic7->wordCount = $wc;
			$topic7->instruction = $ins;
			$topic7->para = $para;
			$topic7->json = $json;
			$topic7->scale=$scale;
				
			array_push($topicArr,$topic7);
		}
		$stmt->close();

		$stmt = $con->prepare("SELECT `component_id`,`component_edge_id`,`parent_edge_id`,`scenario_type`,`scenario_subtype`,`scenario_name`,`scenario_description`,`scenario_duration`,`scenario_image`, wordCount, instruction, para, json, file,`thumbnailImg`,`component_description`,`sequence_no` FROM `tbl_component` WHERE `scenario_subtype`='SpeechRecognition' AND `parent_edge_id`=? AND `status`=1");
		$stmt->bind_param("i",$tree_node_id);
		$stmt->execute();
		$stmt->bind_result($component_id,$component_edge_id,$parent_edge_id,$scenario_type,$scenario_subtype,$scenario_name,$scenario_description,$scenario_duration,$scenario_image, $wc, $ins, $para, $json, $file,$thumbnailImg,$component_description,$sequence_no);
		$stmt->execute();		
		while($stmt->fetch()) {
			$topic8 = new stdClass();
			$topic8->component_id = $component_id;
			$topic8->component_edge_id = $component_edge_id;
			$topic8->parent_edge_id = $parent_edge_id;
			$topic8->scenario_type = $scenario_type;
			$topic8->scenario_subtype = $scenario_subtype;
			$topic8->scenario_name = $scenario_name;
			$topic8->scenario_description = $scenario_description;
			$topic8->scenario_duration = $scenario_duration;
			$topic8->scenario_image = $scenario_image;
			$topic8->thumbnailImg = $thumbnailImg;
			$topic8->component_description = $component_description;
			$topic8->sequence_no = $sequence_no;
			
			$topic8->wordCount = $wc;
			$topic8->instruction = $ins;
			$topic8->para = $para;
			$topic8->json = $json;
			$topic8->file = $file;
			
			array_push($topicArr,$topic8);
		}
		$stmt->close();
		   
		$stmt = $con->prepare("SELECT `component_id`,`component_edge_id`,`parent_edge_id`,`scenario_type`,`scenario_subtype`,`scenario_name`,`scenario_description`,`scenario_image`, instruction, file,`thumbnailImg`,`component_description`,`sequence_no` FROM `tbl_component` WHERE `scenario_subtype`='Resources' AND `parent_edge_id`=? AND `status`=1");
		$stmt->bind_param("i",$tree_node_id);
		$stmt->execute();
		$stmt->bind_result($component_id,$component_edge_id,$parent_edge_id,$scenario_type,$scenario_subtype,$scenario_name,$scenario_description,$scenario_image,$ins, $file,$thumbnailImg,$component_description,$sequence_no);
		$stmt->execute();		
		while($stmt->fetch()) {
			$topic9 = new stdClass();
			$topic9->component_id = $component_id;
			$topic9->component_edge_id = $component_edge_id;
			$topic9->parent_edge_id = $parent_edge_id;
			$topic9->scenario_type = $scenario_type;
			$topic9->scenario_subtype = $scenario_subtype;
			$topic9->scenario_name = $scenario_name;
			$topic9->scenario_description = $scenario_description;
			$topic9->thumbnailImg = $thumbnailImg;
			$topic9->component_description = $component_description;
			$topic9->sequence_no = $sequence_no;
			
			$topic9->instruction = $ins;
			$topic9->file = $file;
			
			array_push($topicArr,$topic9);
		}   
		$stmt->close();

		$stmt = $con->prepare("SELECT `component_id`,`component_edge_id`,`parent_edge_id`,`scenario_type`,`scenario_subtype`,`scenario_name`,`scenario_description` ,instruction,`thumbnailImg`,`component_description`,`sequence_no` FROM `tbl_component` WHERE `scenario_subtype`='Conversation Video' AND `parent_edge_id`=? AND `status`=1");
		$stmt->bind_param("i",$tree_node_id);
		$stmt->execute();
		$stmt->bind_result($component_id,$component_edge_id,$parent_edge_id,$scenario_type,$scenario_subtype,$scenario_name,$scenario_description, $ins,$thumbnailImg,$component_description,$sequence_no);
		$stmt->execute();		
		while($stmt->fetch()) {
			$topic10 = new stdClass();
			$topic10->component_id = $component_id;
			$topic10->component_edge_id = $component_edge_id;
			$topic10->parent_edge_id = $parent_edge_id;
			$topic10->scenario_type = $scenario_type;
			$topic10->scenario_subtype = $scenario_subtype;
			$topic10->scenario_name = $scenario_name;
			$topic10->scenario_description = $scenario_description;
			$topic10->thumbnailImg = $thumbnailImg;
			$topic10->component_description = $component_description;
			$topic10->sequence_no = $sequence_no;
			
			$topic10->instruction = $ins;
			array_push($topicArr,$topic10);
		}     
		$stmt->close();
		
		//echo '<pre>';print_r($topicArr);exit;	
	   
		return $topicArr;
	} 
	
	 public function getComponentId($ctid){//component_id as getScenarioByChapterId for topic
      
        $data = array();
        if(!is_numeric($ctid)){
            return $data;
        }
        $sql = " select * from tbl_component_data WHERE component_id = :id ";
        $stmt = $this->dbConn->prepare($sql);   
        
        $stmt->bindValue(':id', $ctid, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
	    $stmt->closeCursor();
        if(count($RESULT) > 0 ){
			return $RESULT;
		 }else{
			 return false;
		  } 
       // $data = array_shift( $RESULT );
        //return $RESULT;
        
    }
	 public function getRegionConfig($host){//region info
      
      
       $sql='Select * from tblx_region where domain_url =:host';

        $stmt = $this->dbConn->prepare($sql);   
        
        $stmt->bindValue(':host', $host, PDO::PARAM_STR);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
	    $stmt->closeCursor();
        if(count($RESULT) > 0 ){
			return $RESULT;
		 }else{
			 return false;
		  } 
      
    }
	
	public function getQuizQuestionData($edgeId){//Get question by parent edge id( as component_id in tbl_component)
		
        $data = array();
        if(!is_numeric($edgeId)){
            return $data;
        }
        $sql = " select * from tbl_questions WHERE parent_edge_id = :id ";
        $stmt = $this->dbConn->prepare($sql);   
        // echo "<pre>";print_r($edgeId);exit;  
      
        $stmt->bindValue(':id', $edgeId, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
	    $stmt->closeCursor();
        if(count($RESULT) > 0 ){
			return $RESULT;
		 }else{
			 return false;
		  } 
		//echo "<pre>";print_r($RESULT);exit;  
		  
       // $data = array_shift( $RESULT );
        //return $RESULT;
        
    }
	public function getQuizQuestionDataSkill($edgeId){//Get question by parent edge id( as component_id in tbl_component)
		
        $data = array();
        if(!is_numeric($edgeId)){
            return $data;
        }
       $sql = "SELECT tq.*, trc.competency, tqr.compentency_id FROM tbl_questions AS tq
				JOIN tbl_questions_rubric AS tqr ON tqr.question_id=tq.id 
				JOIN tbl_rubric_competency AS trc ON trc.id=tqr.compentency_id WHERE parent_edge_id = :id";
			
        $stmt = $this->dbConn->prepare($sql);   
        // echo "<pre>";print_r($edgeId);exit;  
      
        $stmt->bindValue(':id', $edgeId, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
	    $stmt->closeCursor();
        if(count($RESULT) > 0 ){
			return $RESULT;
		 }else{
			 return false;
		  } 
		//echo "<pre>";print_r($RESULT);exit;  
		  
       // $data = array_shift( $RESULT );
        //return $RESULT;
        
    }
	 public function refreshtoken($login,$password){
                
                $serviceObj = new serviceController();
                
                $extra = array();
                $extra['appVersion'] = WEB_SERVICE_APP_VERSION;
                $extra['deviceId'] = WEB_SERVICE_DEVICE_ID;
                $extra['platform'] = WEB_SERVICE_PLATFORM;
                $params->login = $login;
				$params->password = $password;
				
				// echo "<pre>";print_r($params);
                $res = $serviceObj->processRequest('', 'refreshtoken', $params, $extra );
                $res_json = json_encode($res);
                $res = json_decode($res_json, true);
               //echo "<pre>";print_r($res);
                
                if(is_array($res) ){
                    $status = trim(strtolower($res['retCode']));
					
                    if($status == 'success'){
                       return  $res['retVal'];
                        
                    }else{
						return false;
					}
                }
                
     }
	 
	 public function getUserLogDataByServerId($user_id) {

        $sql = "select user_id,first_name,last_name from user where user_id = :uid ";
		  
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':uid', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $row = array();
        if (count($RESULT)) {
            $row = array_shift($RESULT);
        }
        $stmt->closeCursor();
        if (isset($row) && !empty($row) && count($row)) {
            return $row;
        }

        return array();
    }
	
	public function getUserBatchInfo($userID){
        
        $batch_sql = " AND bum.status = 1 "; // default 
      
       //$sql="SELECT  * FROM tblx_batch b JOIN tblx_batch_user_map bum ON bum.batch_id = b.batch_id AND bum.status = 1 WHERE bum.user_id = ".$user_id."  ORDER BY b.batch_id";
		$sql ="SELECT  * FROM tblx_batch b JOIN tblx_batch_user_map bum ON bum.batch_id = b.batch_id $batch_sql "
		. "WHERE bum.user_id = :userID  ORDER BY b.batch_id";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':userID', $userID, PDO::PARAM_INT);
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		return $RESULT;
	}
	
	 public function placementTracking($user_token,$product_id){
                
                $serviceObj = new serviceController();
                
                $extra = array();
                $extra['appVersion'] = WEB_SERVICE_APP_VERSION;
                $extra['deviceId'] = WEB_SERVICE_DEVICE_ID;
                $extra['platform'] = WEB_SERVICE_PLATFORM;
                $params->product_id = $product_id;
				// echo "<pre>";print_r($params);
                $res = $serviceObj->processRequest($user_token, 'checkPlacement', $params, $extra );
                $res_json = json_encode($res);
                $res = json_decode($res_json, true);
               //echo "<pre>";print_r($res);
                
                if(is_array($res) ){
                    $status = trim(strtolower($res['retCode']));
					
                    if($status == 'success'){
                       return  $res['retVal'];
                        
                    }else{
						return false;
					}
                }
                
    }
	public function placementTestTracking($user_id,$batch_id,$product_id,$region_id,$exam_type){
		    $whr = '';
			if($exam_type!=""){
			  $whr.= ' AND exam_type IN("'.$exam_type.'")';
			  
			 }
            $sql ="SELECT  * FROM tblx_placement_result WHERE user_id=:user_id AND batch_id=:batch_id AND product_id=:product_id AND region_id=:region_id $whr";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			$stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
			$stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
			$stmt->bindValue(':region_id', $region_id, PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			if(count($RESULT)>0){
				//echo "<pre>up";print_r($RESULT);//exit;
				return $RESULT[0];
			}else{
				return false;
			}
           
                
    }
	
	public function setTestScore($user_id,$batch_id,$product_id,$score,$total_ques,$time_spent,$level_assigned,$region_id,$testType){
		//echo "<pre>";print_r($testType);exit;
		if($testType=='post'){
			$sql ="SELECT  * FROM tblx_placement_result WHERE user_id=:user_id AND batch_id=:batch_id AND region_id=:region_id AND exam_type=:exam_type";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			$stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
			$stmt->bindValue(':exam_type', $testType, PDO::PARAM_STR);
			$stmt->bindValue(':region_id', $region_id, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			//echo "<pre>";print_r($RESULT);
			if(count($RESULT)>0){
				echo "<pre>up";print_r($RESULT);//exit;
				$sql = "update  tblx_placement_result SET score=:score,total_questions=:total_questions,date_attempted=NOW(),time_spent=:time_spent,level_assigned=:level_assigned WHERE user_id=:user_id AND batch_id=:batch_id AND region_id=:region_id AND exam_type=:exam_type";
				$stmt = $this->dbConn->prepare($sql);
				
				$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
				$stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
				//$stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
				$stmt->bindValue(':score', $score, PDO::PARAM_STR);
				$stmt->bindValue(':total_questions', $total_ques, PDO::PARAM_STR);			
				
				$stmt->bindValue(':time_spent', $time_spent, PDO::PARAM_INT);
				$stmt->bindValue(':level_assigned', $level_assigned, PDO::PARAM_STR);
				$stmt->bindValue(':exam_type', $testType, PDO::PARAM_STR);
				$stmt->bindValue(':region_id', $region_id, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor();
				return true;
			}else{
					echo "<pre>new";print_r($RESULT);//exit;
				$sql = "insert into tblx_placement_result (user_id,batch_id,product_id,score,total_questions,date_attempted,time_spent,level_assigned,exam_type,region_id) Values (:user_id,:batch_id, :product_id,:score,:total_questions,NOW(),:time_spent, :level_assigned,:exam_type,:region_id) ";
				$stmt = $this->dbConn->prepare($sql);
				
				$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
				$stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
				$stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
				$stmt->bindValue(':score', $score, PDO::PARAM_STR);
				$stmt->bindValue(':total_questions', $total_ques, PDO::PARAM_STR);			
				$stmt->bindValue(':time_spent', $time_spent, PDO::PARAM_INT);
				$stmt->bindValue(':level_assigned', $level_assigned, PDO::PARAM_STR);
				$stmt->bindValue(':exam_type', $testType, PDO::PARAM_STR);
				$stmt->bindValue(':region_id', $region_id, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor();
				return true;
			}
		
			
		}else{
			$testType="pre";
			$sql = "insert into tblx_placement_result (user_id,batch_id,product_id,score,total_questions,date_attempted,time_spent,level_assigned,exam_type,region_id) Values (:user_id,:batch_id, :product_id,:score,:total_questions,NOW(),:time_spent, :level_assigned,:exam_type,:region_id) ";
			$stmt = $this->dbConn->prepare($sql);
			
			$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			$stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
			$stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
			$stmt->bindValue(':score', $score, PDO::PARAM_STR);
			$stmt->bindValue(':total_questions', $total_ques, PDO::PARAM_STR);			
			$stmt->bindValue(':time_spent', $time_spent, PDO::PARAM_INT);
			$stmt->bindValue(':level_assigned', $level_assigned, PDO::PARAM_STR);
			$stmt->bindValue(':exam_type', $testType, PDO::PARAM_STR);
			$stmt->bindValue(':region_id', $region_id, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();
			return true;
		}
	}
		public function setTestScoreSkill($user_id,$batch_id,$product_id,$score,$total_ques,$time_spent,$level_assigned,$region_id,$testType,$skillArr){
		//echo "<pre>";print_r($testType);exit;
		$skill=json_encode($skillArr);
		if($testType=='post'){
			$sql ="SELECT  * FROM tblx_placement_result WHERE user_id=:user_id AND batch_id=:batch_id AND region_id=:region_id AND exam_type=:exam_type";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			$stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
			$stmt->bindValue(':exam_type', $testType, PDO::PARAM_STR);
			$stmt->bindValue(':region_id', $region_id, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			//echo "<pre>";print_r($RESULT);
			if(count($RESULT)>0){
				echo "<pre>up";print_r($RESULT);//exit;
				$sql = "update  tblx_placement_result SET score=:score,total_questions=:total_questions,date_attempted=NOW(),time_spent=:time_spent,level_assigned=:level_assigned WHERE user_id=:user_id AND batch_id=:batch_id AND region_id=:region_id AND exam_type=:exam_type,skill=:skill";
				$stmt = $this->dbConn->prepare($sql);
				
				$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
				$stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
				//$stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
				$stmt->bindValue(':score', $score, PDO::PARAM_STR);
				$stmt->bindValue(':total_questions', $total_ques, PDO::PARAM_STR);			
				
				$stmt->bindValue(':time_spent', $time_spent, PDO::PARAM_INT);
				$stmt->bindValue(':level_assigned', $level_assigned, PDO::PARAM_STR);
				$stmt->bindValue(':exam_type', $testType, PDO::PARAM_STR);
				$stmt->bindValue(':region_id', $region_id, PDO::PARAM_INT);
				$stmt->bindValue(':skill', $skill, PDO::PARAM_STR);
				$stmt->execute();
				$stmt->closeCursor();
				return true;
			}else{
					echo "<pre>new";print_r($RESULT);//exit;
				$sql = "insert into tblx_placement_result (user_id,batch_id,product_id,score,total_questions,date_attempted,time_spent,level_assigned,exam_type,region_id,skill) Values (:user_id,:batch_id, :product_id,:score,:total_questions,NOW(),:time_spent, :level_assigned,:exam_type,:region_id,:skill) ";
				$stmt = $this->dbConn->prepare($sql);
				
				$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
				$stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
				$stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
				$stmt->bindValue(':score', $score, PDO::PARAM_STR);
				$stmt->bindValue(':total_questions', $total_ques, PDO::PARAM_STR);			
				$stmt->bindValue(':time_spent', $time_spent, PDO::PARAM_INT);
				$stmt->bindValue(':level_assigned', $level_assigned, PDO::PARAM_STR);
				$stmt->bindValue(':exam_type', $testType, PDO::PARAM_STR);
				$stmt->bindValue(':region_id', $region_id, PDO::PARAM_INT);
				$stmt->bindValue(':skill', $skill, PDO::PARAM_STR);
				$stmt->execute();
				$stmt->closeCursor();
				return true;
			}
		
			
		}else{
			$testType="pre";
			$sql = "insert into tblx_placement_result (user_id,batch_id,product_id,score,total_questions,date_attempted,time_spent,level_assigned,exam_type,region_id,skill) Values (:user_id,:batch_id, :product_id,:score,:total_questions,NOW(),:time_spent, :level_assigned,:exam_type,:region_id,:skill) ";
			$stmt = $this->dbConn->prepare($sql);
			
			$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			$stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
			$stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
			$stmt->bindValue(':score', $score, PDO::PARAM_STR);
			$stmt->bindValue(':total_questions', $total_ques, PDO::PARAM_STR);			
			$stmt->bindValue(':time_spent', $time_spent, PDO::PARAM_INT);
			$stmt->bindValue(':level_assigned', $level_assigned, PDO::PARAM_STR);
			$stmt->bindValue(':exam_type', $testType, PDO::PARAM_STR);
			$stmt->bindValue(':region_id', $region_id, PDO::PARAM_INT);
			$stmt->bindValue(':skill', $skill, PDO::PARAM_STR);
			$stmt->execute();
			$stmt->closeCursor();
			return true;
		}
	}
	
	public function getCourseByEdgeId($edge_id){
		$con1 = createConnection1();
		$stmt = $con->prepare("SELECT c.course_id FROM generic_mpre_tree gmt
								JOIN course c ON c.tree_node_id = gmt.tree_node_id
								WHERE  gmt.edge_id IN(SELECT  edge_id FROM  generic_mpre_tree WHERE  edge_id IN(SELECT  tree_node_super_root FROM  generic_mpre_tree WHERE  edge_id=?))");
		$stmt->bind_param("i",$edge_id);
		$stmt->execute();
		$stmt->bind_result($course_id);
		$stmt->fetch();
		$stmt->close();
		return $course_id;
	
	}

		
	 public function getQuizQuestionById($qid){//Get question by question id
		
        $data = array();
        if(!is_numeric($qid)){
            return $data;
        }
        $sql = " select * from tbl_questions WHERE id = :id ";
        $stmt = $this->dbConn->prepare($sql);   
        // echo "<pre>";print_r($edgeId);exit;  
      
        $stmt->bindValue(':id', $qid, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
	    $stmt->closeCursor();
        if(count($RESULT) > 0 ){
			return $RESULT[0];
		 }else{
			 return false;
		  } 
		//echo "<pre>";print_r($RESULT);exit;  
		  
       // $data = array_shift( $RESULT );
        //return $RESULT;
        
    }
	

 public function checkQuizAns($qid, $ans,$q_time_taken){
        // check whether answer is correct or not & what is the correct answer
        $ans = trim($ans);
		//echo $qid;
        $ans_data = $this->getQuizQuestionById($qid);
         //print_r($ans_data);exit;
      if(count($ans_data)){
		    $correct_ans = array();
		    $optionArr=array();
	        $optionFileArr=array();
		    $ques_format =trim($ans_data['question_type']);
			$optionArr[] = $ans_data['option_1'];
			$optionArr[] = $ans_data['option_2'];
			$optionArr[] = $ans_data['option_3'];
			$optionArr[] = $ans_data['option_4'];
			$optionArr[] = $ans_data['option_5'];
			$optionArr[] = $ans_data['option_6'];
			$optionArr[] = $ans_data['option_7'];
			$optionArr[] = $ans_data['option_8'];
			
			$optionFileArr[] = $ans_data['option1_file'];
			$optionFileArr[] = $ans_data['option2_file'];
			$optionFileArr[] = $ans_data['option3_file'];
			$optionFileArr[] = $ans_data['option4_file'];
			$optionFileArr[] = $ans_data['option5_file'];
			$optionFileArr[] = $ans_data['option6_file'];
			$optionFileArr[] = $ans_data['option7_file'];
			$optionFileArr[] = $ans_data['option8_file'];
			
			$isPractice=$ans_data['isPractice'];
			$optionArr= array_filter($optionArr, 'strlen'); 
			$optionFileArr= array_filter($optionFileArr, 'strlen');
			 
			 $option_feedback=$ans_data['option_feedback'];
			 $option_feedback = displayText($option_feedback);
			 $feedback=json_decode($option_feedback);	
			
			$feedbackArr=array();
			$feedbackArr[1] = $feedback->option1_feedback;
			$feedbackArr[2] = $feedback->option2_feedback;
			$feedbackArr[3] = $feedback->option3_feedback;
			$feedbackArr[4] = $feedback->option4_feedback;
			$feedbackArr[5] = $feedback->option5_feedback;
			$feedbackArr[6] = $feedback->option6_feedback;
			$feedbackArr[7] = $feedback->option7_feedback;
			$feedbackArr[8] = $feedback->option8_feedback;
			$feedbackArr=array_filter($feedbackArr, 'strlen');
			 // print_r($optionArr);  
			 //print_r($feedbackArr); 
             $evaluation_type=$ans_data['evaluation_type'];
			 $evaluation_subtype=$ans_data['evaluation_subtype'];
			  //print_r($ans_data);  
                     //trim(strtolower($ans_data['question_type']));                
               if($ques_format == 'FB-TT-AU'){
                 // print_r($ans_data);  
					$searchForValue = '^';
					$correct_ans = $ans_data['correct_answer'];
					if( strpos($correct_ans, $searchForValue) !== false ) {
						//$correct_ans = str_replace("^", ",",$correct_ans);
						$correct_arr=explode('^',$correct_ans);
						$correct_ansValue=array();
						foreach($correct_arr as $key=>$o){
						  $correct_ansValue[]= $o;
						}
						$correct_ansValue=implode('^',$correct_ansValue);
					}else{
						$correct_ansValue=$correct_ans;
					}
					
                    if(count($correct_ans)){
                        $rarr['status'] = 1;
                        $rarr['correct_ans'] = $correct_ansValue;
						$rarr['taken_time'] = $q_time_taken;
						$rarr['feedback'] = !empty($feedbackArr[2])?$feedbackArr[2]:'';
                        $rarr['is_correct'] = 0; 
						//  print_r($ans);
						  //  print_r($correct_ans);exit;
					  if( strcasecmp($ans, displayText($correct_ans)) === 0){
                        //if( $ans == $correct_ans){
                            $rarr['is_correct'] = 1;
							$rarr['feedback'] = !empty($feedbackArr[1])?$feedbackArr[1]:'';
                        }

                    }
                    
                }

               
                if($ques_format == 'RA-TT-AU' ){
					//print_r($ans_data);
                    
                    if(count($ans)){
                        $rarr['status'] = 1;
						$rarr['taken_time'] = $q_time_taken;
						$rarr['feedback'] = $feedbackArr;
                        $rarr['is_correct'] = 0; 
						if($evaluation_type==1){
						if($ans >=90){
							  $rarr['is_correct'] =1;
							  $rarr['correct_ans']="Perfect";
							}else if($ans>=70 && $ans<=89){
							    $rarr['is_correct'] =1;
								$rarr['correct_ans']="Good & Intelligible";
							 }else if($ans>=56 && $ans<=69){				
								$rarr['is_correct'] =0;
								$rarr['correct_ans']="It sounds ok";
							}else if($ans>=1 && $ans<=55){
								$rarr['is_correct'] =0;
								$rarr['correct_ans']="That doesn't sound good";
							}else{
								$rarr['is_correct'] =0;
								$rarr['correct_ans']="That doesn't sound good";
							}
						
                       }else{
						   $rarr['is_correct'] =1;
						   $rarr['correct_ans']="";
					   }	
                    }				 
                }
			 if($ques_format == 'EW-TT-AU' ){
					//print_r($ans_data);
					if(count($ans)){
                        $rarr['status'] = 1;
                        $rarr['correct_ans'] = '';
						$rarr['taken_time'] = $q_time_taken;
						$rarr['feedback'] = $feedbackArr;
                        $rarr['is_correct'] = 1;

                      
                    }
               }
			 
               /*  if($ques_format == 'DD-TT-AU' ){
					//print_r($ans_data);
				 $correct_ans = $ans_data['correct_answer'];
					$correct_ans = str_replace(";", ",",$correct_ans);
					$correct_arr=explode(',',$correct_ans);
					$correct_ansValue=array();
					foreach($correct_arr as $o){
						  $correct_ansValue[]= $o;
					}
					$correct_ansValue=implode(',',$correct_ansValue);
					if(count($correct_ans)){
                        $rarr['status'] = 1;
                        $rarr['correct_ans'] = $correct_ansValue;
						$rarr['taken_time'] = $q_time_taken;
                        $rarr['is_correct'] = 0; 
                        if( $ans == $correct_ans){
                            $rarr['is_correct'] = 1;
                        }
                    }
					
					
                }//end dd template */


			 
          if(count($optionArr) ){		   
             if($ques_format == 'MC-TT-AU'){
                    // correct answer 
					$correct_ans = $ans_data['correct_answer'];
					$i=1;
                    foreach ($optionArr as $key=>$ao){
                        if($correct_ans == $i){
                            $correct_ansValue = $ao;
							$feedback=$feedbackArr[$ans];
                        }else{
							if( $ans == $i){
							$feedback=$feedbackArr[$ans];
							}
						}
                     $i++;
					}
                    if(count($correct_ans)){
                        $rarr['status'] = 1;
                        $rarr['correct_ans'] = $correct_ans;//$correct_ansValue;
						$rarr['taken_time'] = $q_time_taken;
						$rarr['feedback'] = !empty($feedback)?$feedback:'';
                        $rarr['is_correct'] = 0; 
						
                        if( $ans == $correct_ans){
                            $rarr['is_correct'] = 1;
                        }
                    }
                }
				
			if($ques_format == 'MC-TI-AU' ){
				   
                    // correct answer 
					$correct_ans = $ans_data['correct_answer'];
					$i=1;
                    foreach ($optionArr as $key=>$ao){
                        if($correct_ans == $i){
                            $correct_ansValue = $ao;
							$feedback=$feedbackArr[$ans];
                        }else{
							if( $ans == $i){
							$feedback=$feedbackArr[$ans];
							}
						}
                     $i++;
					}
					//print_r($correct_ansValue);
                    if(count($correct_ans)){
                        $rarr['status'] = 1;
                        $rarr['correct_ans'] =$correct_ans;//$correct_ansValue;
						$rarr['taken_time'] = $q_time_taken;
						$rarr['feedback'] = !empty($feedback)?$feedback:'';
                        $rarr['is_correct'] = 0; 
                        if( $ans == $correct_ans){
                            $rarr['is_correct'] = 1;
                        }
                    }
                }	
			
				if($ques_format == 'MC-TA-AU' ){
                    // correct answer 
					$correct_ans = $ans_data['correct_answer'];
					$i=1;
                    foreach ($optionArr as $key=>$ao){
                        if($correct_ans == $i){
                            $correct_ansValue = $ao;
							$feedback=$feedbackArr[$ans];
                        }else{
							if( $ans == $i){
							$feedback=$feedbackArr[$ans];
							}
						}
                     $i++;
					}

                    if(count($correct_ans)){
                        $rarr['status'] = 1;
                        $rarr['correct_ans'] = $correct_ans;//$correct_ansValue;
						$rarr['taken_time'] = $q_time_taken;
						$rarr['feedback'] = !empty($feedback)?$feedback:'';
                        $rarr['is_correct'] = 0; 
                        if( $ans == $correct_ans){
                            $rarr['is_correct'] = 1;
                        }
                    }
                }	
				
			if($ques_format == 'MMC-TT-AU' ){
				 //print_r($ans_data);
                    // correct answer 
					$correct_ans = $ans_data['correct_answer'];
					$correct_ans = str_replace(' ', '', $correct_ans);
					$correct_arr=explode(',',$correct_ans);

					$j=0;
					$correct_ansValue=array();
					foreach($correct_arr as $o){
						$i=1;
						 foreach($optionArr as $key=>$ao){
							if($correct_arr[$j]==$i){
							   $correct_ansValue[]= $ao;
							  }
							 // print_r($correct_arr[$j]);

							$i++;
						 } 
                        $j++;
					}
					
					$ans_arr=explode(',',$ans);
					$k=1;
					$feedback=array();
					$result = array_diff($ans_arr, $correct_arr);
					 //print_r(result);
					 foreach($result as $key=>$feedValue){
						 $key=$key+1;
						 $feedback[]=$feedbackArr[$key];

						/*  if($feedbackArr[$k]==$result1){
							  print_r($feedbackArr[$k]);
					      $feedback[]=$feedbackArr[$k];
						  
						 }	 */		
                      $k++;						
					} 
					
					//$feedback=$feedbackArr[$result];				
						  
					$correct_ansValue=implode(',',$correct_ansValue);
					// print_r($feedback);

                    if(count($correct_ans)){
                        $rarr['status'] = 1;
                        $rarr['correct_ans'] =$correct_ans; //$correct_ansValue;
						$rarr['taken_time'] = $q_time_taken;
					    $rarr['feedback'] = !empty($feedback)?$feedback:'';
						//$rarr['feedback'] = !empty($feedbackArr[1])?$feedbackArr[1]:'';
                        $rarr['is_correct'] = 0; 
					  //if( strcmp($ans, $correct_ans) === 0){
                        if( $ans == $correct_ans){
                            $rarr['is_correct'] = 1;
							$rarr['feedback'] = !empty($feedback)?$feedback:'';
							//$rarr['feedback'] = !empty($feedbackArr[0])?$feedbackArr[0]:'';
                        }
                    }
                }		
			if($ques_format == 'MT-TT-AU'){
					$i=1;
					 // print_r($ans_data);
					 //print_r($optionFileArr);
					// $arr_rand = $optionFileArr; 
					// $correct_arr=implode(',',$arr_rand);
					 $correct_ansValue=array();
					 $correct_ans=array();
					 $feedback=array();
					 $j=1;
					 foreach($optionFileArr as $key=>$o){
					   $correct_ansValue[]= $o;
					   $correct_ans[]= $j;
					   $feedback[]=$feedbackArr[$j];
					   $j++;
					 }
					  //  echo ($ans);print_r($correct_ansValue);exit;
				   /*  $correct_ans = $ans_data['correct_answer'];
					$correct_arr=explode(',',$correct_ans);
					$j=0;
					$correct_ansValue=array();
					foreach($correct_arr as $o){
						$i=1;
						 foreach($optionFileArr as $ao){
							if($correct_arr[$j]==$i){
							  $correct_ansValue[]= $ao;
							 // print_r($correct_arr[$j]);
							}
							$i++;
						 } 
                        $j++;
                    
					} */
					 
				  
				 
					$correct_ansValue=implode(', ',$correct_ansValue);
					 // print_r($correct_ansValue);
					  $correct_ans=implode(',',$correct_ans);
					  // print_r($ans);
					  	 // print_r($correct_ans);
                    if(count($correct_ansValue)){
                        $rarr['status'] = 1;
                        $rarr['correct_ans'] =$correct_ans; //$correct_ansValue;
						$rarr['taken_time'] = $q_time_taken;
						$rarr['feedback'] = !empty($feedbackArr[2])?$feedbackArr[2]:'';
                        $rarr['is_correct'] = 0; 
                        //if( $ans == $correct_ansValue){
					  if( $ans == $correct_ans){
                            $rarr['is_correct'] = 1;
							$rarr['feedback'] = !empty($feedbackArr[1])?$feedbackArr[1]:'';
                        }
                    }
                }	
					
		 	if($ques_format == 'MT-TI-AU' ){
                    $i=1;
					 // print_r($ans_data);
					 //print_r($optionFileArr);
					// $arr_rand = $optionFileArr; 
					// $correct_arr=implode(',',$arr_rand);
					 $correct_ansValue=array();
					 $correct_ans=array();
					  $feedback=array();
					 $j=1;
					 foreach($optionFileArr as $key=>$o){
					   $correct_ansValue[]= $o;
					   $correct_ans[]= $j;
					    $feedback[]=$feedbackArr[$j];
					   $j++;
					 }

					$correct_ansValue=implode(', ',$correct_ansValue);
					 // print_r($correct_ansValue);
					  $correct_ans=implode(',',$correct_ans);
					  // print_r($ans);
					  	 // print_r($correct_ans);
                    if(count($correct_ansValue)){
                        $rarr['status'] = 1;
                        $rarr['correct_ans'] =$correct_ans; //$correct_ansValue;
						$rarr['taken_time'] = $q_time_taken;
						$rarr['feedback'] = !empty($feedbackArr[2])?$feedbackArr[2]:'';
                        $rarr['is_correct'] = 0; 
                        //if( $ans == $correct_ansValue){
					  if( $ans == $correct_ans){
                            $rarr['is_correct'] = 1;
							$rarr['feedback'] = !empty($feedbackArr[1])?$feedbackArr[1]:'';
                        }
                    }
                }	
					
			if($ques_format == 'MT-II-AU' ){
                  	$i=1;
					 // print_r($ans_data);
					 //print_r($optionFileArr);
					// $arr_rand = $optionFileArr; 
					// $correct_arr=implode(',',$arr_rand);
					 $correct_ansValue=array();
					 $correct_ans=array();
					 $feedback=array();
					 $j=1;
					 foreach($optionFileArr as $key=>$o){
					   $correct_ansValue[]= $o;
					   $correct_ans[]= $j;
					   $feedback[]=$feedbackArr[$j];
					   $j++;
					 }
					
					$correct_ansValue=implode(', ',$correct_ansValue);
					 // print_r($correct_ansValue);
					  $correct_ans=implode(',',$correct_ans);
					  // print_r($ans);
					  	 // print_r($correct_ans);
                    if(count($correct_ansValue)){
                        $rarr['status'] = 1;
                        $rarr['correct_ans'] =$correct_ans; //$correct_ansValue;
						$rarr['taken_time'] = $q_time_taken;
						$rarr['feedback'] = !empty($feedbackArr[2])?$feedbackArr[2]:'';
                        $rarr['is_correct'] = 0; 
                        //if( $ans == $correct_ansValue){
					  if( $ans == $correct_ans){
                            $rarr['is_correct'] = 1;
							$rarr['feedback'] = !empty($feedbackArr[1])?$feedbackArr[1]:'';
                        }
                    }
                } 
			if($ques_format == 'MTF-TT-AU' ){
                    // correct answer 
					$correct_ans = $ans_data['correct_answer'];
					$i=1;
                    foreach ($optionArr as $ao){
                        if($correct_ans == $i){
                            $correct_ansValue = $ao;
                        }
                     $i++;
					}

                    if(count($correct_ans)){
                        $rarr['status'] = 1;
                        $rarr['correct_ans'] = $correct_ansValue;
						$rarr['taken_time'] = $q_time_taken;
						$rarr['feedback'] = !empty($feedbackArr[2])?$feedbackArr[2]:'';
                        $rarr['is_correct'] = 0; 
                        if( $ans == $correct_ans){
                            $rarr['is_correct'] = 1;
							$rarr['feedback'] = !empty($feedbackArr[1])?$feedbackArr[1]:'';
                       
                        }
                    }
                }//mt template
          
               if($ques_format == 'DD-TT-AU' ){
					//print_r($ans_data);
                    // correct answer 
                   $order_arr = array();
					
					 $arr_rand = $optionArr; 
					
					  //$correct_ansValue=implode(',',$arr_rand);
					  $j=0;
                    foreach ($arr_rand as $ao){
                        $order_arr[$j] = $ao;
                       $j++;
					}
                   // ksort($order_arr);
                    $content = '';
                    $content_trimmed = '';
                    $i=0;
                   // print_r($order_arr);
                    foreach($order_arr as $o){
						
                        $content_trimmed .= trim($o);
                        if($i != 0){
                            $content .= ' ';
                        }
                        $content .= trim($o);
                        $i++;
                    }
					 // print_r($content_trimmed);
					  // print_r($content);
                    $correct_ans = $content; 
					$ans_val=explode(',',$ans);
				  $ans_match=implode('',$ans_val);
				  $ans_match=filter_string_special($ans_match);
				  $content_trimmed=explode(',',$content_trimmed);
				  $content_trimmed=implode('',$content_trimmed);
				 // $content_trimmed=filter_string_special($content_trimmed);
                  //echo ($ans_match);print_r($content_trimmed);//exit;
				$correct_ansCheck = $ans_data['correct_answer'];
					$correct_ansCheck = str_replace(";", ",",$correct_ansCheck);
					$correct_arr=explode(',',$correct_ansCheck);
					$correct_ansValue1=array();
					foreach($correct_arr as $o){
						  $correct_ansValue1[]= $o;
					}
					$correct_ansValue2=implode(',',$correct_ansValue1);
					/* if(count($correct_ansValue1)){
                        $rarr['status'] = 1;
                        $rarr['correct_ans'] = $correct_ansValue2;
						$rarr['correct_ansValue'] = $correct_ans;
						$rarr['taken_time'] = $q_time_taken;
                        $rarr['is_correct'] = 0; 
                        if( $ans == $correct_ansValue1){
                            $rarr['is_correct'] = 1;
                        }
                    } */
					// echo ($correct_ansValue2);print_r($correct_ans);
                     if(count($correct_ans)){
                        $rarr['status'] = 1;
						$rarr['correct_ans'] = $correct_ansValue2;
						 $rarr['correct_ansValue'] = displayText($correct_ans);
                        //$rarr['correct_ans'] = $correct_ans;
						$rarr['taken_time'] = $q_time_taken;
						$rarr['feedback'] = !empty($feedbackArr[2])?$feedbackArr[2]:'';
                        $rarr['is_correct'] = 0; 
                        if( strcmp($ans_match, $content_trimmed) === 0){
                            $rarr['is_correct'] = 1;
							$rarr['feedback'] = !empty($feedbackArr[1])?$feedbackArr[1]:'';
                        }
                    } 

                }//end dd template
				
			 if($ques_format == 'CW-TT-AU' ){
					//print_r($ans_data);exit;
                    // correct answer 
                    $correct_ans = $ans_data['correct_answer'];
					
					$random_ans = $ans_data['random_answers'];
					$random_ans = str_replace("^", ",",$random_ans);
					$random_arr=explode(',',$random_ans);
					$random_ansCount=count($random_arr);
                    $correct_ansValue;
					$randam_ansPosition;
					$k=1;
					 foreach($random_arr as $key=>$o){
						if($correct_ans==$o){
						   $randam_ansPosition=$key;//position	
						    $randam_ansValue=$o;	//value
						}  
						$k++;
					}
					$i=1;
                    foreach ($optionArr as $key=>$ao){
                        if($correct_ans == $ao){
                           $correct_ansPos=$i;//position	
						    $correct_ansValue=$o;	//value
                        }
                     $i++;
					}
                   
					//print_r($ans);
                  //print_r($correct_ansPos);
				//print_r($correct_ans);exit;
					
                    if(count($correct_ans)){
                        $rarr['status'] = 1;
						$rarr['random_ansPos'] = $randam_ansPosition;
						$rarr['random_ansCount'] = $random_ansCount;
						$rarr['correct_ansPos'] = $correct_ansPos;
                        $rarr['correct_ans'] = $correct_ans;
						$rarr['taken_time'] = $q_time_taken;
						$rarr['feedback'] = $feedbackArr;
                        $rarr['is_correct'] = 0; 
						 if( strcmp($ans, $correct_ans) === 0){
						// if( $ans == $correct_ansPos){
                            $rarr['is_correct'] = 1;
                        }
                    } 
                }
				
            }
        } else{
		     $rarr = array('status' => 0);	
			
		}
        return $rarr;
    }

	
	
	
	
 public function submitMcqAns($submitData,$total_ques, $quiz_edge_id,$package_code,$course_code,$attempt_id,$type_of_test){
	
	try{	 
			$params = new stdClass();
		
			$paramArr = array();
			$ques_arr =array();
			  //echo "<pre>";print_r($submitData);	
	         foreach($submitData as $key=>$value){
				
				$ques_data = $this->getQuizQuestionById($value['qid']);
				$ques_format =trim($ques_data['question_type']);

			    $objAns = new stdClass();
				$objAns->ques_uniqid=$value['qid'];
				$objAns->ans_uniqid=$value['is_correct'];
				$objAns->date_ms=$value['time_taken'];

				if($ques_format=='FB-TT-AU'){
				  $user_ans = str_replace("^", ",",$value['ans']);
				  $objAns->essay_answer=$value['ans']; 
				  $objAns->user_response=$value['ans'];	
				}
				
				else if($ques_format=='DD-TT-AU' && $ques_format=='EW-TT-AU'){
				  $objAns->essay_answer=$value['ans']; 
				  $objAns->user_response=$value['ans'];	
				}else{
				   $objAns->essay_answer='';
				   $objAns->user_response=$value['ans'];			  
				 }
				if($ques_format=='RA-TT-AU' && $ques_format=='AV-TT-AU'){
				  $objAns->av_media_files='';
				}else{
				   $objAns->av_media_files='';
				 }
				 $objAns->correct=$value['is_correct'];
				
				 $ques_arr[]=$objAns;

			}
			    $obj = new stdClass();
				$obj->total_ques=$total_ques;
				$obj->test_uniqid = $quiz_edge_id;
				$obj->package_code=$package_code;
				$obj->course_code=$course_code;
				$obj->attempt_id=$attempt_id;
				$obj->type_of_test =$type_of_test;				
                $obj->ques_arr=$ques_arr;
				array_push($paramArr,$obj);
		

		return $paramArr;
		
	   }//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		} 
		 
	}
 
	
      
}

?>