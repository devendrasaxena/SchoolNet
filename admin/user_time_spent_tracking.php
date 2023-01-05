<?php 
ini_set('max_execution_time', 10000000);
include("../config/config.php");
//ini_set('display_errors',1);
//error_reporting(E_ALL);  

$cron_log_content = "".PHP_EOL;
$cron_log_content .= "user time spent tracking ";
$cron_log_content .= " - " .date('g:i A, j M Y');
$quiz_last_attempted_date = "";
$record_type = "chapter";
$no_of_attempt =0;
$quiz_time_sp=0;
	$con = createConnection();
	$con1 = createConnection();
	 $sql1 = "SELECT rpt_id from rpt_user_time_spent_tracking limit 1";
        $stmt1 = $con->prepare($sql1);
        $stmt1->execute();
        $rptDataExist = $stmt1->fetch();
        $stmt1->close();

        if( $rptDataExist ){
           
           $sql = "TRUNCATE table rpt_user_time_spent_tracking";
            $stmt = $con->prepare($sql);
            $stmt->execute();
            $stmt->close();

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
$ttl_chapter =  array();
$course_list = getAllCourseByClientId(2);
foreach($course_list as $crs_key=>$crs_val){

	
	$course_edge_id = $crs_val->edge_id;
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
	$ttl_chapter[]=$number_of_chapters;
	
	
	
}



$ttl_chapter = array_sum($ttl_chapter);

	$query = "SELECT country_name,center_id,batch_id,user_id,user_name,user_email,user_login_id,user_joining_date,user_country,region_id,SUM(quiz_time_sp) as time_sp,MAX(quiz_last_attempted_date) as last_attempted_date from rpt_user_session_tracking_temp group by user_id";  
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

		$current_level = getUserCurrentLevel($con,$user_id);

		//get completed chapter 
			$number_of_completed_chapter = 0;
			$stmt = $con->prepare("SELECT SUM(completed_chapter) as completed_chapter from rpt_user_performance where user_id=?");
			$stmt->bind_param("i",$user_id);
			$stmt->execute();
			$stmt->bind_result($number_of_completed_chapter);
			$stmt->fetch();
			$stmt->close();	 
		$number_of_completed_chapter = !empty($number_of_completed_chapter)?$number_of_completed_chapter:0;

		
		$region_ids = trim($dataVal['region_id']);
		$region_ids = !empty($region_ids)?$region_ids:0;
				
		$sql = "insert into rpt_user_time_spent_tracking (country_name,center_id, batch_id,user_id,user_name, user_email, user_login_id,user_joining_date,user_country, ttl_chapter,completed_chapter,course_time,last_attempted_date,current_level,region_id,create_date) Values (?, ?,?,?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,NOW())";
		$stmt = $con1->prepare($sql);
		$stmt->bind_param('siiisssssiiisss', $dataVal['country_name'], $dataVal['center_id'], $dataVal['batch_id'], $dataVal['user_id'], $dataVal['user_name'], $dataVal['user_email'], $dataVal['user_login_id'], $dataVal['user_joining_date'], $dataVal['user_country'],$ttl_chapter,$number_of_completed_chapter,$dataVal['time_sp'],$dataVal['last_attempted_date'],$current_level,$region_ids);
		$stmt->execute(); 
		$stmt->close(); 
									
					
									
				
				
				
				
		
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

function getUserCurrentLevel($con,$user_id){
	
	$stmt = $con->prepare("select user_current_level from tbl_user_lti_score where user_id=?");
	$stmt->bind_param("i",$user_id);	
	$stmt->execute();
	$stmt->bind_result($level);
	$stmt->fetch();
	$stmt->close();
	if(isset($level) && !empty($level) && $level!=NULL){
		return 'Level '.$level;
	}
	return 'NA';
}

   
    ?>

    

<?php 
$cron_log_content .= "time_spent - ".date('g:i A, j M Y');
$cron_log_content .= PHP_EOL;
file_put_contents('cron-log.txt', $cron_log_content, FILE_APPEND);
echo "Completed!";
?>