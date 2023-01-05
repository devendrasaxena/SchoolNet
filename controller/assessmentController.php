<?php
class assessmentController {

    public $dbConn;
    public function __construct() {
		$this->dbConn = DBConnection::createConn();
		
    }

	public function getCourseByClientId($clientUserId){
		 	 //echo "<pre>";print_r($_SESSION['client_id']);exit;
			
		
			$sql = "SELECT c.course_id, c.tree_node_id, c.code, c.title, c.description, c.course_type, c.duration, c.course_status, gmt.is_active, c.updated_by, c.created_date, c.modified_date, c.delivery_type_id FROM  course c JOIN generic_mpre_tree gmt ON gmt.tree_node_id = c.tree_node_id  WHERE c.client_id =:client_id AND gmt.is_active = 1";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':client_id', $clientUserId, PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			
			if(count($RESULT) > 0 ){
				return $RESULT;
				}else{
					return false;
				} 
		
    }
	
	public function getCourseByCourseId($course_id){
		 	
			$sql = "SELECT c.tree_node_id, c.code, c.title, c.description, c.course_type, c.duration, c.course_status, gmt.is_active, c.updated_by, c.created_date, c.modified_date, c.delivery_type_id,c.thumnailImg FROM  course c JOIN generic_mpre_tree gmt ON gmt.tree_node_id = c.tree_node_id  WHERE c.course_id =:course_id AND gmt.is_active = 1";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':course_id', $course_id, PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			
			if(count($RESULT) > 0 ){
				return $RESULT;
				}else{
					return false;
				} 
		
    }
	public function getSuperClientId($user_group_id){
		 	 //echo "<pre>";print_r($_SESSION['client_id']);exit;
			
		
			$sql = "select user_id from user_role_map where user_group_id=:user_group_id and role_definition_id=3";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':user_group_id',$user_group_id, PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			
			if(count($RESULT) > 0 ){
					return $RESULT[0]['user_id'];
				}else{
					return false;
				} 
		
    }
	
