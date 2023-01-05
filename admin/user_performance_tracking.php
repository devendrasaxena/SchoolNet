<?php 
include("../config/config.php");
ini_set('max_execution_time', 0);
//ini_set('display_errors',1);
//error_reporting(E_ALL);  

$cron_log_content = "".PHP_EOL;
$cron_log_content .= "user performance tracking ";
$cron_log_content .= " - " .date('g:i A, j M Y');

$con = createConnection();

$query = "SELECT m1 . * FROM rpt_user_session_tracking_temp m1 LEFT JOIN rpt_user_session_tracking_temp m2 ON ( m1.user_id = m2.user_id AND m1.chapter_edge_id = m2.chapter_edge_id AND m1.center_id = m2.center_id AND m1.batch_id = m2.batch_id AND m1.record_type = m2.record_type AND m1.rpt_id > m2.rpt_id) WHERE m2.rpt_id IS NULL";



/* SELECT *,MAX(attempt) from rpt_user_session_tracking_temp where record_type='chapter' group by user_id,chapter_edge_id,center_id,batch_id limit 100"; */
//echo $query; die;
$stmt = $con->prepare($query);
$stmt->execute();
$RESULT = get_dbresult($stmt);
$stmt->close();
foreach( $RESULT as $row ){
    $data[] = $row;
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

function getChapterDetailByChapterEdgeid($edge_id){
	$con = createConnection();
	$query = "Select sn.* from session_node sn join generic_mpre_tree gmt on sn.tree_node_id=gmt.tree_node_id where gmt.edge_id=$edge_id";
	//echo $query; die;
	$stmt = $con->prepare($query);
	$stmt->execute();
	$RESULT = get_dbresult($stmt);
	$stmt->close();
	return $RESULT[0];


}

function getChpaterCount($con,$topic_edge_id,$skill_id){
	$chapterArray = array();
	$stmt = $con->prepare("SELECT count(*) as cnt FROM generic_mpre_tree gmt
									JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
									JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id
									WHERE gmt.is_active = 1 AND tree_node_parent = ? AND tnd.tree_node_category_id=2 and cm.chapterSkill=?");
			$stmt->bind_param("ii",$topic_edge_id,$skill_id);
			$stmt->execute();
			$stmt->bind_result($cnt);
			$stmt->fetch();
			$stmt->execute();
			$stmt->close();
		return $cnt;
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






//Calculate topic skill wise score
if ( $data > 0 && !empty($data) ){ 
    
   $arr = array();
	foreach($data  as $key => $value){
		
		$region_ids = $value['region_id'];
		$region_ids = trim($region_ids);
		$region_ids = !empty($region_ids)?$region_ids:0;
		if($value['topic_type'] == 1 && $value['record_type']=='chapter'){
			
			if($value['topic_edge_id'] == $topic_edge_id && $value['user_id'] == $user_id){	
			
			$chk = chkComponentCompletion($con,$user_id,$value['chapter_edge_id']);
			if($chk==true){
				$value['chapter_completed']=1;
			}else{
				$value['chapter_completed']=0;
			}

				if($value['last_attempted_date']!=""){
					$dateTimestamp1 = strtotime($last_attempted); 
					$dateTimestamp2 = strtotime($value['last_attempted_date']); 
					if ($dateTimestamp2 > $dateTimestamp1) {
						$last_attempted = $value['last_attempted_date'];
					}
					
				}
								 
				if($value['skill_name']=='Reading')
				 {	
					$readin_chapter++;
					if($value['chapter_completed']==1)
					{
						$readin_chapter_completed++;
						$ch_detail = getChapterDetailByChapterEdgeid($value['chapter_edge_id']);
						$readin_chapter_completed_arr[$readin_chapter_completed] = $ch_detail;
					
					}
					 
					$ttl_reading_ques = $ttl_reading_ques + $value['chapter_ttl_ques'];
					$ttl_reading_correct = $ttl_reading_correct + $value['chapter_ttl_correct'];
					if($ttl_reading_ques!=0){
					$reading_per = ($ttl_reading_correct*100)/$ttl_reading_ques;
					$reading_per = !empty($reading_per)?round($reading_per):0;
					}else{
						$reading_per=0;
					}
					
					$arr[$value['user_id']][$value['topic_edge_id']][$value['skill_id']] = array('user_name'=>$value['user_name'],'course_id'=>$value['course_id'],'course_name'=>$value['course'],'topic_edge_id'=>$value['topic_edge_id'],'topic_name'=>$value['topic_name'],'skill_per'=>$reading_per,'skill_ques'=>$ttl_reading_ques,'skill_correct'=>$ttl_reading_correct,'customer_id'=>$value['customer_id'], 'customer'=>$value['customer'], 'country_name'=>$value['country_name'], 'center_id'=>$value['center_id'], 'center_code'=>$value['center_code'], 'center_name'=>$value['center_name'], 'batch_id'=>$value['batch_id'], 'batch_code'=>$value['batch_code'], 'batch_name'=>$value['batch_name'], 'user_id'=>$value['user_id'], 'user_name'=>$value['user_name'], 'user_email'=>$value['user_email'], 'user_login_id'=>$value['user_login_id'], 'user_role'=>$value['user_role'], 'user_joining_date'=>$value['user_joining_date'], 'user_country'=>$value['user_country'],'user_language'=>$value['user_language'],'user_status'=>$value['user_status'],'course_edge_id'=>$value['course_edge_id'], 'course_id'=>$value['course_id'],'course_code'=>$value['course_code'], 'course'=>$value['course'], 'course_description'=>$value['course_description'],'topic_edge_id'=>$value['topic_edge_id'],'topic_name'=>$value['topic_name'],'topic_description'=>$value['topic_description'],'topic_type'=>$value['topic_type'],'assessment_type'=>$value['assessment_type'],'last_attempted'=>$last_attempted,'chapter_completed'=>$readin_chapter_completed,'ttl_chapter'=>$readin_chapter,'skill_id'=>$value['skill_id'],'skill_name'=>$value['skill_name'],'completed_chapter_arr'=>$readin_chapter_completed_arr,'region_ids'=>$region_ids);			

					 
				 
				 }
				elseif($value['skill_name']=='Writing')
				 {
					$writing_chapter++;
					if($value['chapter_completed']==1)
					{
						$writing_chapter_completed++;
						$ch_detail = getChapterDetailByChapterEdgeid($value['chapter_edge_id']);
						$writing_chapter_completed_arr[$writing_chapter_completed] = $ch_detail;
					}
					$ttl_writing_ques = $ttl_writing_ques + $value['chapter_ttl_ques'];
					$ttl_writing_correct = $ttl_writing_correct + $value['chapter_ttl_correct'];
					if($ttl_writing_ques!=0){
					$writing_per = ($ttl_writing_correct*100)/$ttl_writing_ques;
					$writing_per = !empty($writing_per)?round($writing_per):0;
					}else{
						$writing_per=0;
					}
					
					$arr[$value['user_id']][$value['topic_edge_id']][$value['skill_id']] = array('user_name'=>$value['user_name'],'course_id'=>$value['course_id'],'course_name'=>$value['course'],'topic_edge_id'=>$value['topic_edge_id'],'topic_name'=>$value['topic_name'],'skill_per'=>$writing_per,'skill_ques'=>$ttl_writing_ques,'skill_correct'=>$ttl_writing_correct,'customer_id'=>$value['customer_id'], 'customer'=>$value['customer'], 'country_name'=>$value['country_name'], 'center_id'=>$value['center_id'], 'center_code'=>$value['center_code'], 'center_name'=>$value['center_name'], 'batch_id'=>$value['batch_id'], 'batch_code'=>$value['batch_code'], 'batch_name'=>$value['batch_name'], 'user_id'=>$value['user_id'], 'user_name'=>$value['user_name'], 'user_email'=>$value['user_email'], 'user_login_id'=>$value['user_login_id'], 'user_role'=>$value['user_role'], 'user_joining_date'=>$value['user_joining_date'], 'user_country'=>$value['user_country'],'user_language'=>$value['user_language'],'user_status'=>$value['user_status'],'course_edge_id'=>$value['course_edge_id'], 'course_id'=>$value['course_id'],'course_code'=>$value['course_code'], 'course'=>$value['course'], 'course_description'=>$value['course_description'],'topic_edge_id'=>$value['topic_edge_id'],'topic_name'=>$value['topic_name'],'topic_description'=>$value['topic_description'],'topic_type'=>$value['topic_type'],'assessment_type'=>$value['assessment_type'],'last_attempted'=>$last_attempted,'chapter_completed'=>$writing_chapter_completed,'ttl_chapter'=>$writing_chapter,'skill_id'=>$value['skill_id'],'skill_name'=>$value['skill_name'],'completed_chapter_arr'=>$writing_chapter_completed_arr,'region_ids'=>$region_ids);		
					 
					 
					 
					 
				 }
				elseif($value['skill_name']=='Speaking')
				 {
					$speaking_chapter++;
					if($value['chapter_completed']==1)
					{
						$speaking_chapter_completed++;
						$ch_detail = getChapterDetailByChapterEdgeid($value['chapter_edge_id']);
						$speaking_chapter_completed_arr[$speaking_chapter_completed] = $ch_detail;
					}
					$ttl_speaking_ques = $ttl_speaking_ques + $value['chapter_ttl_ques'];
					$ttl_speaking_correct= $ttl_speaking_correct + $value['chapter_ttl_correct'];
					if($ttl_speaking_ques!=0){
					$speaking_per = ($ttl_speaking_correct*100)/$ttl_speaking_ques;
					$speaking_per = !empty($speaking_per)?round($speaking_per):0;
					}else{
						$speaking_per=0;
					}
					$arr[$value['user_id']][$value['topic_edge_id']][$value['skill_id']] = array('user_name'=>$value['user_name'],'course_id'=>$value['course_id'],'course_name'=>$value['course'],'topic_edge_id'=>$value['topic_edge_id'],'topic_name'=>$value['topic_name'],'skill_per'=>$speaking_per,'skill_ques'=>$ttl_speaking_ques,'skill_correct'=>$ttl_speaking_correct,'customer_id'=>$value['customer_id'], 'customer'=>$value['customer'], 'country_name'=>$value['country_name'], 'center_id'=>$value['center_id'], 'center_code'=>$value['center_code'], 'center_name'=>$value['center_name'], 'batch_id'=>$value['batch_id'], 'batch_code'=>$value['batch_code'], 'batch_name'=>$value['batch_name'], 'user_id'=>$value['user_id'], 'user_name'=>$value['user_name'], 'user_email'=>$value['user_email'], 'user_login_id'=>$value['user_login_id'], 'user_role'=>$value['user_role'], 'user_joining_date'=>$value['user_joining_date'], 'user_country'=>$value['user_country'],'user_language'=>$value['user_language'],'user_status'=>$value['user_status'],'course_edge_id'=>$value['course_edge_id'], 'course_id'=>$value['course_id'],'course_code'=>$value['course_code'], 'course'=>$value['course'], 'course_description'=>$value['course_description'],'topic_edge_id'=>$value['topic_edge_id'],'topic_name'=>$value['topic_name'],'topic_description'=>$value['topic_description'],'topic_type'=>$value['topic_type'],'assessment_type'=>$value['assessment_type'],'last_attempted'=>$last_attempted,'chapter_completed'=>$speaking_chapter_completed,'ttl_chapter'=>$speaking_chapter,'skill_id'=>$value['skill_id'],'skill_name'=>$value['skill_name'],'completed_chapter_arr'=>$speaking_chapter_completed_arr,'region_ids'=>$region_ids);	
					 
				 }
				elseif($value['skill_name']=='Listening')
				 {	
					$listening_chapter++;
					if($value['chapter_completed']==1)
					{
					$listening_chapter_completed++;
					$ch_detail = getChapterDetailByChapterEdgeid($value['chapter_edge_id']);
					$listening_chapter_completed_arr[$listening_chapter_completed] = $ch_detail;
					}
					 $ttl_listening_ques = $ttl_listening_ques + $value['chapter_ttl_ques'];
					 $ttl_listening_correct = $ttl_listening_correct + $value['chapter_ttl_correct'];
					 if($ttl_listening_ques!=0){
					 $listening_per = ($ttl_listening_correct*100)/$ttl_listening_ques;
					 $listening_per = !empty($listening_per)?round($listening_per):0;
					 }else{
						$listening_per=0;
					 }
					 
					 $arr[$value['user_id']][$value['topic_edge_id']][$value['skill_id']] = array('user_name'=>$value['user_name'],'course_id'=>$value['course_id'],'course_name'=>$value['course'],'topic_edge_id'=>$value['topic_edge_id'],'topic_name'=>$value['topic_name'],'skill_per'=>$listening_per,'skill_ques'=>$ttl_listening_ques,'skill_correct'=>$ttl_listening_correct,'customer_id'=>$value['customer_id'], 'customer'=>$value['customer'], 'country_name'=>$value['country_name'], 'center_id'=>$value['center_id'], 'center_code'=>$value['center_code'], 'center_name'=>$value['center_name'], 'batch_id'=>$value['batch_id'], 'batch_code'=>$value['batch_code'], 'batch_name'=>$value['batch_name'], 'user_id'=>$value['user_id'], 'user_name'=>$value['user_name'], 'user_email'=>$value['user_email'], 'user_login_id'=>$value['user_login_id'], 'user_role'=>$value['user_role'], 'user_joining_date'=>$value['user_joining_date'], 'user_country'=>$value['user_country'],'user_language'=>$value['user_language'],'user_status'=>$value['user_status'],'course_edge_id'=>$value['course_edge_id'], 'course_id'=>$value['course_id'],'course_code'=>$value['course_code'], 'course'=>$value['course'], 'course_description'=>$value['course_description'],'topic_edge_id'=>$value['topic_edge_id'],'topic_name'=>$value['topic_name'],'topic_description'=>$value['topic_description'],'topic_type'=>$value['topic_type'],'assessment_type'=>$value['assessment_type'],'last_attempted'=>$last_attempted,'chapter_completed'=>$listening_chapter_completed,'ttl_chapter'=>$listening_chapter,'skill_id'=>$value['skill_id'],'skill_name'=>$value['skill_name'],'completed_chapter_arr'=>$listening_chapter_completed_arr,'region_ids'=>$region_ids);	
				
				}elseif($value['skill_name']=='Vocabulary')
				{
					$vocabulary_chapter++;
					if($value['chapter_completed']==1)
					{
						$vocabulary_chapter_completed++;
						$ch_detail = getChapterDetailByChapterEdgeid($value['chapter_edge_id']);
						$vocabulary_chapter_completed_arr[$vocabulary_chapter_completed] = $ch_detail;
					}
					 $ttl_vocabulary_ques = $ttl_vocabulary_ques + $value['chapter_ttl_ques'];
					 $ttl_vocabulary_correct = $ttl_vocabulary_correct + $value['chapter_ttl_correct'];
					 if($ttl_vocabulary_ques!=0){
					 $vocabulary_per = ($ttl_vocabulary_correct*100)/$ttl_vocabulary_ques;
					 $vocabulary_per = !empty($vocabulary_per)?round($vocabulary_per):0;
					 }else{
						 $vocabulary_per=0;
					 }
					 
					 $arr[$value['user_id']][$value['topic_edge_id']][$value['skill_id']] = array('user_name'=>$value['user_name'],'course_id'=>$value['course_id'],'course_name'=>$value['course'],'topic_edge_id'=>$value['topic_edge_id'],'topic_name'=>$value['topic_name'],'skill_per'=>$vocabulary_per,'skill_ques'=>$ttl_vocabulary_ques,'skill_correct'=>$ttl_vocabulary_correct,'customer_id'=>$value['customer_id'], 'customer'=>$value['customer'], 'country_name'=>$value['country_name'], 'center_id'=>$value['center_id'], 'center_code'=>$value['center_code'], 'center_name'=>$value['center_name'], 'batch_id'=>$value['batch_id'], 'batch_code'=>$value['batch_code'], 'batch_name'=>$value['batch_name'], 'user_id'=>$value['user_id'], 'user_name'=>$value['user_name'], 'user_email'=>$value['user_email'], 'user_login_id'=>$value['user_login_id'], 'user_role'=>$value['user_role'], 'user_joining_date'=>$value['user_joining_date'], 'user_country'=>$value['user_country'],'user_language'=>$value['user_language'],'user_status'=>$value['user_status'],'course_edge_id'=>$value['course_edge_id'], 'course_id'=>$value['course_id'],'course_code'=>$value['course_code'], 'course'=>$value['course'], 'course_description'=>$value['course_description'],'topic_edge_id'=>$value['topic_edge_id'],'topic_name'=>$value['topic_name'],'topic_description'=>$value['topic_description'],'topic_type'=>$value['topic_type'],'assessment_type'=>$value['assessment_type'],'last_attempted'=>$last_attempted,'chapter_completed'=>$vocabulary_chapter_completed,'ttl_chapter'=>$vocabulary_chapter,'skill_id'=>$value['skill_id'],'skill_name'=>$value['skill_name'],'completed_chapter_arr'=>$vocabulary_chapter_completed_arr,'region_ids'=>$region_ids);
					 
				}elseif($value['skill_name']=='Grammar')
				{	
					$grammar_chapter++;
					if($value['chapter_completed']==1)
					{
						$grammar_chapter_completed++;
						$ch_detail = getChapterDetailByChapterEdgeid($value['chapter_edge_id']);
						$grammar_chapter_completed_arr[$grammar_chapter_completed] = $ch_detail;
					}
					 $ttl_grammar_ques = $ttl_grammar_ques + $value['chapter_ttl_ques'];
					 $ttl_grammar_correct = $ttl_grammar_correct + $value['chapter_ttl_correct'];
					 if($ttl_grammar_ques!=0){
					 $grammar_per = ($ttl_grammar_correct*100)/$ttl_grammar_ques;
					 $grammar_per = !empty($grammar_per)?round($grammar_per):0;
					 }else{
						$grammar_per=0;
					 }
					 $arr[$value['user_id']][$value['topic_edge_id']][$value['skill_id']] = array('user_name'=>$value['user_name'],'course_id'=>$value['course_id'],'course_name'=>$value['course'],'topic_edge_id'=>$value['topic_edge_id'],'topic_name'=>$value['topic_name'],'skill_per'=>$grammar_per,'skill_ques'=>$ttl_grammar_ques,'skill_correct'=>$ttl_grammar_correct,'customer_id'=>$value['customer_id'], 'customer'=>$value['customer'], 'country_name'=>$value['country_name'], 'center_id'=>$value['center_id'], 'center_code'=>$value['center_code'], 'center_name'=>$value['center_name'], 'batch_id'=>$value['batch_id'], 'batch_code'=>$value['batch_code'], 'batch_name'=>$value['batch_name'], 'user_id'=>$value['user_id'], 'user_name'=>$value['user_name'], 'user_email'=>$value['user_email'], 'user_login_id'=>$value['user_login_id'], 'user_role'=>$value['user_role'], 'user_joining_date'=>$value['user_joining_date'], 'user_country'=>$value['user_country'],'user_language'=>$value['user_language'],'user_status'=>$value['user_status'],'course_edge_id'=>$value['course_edge_id'], 'course_id'=>$value['course_id'],'course_code'=>$value['course_code'], 'course'=>$value['course'], 'course_description'=>$value['course_description'],'topic_edge_id'=>$value['topic_edge_id'],'topic_name'=>$value['topic_name'],'topic_description'=>$value['topic_description'],'topic_type'=>$value['topic_type'],'assessment_type'=>$value['assessment_type'],'last_attempted'=>$last_attempted,'chapter_completed'=>$grammar_chapter_completed,'ttl_chapter'=>$grammar_chapter,'skill_id'=>$value['skill_id'],'skill_name'=>$value['skill_name'],'completed_chapter_arr'=>$grammar_chapter_completed_arr,'region_ids'=>$region_ids);
				}
				 
				
			 }
			else{
				$last_attempted = $value['last_attempted_date'];
				$reading_per = $ttl_reading_correct = $ttl_reading_ques = $writing_per = $ttl_writing_correct = $ttl_writing_ques =	$speaking_per = $ttl_speaking_correct = $ttl_speaking_ques = $listening_per = $ttl_listening_correct = $ttl_listening_ques =
				$vocabulary_per = $ttl_vocabulary_correct = $ttl_vocabulary_ques = $grammar_per = $ttl_grammar_correct = $ttl_grammar_ques = $readin_chapter = $readin_chapter_completed = $writing_chapter = $writing_chapter_completed = $speaking_chapter = $speaking_chapter_completed = $listening_chapter = $listening_chapter_completed = $vocabulary_chapter = $vocabulary_chapter_completed = $grammar_chapter = $grammar_chapter_completed = 0;
				$readin_chapter_completed_arr = $writing_chapter_completed_arr = $speaking_chapter_completed_arr = $listening_chapter_completed_arr = $vocabulary_chapter_completed_arr = $grammar_chapter_completed_arr = array();
				
				
				
				
								 
				if($value['skill_name']=='Reading')
				 {	
					$readin_chapter++;
					if($value['chapter_completed']==1)
					{
						$readin_chapter_completed++;
						$ch_detail = getChapterDetailByChapterEdgeid($value['chapter_edge_id']);
						$readin_chapter_completed_arr[$readin_chapter_completed] = $ch_detail;
					}
					 
					$ttl_reading_ques = $ttl_reading_ques + $value['chapter_ttl_ques'];
					$ttl_reading_correct = $ttl_reading_correct + $value['chapter_ttl_correct'];
					 if($ttl_reading_ques!=0){
					$reading_per = ($ttl_reading_correct*100)/$ttl_reading_ques;
					$reading_per = !empty($reading_per)?round($reading_per):0;
					 }else{
						$reading_per=0;
					 }
					
					$arr[$value['user_id']][$value['topic_edge_id']][$value['skill_id']] = array('user_name'=>$value['user_name'],'course_id'=>$value['course_id'],'course_name'=>$value['course'],'topic_edge_id'=>$value['topic_edge_id'],'topic_name'=>$value['topic_name'],'skill_per'=>$reading_per,'skill_ques'=>$ttl_reading_ques,'skill_correct'=>$ttl_reading_correct,'customer_id'=>$value['customer_id'], 'customer'=>$value['customer'], 'country_name'=>$value['country_name'], 'center_id'=>$value['center_id'], 'center_code'=>$value['center_code'], 'center_name'=>$value['center_name'], 'batch_id'=>$value['batch_id'], 'batch_code'=>$value['batch_code'], 'batch_name'=>$value['batch_name'], 'user_id'=>$value['user_id'], 'user_name'=>$value['user_name'], 'user_email'=>$value['user_email'], 'user_login_id'=>$value['user_login_id'], 'user_role'=>$value['user_role'], 'user_joining_date'=>$value['user_joining_date'], 'user_country'=>$value['user_country'],'user_language'=>$value['user_language'],'user_status'=>$value['user_status'],'course_edge_id'=>$value['course_edge_id'], 'course_id'=>$value['course_id'],'course_code'=>$value['course_code'], 'course'=>$value['course'], 'course_description'=>$value['course_description'],'topic_edge_id'=>$value['topic_edge_id'],'topic_name'=>$value['topic_name'],'topic_description'=>$value['topic_description'],'topic_type'=>$value['topic_type'],'assessment_type'=>$value['assessment_type'],'last_attempted'=>$last_attempted,'chapter_completed'=>$readin_chapter_completed,'ttl_chapter'=>$readin_chapter,'skill_id'=>$value['skill_id'],'skill_name'=>$value['skill_name'],'completed_chapter_arr'=>$readin_chapter_completed_arr,'region_ids'=>$region_ids);			

					 
				 
				 }
				elseif($value['skill_name']=='Writing')
				 {
					$writing_chapter++;
					if($value['chapter_completed']==1)
					{
						$writing_chapter_completed++;
						$ch_detail = getChapterDetailByChapterEdgeid($value['chapter_edge_id']);
						$writing_chapter_completed_arr[$writing_chapter_completed] = $ch_detail;
					}
					$ttl_writing_ques = $ttl_writing_ques + $value['chapter_ttl_ques'];
					$ttl_writing_correct = $ttl_writing_correct + $value['chapter_ttl_correct'];
					if($ttl_writing_ques!=0){
					$writing_per = ($ttl_writing_correct*100)/$ttl_writing_ques;
					$writing_per = !empty($writing_per)?round($writing_per):0;
					}else{
						$writing_per=0;
					}
					
					$arr[$value['user_id']][$value['topic_edge_id']][$value['skill_id']] = array('user_name'=>$value['user_name'],'course_id'=>$value['course_id'],'course_name'=>$value['course'],'topic_edge_id'=>$value['topic_edge_id'],'topic_name'=>$value['topic_name'],'skill_per'=>$writing_per,'skill_ques'=>$ttl_writing_ques,'skill_correct'=>$ttl_writing_correct,'customer_id'=>$value['customer_id'], 'customer'=>$value['customer'], 'country_name'=>$value['country_name'], 'center_id'=>$value['center_id'], 'center_code'=>$value['center_code'], 'center_name'=>$value['center_name'], 'batch_id'=>$value['batch_id'], 'batch_code'=>$value['batch_code'], 'batch_name'=>$value['batch_name'], 'user_id'=>$value['user_id'], 'user_name'=>$value['user_name'], 'user_email'=>$value['user_email'], 'user_login_id'=>$value['user_login_id'], 'user_role'=>$value['user_role'], 'user_joining_date'=>$value['user_joining_date'], 'user_country'=>$value['user_country'],'user_language'=>$value['user_language'],'user_status'=>$value['user_status'],'course_edge_id'=>$value['course_edge_id'], 'course_id'=>$value['course_id'],'course_code'=>$value['course_code'], 'course'=>$value['course'], 'course_description'=>$value['course_description'],'topic_edge_id'=>$value['topic_edge_id'],'topic_name'=>$value['topic_name'],'topic_description'=>$value['topic_description'],'topic_type'=>$value['topic_type'],'assessment_type'=>$value['assessment_type'],'last_attempted'=>$last_attempted,'chapter_completed'=>$writing_chapter_completed,'ttl_chapter'=>$writing_chapter,'skill_id'=>$value['skill_id'],'skill_name'=>$value['skill_name'],'completed_chapter_arr'=>$writing_chapter_completed_arr,'region_ids'=>$region_ids);		
					 
					 
					 
					 
				 }
				elseif($value['skill_name']=='Speaking')
				 {
					$speaking_chapter++;
					if($value['chapter_completed']==1)
					{
					$speaking_chapter_completed++;
					$ch_detail = getChapterDetailByChapterEdgeid($value['chapter_edge_id']);
					$speaking_chapter_completed_arr[$speaking_chapter_completed] = $ch_detail;
					}
					$ttl_speaking_ques = $ttl_speaking_ques + $value['chapter_ttl_ques'];
					$ttl_speaking_correct= $ttl_speaking_correct + $value['chapter_ttl_correct'];
					if($ttl_speaking_ques!=0){
					$speaking_per = ($ttl_speaking_correct*100)/$ttl_speaking_ques;
					$speaking_per = !empty($speaking_per)?round($speaking_per):0;
					}else{
						$speaking_per=0;
					}
					$arr[$value['user_id']][$value['topic_edge_id']][$value['skill_id']] = array('user_name'=>$value['user_name'],'course_id'=>$value['course_id'],'course_name'=>$value['course'],'topic_edge_id'=>$value['topic_edge_id'],'topic_name'=>$value['topic_name'],'skill_per'=>$speaking_per,'skill_ques'=>$ttl_speaking_ques,'skill_correct'=>$ttl_speaking_correct,'customer_id'=>$value['customer_id'], 'customer'=>$value['customer'], 'country_name'=>$value['country_name'], 'center_id'=>$value['center_id'], 'center_code'=>$value['center_code'], 'center_name'=>$value['center_name'], 'batch_id'=>$value['batch_id'], 'batch_code'=>$value['batch_code'], 'batch_name'=>$value['batch_name'], 'user_id'=>$value['user_id'], 'user_name'=>$value['user_name'], 'user_email'=>$value['user_email'], 'user_login_id'=>$value['user_login_id'], 'user_role'=>$value['user_role'], 'user_joining_date'=>$value['user_joining_date'], 'user_country'=>$value['user_country'],'user_language'=>$value['user_language'],'user_status'=>$value['user_status'],'course_edge_id'=>$value['course_edge_id'], 'course_id'=>$value['course_id'],'course_code'=>$value['course_code'], 'course'=>$value['course'], 'course_description'=>$value['course_description'],'topic_edge_id'=>$value['topic_edge_id'],'topic_name'=>$value['topic_name'],'topic_description'=>$value['topic_description'],'topic_type'=>$value['topic_type'],'assessment_type'=>$value['assessment_type'],'last_attempted'=>$last_attempted,'chapter_completed'=>$speaking_chapter_completed,'ttl_chapter'=>$speaking_chapter,'skill_id'=>$value['skill_id'],'skill_name'=>$value['skill_name'],'completed_chapter_arr'=>$speaking_chapter_completed_arr,'region_ids'=>$region_ids);	
					 
				 }
				elseif($value['skill_name']=='Listening')
				 {	
					$listening_chapter++;
					if($value['chapter_completed']==1)
					{
					$listening_chapter_completed++;
					$ch_detail = getChapterDetailByChapterEdgeid($value['chapter_edge_id']);
					$listening_chapter_completed_arr[$listening_chapter_completed] = $ch_detail;
					}
					 $ttl_listening_ques = $ttl_listening_ques + $value['chapter_ttl_ques'];
					 $ttl_listening_correct = $ttl_listening_correct + $value['chapter_ttl_correct'];
					 if($ttl_listening_ques!=0){
					 $listening_per = ($ttl_listening_correct*100)/$ttl_listening_ques;
					 $listening_per = !empty($listening_per)?round($listening_per):0;
					 }else{
						 $listening_per=0;
					 }
					 
					 $arr[$value['user_id']][$value['topic_edge_id']][$value['skill_id']] = array('user_name'=>$value['user_name'],'course_id'=>$value['course_id'],'course_name'=>$value['course'],'topic_edge_id'=>$value['topic_edge_id'],'topic_name'=>$value['topic_name'],'skill_per'=>$listening_per,'skill_ques'=>$ttl_listening_ques,'skill_correct'=>$ttl_listening_correct,'customer_id'=>$value['customer_id'], 'customer'=>$value['customer'], 'country_name'=>$value['country_name'], 'center_id'=>$value['center_id'], 'center_code'=>$value['center_code'], 'center_name'=>$value['center_name'], 'batch_id'=>$value['batch_id'], 'batch_code'=>$value['batch_code'], 'batch_name'=>$value['batch_name'], 'user_id'=>$value['user_id'], 'user_name'=>$value['user_name'], 'user_email'=>$value['user_email'], 'user_login_id'=>$value['user_login_id'], 'user_role'=>$value['user_role'], 'user_joining_date'=>$value['user_joining_date'], 'user_country'=>$value['user_country'],'user_language'=>$value['user_language'],'user_status'=>$value['user_status'],'course_edge_id'=>$value['course_edge_id'], 'course_id'=>$value['course_id'],'course_code'=>$value['course_code'], 'course'=>$value['course'], 'course_description'=>$value['course_description'],'topic_edge_id'=>$value['topic_edge_id'],'topic_name'=>$value['topic_name'],'topic_description'=>$value['topic_description'],'topic_type'=>$value['topic_type'],'assessment_type'=>$value['assessment_type'],'last_attempted'=>$last_attempted,'chapter_completed'=>$listening_chapter_completed,'ttl_chapter'=>$listening_chapter,'skill_id'=>$value['skill_id'],'skill_name'=>$value['skill_name'],'completed_chapter_arr'=>$listening_chapter_completed_arr,'region_ids'=>$region_ids);	
				
				}elseif($value['skill_name']=='Vocabulary')
				{
					$vocabulary_chapter++;
					if($value['chapter_completed']==1)
					{
						$vocabulary_chapter_completed++;
						$ch_detail = getChapterDetailByChapterEdgeid($value['chapter_edge_id']);
						$vocabulary_chapter_completed_arr[$vocabulary_chapter_completed] = $ch_detail;
					}
					 $ttl_vocabulary_ques = $ttl_vocabulary_ques + $value['chapter_ttl_ques'];
					 $ttl_vocabulary_correct = $ttl_vocabulary_correct + $value['chapter_ttl_correct'];
					 if($ttl_vocabulary_ques!=0){
					 $vocabulary_per = ($ttl_vocabulary_correct*100)/$ttl_vocabulary_ques;
					 $vocabulary_per = !empty($vocabulary_per)?round($vocabulary_per):0;
					 }else{
						 $vocabulary_per=0;
					 }
					 
					 $arr[$value['user_id']][$value['topic_edge_id']][$value['skill_id']] = array('user_name'=>$value['user_name'],'course_id'=>$value['course_id'],'course_name'=>$value['course'],'topic_edge_id'=>$value['topic_edge_id'],'topic_name'=>$value['topic_name'],'skill_per'=>$vocabulary_per,'skill_ques'=>$ttl_vocabulary_ques,'skill_correct'=>$ttl_vocabulary_correct,'customer_id'=>$value['customer_id'], 'customer'=>$value['customer'], 'country_name'=>$value['country_name'], 'center_id'=>$value['center_id'], 'center_code'=>$value['center_code'], 'center_name'=>$value['center_name'], 'batch_id'=>$value['batch_id'], 'batch_code'=>$value['batch_code'], 'batch_name'=>$value['batch_name'], 'user_id'=>$value['user_id'], 'user_name'=>$value['user_name'], 'user_email'=>$value['user_email'], 'user_login_id'=>$value['user_login_id'], 'user_role'=>$value['user_role'], 'user_joining_date'=>$value['user_joining_date'], 'user_country'=>$value['user_country'],'user_language'=>$value['user_language'],'user_status'=>$value['user_status'],'course_edge_id'=>$value['course_edge_id'], 'course_id'=>$value['course_id'],'course_code'=>$value['course_code'], 'course'=>$value['course'], 'course_description'=>$value['course_description'],'topic_edge_id'=>$value['topic_edge_id'],'topic_name'=>$value['topic_name'],'topic_description'=>$value['topic_description'],'topic_type'=>$value['topic_type'],'assessment_type'=>$value['assessment_type'],'last_attempted'=>$last_attempted,'chapter_completed'=>$vocabulary_chapter_completed,'ttl_chapter'=>$vocabulary_chapter,'skill_id'=>$value['skill_id'],'skill_name'=>$value['skill_name'],'completed_chapter_arr'=>$vocabulary_chapter_completed_arr,'region_ids'=>$region_ids);
					 
				}elseif($value['skill_name']=='Grammar')
				{	
					$grammar_chapter++;
					if($value['chapter_completed']==1)
					{
						$grammar_chapter_completed++;
						$ch_detail = getChapterDetailByChapterEdgeid($value['chapter_edge_id']);
						$grammar_chapter_completed_arr[$grammar_chapter_completed] = $ch_detail;
					}
					 $ttl_grammar_ques = $ttl_grammar_ques + $value['chapter_ttl_ques'];
					 $ttl_grammar_correct = $ttl_grammar_correct + $value['chapter_ttl_correct'];
					 if($ttl_grammar_ques!=0){
					 $grammar_per = ($ttl_grammar_correct*100)/$ttl_grammar_ques;
					 $grammar_per = !empty($grammar_per)?round($grammar_per):0;
					 }else{
						$grammar_per=0;
					 }
					 $arr[$value['user_id']][$value['topic_edge_id']][$value['skill_id']] = array('user_name'=>$value['user_name'],'course_id'=>$value['course_id'],'course_name'=>$value['course'],'topic_edge_id'=>$value['topic_edge_id'],'topic_name'=>$value['topic_name'],'skill_per'=>$grammar_per,'skill_ques'=>$ttl_grammar_ques,'skill_correct'=>$ttl_grammar_correct,'customer_id'=>$value['customer_id'], 'customer'=>$value['customer'], 'country_name'=>$value['country_name'], 'center_id'=>$value['center_id'], 'center_code'=>$value['center_code'], 'center_name'=>$value['center_name'], 'batch_id'=>$value['batch_id'], 'batch_code'=>$value['batch_code'], 'batch_name'=>$value['batch_name'], 'user_id'=>$value['user_id'], 'user_name'=>$value['user_name'], 'user_email'=>$value['user_email'], 'user_login_id'=>$value['user_login_id'], 'user_role'=>$value['user_role'], 'user_joining_date'=>$value['user_joining_date'], 'user_country'=>$value['user_country'],'user_language'=>$value['user_language'],'user_status'=>$value['user_status'],'course_edge_id'=>$value['course_edge_id'], 'course_id'=>$value['course_id'],'course_code'=>$value['course_code'], 'course'=>$value['course'], 'course_description'=>$value['course_description'],'topic_edge_id'=>$value['topic_edge_id'],'topic_name'=>$value['topic_name'],'topic_description'=>$value['topic_description'],'topic_type'=>$value['topic_type'],'assessment_type'=>$value['assessment_type'],'last_attempted'=>$last_attempted,'chapter_completed'=>$grammar_chapter_completed,'ttl_chapter'=>$grammar_chapter,'skill_id'=>$value['skill_id'],'skill_name'=>$value['skill_name'],'completed_chapter_arr'=>$grammar_chapter_completed_arr,'region_ids'=>$region_ids);
				}
				
				
				
				$topic_edge_id = $value['topic_edge_id'];
				$user_id = $value['user_id'];
				 
			  }
		 
	}
	 
 }


   
   
 //echo "<pre>";  print_r($arr); exit;   

 ?>

     <?php 
        $i=1;
        

        $sql1 = "SELECT * from rpt_user_performance";
        $stmt1 = $con->prepare($sql1);
        $stmt1->execute();
        $rptDataExist = $stmt1->fetch();
        $stmt1->close();

        if( $rptDataExist ){
           
           $sql = "TRUNCATE table rpt_user_performance";
            $stmt = $con->prepare($sql);
            $stmt->execute();
            $stmt->close();

        }
		
		
		
		 foreach($arr  as $key => $value){
			 
			 $reading_ques = $writing_ques = $speaking_ques = $listening_ques = $vocabulary_ques = $grammar_ques = $reading_correct = $writing_correct = $speaking_correct = $listening_correct = $vocabulary_correct =$grammar_correct = array();
				
			foreach($value  as $key1 => $value1){
				
				foreach($value1  as $key2 => $dataVal){
				
				$record_type = 'topic';
		
				$user_id = $dataVal['user_id'];
				$course_code = $dataVal['course_code'];
				$course_edge_id = $dataVal['course_edge_id'];  
				$batch_code = $dataVal['batch_code'];  
				$batch_id = $dataVal['batch_id'];
				$skill_id =$dataVal['skill_id'];
				$skill_name =$dataVal['skill_name'];
				$region_ids =$dataVal['region_ids'];
				$region_ids = trim($region_ids);
				$region_ids = !empty($region_ids)?$region_ids:0;
				$completed_chapter_list = $dataVal['completed_chapter_arr'];
				
				$dataVal['ttl_chapter'] = getChpaterCount($con,$dataVal['topic_edge_id'],$dataVal['skill_id']);
				
				if(count($completed_chapter_list)>0){
					$completed_chapter_list = json_encode($completed_chapter_list);
				}else{
					$completed_chapter_list='NA';
				}			
			
				
				
				
				
				//Save skill record topic wise 
				$sql = "insert into rpt_user_performance (customer_id, customer,country_name,center_id, center_code, center_name,batch_id, batch_code, batch_name,user_id, user_name, user_email, user_login_id, user_role,user_joining_date,user_country,user_language,user_status,course_edge_id,course_id, course_code, course,course_description, topic_edge_id,topic_name,topic_description,topic_type,skill_id,	skill_name,score,ttl_ques,ttl_correct,ttl_chapter,completed_chapter,last_attempted,completed_chapter_list,record_type,region_id,create_date) Values (?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,NOW())";
				//echo $sql; die;
				$stmt = $con->prepare($sql);
				$stmt->bind_param('ississississssssssiisssissiisiiiiissss', $dataVal['customer_id'], $dataVal['customer'], $dataVal['country_name'], $dataVal['center_id'], $dataVal['center_code'], $dataVal['center_name'], $dataVal['batch_id'], $dataVal['batch_code'], $dataVal['batch_name'], $dataVal['user_id'], $dataVal['user_name'], $dataVal['user_email'], $dataVal['user_login_id'], $dataVal['user_role'], $dataVal['user_joining_date'], $dataVal['user_country'],$dataVal['user_language'],$dataVal['user_status'],$dataVal['course_edge_id'], $dataVal['course_id'], $dataVal['course_code'], $dataVal['course'], $dataVal['course_description'],$dataVal['topic_edge_id'],$dataVal['topic_name'],$dataVal['topic_description'],$dataVal['topic_type'],$dataVal['skill_id'],$dataVal['skill_name'],$dataVal['skill_per'],$dataVal['skill_ques'],$dataVal['skill_correct'],$dataVal['ttl_chapter'],$dataVal['chapter_completed'],$dataVal['last_attempted'],$completed_chapter_list,$record_type,$dataVal['region_ids']);
				$stmt->execute(); 
				$stmt->close();
				$i++;
					
			
			
			
			}
			}
				 
			
		
		
		}
				
			 
}          
        
?>
<?php 
$cron_log_content .= "performance_tracking - ".date('g:i A, j M Y');
$cron_log_content .= PHP_EOL;
file_put_contents('cron-log.txt', $cron_log_content, FILE_APPEND);
echo "Completed!";
?>             
