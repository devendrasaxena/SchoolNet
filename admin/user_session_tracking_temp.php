<?php 
ini_set('max_execution_time', 0);
include("../config/config.php");
ini_set('display_errors',1);
error_reporting(E_ALL);  

$cron_log_content = "".PHP_EOL;
$cron_log_content .= "user session tracking ";
$cron_log_content .= " - " .date('g:i A, j M Y');
$quiz_last_attempted_date = "";
$record_type = "chapter";
$no_of_attempt =0;
$quiz_time_sp=0;
	$con = createConnection();
	$con1 = createConnection();
	 $sql1 = "SELECT rpt_id from rpt_user_session_tracking_temp limit 1";
        $stmt1 = $con->prepare($sql1);
        $stmt1->execute();
        $rptDataExist = $stmt1->fetch();
        $stmt1->close();

        if( $rptDataExist ){
           
           $sql = "TRUNCATE table rpt_user_session_tracking_temp";
            $stmt = $con->prepare($sql);
            $stmt->execute();
            $stmt->close();

        }
	//get course, topic and chapter
	//get course list
	$course_list = getAllCourseByClientId(2); 
	$course_arr = array();
	foreach($course_list as $crs_key=>$crs_val){

		
		$course_edge_id = $crs_val->edge_id;
		
		$course = new stdClass();
        $course->edge_id = $crs_val->edge_id;
        $course->course_id = $crs_val->course_id;
        $course->code = $crs_val->code;
        $course->title = $crs_val->title;
        $course->description = $crs_val->description;
		
		
		//Number of chapter
		$number_of_chapters = 0;
		$stmt = $con->prepare("SELECT count(cm.session_node_id) as 'cnt' from generic_mpre_tree gmt 
			JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
			JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id where gmt.tree_node_super_root = ? AND tnd.tree_node_category_id=2  AND gmt.is_active = 1 AND cm.topic_type = 1");
			$stmt->bind_param("i",$course_edge_id);
			$stmt->execute();
			$stmt->bind_result($number_of_chapters);
			$stmt->fetch();
			$stmt->close();	
		
		 $course->number_of_chapters = $number_of_chapters;
	
		$topicList = array();	
		$topicArr = getTopicOrAssessmentByCourseId($course_edge_id);
		
		 foreach($topicArr as $key => $value){
				
				$topic = new stdClass();
				$topic->edge_id = $value->edge_id;
				$topic->name = $value->name;
				$topic->assessment_type = $value->assessment_type;
				$topic->description = $value->description;
				$topic->topic_type = $value->topic_type;
				$chapterList = array();	
				$singleChapterArr = getChpaterByTopicEdgeId($con,$topic->edge_id);
	
				 foreach($singleChapterArr as $chapterArrKey=>$chapterArrVal){
							
							$chapter = new stdClass();
							$chapter->edge_id = $chapterArrVal->edge_id;
							$chapter->name = $chapterArrVal->name;
							$chapter->description = $chapterArrVal->description;
							$chapter->chapterSkill = $chapterArrVal->chapterSkill;
							$chapter->competency = $chapterArrVal->competency;
							$ttlChpaterQCount = 0;
							//total ques of chapter
								 $stmt = $con->prepare("SELECT count(tq.id) as qCount from generic_mpre_tree gmt 
								JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id 
								JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id 
								JOIN tbl_component tc ON gmt.edge_id= tc.parent_edge_id 
								JOIN tbl_questions tq ON tq.parent_edge_id= tc.component_id
								where tc.parent_edge_id = ? and gmt.is_active = 1 and tc.status =1");
								$stmt->bind_param("i",$chapterArrVal->edge_id);
								$stmt->execute();
								$stmt->bind_result($ttlChpaterQCount);
								$stmt->fetch();
								$stmt->close();
							
								$chapter->ttlChpaterQCount = $ttlChpaterQCount; 
							 array_push($chapterList,$chapter);
							
			}
			
				$topic->chapterArr = $chapterList;
			
				array_push($topicList,$topic);
			
		}	
		
		$course->topicArr = $topicList;	
		
		array_push($course_arr,$course);

			

	}
	 

	$query = "SELECT ct.name as customer, ct.client_id as customer_id, c.center_id , c.country as center_country, tb.batch_id,u.user_id, CONCAT(u.first_name,' ',u.last_name) as user_name, u.email_id, uc.loginid, urm.role_definition_id as user_role,u.created_date,am.country as user_country
	FROM client ct 
	
	JOIN tblx_center c on ct.client_id = c.client_id 
    JOIN tblx_batch tb ON c.center_id = tb.center_id 
	JOIN tblx_batch_user_map tbum ON tbum.batch_id = tb.batch_id and tbum.center_id = c.center_id
    JOIN user u on u.user_id = tbum.user_id
    JOIN address_master am on am.address_id = u.address_id
    JOIN user_role_map urm on urm.user_id = u.user_id
    JOIN user_credential uc on uc.user_id = u.user_id
	where  ct.client_id = 2 and urm.role_definition_id = 2  AND uc.is_active=1 order by c.center_id desc";  
//echo $query; die;
$stmt = $con->prepare($query);
$stmt->execute();
$RESULT = get_dbresult($stmt);
$stmt->close();

foreach( $RESULT as $dataVal ){
    
		$center_country="";
		$user_id = $dataVal['user_id'];
		$batch_id = $dataVal['batch_id'];  
		$dataVal['user_country'] = !empty($dataVal['user_country'])?$dataVal['user_country']:'NA';
		$user_language = 'NA'; 
		
		if($dataVal['center_country']!=$center_country){
			$center_country =$dataVal['center_country'];
			$region_ids = getRegioIdsByCountry($con,$dataVal['center_country']);
		}
		$region_ids = trim($region_ids);
		$region_ids = !empty($region_ids)?$region_ids:0;
	
		//get course list 
		foreach($course_arr as $crs_key=>$crs_val){

			
			$chk_crs_time = chkUserCourseTimeSpent($con,$user_id,$crs_val->code);

			
			if($chk_crs_time==true){
			
			
			$course_edge_id = $crs_val->edge_id;
			
			echo $dataVal['course_id'] = $crs_val->course_id;
			$dataVal['code'] = $crs_val->code;
			$dataVal['title'] = $crs_val->title;
			$dataVal['description'] = $crs_val->description;
			$course_code = $dataVal['code'];
			
				
				foreach($crs_val->topicArr as $key => $value){ 
					
						$chk_topic_time = chkTopicTimeSpent($con,$user_id,$value->edge_id);
						
						if($chk_topic_time==true){
							
						$singleChapterArr = $value->chapterArr;
						$ttl_chapter = count($singleChapterArr);
						foreach($singleChapterArr as $chapterArrKey=>$chapterArrVal){
							
							$chk_chapter_time = chkTopicTimeSpent($con,$user_id,$chapterArrVal->edge_id);
							if($chk_chapter_time==true){
							$sql = "SELECT tta.attempt_no, COALESCE( SUM( tta.ttl_correct ) , 0 ) AS ttlCorrect, COALESCE( SUM( tta.ttl_time_sp ) , 0 ) AS ttl_time_sp from temp_test_attempt tta  
							JOIN tbl_component tc ON tta.test_id= tc.component_edge_id 
							where tc.parent_edge_id = ? and tta.user_id = ? GROUP BY tta.attempt_no";	
							$stmt = $con->prepare($sql);
									$stmt->bind_param("ii",$chapterArrVal->edge_id,$user_id);
									$stmt->execute();
									$stmt->bind_result($attempt_no,$ttlChpaterCrrct,$ttl_time_sp);
									$stmt->execute();
									$attemptArr = array();
									
									while($stmt->fetch()) {
									
										$bcm = new stdClass();
										$bcm->quiz_attempt_no = $attempt_no;
										$bcm->ttlChpaterCrrct = $ttlChpaterCrrct;
										$bcm->quiz_time_sp = $ttl_time_sp;
										if($chapterArrVal->ttlChpaterQCount!=0){
										$quiz_score = ($ttlChpaterCrrct/$chapterArrVal->ttlChpaterQCount)*100;
										}else{
											$quiz_score=0;
										}
										$quiz_score = round($quiz_score);
										$quiz_score = !empty($quiz_score)?$quiz_score:0;
										$bcm->quiz_score = $quiz_score;
										array_push($attemptArr,$bcm); 
									 
									}
									$stmt->close();	 
									$no_of_attempt = count($attemptArr);
								
								
							if($no_of_attempt>=0){
								foreach($attemptArr as $key=>$val){					
								$quiz_last_attempted_date = "";
								//get max attempt date
								$sql = "SELECT  MAX( tap.fld_datetime ) AS last_date from temp_ans_push tap 
								JOIN tbl_component tc ON tap.test_id= tc.component_edge_id 
								where tc.parent_edge_id = ? and tap.user_id = ?  and tap.attempt_no = ?";
								$stmt = $con->prepare($sql); 
								$stmt->bind_param("iii",$chapterArrVal->edge_id,$user_id,$val->quiz_attempt_no);
								$stmt->execute();
								$stmt->bind_result($last_date);
								$stmt->fetch();
								$stmt->execute();
								$stmt->close(); 
								if($last_date!=""){
										$quiz_last_attempted_date = date('Y-m-d H:i:s', strtotime($last_date)); 
									}
									
										
					$sql = "insert into rpt_user_session_tracking_temp (country_name,center_id, batch_id,user_id,user_name, user_email, user_login_id,user_joining_date,user_country, course_edge_id,course_id,course_code, course,topic_edge_id,topic_name,topic_description,topic_type,chapter_edge_id,chapter_name,chapter_description,skill_id, skill_name,chapter_ttl_ques,chapter_ttl_correct,chapter_attempt,last_attempted_date,quiz_time_sp,quiz_score,quiz_last_attempted_date,no_of_attempt,record_type,region_id,create_date) Values (?, ?,?,?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())";
					$stmt = $con1->prepare($sql);
					$stmt->bind_param('siiisssssiississsissisiiisiisiss', $dataVal['center_country'], $dataVal['center_id'], $dataVal['batch_id'], $dataVal['user_id'], $dataVal['user_name'], $dataVal['email_id'], $dataVal['loginid'], $dataVal['created_date'], $dataVal['user_country'],$course_edge_id, $dataVal['course_id'], $dataVal['code'], $dataVal['title'],$value->edge_id,$value->name,$value->description,$value->topic_type, $chapterArrVal->edge_id, $chapterArrVal->name,$chapterArrVal->description,$chapterArrVal->chapterSkill,$chapterArrVal->competency,$chapterArrVal->ttlChpaterQCount,$val->ttlChpaterCrrct,$val->quiz_attempt_no, $quiz_last_attempted_date,$val->quiz_time_sp,$val->quiz_score,$quiz_last_attempted_date,$no_of_attempt,$record_type,$region_ids);
					$stmt->execute(); 
					$stmt->close(); 
									
							}
									
								
									
						}
					}
					}
									
				
				
				
				
		
		}
		}
	} 
		}


	

}

function get_dbresult( $Statement ) {
        $RESULT = array();
        $Statement->store_result();
        for ( $i = 0; $i < $Statement->num_rows; $i++ ) {
            $Metadata = $Statement->result_metadata();
            $PARAMS = array();
            while ( $Field = $Metadata->fetch_field() ) {
                $PARAMS[] = &$RESULT[ $i ][ $Field->name ];
            }
            call_user_func_array( array( $Statement, 'bind_result' ), $PARAMS );
            $Statement->fetch();
        }
        return $RESULT;
}

function getAllCourseByClientId($client_id=2){
		$con = createConnection();
		
		
		$courseArr = array();
		
		$topicArr1 = array();
		$stmt = $con->prepare("SELECT c.course_id,c.code, c.title, c.description, gmt.edge_id FROM course c JOIN generic_mpre_tree gmt ON gmt.tree_node_id = c.tree_node_id WHERE c.course_type=0 and c.client_id = ? and level_id!=0 order by level_id");
		$stmt->bind_param("i",$client_id);
		$stmt->execute();
		$stmt->bind_result($course_id,$code,$title,$description,$edge_id);
		$stmt->execute();
		while($stmt->fetch()) {
			$topic1 = new stdClass();
			$topic1->course_id = $course_id;
			$topic1->code = $code;
			$topic1->title = $title;
			$topic1->description = $description;
			$topic1->edge_id = $edge_id;
            
			array_push($courseArr,$topic1);
		}
		$stmt->close();
		return $courseArr;

}


function getTopicOrAssessmentByCourseId($course_edge_id){
		$con = createConnection();
		
		
		$topicArr = array(); 
		$stmt = $con->prepare("SELECT cm.tree_node_id, cm.name,cm.description,cm.assessment_type,
            gmt.edge_id,tnd.tree_node_category_id,topic_type
								FROM generic_mpre_tree gmt
								JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
								JOIN cap_module cm ON cm.tree_node_id = gmt.tree_node_id
								WHERE gmt.is_active = 1 AND tree_node_super_root = ? AND tnd.tree_node_category_id IN(3,5) AND (cm.assessment_type IS NULL) AND topic_type = 1 ORDER BY sequence_id");
		$stmt->bind_param("i",$course_edge_id);
		$stmt->execute();
		$stmt->bind_result($tree_node_id,$name,$description,$assessment_type,$edge_id,$tree_node_category_id,$topic_type);
		$stmt->execute();
		while($stmt->fetch()) {
			$topic2 = new stdClass();
			$topic2->tree_node_id = $tree_node_id;
			$topic2->name = $name;
			$topic2->description = $description;
			$topic2->assessment_type = $assessment_type;
          	$topic2->edge_id = $edge_id;
			$topic2->tree_node_category_id = $tree_node_category_id;
			$topic2->topic_type = $topic_type;
			array_push($topicArr,$topic2);
		}
		$stmt->close();
		
		return $topicArr;

}

function getChpaterByTopicEdgeId($con,$topic_edge_id){
	$chapterArray = array();
	$stmt = $con->prepare("SELECT gmt.edge_id, cm.code, cm.title, cm.chapterSkill, trc.competency FROM generic_mpre_tree gmt
									JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
									JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id
									JOIN tbl_rubric_competency trc ON cm.chapterSkill = trc.id
									WHERE gmt.is_active = 1 AND tree_node_parent = ? AND tnd.tree_node_category_id=2 order by cm.sequence_no");
			$stmt->bind_param("i",$topic_edge_id);
			$stmt->execute();
			$stmt->bind_result($edge_id,$code,$title,$chapterSkill,$competency);
			while($stmt->fetch()) {
				$topic = new stdClass();
				$topic->edge_id = $edge_id;
				$topic->name = $code;
				$topic->description = $title;
				$topic->chapterSkill = $chapterSkill;
				$topic->competency = $competency;
				array_push($chapterArray,$topic);
			}
			$stmt->close();
	return $chapterArray;
}

function chkComponentCompletion($con,$user_id,$componentEdgeId){
	
	$stmt = $con->prepare("select id, completion from tblx_component_completion where user_id=? AND component_edge_id=? AND completion='c'");
	$stmt->bind_param("ii",$user_id,$componentEdgeId);	
	$stmt->execute();
	$stmt->bind_result($topic_c_id,$completion);
	$stmt->fetch();
	$stmt->close();
	if(!empty($topic_c_id) && $topic_c_id!=NULL){
		return true;
	}
	return false;
}

function getRegioIdsByCountry($con,$country_name){
	$regionIdArray = array();
	$stmt = $con->prepare("select region_id from tblx_region_country_map where country_name=?");
	$stmt->bind_param("s",$country_name);	
	$stmt->execute();
	$stmt->bind_result($region_id);
	while($stmt->fetch()) {
		array_push($regionIdArray,$region_id);
	}
	$stmt->close();
	$regionIdArray = array_filter($regionIdArray);
	$regionIdArray = array_unique($regionIdArray);
	$regionids = implode(',',$regionIdArray);
	
	return $regionids;
}

function chkUserCourseTimeSpent($con,$user_id,$course_code){
	
	$stmt = $con->prepare("select id from tblx_component_completion where user_id=? and course_code=?");
	$stmt->bind_param("is",$user_id,$course_code);	
	$stmt->execute();
	$stmt->bind_result($id);
	$stmt->fetch();
	$stmt->close();
	if(!empty($id) && $id!=NULL){
		return true;
	}
	return false;
}
 
function chkTopicTimeSpent($con,$user_id,$topic_edge_id){
	
	$stmt = $con->prepare("select id from tblx_component_completion where user_id=? and component_edge_id=?");
	$stmt->bind_param("is",$user_id,$topic_edge_id);	
	$stmt->execute();
	$stmt->bind_result($id);
	$stmt->fetch();
	$stmt->close();
	if(!empty($id) && $id!=NULL){
		return true;
	}
	return false;
}
   
    ?>

    

<?php 
$cron_log_content .= "session_tracking - ".date('g:i A, j M Y');
$cron_log_content .= PHP_EOL;
file_put_contents('cron-log.txt', $cron_log_content, FILE_APPEND);
echo "Completed!";
?>