   public function getSkillByQuestionId($question_id){
        $con = createConnection();
		
		$stmt = $con->prepare("SELECT compentency_id FROM tbl_questions_rubric WHERE question_id=?");
		$stmt->bind_param("i",$question_id);
		$stmt->execute();
		$stmt->bind_result($compentency_id);
		$stmt->execute();	
		$stmt->fetch();
		$stmt->close();
		
		$stmt = $con->prepare("SELECT id,competency FROM tbl_rubric_competency  WHERE id=?");
		$stmt->bind_param("i",$compentency_id);
		$stmt->bind_result($id,$competency);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		
		$bcm = new stdClass();
		$bcm->id = $id;
		$bcm->competency = $competency;
		return $bcm;
	
  }
/*   public function getDataByIdBatchAndCenter($batch_id,$centerId){
		try{ 
		    $dataArr = array();
			$sql = "Select entity_type, type,is_enabled from tblx_product_configuration WHERE batch_id = $batchID AND institute_id = $centerID";
			$stmt->bind_result($entity_type,$type,$is_enabled);
			$stmt->execute();
			
			$bcm = new stdClass();
			$bcm->entity_type = $entity_type;
			$bcm->type = $type;
			$bcm->is_enabled = $is_enabled;
			array_push($dataArr,$bcm);
			
			$stmt->close();
			while($stmt->fetch()) {
				$bcm = new stdClass();
				$bcm->entity_type = $entity_type;
				$bcm->type = $type;
				$bcm->is_enabled = $is_enabled;
				array_push($courseArr,$bcm);
			}
			
			
			 
		}//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
	}
		 */
	public function getTopicOrAssessmentByCourseId($course_id,$customTopic){
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
	
	public function getCourseByEdgeId($edge_id){
		$con = createConnection();
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

	public function getBatteryByClientId($client_id){
		
		
		$sql = "SELECT DISTINCT battery_id FROM client_battery_map WHERE client_id=:client_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$batteryArr = array();
		while($row = array_shift( $RESULT )) {
			array_push($batteryArr,$row['battery_id']);
		}
        return $batteryArr;
	
	}
	public function getBatteryByCenterId($center_id){
		
		
		$sql = "SELECT DISTINCT battery_id FROM tblx_batch_battery_map WHERE center_id=:center_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$batteryArr = array();
		while($row = array_shift( $RESULT )) {
			array_push($batteryArr,$row['battery_id']);
		}
        return $batteryArr;
	
	}

	public function getTopicByCourseId($course_id){
		$con = createConnection();
		$course_edge_id = getCourseEdgeIdByCourseId($course_id);
		$topicArr = array();
		$stmt = $con->prepare("SELECT `cm`.`tree_node_id`,`cm`.`name`,`cm`.`description`,`cm`.`duration`,`cm`.`instimage`,`cm`.`isQuesRand`,`cm`.`isAnsRand`,`cm`.`timeleft_warn`,`cm`.`assessment_type`,`gmt`.`edge_id`,`tnd`.`tree_node_category_id`,`sequence_id`,`is_survey` ,`topic_label`,`thumnailImg` FROM `generic_mpre_tree` AS `gmt`
			JOIN `tree_node_def` AS `tnd` ON `tnd`.`tree_node_id`=`gmt`.`tree_node_id`
			JOIN `cap_module` AS `cm` ON `cm`.`tree_node_id`=`gmt`.`tree_node_id`
			WHERE `gmt`.`is_active`=1 AND `tree_node_super_root`=? AND `tnd`.`tree_node_category_id` IN(3,5) AND (`cm`.`assessment_type`='mid' OR `cm`.`assessment_type` IS NULL) ORDER BY `sequence_id`");
		$stmt->bind_param("i",$course_edge_id);
		$stmt->execute();
		$stmt->bind_result($edge_id,$tree_node_id,$name,$description,$duration,$assInstFile,$isQuesRand,$isAnsRand,$timeleft_warn,$assessment_type,$tree_node_category_id,$sequence_id,$topic_label,$thumnailImg,$is_survey);
		$stmt->execute();
		while($stmt->fetch()) {
			$topic = new stdClass();
			$topic->tree_node_id = $tree_node_id;
			$topic->name = $name;
			$topic->description = $description;
			$topic->edge_id = $edge_id;
			$topic->topic_label = $topic_label;
			$topic->duration = $duration;
			$topic->sequence_id = $sequence_id;
			$topic->thumnailImg = $thumnailImg;
			$topic->is_survey = $is_survey;
			$topic->assessment_type = $assessment_type;
			
			array_push($topicArr,$topic);
		}
		
		return $topicArr;

   }
	
		
		public function getChapterByTopicEdgeId($topic_edge_id,$customChapter=NULL){
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
			 $stmt->close();
			 closeConnection($con);
			 return $topicArr;
			/* $con = createConnection();
			$topicArr = array();
			$stmt = $con->prepare("SELECT gmp.edge_id,sn.title FROM `generic_mpre_tree` gmp  JOIN session_node sn on gmp.tree_node_id=sn.tree_node_id WHERE gmp.`tree_node_parent` =?");
			$stmt->bind_param("i",$topic_edge_id);
			$stmt->execute();
			$stmt->bind_result($edge_id,$title);
			while($stmt->fetch()) {
				$topic = new stdClass();
				$topic->edge_id = $edge_id;
				$topic->description = $title;
				array_push($topicArr,$topic);
			}
			return $topicArr; */
		  }
		  
		public function getTopicName($tree_node_id){
			$con = createConnection();
			$topicArr = array();
			$stmt = $con->prepare("SELECT name,description,topic_label,thumnailImg,is_survey,assessment_type FROM cap_module WHERE tree_node_id=?");
			$stmt->bind_param("i",$tree_node_id);
			$stmt->bind_result($name,$description,$topic_label,$thumnailImg,$is_survey,$assessment_type);
			$stmt->execute();

			while($stmt->fetch()) {
				$topic = new stdClass();
				$topic->name = $name;
				$topic->description = $description;
				$topic->topic_label = $topic_label;
			    $topic->thumnailImg = $thumnailImg;
			    $topic->is_survey = $is_survey;
			
			    $topic->assessment_type = $assessment_type;

				array_push($topicArr,$topic);
			}
			$stmt->close();
			 closeConnection($con);
			return $topicArr; 
		  }
	public function getTopicParantNodeByChapterEdgeId($chapter_edge_id){
		
			$con = createConnection();
			$stmt = $con->prepare("SELECT tree_node_parent FROM generic_mpre_tree WHERE is_active=1 AND edge_id=?");
			$stmt->bind_param("i",$chapter_edge_id);
			$stmt->bind_result($tree_node_parent);
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();
			//echo "<pre>";print_r($tree_node_parent);
			return $tree_node_parent;
			
	 }	
		
   public function getTopicByChapterEdgeId($chapter_edge_id){
	     $tree_node_parent= $this->getTopicParantNodeByChapterEdgeId($chapter_edge_id);
		 $con = createConnection();
		 $topicArr = array();
		 $stmt = $con->prepare("SELECT `cm`.`tree_node_id`,`cm`.`name`,`cm`.`description`,`cm`.`duration`,`cm`.`instimage`,`cm`.`isQuesRand`,`cm`.`isAnsRand`,`cm`.`timeleft_warn`,`cm`.`assessment_type`,`gmt`.`edge_id`,`tnd`.`tree_node_category_id`,`sequence_id`,`is_survey`,`topic_label`,`thumnailImg`,`topic_type` FROM `generic_mpre_tree` AS `gmt`
			JOIN `tree_node_def` AS `tnd` ON `tnd`.`tree_node_id`=`gmt`.`tree_node_id`
			JOIN `cap_module` AS `cm` ON `cm`.`tree_node_id`=`gmt`.`tree_node_id`
			WHERE `gmt`.`is_active`=1 AND `edge_id`=? ");
		$stmt->bind_param("i",$tree_node_parent);
		$stmt->execute();
		$stmt->bind_result($tree_node_id,$name,$description,$duration,$assInstFile,$isQuesRand,$isAnsRand,$timeleft_warn,$assessment_type,$edge_id,$tree_node_category_id,$sequence_id,$is_survey,$topic_label,$thumnailImg,$topic_type);
		$stmt->execute();
		while($stmt->fetch()) {
			$topic2 = new stdClass();
			$topic2->tree_node_id = $tree_node_id;
			$topic2->name = $name;
			$topic2->edge_id = $edge_id;
			$topic2->duration = $duration;
			$topic2->is_survey = $is_survey;
			$topic2->topic_label = $topic_label;
			$topic2->assessment_type = $assessment_type;
			$topic2->thumnailImg = $thumnailImg;
			$topic2->topic_type = $topic_type;
			array_push($topicArr,$topic2);
		}
		$stmt->close();
		return $topicArr; 
    }	
	
	/* public function getTopicByChapterEdgeId($course_edge_id){
			$con = createConnection();
			$topicArr2 = array();
		$stmt = $con->prepare("SELECT `cm`.`tree_node_id`,`cm`.`name`,`cm`.`description`,`cm`.`duration`,`cm`.`instimage`,`cm`.`isQuesRand`,`cm`.`isAnsRand`,`cm`.`timeleft_warn`,`cm`.`assessment_type`,`gmt`.`edge_id`,`tnd`.`tree_node_category_id`,`sequence_id`,`is_survey`,`topic_label`,`thumnailImg`  FROM `generic_mpre_tree` AS `gmt`
			JOIN `tree_node_def` AS `tnd` ON `tnd`.`tree_node_id`=`gmt`.`tree_node_id`
			JOIN `cap_module` AS `cm` ON `cm`.`tree_node_id`=`gmt`.`tree_node_id`
			WHERE `gmt`.`is_active`=1 AND `tree_node_super_root`=? AND `tnd`.`tree_node_category_id` IN(3,5) AND (`cm`.`assessment_type`='mid' OR `cm`.`assessment_type` IS NULL) ORDER BY `sequence_id`");
		$stmt->bind_param("i",$course_edge_id);
		$stmt->execute();
		$stmt->bind_result($tree_node_id,$name,$description,$duration,$assInstFile,$isQuesRand,$isAnsRand,$timeleft_warn,$assessment_type,$edge_id,$tree_node_category_id,$sequence_id,$is_survey,$topic_label,$thumnailImg);
		$stmt->execute();
		while($stmt->fetch()) {
			$topic2 = new stdClass();
			$topic2->tree_node_id = $tree_node_id;
			$topic2->name = $name;
			$topic2->edge_id = $edge_id;
			$topic2->duration = $duration;
			$topic2->is_survey = $is_survey;
			$topic2->topic_label = $topic_label;
			$topic2->assessment_type = $assessment_type;
			$topic2->thumnailImg = $thumnailImg;

			array_push($topicArr,$topic2);
		}
		$stmt->close();
			return $chArr; 
		  }	 */	
		
	public function getTreeNodeIdByTopicEdgeId($tTopicEdgeId){
		/*  In genric map tree tree_node_parent id topic edge id and tree_node_id is chapter tree_node_id*/
			$con = createConnection();
			$chNodeArr = array();
			//$stmt = $con->prepare("SELECT tree_node_id FROM generic_mpre_tree WHERE tree_node_parent=? AND is_active='1'");
			$stmt = $con->prepare("SELECT `cm`.`tree_node_id`
			    FROM `generic_mpre_tree` AS `gmt`
				JOIN `session_node` AS `cm` ON `cm`.`tree_node_id`=`gmt`.`tree_node_id`
				WHERE `gmt`.`is_active`=1 AND `tree_node_parent`=? ORDER BY `cm`.`sequence_no`");
			
			$stmt->bind_param("i",$tTopicEdgeId);
			$stmt->bind_result($tree_node_id);
			$stmt->execute();

			while($stmt->fetch()) {
				$ch = new stdClass();
				if(!empty($tree_node_id)){
				 $ch->tree_node_id = $tree_node_id;
				 array_push($chNodeArr,$ch);
				}
			}
			$stmt->close();
			return $chNodeArr; 
			
		  }		
		
		
	public function getChapterByTreeNodeId($tree_node_id){
			$con = createConnection();
			$chArr = array();
			$stmt = $con->prepare("SELECT code,title FROM session_node WHERE tree_node_id=? AND is_active='1'");
			$stmt->bind_param("i",$tree_node_id);
			$stmt->bind_result($code,$title);
			$stmt->execute();

			while($stmt->fetch()) {
				$chapter = new stdClass();
				$chapter->name = $code;
				$chapter->description = $title;
				array_push($chArr,$chapter);
			}
			$stmt->close();
			return $chArr; 
		  }	
		public function getChapterByEdgeId($edge_id){
			$con = createConnection();
			$chArr = array();
			$stmt = $con->prepare("SELECT `sess`.`code`,`sess`.`title`
			    FROM `generic_mpre_tree` AS `gmt`
				JOIN `session_node` AS `sess` ON `sess`.`tree_node_id`=`gmt`.`tree_node_id`
				WHERE `gmt`.`is_active`=1 AND `edge_id`=? ORDER BY `sess`.`sequence_no`");
			//$stmt = $con->prepare("SELECT code,title FROM session_node WHERE tree_node_id=? AND is_active='1'");
			
			$stmt->bind_param("i",$edge_id);
			$stmt->bind_result($code,$title);
			$stmt->execute();

			while($stmt->fetch()) {
				$chapter = new stdClass();
				$chapter->name = $code;
				$chapter->description = $title;
				array_push($chArr,$chapter);
			}
			$stmt->close();
			return $chArr; 
		  }
		
     public function getScenarioByChapterId($tree_node_id){
		$con = createConnection1();
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
	     ,`scenario_image`, `thumbnailImg`,`component_description`,`sequence_no`,`is_azure_enable` FROM `tbl_component` WHERE `scenario_subtype`='Role-play' AND `parent_edge_id`=? AND `status`=1");
		$stmt->bind_param("i",$tree_node_id);
		$stmt->execute();
		$stmt->bind_result($component_id,$component_edge_id,$parent_edge_id,$scenario_type,$scenario_subtype,$scenario_name,$scenario_description,$scenario_duration,$scenario_image,$thumbnailImg,$component_description,$sequence_no,$is_azure_enable);		
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
			$topic2->is_azure_enable = $is_azure_enable;
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
		
		$stmt = $con->prepare("SELECT `component_id`,`component_edge_id`,`parent_edge_id`,`scenario_type`,
	     `scenario_subtype`,`scenario_name`,`scenario_description`,`scenario_duration`
	     ,`scenario_image`, `thumbnailImg`,`component_description`,`sequence_no`,`is_azure_enable`,`rp_media`,`rp_transcript` FROM `tbl_component` WHERE `scenario_subtype`='Speech Role-play' AND `parent_edge_id`=? AND `status`=1");
		$stmt->bind_param("i",$tree_node_id);
		$stmt->execute();
		$stmt->bind_result($component_id,$component_edge_id,$parent_edge_id,$scenario_type,$scenario_subtype,$scenario_name,$scenario_description,$scenario_duration,$scenario_image,$thumbnailImg,$component_description,$sequence_no,$is_azure_enable,$rp_media,$rp_transcript);		
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
			$topic2->is_azure_enable = $is_azure_enable;
			$topic2->rp_media = $rp_media;
			$topic2->rp_transcript = $rp_transcript;
			array_push($topicArr,$topic2);
		}
		$stmt->close();
		
		//echo '<pre>';print_r($topicArr);exit;	
	   
		return $topicArr;
	}
	
	/*========= Down to up get tree root question to chapter , chapter to topic and topic to course=========*/
	 public function getChaperEdgeIdByQuesEdgeId($quiz_edge_id){//Get Compontent Id  equal to question parent edge id and in component table parent edge id equal to chapter tree node id(tree_node_id in gernric  map trie)
		$con = createConnection();
		$chArr = array();

		$stmt = $con->prepare("SELECT `parent_edge_id` FROM `tbl_component` WHERE `scenario_type`='Activity' AND `parent_edge_id`=? AND `status`=1");
		$stmt->bind_param("i",$quiz_edge_id);
		$stmt->execute();
		$stmt->bind_result($parent_edge_id);		
		while($stmt->fetch()) {
			array_push($chArr,$parent_edge_id);
		}
		$stmt->close();
        return $chArr;
	 }
	 public function getChapterIdByEdgeId($edge_id){//Get chapter detail ($edge_id equal to edge_id in gernric  map tree than get tree_node_id is equal to tree_node_id in session_node)
		$con = createConnection();
		$nodeArr = array();
       $stmt = $con->prepare("SELECT n.tree_node_id FROM generic_mpre_tree gmt
								JOIN session_node n ON n.tree_node_id = gmt.tree_node_id
								WHERE  gmt.edge_id IN(SELECT  edge_id FROM  generic_mpre_tree WHERE  edge_id IN(SELECT  tree_node_super_root FROM  generic_mpre_tree WHERE  edge_id=?))");
		
		$stmt->bind_param("i",$edge_id);
		$stmt->execute();
		$stmt->bind_result($tree_node_id);		
		while($stmt->fetch()) {
			array_push($nodeArr,$tree_node_id);
		}
		$stmt->close();
        return $nodeArr;
	 }
	 
	 public function getEdgeIdByParentEdgeId($parent_edge_id){//Get chapter detail ($edge_id equal to edge_id in gernric  map tree than get tree_node_id is equal to tree_node_id in session_node)
		$con = createConnection();
		$nodeArr = array();
		
		/*Start Concept*/
		$stmt = $con->prepare("SELECT gmt.edge_id
		FROM generic_mpre_tree  gmt
		JOIN tree_node_def tnd ON tnd.tree_node_id=gmt.tree_node_id
		JOIN tree_node_cat_master tncm ON tnd.tree_node_category_id = tncm.category_id
		WHERE gmt.is_active=1 AND gmt.tree_node_parent=? AND tncm.category_name='CAP_CONCEPT'");
       
		$stmt->bind_param("i",$parent_edge_id);
		$stmt->execute();
		$stmt->bind_result($edge_id);	
		while($stmt->fetch()) {
			$nodeArr['CAP_CONCEPT']=$edge_id;
		}
		$stmt->close();
			/*End Concept*/
			
	 /*Start Practice*/
		$stmt = $con->prepare("SELECT gmt.edge_id
		FROM generic_mpre_tree  gmt
		JOIN tree_node_def tnd ON tnd.tree_node_id=gmt.tree_node_id
		JOIN tree_node_cat_master tncm ON tnd.tree_node_category_id = tncm.category_id
		WHERE gmt.is_active=1 AND gmt.tree_node_parent=? AND tncm.category_name='CAP_PRACTICE'");
        
		$stmt->bind_param("i",$parent_edge_id);
		$stmt->execute();
		$stmt->bind_result($edge_id);	
		while($stmt->fetch()) {
			$nodeArr['CAP_PRACTICE']=$edge_id;
		}
		$stmt->close();
				
		/*Start role play*/
		$stmt = $con->prepare("SELECT gmt.edge_id
		FROM generic_mpre_tree  gmt
		JOIN tree_node_def tnd ON tnd.tree_node_id=gmt.tree_node_id
		JOIN tree_node_cat_master tncm ON tnd.tree_node_category_id = tncm.category_id
		WHERE gmt.is_active=1 AND gmt.tree_node_parent=? AND tncm.category_name='CAP_SC_PRACTICE'");
        
		$stmt->bind_param("i",$nodeArr['CAP_PRACTICE']);
		$stmt->execute();
		$stmt->bind_result($edge_id);	
		while($stmt->fetch()) {
			$nodeArr['CAP_SC_PRACTICE']=$edge_id;
		}
		$stmt->close();
		
		$stmt = $con->prepare("SELECT gmt.edge_id
		FROM generic_mpre_tree  gmt
		JOIN tree_node_def tnd ON tnd.tree_node_id=gmt.tree_node_id
		JOIN tree_node_cat_master tncm ON tnd.tree_node_category_id = tncm.category_id
		WHERE gmt.is_active=1 AND gmt.tree_node_parent=? AND tncm.category_name='CAP_SC_PRACTICE_W'");
        
		$stmt->bind_param("i",$nodeArr['CAP_SC_PRACTICE']);
		$stmt->execute();
		$stmt->bind_result($edge_id);	
		while($stmt->fetch()) {
			$nodeArr['CAP_SC_PRACTICE_W']=$edge_id;
		}
		$stmt->close();
		
		$stmt = $con->prepare("SELECT gmt.edge_id
		FROM generic_mpre_tree  gmt
		JOIN tree_node_def tnd ON tnd.tree_node_id=gmt.tree_node_id
		JOIN tree_node_cat_master tncm ON tnd.tree_node_category_id = tncm.category_id
		WHERE gmt.is_active=1 AND gmt.tree_node_parent=? AND tncm.category_name='CAP_SC_PRACTICE_E'");
        
		$stmt->bind_param("i",$nodeArr['CAP_SC_PRACTICE']);
		$stmt->execute();
		$stmt->bind_result($edge_id);	
		while($stmt->fetch()) {
			$nodeArr['CAP_SC_PRACTICE_E']=$edge_id;
		}
		$stmt->close();
		
		$stmt = $con->prepare("SELECT gmt.edge_id
		FROM generic_mpre_tree  gmt
		JOIN tree_node_def tnd ON tnd.tree_node_id=gmt.tree_node_id
		JOIN tree_node_cat_master tncm ON tnd.tree_node_category_id = tncm.category_id
		WHERE gmt.is_active=1 AND gmt.tree_node_parent=? AND tncm.category_name='CAP_SC_PRACTICE_R'");
       
		$stmt->bind_param("i",$nodeArr['CAP_SC_PRACTICE']);
		$stmt->execute();
		$stmt->bind_result($edge_id);	
		while($stmt->fetch()) {
			$nodeArr['CAP_SC_PRACTICE_R']=$edge_id;
		}
		$stmt->close();
		/*End role play*/
		
		/*Start Quiz*/
		$stmt = $con->prepare("SELECT gmt.edge_id
		FROM generic_mpre_tree  gmt
		JOIN tree_node_def tnd ON tnd.tree_node_id=gmt.tree_node_id
		JOIN tree_node_cat_master tncm ON tnd.tree_node_category_id = tncm.category_id
		WHERE gmt.is_active=1 AND gmt.tree_node_parent=? AND tncm.category_name='CAP_MC_PRACTICE'");
       	
		$stmt->bind_param("i",$nodeArr['CAP_PRACTICE']);
		$stmt->execute();
		$stmt->bind_result($edge_id);	
		while($stmt->fetch()) {
			$nodeArr['CAP_MC_PRACTICE']=$edge_id;
		}
		$stmt->close();
	     /*End Quiz*/
		
		/*Start Vocab*/
		$stmt = $con->prepare("SELECT gmt.edge_id
		FROM generic_mpre_tree  gmt
		JOIN tree_node_def tnd ON tnd.tree_node_id=gmt.tree_node_id
		JOIN tree_node_cat_master tncm ON tnd.tree_node_category_id = tncm.category_id
		WHERE gmt.is_active=1 AND gmt.tree_node_parent=? AND tncm.category_name='CAP_VO_PRACTICE'");
        
		$stmt->bind_param("i",$nodeArr['CAP_PRACTICE']);
		$stmt->execute();
		$stmt->bind_result($edge_id);	
		while($stmt->fetch()) {
			$nodeArr['CAP_VO_PRACTICE']=$edge_id;
		}
		$stmt->close();
	   /*End Vocab*/
		/*Start Game*/
		$stmt = $con->prepare("SELECT gmt.edge_id
		FROM generic_mpre_tree  gmt
		JOIN tree_node_def tnd ON tnd.tree_node_id=gmt.tree_node_id
		JOIN tree_node_cat_master tncm ON tnd.tree_node_category_id = tncm.category_id
		WHERE gmt.is_active=1 AND gmt.tree_node_parent=? AND tncm.category_name='CAP_GAME'");
       
		$stmt->bind_param("i",$parent_edge_id);
		$stmt->execute();
		$stmt->bind_result($edge_id);	
		while($stmt->fetch()) {
			$nodeArr['CAP_GAME']=$edge_id;
		}
		$stmt->close();
			/*End Game*/
			
		/*Start RESOURCES*/
		$stmt = $con->prepare("SELECT gmt.edge_id
		FROM generic_mpre_tree  gmt
		JOIN tree_node_def tnd ON tnd.tree_node_id=gmt.tree_node_id
		JOIN tree_node_cat_master tncm ON tnd.tree_node_category_id = tncm.category_id
		WHERE gmt.is_active=1 AND gmt.tree_node_parent=? AND tncm.category_name='CAP_RESOURCES_PRACTICE'");
       
		$stmt->bind_param("i",$parent_edge_id);
		$stmt->execute();
		$stmt->bind_result($edge_id);	
		while($stmt->fetch()) {
			$nodeArr['CAP_RESOURCES_PRACTICE']=$edge_id;
		}
		$stmt->close();
			/*End RESOURCES*/	
        return $nodeArr;
	 }
	 
	  public function getEdgeIdByTreeNodeId($tree_node_id){//Get chapter edge id  (session_node  $tree_node_id equal to $tree_node_id in gernric_map_tree and tree_node_id is equal to tree_node_id in tree_node_def)
		$con = createConnection();
		$nodeArr = array();
		$stmt = $con->prepare("SELECT gmt.edge_id
		FROM generic_mpre_tree  gmt
		JOIN tree_node_def tnd ON tnd.tree_node_id=gmt.tree_node_id
		JOIN tree_node_cat_master tncm ON tnd.tree_node_category_id = tncm.category_id
		WHERE gmt.is_active=1 AND gmt.tree_node_id=? AND tncm.category_name='SESSION'");
     	
		$stmt->bind_param("i",$tree_node_id);
		$stmt->execute();
		$stmt->bind_result($edge_id);	
		
		while($stmt->fetch()) {
			array_push($nodeArr,$edge_id);
		}
		$stmt->close();
        return $nodeArr;
	 }
	 	
	public function getCompletionPer($user_id,$topicEdgeId){ 
			$sql ="Select id,chapter_id,component_id,score,current_page,total_page,completion_percentage from tblx_user_score where user_id=:user_id and topic_id=:topicEdgeId";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
			$stmt->bindValue(':topicEdgeId',$topicEdgeId,PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor(); 
			/* 	
			$completionPerArr = array();
		   while($row = array_shift( $RESULT )) {
				$bcm = new stdClass();
				$bcm->completion_percentage = $row['completion_percentage'];
				$completionPerArr[$topicEdgeId]=$bcm;			
			} */
			//echo "<pre>";print_r($RESULT[0]);//exit; 
			return $RESULT[0]['completion_percentage'] ;
	}
	
	public function getTopicOrAssessmentBySqId($course_id,$customTopic=NULL){
	
		$con = createConnection();
		
		$seqArr = array();

		$whr.='WHERE gmt.is_active=1 AND cm.is_topic_active="1" AND tnd.tree_node_category_id IN(3,5)';
		 if($customTopic!=""){
			$whr.=' AND gmt.edge_id IN('.$customTopic.')'; 
		} 
		$whr.=' AND (cm.assessment_type="mid" OR cm.assessment_type IS NULL) ORDER BY sequence_id';

		$sql="SELECT `sequence_id` FROM `generic_mpre_tree` AS `gmt`
			JOIN `tree_node_def` AS `tnd` ON `tnd`.`tree_node_id`=`gmt`.`tree_node_id`
			JOIN `cap_module` AS `cm` ON `cm`.`tree_node_id`=`gmt`.`tree_node_id`
			$whr";	 		 
		$stmt = $con->prepare($sql);
		$stmt->execute();
		$stmt->bind_result($sequence_id);
		$stmt->execute();
		while($stmt->fetch()) {
			array_push($seqArr,$sequence_id);
		}
		$stmt->close();
		
		$seqArr=implode(',',$seqArr); 
		$topicArr = array();
       $course_edge_id = $this->getCourseEdgeIdByCourseId($course_id);

		$whr1.='WHERE gmt.is_active=1 AND cm.is_topic_active="1" AND tree_node_super_root="'.$course_edge_id.'" AND tnd.tree_node_category_id IN(3,5)';
		
		$whr1.=' AND cm.sequence_id IN('.$seqArr.')'; 
		
		$whr1.=' AND (cm.assessment_type="mid" OR cm.assessment_type IS NULL) ORDER BY sequence_id';

		$sql="SELECT `cm`.`tree_node_id`,`cm`.`name`,`cm`.`description`,`cm`.`duration`,`cm`.`instimage`,`cm`.`isQuesRand`,`cm`.`isAnsRand`,`cm`.`timeleft_warn`,`cm`.`assessment_type`,`gmt`.`edge_id`,`tnd`.`tree_node_category_id`,`sequence_id`,`topic_label`,`thumnailImg`,`is_survey`,`no_of_attempt`,`ttl_ques_to_show` ,`passing_score`,`no_of_skill_ques` ,`topic_type` FROM `generic_mpre_tree` AS `gmt`
			JOIN `tree_node_def` AS `tnd` ON `tnd`.`tree_node_id`=`gmt`.`tree_node_id`
			JOIN `cap_module` AS `cm` ON `cm`.`tree_node_id`=`gmt`.`tree_node_id`
			$whr1";	 		 

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

		return $topicArr;
		
	}

	public function setAssessmentScore($user_id,$topicEdgeId,$complete_score){ 
			$sql="select score from tblx_user_score where user_id=:user_id and topic_id=:topicEdgeId";
		    $stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
			$stmt->bindValue(':topicEdgeId',$topicEdgeId,PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor(); 
			
			$getScore= $RESULT[0]['score'] ;
				
				if($complete_score>=80){
					$sql="update tblx_user_score set score='$complete_score',completion_percentage='100',attempted_date=NOW(),modified_date=NOW()  where user_id=:user_id and topic_id=:topicEdgeId";
					$stmt = $this->dbConn->prepare($sql);
					$stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
					$stmt->bindValue(':topicEdgeId',$topicEdgeId,PDO::PARAM_INT);
					$stmt->execute();
					$stmt->closeCursor(); 
			   }else{
					if($getScore<80){
						$sql="update tblx_user_score set score='$complete_score',attempted_date=NOW(),modified_date=NOW() where user_id=:user_id and topic_id=:topicEdgeId";
						$stmt = $this->dbConn->prepare($sql);
						$stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
						$stmt->bindValue(':topicEdgeId',$topicEdgeId,PDO::PARAM_INT);
						$stmt->execute();
						$stmt->closeCursor(); 	
				   } 		
			    }
				
			return $getScore;
	}
	public function getTopicOrAssessmentByTopicEdgeId($customTopic=NULL){
	
		$con = createConnection();
		
		$seqArr = array();

		$whr.='WHERE gmt.is_active=1 AND cm.is_topic_active="1" AND tnd.tree_node_category_id IN(3,5)';
		 if($customTopic!=""){
			$whr.=' AND gmt.edge_id IN('.$customTopic.')'; 
		} 
		$whr.=' AND (cm.assessment_type="mid" OR cm.assessment_type IS NULL) ORDER BY sequence_id';

		$sql="SELECT `sequence_id`,gmt.edge_id FROM `generic_mpre_tree` AS `gmt`
			JOIN `tree_node_def` AS `tnd` ON `tnd`.`tree_node_id`=`gmt`.`tree_node_id`
			JOIN `cap_module` AS `cm` ON `cm`.`tree_node_id`=`gmt`.`tree_node_id`
			$whr";	 		 
		$stmt = $con->prepare($sql);
		$stmt->execute();
		$stmt->bind_result($sequence_id,$edge_id);
		$stmt->execute();
		while($stmt->fetch()) {
			$topic2 = new stdClass();
			$topic2->sequence_id = $sequence_id;
			$topic2->edge_id = $edge_id;
			array_push($seqArr,$topic2);
		}
		$stmt->close();

		return $seqArr;
		
	}
	
}


?>