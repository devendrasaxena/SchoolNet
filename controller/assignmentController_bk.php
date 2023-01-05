<?php

/**
* 
*/
class assignmentController
{
	
	public $dbConn;
    public function __construct() {
		$this->dbConn = DBConnection::createConn();
		
    }

    public function getTotalAssignmentsByTeacher($creator_id, $batch_id='',$status='',$title=''){
    	// echo $creator_id; die;
		$sql = "SELECT count(*) total from  tblx_assignments WHERE created_by ='$creator_id'";

		if($batch_id != ''){
			$sql .= " AND batch_code = '$batch_id'";
		}
		if($status != ''){
			$status = $status == 'active' ? '1':'0';
			$sql .= " AND (status = '$status' OR status = '')";
		}
		if($title != ''){
			$sql .= " AND assignment_name like '%$title%'";
		}
		$sql .= " order by id desc";

		// echo $sql; die;
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		return $RESULT;
		
    }
    public function getAssignmentsByTeacher($creator_id, $start = 0, $limit = 10, $batch_id='',$status='',$title=''){
    	// echo $creator_id; die;
		$sql = "SELECT * from  tblx_assignments WHERE created_by ='$creator_id'";

		if($batch_id != ''){
			$sql .= " AND batch_code = '$batch_id'";
		}
		if($status != ''){
			$status = $status == 'active' ? '1':'0';
			$sql .= " AND (status = '$status' OR status = '')";
		}
		if($title != ''){
			$sql .= " AND assignment_name like '%$title%'";
		}
		$sql .= " order by id desc";

		if( !empty($limit) ){
			$sql .= " LIMIT $start, $limit";
		}
		// echo $sql; die;
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		
		if(count($RESULT) > 0 ){
			return $RESULT;
		}else{
			return false;
		} 
		
    }

    public function getTotalUserDetails($roleID, $batch_id='', $status='', $key='', $user_id = ''){
		
		$center_id = $_SESSION['center_id'];
		$sql = "SELECT count(DISTINCT(u.user_id)) as total  FROM user_role_map uld JOIN user u ON u.user_id = uld.user_id JOIN user_credential uc on u.user_id= uc.user_id JOIN tblx_batch_user_map ubm ON u.user_id = ubm.user_id";

		if($_SESSION['role_id'] == 1){ 
		 $sql .= " JOIN tblx_batch_user_map tbum ON tbum.batch_id = ubm.batch_id and tbum.center_id = ubm.center_id ";
		}
		
		

		$sql .= " WHERE uld.role_definition_id = '$roleID'  AND ubm.center_id='$center_id'";
		
		if($_SESSION['role_id'] == 1){ 
		 $sql .= " AND tbum.user_id = '".$_SESSION['user_id']."' ";
		}
		
		if($status!=""  || $status=='0'){
			$sql .= " AND uc.is_active = '$status'";
		}
		if($key != ''){
			$sql .= " AND (u.first_name like '%$key%' OR u.last_name like '%$key%')";
		}
		if($batch_id != ''){
			$sql .= " AND ubm.batch_id = '$batch_id'";
		}
		if($user_id != ''){
			$sql .= " AND uld.user_id = '$user_id'";
		}
      
		

        $stmt = $this->dbConn->prepare($sql);
        $stmt->execute();
        $RESULT = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        
      	return $RESULT;
			
	
	
	
	
	}
	public function getAllUserDetails($roleID, $start = 0, $limit = 10, $batch_id='', $status='', $key='', $user_id = ''){

	  	$center_id = $_SESSION['center_id'];
		$sql = "SELECT DISTINCT(u.user_id),uld.*, uc.is_active, u.first_name, u.last_name,ubm.batch_id,u.email_id FROM user_role_map uld JOIN user u ON u.user_id = uld.user_id JOIN user_credential uc on u.user_id= uc.user_id JOIN tblx_batch_user_map ubm ON u.user_id = ubm.user_id";

		if($_SESSION['role_id'] == 1){ 
		 $sql .= " JOIN tblx_batch_user_map tbum ON tbum.batch_id = ubm.batch_id and tbum.center_id = ubm.center_id ";
		}
		
		

		$sql .= " WHERE uld.role_definition_id = '$roleID'  AND ubm.center_id='$center_id'";
		
		if($_SESSION['role_id'] == 1){ 
		 $sql .= " AND tbum.user_id = '".$_SESSION['user_id']."' ";
		}
		
		if($status!=""  || $status=='0'){
			$sql .= " AND uc.is_active = '$status'";
		}
		if($key != ''){
			$sql .= " AND (u.first_name like '%$key%' OR u.last_name like '%$key%')";
		}
		if($batch_id != ''){
			$sql .= " AND ubm.batch_id = '$batch_id'";
		}
		if($user_id != ''){
			$sql .= " AND uld.user_id = '$user_id'";
		}
		$sql .= " order by uld.user_id DESC";
		if( !empty($limit) ){
			$sql .= " LIMIT $start, $limit";
		}
        $stmt = $this->dbConn->prepare($sql);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		//$user_id=$RESULT[0]['user_id'];
		$userArr = array();
		while($row = array_shift( $RESULT ) ) {
			array_push($userArr,$row);
		}
      	return $userArr;
			
	}
	
