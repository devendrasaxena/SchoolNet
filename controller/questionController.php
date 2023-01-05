<?php
// Function for assessment
function getAssessmentNameByEdgeId($edge_id){ 
	$con = createConnection();
	$stmt = $con->prepare("SELECT cm.name, cm.description, cm.instimage, cm.duration, cm.isQuesRand, cm.isAnsRand, cm.timeleft_warn FROM cap_module cm
							JOIN generic_mpre_tree gmt ON cm.tree_node_id = gmt.tree_node_id
							WHERE gmt.edge_id=?");
	$stmt->bind_param("i",$edge_id);
	$stmt->bind_result($name, $description,$assInstFile,$duration,$isQuesRand,$isAnsRand,$timeleft_warn);
	$stmt->execute();
	$stmt->fetch(); 
	$stmt->close();

	$bcm = new stdClass();
	$bcm->name = $name;
	$bcm->description = $description;
	$bcm->assInstFile = $assInstFile;
	$bcm->isQuesRand = $isQuesRand;
	$bcm->isAnsRand = $isAnsRand;
	$bcm->duration = $duration;
	$bcm->timeleft_warn = $timeleft_warn;

	return $bcm;
}

function getQuestionList($parent_edge_id,$rand){   
		$con = createConnection();
		if($rand==true){
			$sql="SELECT id FROM  tbl_questions where parent_edge_id=? and status='1' and isPractice='0' order by RAND()";
		}
		else{$sql="SELECT id FROM  tbl_questions where parent_edge_id=? and status='1' and isPractice='0'";}
		
		$stmt = $con->prepare($sql);
		$stmt->bind_param("i",$parent_edge_id);
		$stmt->execute();
		$stmt->bind_result($id);
		$stmt->execute();
		$questions = array();
		while($stmt->fetch()) {
			$bcm = new stdClass();
			$bcm->id = $id;
			array_push($questions,$bcm);
		}
		return $questions;
	}
	
function getPracticeQuestionList($parent_edge_id,$rand){  
		$con = createConnection();
		if($rand==true){
			$sql="SELECT id FROM  tbl_questions where parent_edge_id=? and status='1' and isPractice='1' order by RAND()";
		}
		else{$sql="SELECT id FROM  tbl_questions where parent_edge_id=? and status='1' and isPractice='1'";}
		
		$stmt = $con->prepare($sql);
		$stmt->bind_param("i",$parent_edge_id);
		$stmt->execute();
		$stmt->bind_result($id);
		$stmt->execute();
		$questions = array();
		while($stmt->fetch()) {
			$bcm = new stdClass();
			$bcm->id = $id;
			array_push($questions,$bcm);
		}
		return $questions;
	}

function uploadedFileName($edge_id){
	$con = createConnection();
	$stmt = $con->prepare("SELECT uploaded_file FROM tbl_questions WHERE parent_edge_id = ? ORDER BY id DESC LIMIT 1");
	$stmt->bind_param("i",$edge_id);
	$stmt->bind_result($uploaded_file);
	$stmt->execute();
	$stmt->fetch();
	$stmt->close();
	return $uploaded_file;	

}
	
function getQuestiondetail($quesid){
		$con = createConnection();
		$stmt = $con->prepare("SELECT id,parent_edge_id,sequence_id,instruction,question_stem,option_1,option_2,option_3,option_4,option1_file,option2_file,option3_file,option4_file,correct_answer,random_answers,question_type,fld_rp_type,fld_rp_type_file,fld_rp_type_time,fld_image,fld_audio,fld_video,fld_audio_transcript FROM  tbl_questions where id=?");
		$stmt->execute();
		$stmt->bind_param("i",$quesid);
		$stmt->bind_result($id, $parent_edge_id, $sequence_id, $instruction, $question_stem, $option_1, $option_2, $option_3, $option_4,$option1_file,$option2_file,$option3_file,$option4_file,$correct_answer,$random_answers,$question_type,$fld_rp_type,$fld_rp_type_file,$fld_rp_type_time,$fld_image,$fld_audio,$fld_video,$fld_audio_transcript);
		$stmt->execute();
		$questionDetails = array();
		while($stmt->fetch()) {
			$obj = new stdClass();
			$obj->id = $id;
			$obj->parent_edge_id = $parent_edge_id;
			$obj->sequence_id = $sequence_id;
			$obj->instruction = $instruction;
			$obj->question_stem = $question_stem;
			$obj->option_1 = $option_1;
			$obj->option_2 = $option_2;
			$obj->option_3 = $option_3;
			$obj->option_4 = $option_4;
			$obj->option1_file = $option1_file;
			$obj->option2_file = $option2_file;
			$obj->option3_file = $option3_file;
			$obj->option4_file = $option4_file;
			$obj->fld_rp_type = $fld_rp_type;
			$obj->fld_rp_type_file = $fld_rp_type_file;
			$obj->fld_rp_type_time = $fld_rp_type_time;
			$obj->fld_image = $fld_image;
			$obj->fld_audio = $fld_audio;
			$obj->fld_video = $fld_video;
			$obj->fld_audio_transcript = $fld_audio_transcript;
			$obj->correct_answer = $correct_answer;
			$obj->random_answers = $random_answers;
			$obj->question_type = $question_type;
			array_push($questionDetails,$obj);
		}
		return $questionDetails;
}



function getQuestionRubrics($ques_id){
		$con = createConnection();
		$stmt = $con->prepare("SELECT trc.competency, tqr.question_rubric FROM tbl_questions_rubric tqr JOIN tbl_rubric_competency trc ON trc.id = tqr.compentency_id WHERE question_id = ?");
		$stmt->bind_param("i",$ques_id);
		$stmt->bind_result($competency, $question_rubric);
		$stmt->execute();
		$stmt->fetch();		
		$stmt->close();
		//echo $question_rubric;exit;
		$keyArr = array();
		if($question_rubric != ''){
			$keys = json_decode($question_rubric);			
			foreach($keys as $key => $value){
				if($value != 1){
					$stmt = $con->prepare("SELECT trg.rubric, rubric_key FROM tbl_rubric_key trk JOIN tbl_rubric_group trg ON trg.group_id = trk.rubric_group WHERE key_id = $value");
					$stmt->bind_result($rubric, $rubric_key);
					$stmt->execute();
					$stmt->fetch();
					$stmt->close();
					$bcm = new stdClass();
					$bcm->question_rubric = $question_rubric;
					$bcm->key_id = $value;
					$bcm->rubric_group = $rubric;
					$bcm->rubric_key = $rubric_key;
					array_push($keyArr,$bcm);
				}
			}
		}
		//echo "<pre>";print_r($keyArr);exit;
		return array('competency' => $competency, 'keyArr' => $keyArr);
}

function getQuestionAvRubrics($question_id, $serial){
		$con = createConnection();
		$stmt = $con->prepare("SELECT trc.competency, tqr.question_rubric FROM tbl_questions_rubric tqr JOIN tbl_rubric_competency trc ON trc.id = tqr.compentency_id WHERE question_id = ? LIMIT $serial, 1");
		$stmt->bind_param("i",$question_id);
		$stmt->bind_result($competency, $question_rubric);
		$stmt->execute();
		$stmt->fetch();		
		$stmt->close();
		
		$keyArr = array();
		if($question_rubric != ''){
			$keys = json_decode($question_rubric);			
			foreach($keys as $key => $value){
				if($value != 1){
					$stmt = $con->prepare("SELECT trg.rubric, rubric_key FROM tbl_rubric_key trk JOIN tbl_rubric_group trg ON trg.group_id = trk.rubric_group WHERE key_id = $value");
					$stmt->bind_result($rubric, $rubric_key);
					$stmt->execute();
					$stmt->fetch();
					$stmt->close();
					$bcm = new stdClass();
					$bcm->question_rubric = $question_rubric;
					$bcm->key_id = $value;
					$bcm->rubric_group = $rubric;
					$bcm->rubric_key = $rubric_key;
					array_push($keyArr,$bcm);
				}
			}
		}
		return array('competency' => $competency, 'keyArr' => $keyArr);
}


function getRubricValue($rubric_id){
	$con = createConnection();
	$stmt = $con->prepare("SELECT value_1, value_2, value_3, value_4 FROM  tbl_rubric_key_values WHERE rubric_id = ?");						
	$stmt->bind_param("i", $rubric_id);
	$stmt->execute();
	$stmt->bind_result($value_1, $value_2, $value_3, $value_4);
	$stmt->fetch();
	$stmt->close();
	
	$obj = new stdclass();
	$obj->value_1 = $value_1;
	$obj->value_2 = $value_2;
	$obj->value_3 = $value_3;
	$obj->value_4 = $value_4;
	return $obj;
}

function getCorrectAns($question_id, $ans_id, $ques_type){
	$con = createConnection();
	if($ques_type == 'MC-TT-AU'){
		$stmt = $con->prepare("SELECT option_".$ans_id." rightAns FROM  tbl_questions WHERE id = ?");						
		$stmt->bind_param("i",$question_id);
		$stmt->execute();
		$stmt->bind_result($rightAns);
		$stmt->fetch();
		$stmt->close();
		return $rightAns;
	}
	
	if($ques_type == 'DD-TT-AU'){
		$exp = explode(";",$ans_id);
		$stmt = $con->prepare("SELECT option_".$exp[0]." opt1, option_".$exp[1]." opt2, option_".$exp[2]." opt3, option_".$exp[3]." opt4 FROM  tbl_questions WHERE id = ?");						
		$stmt->bind_param("i", $question_id);
		$stmt->execute();
		$stmt->bind_result($opt1, $opt2, $opt3, $opt4);
		$stmt->fetch();
		$stmt->close();
		return $opt1.", ".$opt2.", ". $opt3.", ". $opt4;
	}
	
	if($ques_type == 'MMC-TT-AU'){
		
		$ans_id=array_map('trim',explode(',',$ans_id));
		$formatAns=array();
		foreach($ans_id as $key=>$val){
		$stmt = $con->prepare("SELECT option_".$val." rightAns FROM  tbl_questions WHERE id = ?");
		$stmt->bind_param("i", $question_id);
		$stmt->execute();
		$stmt->bind_result($rightAns);
		$stmt->fetch();
		$stmt->close();
		$formatAns[].=$val.'.'.$rightAns;
		}
		$formatAns=implode(', ',$formatAns);
		return $formatAns;
	}	
 }

 function getQuestions(){
	$con = createConnection();
	$qry="SELECT  id,question_stem,correct_answer,question_type from tbl_questions where id IN(18687,18775,1)";
	$stmt = $con->prepare($qry);
	$stmt->bind_result($ques_id,$question_stem,$correct_answer,$question_type);
    $stmt->execute();
	//$metaResults = $stmt->result_metadata();
	$questionDetails = array();
		while($stmt->fetch()) { 
				$bcm=new stdClass();
				//getting question detail and assign view
				$ques_detail_arr=getQuestiondetail($ques_id);
				$ques_detail=$ques_detail_arr[0];
				$bcm->id = $ques_detail->id;
				$bcm->instruction = $ques_detail->instruction;
				$bcm->question_stem = $ques_detail->question_stem;
				$bcm->option_1 = $ques_detail->option_1;
				$bcm->option_2 = $ques_detail->option_2;
				$bcm->option_3 = $ques_detail->option_3;
				$bcm->option_4 = $ques_detail->option_4;
				$bcm->option1_file = $ques_detail->option1_file;
				$bcm->option2_file = $ques_detail->option2_file;
				$bcm->option3_file = $ques_detail->option3_file;
				$bcm->option4_file = $ques_detail->option4_file;
				$bcm->fld_rp_type = $ques_detail->fld_rp_type;
				$bcm->fld_rp_type_file = $ques_detail->fld_rp_type_file;
				$bcm->fld_rp_type_time = $ques_detail->fld_rp_type_time;
				$bcm->fld_image = $ques_detail->fld_image;
				$bcm->fld_audio = $ques_detail->fld_audio;
				$bcm->fld_video = $ques_detail->fld_video;
				$bcm->fld_audio_transcript = $ques_detail->fld_audio_transcript;
				$bcm->correct_answer = trim($ques_detail->correct_answer);
				$bcm->random_answers = $ques_detail->random_answers;
				$bcm->question_type = $ques_detail->question_type;
			array_push($questionDetails,$bcm);
		}
		 return($questionDetails);
	
	
	}

function getQuestionDetails($question_id){
		$con = createConnection();
		$stmt = $con->prepare("SELECT id,parent_edge_id,sequence_id,instruction,question_stem,option_1,option_2,option_3,option_4,option_5,option_6,option_7,option_8,option1_file,option2_file,option3_file,option4_file,option5_file,option6_file,option7_file,option8_file,correct_answer,random_answers,question_type,fld_rp_type,fld_rp_type_file,fld_rp_type_time,fld_position,fld_image,fld_audio,fld_video,fld_audio_transcript FROM  tbl_questions where id=?");
		$stmt->bind_param("i",$question_id);
		$stmt->execute();
		$stmt->bind_result($id, $parent_edge_id, $sequence_id, $instruction, $question_stem, $option_1, $option_2, $option_3, $option_4,$option_5,$option_6,$option_7,$option_8,$option1_file,$option2_file,$option3_file,$option4_file,$option5_file,$option6_file,$option7_file,$option8_file,$correct_answer,$random_answers,$question_type,$fld_rp_type,$fld_rp_type_file,$fld_rp_type_time,$fld_position,$fld_image,$fld_audio,$fld_video,$fld_audio_transcript);
		$stmt->execute();
		$questionDetails = array();
		while($stmt->fetch()) {
			$bcm = new stdClass();
			$bcm->id = $id;
			$bcm->parent_edge_id = $parent_edge_id;
			$bcm->sequence_id = $sequence_id;
			$bcm->instruction = $instruction;
			$bcm->question_stem = $question_stem;
			$bcm->option_1 = $option_1;
			$bcm->option_2 = $option_2;
			$bcm->option_3 = $option_3;
			$bcm->option_4 = $option_4;
			$bcm->option_5 = $option_5;
			$bcm->option_6 = $option_6;
			$bcm->option_7 = $option_7;
			$bcm->option_8 = $option_8;
			$bcm->option1_file = $option1_file;
			$bcm->option2_file = $option2_file;
			$bcm->option3_file = $option3_file;
			$bcm->option4_file = $option4_file;
			$bcm->option5_file = $option5_file;
			$bcm->option6_file = $option6_file;
			$bcm->option7_file = $option7_file;
			$bcm->option8_file = $option8_file;
			$bcm->fld_rp_type = $fld_rp_type;
			$bcm->fld_rp_type_file = $fld_rp_type_file;
			$bcm->fld_rp_type_time = $fld_rp_type_time;
			$bcm->fld_position = $fld_position;
			$bcm->fld_image = $fld_image;
			$bcm->fld_audio = $fld_audio;
			$bcm->fld_video = $fld_video;
			$bcm->fld_audio_transcript = $fld_audio_transcript;
			$bcm->correct_answer = $correct_answer;
			$bcm->random_answers = $random_answers;
			$bcm->question_type = $question_type;
			array_push($questionDetails,$bcm);
		}
		//echo  "<pre>";print_r($questionDetails);
		return $questionDetails;
	}

//Save user's answer

 function saveAnswer($quesid,$ans,$uid,$testid,$time_sp='',$correct){
	 
	//// Now Adding  Batch 
	$battId=!empty($battId)?$battId:'';
	$date=date('Y-m-d H:i:s');
	$sql = "INSERT INTO temp_ans_push(user_id,test_id,ques_id,user_response,correct,time_sp,fld_datetime) VALUES(?,?,?,?,?,?,?)";
	$con = createConnection();
	$stmt = $con->prepare($sql);
	$stmt->bind_param('issssss',$uid,$testid,$quesid,$ans,$correct,$time_sp,$date);
	return $stmt->execute();
  
}


//Save user's answer

 function saveBatAnswer($quesid,$ans,$uid,$testid,$time_sp='',$correct,$battId){
	 
	//// Now Adding  Batch 
	$battId=!empty($battId)?$battId:'';
	$date=date('Y-m-d H:i:s');
	$sql = "INSERT INTO temp_ans_push(user_id,test_id,battery_id,ques_id,user_response,correct,time_sp,fld_datetime) VALUES(?,?,?,?,?,?,?,?)";
	$con = createConnection();
	$stmt = $con->prepare($sql);
	$stmt->bind_param('isssssss',$uid,$testid,$battId,$quesid,$ans,$correct,$time_sp,$date);
	return $stmt->execute();
  
}
//Update user's answer

 function updBatAnswer($ans,$time_sp=0,$correct,$tmpAnsPushId){
	 
	//// Now Adding  Batch 
	$date=date('Y-m-d H:i:s');
	$con = createConnection();
	$stmt = $con->prepare("UPDATE temp_ans_push SET  user_response= ?,correct=?,time_sp=time_sp+'$time_sp',fld_datetime=? where id= ?");
	$stmt->bind_param("sisi",$ans,$correct,$date,$tmpAnsPushId);
	return $stmt->execute();	
	$stmt->close();	
}


// Code for report

function getTestReport($role_id,$batch_id){
	$center_id=$_SESSION['center_id'];
	$con = createConnection();
		$stmt = $con->prepare("SELECT  tbum.user_id from  tblx_batch_user_map tbum JOIN user_role_map urp ON tbum.user_id=urp.user_id where urp.role_definition_id=? and tbum.batch_id=? and tbum.center_id=?");
		$stmt->bind_param("iii",$role_id,$batch_id,$center_id);
		$stmt->execute();
		$stmt->bind_result($user_id);
		$arr_userId = array();
		while($stmt->fetch()) {
			$bcm = new stdClass();
			$bcm->user_id = $user_id;
			array_push($arr_userId,$bcm);
		}
		return $arr_userId;
}

//Get user Test battery 

function getUserCompletedTest($user_id,$order_by='',$limit=''){
	$center_id=$_SESSION['center_id'];
	$con = createConnection();
	if($order_by!="")
	{
		$order_by='order by attempt_date desc';
	}
	else{$order_by='';}
	
	if($limit!="")
	{
		$limit='limit '.$limit;
	}
	else{$limit='';}
	
		
	$stmt = $con->prepare("SELECT test_id,battery_id,status FROM tbl_test_complete_status WHERE  user_id = ? and status='1' and (battery_id='' or battery_id IS NULL)  $order_by $limit");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($test_id,$battery_id,$status);
	$arr_tests = array();
	while($stmt->fetch()) {
		$bcm = new stdClass();
		$bcm->test_id = $test_id;
		$bcm->battery_id = $battery_id;
		$bcm->status = $status;
		array_push($arr_tests,$bcm);
	}
	
		
	$stmt = $con->prepare("SELECT test_id,battery_id,status FROM tbl_test_complete_status  WHERE  user_id = ? and status='1' and (battery_id!='' and battery_id IS NOT NULL) group by battery_id $order_by $limit");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($test_id,$battery_id,$status);
	while($stmt->fetch()) {
		$bcm = new stdClass();
		$bcm->test_id = $test_id;
		$bcm->battery_id = $battery_id;
		$bcm->status = $status;
		$c_status=getBatteryCompleteStatus($user_id,$battery_id);
			if($c_status==1){
				array_push($arr_tests,$bcm);
			}
		
		
	}
	
	return $arr_tests;
		
		
}




function getUserAttmptTest($user_id,$order_by='',$limit=''){
	$center_id=$_SESSION['center_id'];
	$con = createConnection();
	if($order_by!="")
	{
		$order_by='order by attempt_date desc';
	}
	else{$order_by='';}
	
	if($limit!="")
	{
		$limit='limit '.$limit;
	}
	else{$limit='';}
	
		
	$stmt = $con->prepare("SELECT test_id,battery_id,status FROM tbl_test_complete_status WHERE  user_id = ? and status='1' and (battery_id='' or battery_id IS NULL)  $order_by $limit");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($test_id,$battery_id,$status);
	$arr_tests = array();
	while($stmt->fetch()) {
		$bcm = new stdClass();
		$bcm->test_id = $test_id;
		$bcm->battery_id = $battery_id;
		$bcm->status = $status;
		array_push($arr_tests,$bcm);
	}
	
		
	$stmt = $con->prepare("SELECT test_id,battery_id,status FROM tbl_test_complete_status  WHERE  user_id = ? and status='1' and (battery_id!='' and battery_id IS NOT NULL) group by battery_id $order_by $limit");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($test_id,$battery_id,$status);
	while($stmt->fetch()) {
		$bcm = new stdClass();
		$bcm->test_id = $test_id;
		$bcm->battery_id = $battery_id;
		$bcm->status = $status;
		array_push($arr_tests,$bcm);
	}
	
	
	
	return $arr_tests;
		
		
}





function getUserTest($user_id,$order_by='',$limit=''){
	$center_id=$_SESSION['center_id'];
	$con = createConnection();
	if($order_by!="")
	{
		$order_by='order by fld_datetime desc';
	}
	else{$order_by='';}
	
	if($limit!="")
	{
		$limit='limit '.$limit;
	}
	else{$limit='';}
	/* 	$stmt = $con->prepare("SELECT test_id from  temp_ans_push where user_id='$user_id'  group by test_id");
		$stmt->execute();
		$stmt->bind_result($test_id);
		$arr_tests = array();
		while($stmt->fetch()) {
			$bcm = new stdClass();
			$bcm->test_id = $test_id;
			array_push($arr_tests,$bcm);
		}
		return $arr_tests; */
		
	$stmt = $con->prepare("SELECT test_id,battery_id,ques_id, user_response FROM temp_ans_push WHERE  user_id = ? and (battery_id='' or battery_id IS NULL) group by test_id $order_by $limit");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($test_id,$battery_id,$ques_id, $ans_id);
	$arr_tests = array();
	while($stmt->fetch()) {
		$bcm = new stdClass();
		$bcm->test_id = $test_id;
		$bcm->battery_id = $battery_id;
		$bcm->ques_id = $ques_id;
		$bcm->ans_id = $ans_id;
		array_push($arr_tests,$bcm);
	}
	
		
	$stmt = $con->prepare("SELECT test_id,battery_id,ques_id, user_response FROM temp_ans_push WHERE  user_id = ? and (battery_id!='' and battery_id IS NOT NULL) group by battery_id $order_by $limit");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($test_id,$battery_id,$ques_id, $ans_id);
	while($stmt->fetch()) {
		$bcm = new stdClass();
		$bcm->test_id = $test_id;
		$bcm->battery_id = $battery_id;
		$bcm->ques_id = $ques_id;
		$bcm->ans_id = $ans_id;
		array_push($arr_tests,$bcm);
	}
	
	
	
	return $arr_tests;
		
		
}



function getTestResult($test_id,$user_id){

	$con = createConnection();
	 
		
	$stmt = $con->prepare("SELECT count(`ques_id`) as qCount,SUM(`correct`) as ttlCorrect,MAX(fld_datetime) FROM temp_ans_push WHERE  test_id = ?  and user_id= ? and (battery_id='' or battery_id IS NULL)");
    $stmt->bind_param("ii",$test_id,$user_id);
    $stmt->execute();
    $stmt->bind_result($qCount,$ttlCorrect,$fld_datetime);
	$arr_tests = array();
	while($stmt->fetch()) {
		$bcm = new stdClass();
		$bcm->qCount = $qCount;
		$bcm->ttlCorrect = $ttlCorrect;
		$bcm->fld_datetime = $fld_datetime;
		array_push($arr_tests,$bcm);
	}
	return $arr_tests;
		
		
}

function getBatteryResult($test_id,$user_id,$course_count=''){

	$con = createConnection();
	
	if($course_count!=1){
		$stmt = $con->prepare("SELECT count(`ques_id`) as qCount,SUM(`correct`) as ttlCorrect,MAX(fld_datetime) FROM temp_ans_push WHERE  battery_id = ?  and user_id= ? and (battery_id!='' or battery_id IS NOT NULL)");
		$stmt->bind_param("ii",$test_id,$user_id);
		$stmt->execute();
		$stmt->bind_result($qCount,$ttlCorrect,$fld_datetime);
		$arr_tests = array();
		while($stmt->fetch()) {
			$bcm = new stdClass();
			$bcm->qCount = $qCount;
			$bcm->ttlCorrect = $ttlCorrect;
			$bcm->fld_datetime = $fld_datetime;
			array_push($arr_tests,$bcm);
		}
		return $arr_tests;
	}
	
	else{
		
		$stmt = $con->prepare("SELECT count(`ques_id`) as qCount,SUM(`correct`) as ttlCorrect,MAX(fld_datetime) FROM temp_ans_push WHERE  battery_id = ?  and user_id= ? and (battery_id!='' or battery_id IS NOT NULL)");
		$stmt->bind_param("ii",$test_id,$user_id);
		$stmt->execute();
		$stmt->bind_result($qCount,$ttlCorrect,$fld_datetime);
		$arr_tests = array();
		while($stmt->fetch()) {
			$bcm = new stdClass();
			$bcm->qCount = $qCount;
			$bcm->ttlCorrect = $ttlCorrect;
			$bcm->fld_datetime = $fld_datetime;
			array_push($arr_tests,$bcm);
		}
		return $arr_tests;

		
		
	}
		
		
}
function getUserTestquestion($user_id,$tid){
	$con = createConnection();

	$stmt = $con->prepare("SELECT test_id, ques_id,user_response,correct,time_sp FROM temp_ans_push WHERE  user_id = ? && test_id=? and (battery_id='' or battery_id IS NULL)");
    $stmt->bind_param("ii", $user_id,$tid);
    $stmt->execute();
    $stmt->bind_result($test_id, $ques_id, $ans_id, $correct, $time_sp);
	$arr_tests = array();
	while($stmt->fetch()) {
		$bcm = new stdClass();
		$bcm->test_id = $test_id;
		$bcm->ques_id = $ques_id;
		$bcm->ans_id = $ans_id;
		$bcm->correct = $correct;
		$bcm->time_sp = $time_sp;
		array_push($arr_tests,$bcm);
	}
	return $arr_tests;
		
		
}





function chkPrevAssessment($uid,$testId){
	$con = createConnection();
	$stmt = $con->prepare("SELECT id FROM temp_ans_push WHERE  user_id = ? and test_id=? and (battery_id='' or battery_id IS NULL)");
    $stmt->bind_param("ii", $uid,$testId);
	if($stmt->execute()){
	 $stmt->bind_result($test_id);
	 return true;
	}
	else{ return false; }
	
}


function deletePrevAssmnt($uid,$testId){
	if($uid!="" && $testId!=""){
		$con = createConnection();
		$stmt = $con->prepare("DELETE FROM temp_ans_push WHERE  user_id = ? and test_id=? and (battery_id='' or battery_id IS NULL)");
		$stmt->bind_param("ii", $uid,$testId);
		return $stmt->execute();
	}
	else{ return false;}
	
}


function chkPrevBat($uid,$testId,$battId){
	
	$con = createConnection();
	$stmt = $con->prepare("SELECT id FROM temp_ans_push WHERE  user_id = ? and test_id=? and battery_id=?");
    $stmt->bind_param("iii", $uid,$testId,$battId);
	if($stmt->execute()){
	 $stmt->bind_result($test_id);
	 return true;
	}
	else{ return false; }
	
}

function deletePrevBat($uid,$testId,$battId){
	if($uid!="" && $testId!=""){
		$con = createConnection();
		$stmt = $con->prepare("DELETE FROM temp_ans_push WHERE  user_id = ? and test_id=? and battery_id=?");
		$stmt->bind_param("iii", $uid,$testId,$battId);
		return $stmt->execute();
	}
	else{ return false;}
	
}

function chkBatQuesEntry($uid,$testId,$battId,$ques_id){
	
	$con = createConnection();
	$stmt = $con->prepare("SELECT id FROM temp_ans_push WHERE  user_id = ? and test_id=? and battery_id=? and ques_id=?");
    $stmt->bind_param("iiii", $uid,$testId,$battId,$ques_id);
	if($stmt->execute()){
	 $stmt->bind_result($test_id);
	 $arr_bat_entry = array();
	while($stmt->fetch()) {
		$bcm = new stdClass();
		$bcm->test_id = $test_id;
	 	array_push($arr_bat_entry,$bcm);
	}
	return $arr_bat_entry[0]->test_id;
	}
	else{ return false; }
	
}

function deleteBatQuesEntry($tmpAnsPushId){
	if($tmpAnsPushId!=""){
		$con = createConnection();
		$stmt = $con->prepare("DELETE FROM temp_ans_push WHERE  id = ?");
		$stmt->bind_param("i", $tmpAnsPushId);
		return $stmt->execute();
	}
	else{ return false;}
	
}

//Check and delete individual question in test
function chkTestQuesEntry($uid,$testId,$ques_id){
	
	$con = createConnection();
	$stmt = $con->prepare("SELECT id FROM temp_ans_push WHERE  user_id = ? and test_id=? and ques_id=? and (battery_id='' or battery_id IS NULL)");
    $stmt->bind_param("iii", $uid,$testId,$ques_id);
	if($stmt->execute()){
		$stmt->bind_result($test_id);
		$arr_bat_entry = array();
		while($stmt->fetch()) {
		$bcm = new stdClass();
		$bcm->test_id = $test_id;
		array_push($arr_bat_entry,$bcm);
		}
		return $arr_bat_entry[0]->test_id;
	}
	else{ return false; }
	
}

function deleteTestQuesEntry($tmpAnsPushId){
	if($tmpAnsPushId!=""){
		$con = createConnection();
		$stmt = $con->prepare("DELETE FROM temp_ans_push WHERE  id = ?");
		$stmt->bind_param("i", $tmpAnsPushId);
		return $stmt->execute();
	}
	else{ return false;}
	
}




 function userAnswerView($correcAns,$isCorrect,$ans,$quesType){
	
	$color=$isCorrect?'green':'red';
	//Section to show user answer 
	$html='<div class="divUserAns">Your Answer:  <span style="color:'.$color.'">'. $ans.'</span> ';
	if($isCorrect==false){ 
		$html.='<span style="color:'.$color.'">Incorrect</span>';
	}
	elseif($isCorrect==true){ 
		$html.='<span style="color:'.$color.'">Correct</span>';
	}
	
	$html.='</div>';
	//End of Section to show user answer 
		
		
	//Section to show Correct Answer 
	if($isCorrect==false){
		$html.='<div class="divRightAns">
		Correct Answer  <span style="color:green">'.$correcAns.'</span>
		</div>';
	} 
	//End of Section to show Correct Answer 
		
	return $html;
		
} 

function deleteTest(){

	$con = createConnection();
	$stmt = $con->prepare("DELETE FROM temp_ans_push WHERE user_id=?");
	$stmt->bind_param("i",$_SESSION['user_id']);
	$stmt->execute();
	$stmt->close();
	$con = createConnection();
	$stmt = $con->prepare("DELETE FROM tbl_test_complete_status WHERE user_id=?");
	$stmt->bind_param("i",$_SESSION['user_id']);
	$stmt->execute();
	$stmt->close();
	$stmt = $con->prepare("DELETE FROM tbl_stanine_score WHERE user_id=?");
	$stmt->bind_param("i",$_SESSION['user_id']);
	return $stmt->execute();	
	
}

function getTests(){
	$testIds=TESTIDS;
	if($testIds!=""){
		$con = createConnection();

		$stmt = $con->prepare("SELECT cm.name,gmt.edge_id FROM cap_module cm
							JOIN generic_mpre_tree gmt ON cm.tree_node_id = gmt.tree_node_id
							WHERE gmt.edge_id IN($testIds)");
		$stmt->execute();
		$stmt->bind_result($name,$edge_id);
		$arr_tests = array();
		while($stmt->fetch()) {
			$bcm = new stdClass();
			$bcm->test_name = $name;
			$bcm->edge_id = $edge_id;
			array_push($arr_tests,$bcm);
		}
	return $arr_tests;
	}
	else{ return false;}
	
}

function getBatteryTestResult($batt_id,$test_id,$user_id){

	$con = createConnection();
	 
		
	$stmt = $con->prepare("SELECT count(`ques_id`) as qCount,SUM(`correct`) as ttlCorrect,ttcs.time_spent,ttcs.attempt_date  FROM temp_ans_push tap INNER JOIN tbl_test_complete_status ttcs ON tap.battery_id=ttcs.battery_id and tap.test_id=ttcs.test_id and tap.user_id=ttcs.user_id WHERE  tap.battery_id = ? and tap.test_id = ?  and tap.user_id= ?");
    $stmt->bind_param("iii",$batt_id,$test_id,$user_id);
    $stmt->execute();
    $stmt->bind_result($qCount,$ttlCorrect,$time_sp,$fld_datetime);
	$arr_tests = array();
	while($stmt->fetch()) {
		$bcm = new stdClass();
		$bcm->qCount = $qCount;
		$bcm->ttlCorrect = $ttlCorrect;
		$bcm->time_sp = $time_sp;
		$bcm->fld_datetime = $fld_datetime;
		array_push($arr_tests,$bcm);
	} 
	return $arr_tests;
		
		
}

function updateComplete($uid,$testId,$battId=''){

	$con = createConnection();
	$date=date('Y-m-d H:i:s');
	if($battId!="")	{
		$stmt = $con->prepare("SELECT id,status FROM tbl_test_complete_status WHERE  user_id = ? and test_id= ? and battery_id= ? ");
		$stmt->bind_param("iii",$uid,$testId,$battId);
		$stmt->execute();
		$stmt->bind_result($id,$status);
		$arr_tests = array();
		while($stmt->fetch()) {
			$bcm = new stdClass();
			$bcm->id = $id;
			$bcm->status = $status;
			array_push($arr_tests,$bcm);
		}
		
		if(count($arr_tests)>0){
			
			foreach($arr_tests as $key=>$val){
				if($val->status=='1'){
					
					$stmt = $con->prepare("UPDATE tbl_test_complete_status SET  status = '0' ,attempt_date=? where id= ?");
					$stmt->bind_param("si",$date,$val->id);
					$stmt->execute();	
					$stmt->close();	
				}
			}
		}
		else{
		
			$status='0';
			$sql = "INSERT INTO tbl_test_complete_status(user_id,test_id,battery_id,attempt_date,status) VALUES(?,?,?,?,?)";
			$stmt = $con->prepare($sql);
			$stmt->bind_param('iiiss',$uid,$testId,$battId,$date,$status);
			$stmt->execute();
			$stmt->close();	
		}
	}
	elseif($testId!=""){
		$stmt = $con->prepare("SELECT id,status,attempt_date FROM tbl_test_complete_status WHERE  user_id = ? and test_id= ? and (battery_id='' or battery_id IS NULL)");
		$stmt->bind_param("ii",$uid,$testId);
		$stmt->execute();
		$stmt->bind_result($id,$status,$attempt_date);
		$arr_tests = array();
		while($stmt->fetch()) {
			$bcm = new stdClass();
			$bcm->id = $id;
			$bcm->status = $status;
			$bcm->attempt_date = $attempt_date;
			array_push($arr_tests,$bcm);
		}
		if(count($arr_tests)>0){
			
			foreach($arr_tests as $key=>$val){
				if($val->status=='1'){
					$stmt = $con->prepare("UPDATE tbl_test_complete_status SET  status = '0',attempt_date=? where id= ?");
					$stmt->bind_param("si",$date,$val->id);
					$stmt->execute();	
					$stmt->close();	
				}
			}
		}
		else{
		
			$status='0';
			$sql = "INSERT INTO tbl_test_complete_status(user_id,test_id,attempt_date,status) VALUES(?,?,?,?)";
			$stmt = $con->prepare($sql);
			$stmt->bind_param('iiss',$uid,$testId,$date,$status);
			$stmt->execute();
			$stmt->close();	

		}
			
	}	
		
}


function updateCompleteStatus($uid,$testId,$scndSpent,$battId=''){

	$con = createConnection();
	$date=date('Y-m-d H:i:s');
	
	if($battId!="")	{
		$stmt = $con->prepare("SELECT id,status FROM tbl_test_complete_status WHERE  user_id = ? and test_id= ? and battery_id= ? ");
		$stmt->bind_param("iii",$uid,$testId,$battId);
		$stmt->execute();
		$stmt->bind_result($id,$status);
		$arr_tests = array();
		while($stmt->fetch()) {
			$bcm = new stdClass();
			$bcm->id = $id;
			$bcm->status = $status;
			array_push($arr_tests,$bcm);
		}
		
		if(count($arr_tests)>0){
			
			foreach($arr_tests as $key=>$val){
				
					$stmt = $con->prepare("UPDATE tbl_test_complete_status SET  status = '1',attempt_date=?,time_spent=? where id=?");
					$stmt->bind_param("sis",$date,$scndSpent,$val->id);
					$stmt->execute();	
					$stmt->close();	
				
			}
		}
		else{
		
			$status='1';
			$sql = "INSERT INTO tbl_test_complete_status(user_id,test_id,battery_id,attempt_date,time_spent,status) VALUES(?,?,?,?,?)";
			$stmt = $con->prepare($sql);
			$stmt->bind_param('iiisis',$uid,$testId,$battId,$date,$scndSpent,$status);
			$stmt->execute();
			$stmt->close();	
		}
	}
	elseif($testId!=""){
		$stmt = $con->prepare("SELECT id,status FROM tbl_test_complete_status WHERE  user_id = ? and test_id= ? and (battery_id='' or battery_id IS NULL)");
		$stmt->bind_param("ii",$uid,$testId);
		$stmt->execute();
		$stmt->bind_result($id,$status);
		$arr_tests = array();
		while($stmt->fetch()) {
			$bcm = new stdClass();
			$bcm->id = $id;
			$bcm->status = $status;
			array_push($arr_tests,$bcm);
		}
		if(count($arr_tests)>0){
			
			foreach($arr_tests as $key=>$val){
					$stmt = $con->prepare("UPDATE tbl_test_complete_status SET  status = '1',attempt_date=?,time_spent=? where id= ?");
					$stmt->bind_param("sii",$date,$scndSpent,$val->id);
					$stmt->execute();	
					$stmt->close();	
			}
		}
		else{
		
			$status='1';
			$sql = "INSERT INTO tbl_test_complete_status(user_id,test_id,attempt_date,time_spent,status) VALUES(?,?,?,?,?)";
			$stmt = $con->prepare($sql);
			$stmt->bind_param('iisis',$uid,$testId,$date,$scndSpent,$status);
			$stmt->execute();
			$stmt->close();	

		}
			
	}	
		
}


function getAnsDtl($uid,$testId,$ques_id,$battId=''){
	
	$con = createConnection();
	if($battId!=''){
		$stmt = $con->prepare("SELECT id,user_response,time_sp,correct FROM temp_ans_push WHERE  user_id = ? and test_id=? and battery_id=? and ques_id=?");
		$stmt->bind_param("iiii", $uid,$testId,$battId,$ques_id);
		if($stmt->execute()){
			$stmt->bind_result($id,$ans_id,$time_sp,$correct);

			$ansDetails = array();
			while($stmt->fetch()) {
			$bcm = new stdClass();
			$bcm->id = $id;
			$bcm->ans_id = $ans_id;
			$bcm->time_sp = $time_sp;
			$bcm->correct = $correct;
			array_push($ansDetails,$obj);
			}
			return $ansDetails;
		}
		else{ return false; }
	
	}
	elseif($testId!=''){
		$stmt = $con->prepare("SELECT id,user_response,time_sp,correct FROM temp_ans_push WHERE  user_id = ? and test_id=? and (battery_id='' or battery_id IS NULL) and ques_id=?");
		$stmt->bind_param("iii", $uid,$testId,$ques_id);
		if($stmt->execute()){
			$stmt->bind_result($id,$ans_id,$time_sp,$correct);

			$ansDetails = array();
			while($stmt->fetch()) {
			$bcm = new stdClass();
			$bcm->id = $id;
			$bcm->ans_id = $ans_id;
			$bcm->time_sp = $time_sp;
			$bcm->correct = $correct;
			array_push($ansDetails,$obj);
			}
			return $ansDetails;
		}
		else{ return false; }
	
	}


}


function getCompleteTblData($uid,$testId,$battId=''){
	
	$con = createConnection();
	if($battId!=''){
		$stmt = $con->prepare("SELECT id,status,time_spent FROM tbl_test_complete_status WHERE  user_id = ? and test_id= ? and battery_id= ? ");
		$stmt->bind_param("iii",$uid,$testId,$battId);
		if($stmt->execute()){
		$stmt->bind_result($id,$status,$time_spent);
		$arr_complete_data = array();
		while($stmt->fetch()) {
			$bcm = new stdClass();
			$bcm->id = $id;
			$bcm->status = $status;
			$bcm->time_spent = $time_spent;
			array_push($arr_complete_data,$bcm);
		}

		return $arr_complete_data;
		}
		return false;
	}
	elseif($testId!=''){
		$stmt = $con->prepare("SELECT id,status,time_spent FROM tbl_test_complete_status WHERE  user_id = ? and test_id= ? and (battery_id='' or battery_id IS NULL)");
		$stmt->bind_param("ii",$uid,$testId);
		$stmt->execute();
		$stmt->bind_result($id,$status,$time_spent);
		$arr_complete_data = array();
		if($stmt->execute()){
		while($stmt->fetch()) {
			$bcm = new stdClass();
			$bcm->id = $id;
			$bcm->status = $status;
			$bcm->time_spent = $time_spent;
			array_push($arr_complete_data,$bcm);
		}

		return $arr_complete_data;
		}
			return false;
	
	}


}

function getBatteryCompleteStatus($uid,$battId,$course_count=''){
	
	$con = createConnection();
	global $userObj,$adminObj;
	$complete=3;
	$unComplete=0;
	$resume=0;
	$studentData = $adminObj->getUserDataByID($uid, 2);
	/* $course_ids=$studentData->course_id;
	$course_id_arr=explode(',',$course_ids);
	$course_count=count($course_id_arr); */
	$course_count='';
	if($course_count>1)
		{
	
		$testList=$userObj->getBatteryTestList($battId);
		foreach($testList as $key=>$test){
			$status= chkComplete($battId,$test['edge_id'],$uid);
			if($status=='1' || $status=='0'){
				if($status=='1'){
					$complete=1;$resume=1;//All test completed
				}else{
					$unComplete=1;$resume=1;$complete=2;//Resume mode
				}
			}
			else{
				if($complete!=3){$complete=2;}$unComplete=1;//Resume mode//Resume mode
			}
			
			
			}
	
	
	}
	elseif($course_count==1){
		$course_id=	$userObj->getUserCourseId($uid);
		$testList=$userObj->getUserBatteryTestList($uid,$course_id);
		foreach($testList as $key=>$test){
			$status= chkComplete($battId,$test['edge_id'],$uid);
			if($status=='1' || $status=='0'){
				if($status=='1'){
					$complete=1;$resume=1;//All test completed
				}else{
					$unComplete=1;$resume=1;$complete=2;//Resume mode
				}
			}
			else{
				if($complete!=3){$complete=2;}$unComplete=1;//Resume mode//Resume mode
			}
			
		}
	}else{
		$testList=$userObj->getBatteryTestList($battId);
		foreach($testList as $key=>$test){
			$status= chkComplete($battId,$test['edge_id'],$uid);
			if($status=='1' || $status=='0'){
				if($status=='1'){
					$complete=1;//All test completed
				}else{
					$unComplete=1;$resume=1;$complete=2;//Resume mode
				}
			}
			else{
				if($complete!=3){$complete=2;}$unComplete=1;//Resume mode//Resume mode
			}
			
			
			}
	}
	
	
	if($unComplete==1){
		if($resume==1 || $complete==2){
			return 2;
		}else{
			return 3;
		}
	}elseif($unComplete==0 && $complete==1){
		return 1;
	}else{
		return 3;
	}


}

function chkComplete($battId,$testId,$uid){
		$con = createConnection();
		$stmt = $con->prepare("SELECT status FROM tbl_test_complete_status where user_id = ? and test_id= ? and battery_id= ?");
		$stmt->bind_param("iii",$uid,$testId,$battId);
		$stmt->execute();
		$stmt->bind_result($status);
		if($stmt->execute()){
		while($stmt->fetch()) {
			$status=$status;
		}
		$stmt->close();	
		return $status;	
		}
		else{return false;}
}

function getTestToAttempt($uid,$battId,$course_count=''){
	
	$con = createConnection();
	$status='0';
	global $userObj;
	
	if($course_count>1){
	
		$stmt = $con->prepare("SELECT test_id FROM tbl_test_complete_status where user_id = ? and battery_id= ? and status= ?");
		$stmt->bind_param("iis",$uid,$battId,$status);
		$stmt->execute();
		$stmt->bind_result($test_id);
		$stmt->execute();
		while($stmt->fetch()) {
			$test_id=$test_id;
		}
		$stmt->close();
		if(!$test_id){
			$testList=$userObj->getBatteryTestList($battId);
			foreach($testList as $key=>$test){
				$status=chkComplete($battId,$test['edge_id'],$uid);
				if($status!='1'){
					$test_arr[]=$test['edge_id'];
				}
			}
			
			return $test_arr[0];
		}
		else{
			return $test_id;
		}
			
	}
	elseif($course_count==1){
		$course_id=	$userObj->getUserCourseId($uid);
		$testList=$userObj->getUserBatteryTestList($uid,$course_id);
		foreach($testList as $key=>$test){
				$status=chkComplete($battId,$test['edge_id'],$uid);
				if($status!='1'){
					$test_arr[]=$test['edge_id'];
				}
			}
			
			return $test_arr[0];
		
	
	}else{
	
		$stmt = $con->prepare("SELECT test_id FROM tbl_test_complete_status where user_id = ? and battery_id= ? and status= ?");
		$stmt->bind_param("iis",$uid,$battId,$status);
		$stmt->execute();
		$stmt->bind_result($test_id);
		$stmt->execute();
		while($stmt->fetch()) {
			$test_id=$test_id;
		}
		$stmt->close();
		if(!$test_id){
			$testList=$userObj->getBatteryTestList($battId);
			foreach($testList as $key=>$test){
				$status=chkComplete($battId,$test['edge_id'],$uid);
				if($status!='1'){
					$test_arr[]=$test['edge_id'];
				}
			}
			
			return $test_arr[0];
		}
		else{
			return $test_id;
		}
			
	}


}

function getBatAnsEntry($uid,$testId,$battId,$ques_id){
	
	$con = createConnection();
	$stmt = $con->prepare("SELECT user_response,time_sp,correct FROM temp_ans_push WHERE  user_id = ? and test_id=? and battery_id=? and ques_id=?");
    $stmt->bind_param("iiii", $uid,$testId,$battId,$ques_id);
	if($stmt->execute()){
	 $stmt->bind_result($ans_id,$time_sp,$correct);
	 $arr_bat_entry = array();
	while($stmt->fetch()) {
		$bcm = new stdClass();
		$bcm->ans_id = $ans_id;
		$bcm->time_sp = $time_sp;
		$bcm->correct = $correct;
	 	array_push($arr_bat_entry,$bcm);
	}
	return $arr_bat_entry[0];
	}
	else{ return false; }
	
}

function updCmpltTime($uid,$testId,$time_sp,$battId=''){

	$con = createConnection();
	$date=date('Y-m-d H:i:s');
	
	if($battId!="")	{
		
				$stmt = $con->prepare("UPDATE tbl_test_complete_status SET  time_spent = $time_sp,attempt_date=? where user_id = ? and test_id= ? and battery_id= ?");
				$stmt->bind_param("siii",$date,$uid,$testId,$battId);
				$stmt->execute();	
				$stmt->close();	
				}
		
	elseif($testId!=""){
		
				$stmt = $con->prepare("UPDATE tbl_test_complete_status SET time_spent = $time_sp,attempt_date=? where  user_id = ? and test_id= ? and (battery_id='' or battery_id IS NULL)");
				$stmt->bind_param("sii",$date,$uid,$testId);
				$stmt->execute();	
				$stmt->close();	
	}			
}

function getTestStatus($uid,$testId){
	$complete=3;
	$con = createConnection();
		$stmt = $con->prepare("SELECT status FROM tbl_test_complete_status where user_id = ? and test_id= ? and (battery_id='' or battery_id IS NULL)");
		$stmt->bind_param("ii",$uid,$testId);
		$stmt->execute();
		$stmt->bind_result($status);
		if($stmt->execute()){
			while($stmt->fetch()) {
				$status=$status;
			}
			if($status=='0' || $status=='1'){
				if($status=='1'){
					$complete=1;
				}else{
					$complete=2;
				}
			}
			else{
				if($complete!=3){$complete=2;}
			}
		}
	return $complete;
}

function getTestResult2($test_id,$user_id){
	global $userObj;
	$con = createConnection();
	$quesCount= $userObj->getTestQuesCount($test_id);
	$quesCount=$quesCount['qCount'];
	$userResult=getTestResult($test_id,$user_id);
	$userResult=$userResult[0];
	$per=round(($userResult->ttlCorrect*100)/$quesCount);
	$attemptDate=$userObj->getMaxAttemptDate($test_id,$user_id);
	
	return json_encode(array('per'=>$per,'ttlCorrect'=>$userResult->ttlCorrect,'qCount'=>$quesCount,'attemptDate'=>$attemptDate));
		

}



function doneBatteryComplete($uid,$battId){
	$con = createConnection();
	$stmt = $con->prepare("UPDATE tbl_test_complete_status SET  battery_status = '1' where user_id=? and battery_id=?");
	$stmt->bind_param("ii",$uid,$battId);
	$stmt->execute();	
	$stmt->close();	
	
}
function doneComplete($uid,$testId,$battId=''){
	
	$con = createConnection();
	$date=date('Y-m-d H:i:s');
	
	if($battId!="")	{
		$stmt = $con->prepare("SELECT id,status FROM tbl_test_complete_status WHERE  user_id = ? and test_id= ? and battery_id= ? ");
		$stmt->bind_param("iii",$uid,$testId,$battId);
		$stmt->execute();
		$stmt->bind_result($id,$status);
		$arr_tests = array();
		while($stmt->fetch()) {
			$bcm = new stdClass();
			$bcm->id = $id;
			$bcm->status = $status;
			array_push($arr_tests,$bcm);
		}
		
		if(count($arr_tests)>0){
			
			foreach($arr_tests as $key=>$val){
				
					$stmt = $con->prepare("UPDATE tbl_test_complete_status SET  status = '1'where id=?");
					$stmt->bind_param("i",$val->id);
					$stmt->execute();	
					$stmt->close();	
				
			}
		}
		
	}
	elseif($testId!=""){
		$stmt = $con->prepare("SELECT id,status FROM tbl_test_complete_status WHERE  user_id = ? and test_id= ? and (battery_id='' or battery_id IS NULL)");
		$stmt->bind_param("ii",$uid,$testId);
		$stmt->execute();
		$stmt->bind_result($id,$status);
		$arr_tests = array();
		while($stmt->fetch()) {
			$bcm = new stdClass();
			$bcm->id = $id;
			$bcm->status = $status;
			array_push($arr_tests,$bcm);
		}
		if(count($arr_tests)>0){
			
			foreach($arr_tests as $key=>$val){
					$stmt = $con->prepare("UPDATE tbl_test_complete_status SET  status = '1' where id= ?");
					$stmt->bind_param("i",$val->id);
					$stmt->execute();	
					$stmt->close();	
			}
		}
		
			
	}	
	
}


function chkTime($ansPushId){
	
	$con = createConnection();
	$date=date('Y-m-d H:i:s');
	$stmt = $con->prepare("SELECT TIMESTAMPDIFF(SECOND, fld_datetime,'$date')  from temp_ans_push where id=?");
	$stmt->bind_param("i",$ansPushId);
	
	if($stmt->execute()){
	 $stmt->bind_result($diffr);
	 $arr_dffr = array();
	while($stmt->fetch()) {
		$bcm = new stdClass();
		$bcm->diffr = $diffr;
		array_push($arr_dffr,$bcm);
	}
	return $arr_dffr[0]->diffr;
	}
	else{ return false; }
		
		
	
}

//Get user Test battery 

function getUserCompletedBattery($user_id,$order_by='',$limit=''){
	$center_id=$_SESSION['center_id'];
	$con = createConnection();
	if($order_by!="")
	{
		$order_by='order by attempt_date desc';
	}
	else{$order_by='';}
	
	if($limit!="")
	{
		$limit='limit '.$limit;
	}
	else{$limit='';}
	
		
	$stmt = $con->prepare("SELECT battery_id FROM tbl_test_complete_status WHERE  user_id = ? and battery_status='1' group by battery_id $order_by $limit");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($battery_id);
	$arr_tests = array();
	while($stmt->fetch()) {
		$bcm = new stdClass();
		$bcm->battery_id = $battery_id;
		array_push($arr_tests,$bcm);
	}
	
		
	return $arr_tests;
		
		
}

function getPracticeVocabForXML($component_id){
	$con = createConnection();
	$stmt = $con->prepare("SELECT id,course_edge_id,parent_edge_id,vocb_id,word,meaning,pronunciation,etymologies,word_usage,vocab_audio FROM  tbl_vocabulary where status='1' and parent_edge_id=?");
	$stmt->bind_param("i",$component_id);
	$stmt->execute();
	$stmt->bind_result($id, $course_edge_id, $parent_edge_id, $vocb_id, $word, $meaning, $pronunciation, $etymologies, $word_usage, $vocab_audio);
	$stmt->execute();

	$voabDetails = array();
	while($stmt->fetch()) {
		$bcm = new stdClass();
		$bcm->id = $id;
		$bcm->course_edge_id = $course_edge_id;
		$bcm->parent_edge_id = $parent_edge_id;
		$bcm->vocb_id = $vocb_id;
		$bcm->word = $word;
		$bcm->meaning = $meaning;
		$bcm->pronunciation = $pronunciation;
		$bcm->etymologies = $etymologies;
		$bcm->word_usage = $word_usage;
		$bcm->vocab_audio = $vocab_audio;
		array_push($voabDetails,$bcm);
	}
	return $voabDetails;
}


?>