    public function getAssignmentById($assignment_id){
    	// echo $creator_id; die;
		$sql = "SELECT * from  tblx_assignments WHERE id =:assignment_id order by id desc";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':assignment_id', $assignment_id, PDO::PARAM_INT);
		$stmt->execute();
		$RESULT = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		
		if(count($RESULT) > 0 ){
			return $RESULT;
		}else{
			return false;
		} 
		
    }

    public function getAllClassForTrainer($trainer_id, $center_id)
    {
    	// print_r($_SESSION); die;
    	$sql = "SELECT b.* from tblx_batch as b join tblx_batch_user_map as bum on b.batch_id = bum.batch_id WHERE bum.user_id =:user_id AND b.center_id = :center_id";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':user_id', $trainer_id, PDO::PARAM_INT);
		$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
		$stmt->execute();

		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		
		if(count($RESULT) > 0 ){
			return $RESULT;
		}else{
			return false;
		} 
    }

    
    public function getProductConfigurationByClassAndTrainer($batch_id, $center_id, $type)
    {
    	//$sql = "SELECT * from tblx_product_configuration WHERE institute_id =:center_id AND batch_id = :batch_id AND type = :type";
		$sql = "SELECT * from tblx_product_configuration WHERE institute_id =:center_id AND batch_id = :batch_id";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
		$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
		//$stmt->bindValue(':type', $type, PDO::PARAM_STR);
		$stmt->execute();
		// $stmt->debugDumpParams();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		 
		if(count($RESULT) > 0 ){
			return $RESULT;
		}else{
			return false;
		} 
    }

    public function saveAssignment($postArray, $isUpdate = false)
    {


    	if($postArray['submission_date'] != ''){
    		list($d,$m,$y) = explode('-', $postArray['submission_date']);
    		$submission_date = $y.'-'.$m.'-'.$d;
    	}

    	$batch_code 			= $postArray['batch_id'];
	    $course_code 			= $postArray['level_id'];
	    $topic_edge_id 			= $postArray['topic_id'];
	    $chapter_edge_id 		= $postArray['chapter_id'];
	    $assignment_name 		= strip_tags($postArray['title']);
	    $assignment_end_date 	= '';//$submission_date;
	    $assignment_desc 		= strip_tags($postArray['description']);
	    $created_by 			= $postArray['user_id'];
	    $client_id 				= $postArray['client_id'];
	    $client_id 				= $postArray['client_id'];
	    $status 				= $postArray['status'];
	    

	    $file = '';
    	if($postArray['assignment_file'] != ''){
    		$file = $this->uploadAssignmentFile($postArray['assignment_file'], '../emp-ilt/view/assignment_files/');
    	}

    	if($isUpdate){
	    	$assignment_id = $postArray['assignment_id'];
	    	$sql = "UPDATE tblx_assignments set `batch_code` = '$batch_code',`course_code` = '$course_code',`topic_edge_id` = '$topic_edge_id',`chapter_edge_id` = '$chapter_edge_id',`assignment_name` = '$assignment_name',`assignment_end_date` = '$assignment_end_date',`assignment_desc` = '$assignment_desc',`created_by` = '$created_by',`client_id` = '$client_id',`status` = '$status',`assignment_file` = '$file' WHERE id = '$assignment_id'";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->execute();
			$stmt->closeCursor();
			return true;

	    } else {
	    	$sql = "INSERT INTO tblx_assignments(`batch_code`,`course_code`,`topic_edge_id`,`chapter_edge_id`,`assignment_name`,`assignment_end_date`,`assignment_desc`,`created_by`,`client_id`,`assignment_file`,`status`, `created_date`) VALUES('$batch_code','$course_code','$topic_edge_id','$chapter_edge_id','$assignment_name','$assignment_end_date','$assignment_desc','$created_by','$client_id', '$file', '$status', NOW())";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->execute();
			$stmt->closeCursor();
			return true;
	    }
    	
    }

    public function uploadAssignmentFile($file, $path)
    {
    	$name_array = explode('.', $file['name']);
    	$not_allowed = $this->getNotAllowedExtensions();
    	foreach ($not_allowed as $value) {
    		if(in_array($value, $name_array)){
	    		return null;
	    	}
    	}
    	
    	$ext = end($name_array);
    	array_pop($name_array);
    	$extArray = $this->getAllowedExtensions();
    	if(in_array($ext, $extArray)){
    		$filename = strtolower(implode('_', $name_array)).'_'.time().'.'.$ext;
    		
    		$tmp_name = $file['tmp_name'];
    		if(move_uploaded_file($tmp_name, $path.$filename)){
    			return $filename;
    		}
    	}
    	return null;
    }

    public function getAllowedExtensions()
    {
    	$sql = "SELECT * from tblx_allow_extensions WHERE file_type ='document'";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		
		$exts = [];
		if(count($RESULT) > 0 ){
			foreach ($RESULT as $row) {
				$exts[] = $row['extension'];
			}
			
		}
		return $exts;
    }
    public function getNotAllowedExtensions()
    {
    	$sql = "SELECT * from tblx_allow_extensions WHERE file_type ='not_allowed'";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		
		$exts = [];
		if(count($RESULT) > 0 ){
			foreach ($RESULT as $row) {
				$exts[] = $row['extension'];
			}
			
		}
		return $exts;
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

    public function getTopicOrAssessmentByCourseId($course_id, $topic_id){
		$con = createConnection();
		$course_edge_id = $this->getCourseEdgeIdByCourseId($course_id);
		$topicArr = array();
		$stmt = $con->prepare("SELECT `cm`.`tree_node_id`,`cm`.`name`,`cm`.`description`,`cm`.`duration`,`cm`.`instimage`,`cm`.`isQuesRand`,`cm`.`isAnsRand`,`cm`.`timeleft_warn`,`cm`.`assessment_type`,`gmt`.`edge_id`,`tnd`.`tree_node_category_id`,`sequence_id`,`topic_label`,`thumnailImg`,`is_survey`,`no_of_attempt`,`ttl_ques_to_show` ,`passing_score`,`no_of_skill_ques` ,`topic_type` FROM `generic_mpre_tree` AS `gmt`
			JOIN `tree_node_def` AS `tnd` ON `tnd`.`tree_node_id`=`gmt`.`tree_node_id`
			JOIN `cap_module` AS `cm` ON `cm`.`tree_node_id`=`gmt`.`tree_node_id`
			WHERE `gmt`.`is_active`=1 AND cm.`is_topic_active`='1' AND `tnd`.`tree_node_category_id` IN(3,5) AND (`cm`.`assessment_type`='mid' OR `cm`.`assessment_type` IS NULL) AND `gmt`.`edge_id` = '$topic_id' ORDER BY `sequence_id`");
		$stmt->bind_param("i",$course_edge_id);
		$stmt->execute();
		$stmt->bind_result($tree_node_id,$name,$description,$duration,$assInstFile,$isQuesRand,$isAnsRand,$timeleft_warn,$assessment_type,$edge_id,$tree_node_category_id,$sequence_id,$topic_label,$thumnailImg,$is_survey,$no_of_attempt,$ttl_ques_to_show,$passing_score,$no_of_skill_ques,$topic_type);
		$stmt->execute();
		$stmt->fetch();
		
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
		
		$stmt->close();
		
		return $topic2;
	}

	public function getStudentBatch($student_id)
	{
		$sql = "SELECT * from tblx_batch_user_map WHERE user_id ='$student_id'";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		
		$batch_id = '';
		if(count($RESULT) > 0 ){
			$batch_id = $RESULT['batch_id'];
		}
		return $batch_id;
	}
	public function getStudentAssignments($student_id)
	{
		$batch_id = $this->getStudentBatch($student_id);
		if($batch_id != ''){
			$sql = "SELECT * from  tblx_assignments WHERE batch_code ='$batch_id' AND status = '1' order by id desc";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			if(count($RESULT) > 0 ){
				return $RESULT;
			}else{
				return false;
			} 
		}
		
	}

	public function submitAssignmentResponse($student_id, $postArray, $file)
	{
		$assignment_id 	= $postArray['assignment_id'];
	    $user_id 		= $student_id;
	    $response_text 	= $postArray['response_text'];
	    $responded_date = date('Y-m-d H:i:s');
	    $status 		= '1';
	    
		$response_file = '';
    	if($file['assignment_file'] != ''){
    		$response_file = $this->uploadAssignmentFile($file['assignment_file'], '../emp-ilt/view/assignment_files/');
    	}

    	
    	$sql = "UPDATE tblx_assignment_response set `status` = '0' WHERE assignment_id = '$assignment_id' AND user_id = '$user_id'";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$stmt->closeCursor();

	    
    	$sql = "INSERT INTO tblx_assignment_response(`assignment_id`,`user_id`,`response_text`,`responded_date`,`status`,`response_file`) VALUES('$assignment_id','$user_id','$response_text','$responded_date','$status','$response_file')";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$stmt->closeCursor();
		return true;
	    
	}

	public function getAllResponsesById($assignment_id)
	{
		$sql = "SELECT * FROM tblx_assignment_response WHERE assignment_id = '$assignment_id' AND status = '1'";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		if(count($RESULT) > 0){
			return $RESULT;
		} else {
			return false;
		}
		
	}

	public function getAllResponsesByIdAndStudent($assignment_id,$student_id)
	{
		$sql = "SELECT * FROM tblx_assignment_response WHERE assignment_id = '$assignment_id' AND user_id = '$student_id' AND status = '1'";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		if(count($RESULT) > 0){
			return $RESULT;
		} else {
			return false;
		}
		
	}

	public function getEvolutionByStudentAndAssignment($assignment_id, $student_id)
	{
		$sql = "SELECT * FROM tblx_assignment_evaluation WHERE assignment_id = '$assignment_id' AND student_id = '$student_id'";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		if(count($RESULT) > 0){
			return $RESULT;
		} else {
			return false;
		}
	}
	public function getEvolutionByAssignmentId($assignment_id)
	{
		$sql = "SELECT * FROM tblx_assignment_evaluation WHERE assignment_id = '$assignment_id'";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		if(count($RESULT) > 0){
			return $RESULT;
		} else {
			return false;
		}
	}

	public function updateResponseStatus($assignment_id,$student_id)
	{
		$sql = "UPDATE `tblx_assignment_response` SET `isEvaluated`='1' WHERE assignment_id = '$assignment_id' AND user_id = '$student_id' AND status = '1'";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$stmt->closeCursor();

		return true;
	}

	public function createEvaluation($data)
	{
		$assignment_id = $data['assignment_id']; 
		$student_id = $data['student_id']; 
		$teacher_id = $data['teacher_id']; 
		$evaluated_comment = $data['comment']; 
		$evaluated_rating = $data['rating']; 
		$evaluated_date = date('Y-m-d'); 
		$status = '1';
		$evo = $this->getEvolutionByStudentAndAssignment($assignment_id, $student_id);
		if($evo){
			$id = $evo['id'];
			$sql = "UPDATE `tblx_assignment_evaluation` SET `assignment_id`='$assignment_id',`student_id`='$student_id',`teacher_id`='$teacher_id',`evaluated_comment`='$evaluated_comment',`evaluated_rating`='$evaluated_rating',`evaluated_date`=$evaluated_date,`status`=$status WHERE id = '$id'";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->execute();
			$stmt->closeCursor();
			$this->updateResponseStatus($assignment_id, $student_id);
			return true;
		} else {
			$sql = "INSERT INTO `tblx_assignment_evaluation`(`assignment_id`, `student_id`, `teacher_id`, `evaluated_comment`, `evaluated_rating`, `evaluated_date`, `status`) VALUES ('$assignment_id', '$student_id', '$teacher_id', '$evaluated_comment', '$evaluated_rating', '$evaluated_date', '$status')";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->execute();
			$stmt->closeCursor();
			$this->updateResponseStatus($assignment_id, $student_id);
			return true;
		}
	}

}

?>