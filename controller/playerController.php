<?php
class playerController {

    public $con;
    public function __construct() {
		$this->con = createConnection();
		
    }

	function getQuestions(){

	$qry="SELECT  id,question_stem,correct_answer,question_type from tbl_questions where id IN(18687,18775,1)";
	$stmt = $this->con->prepare($qry);
	$stmt->bind_result($ques_id,$question_stem,$correct_answer,$question_type);
    $stmt->execute();
	//$metaResults = $stmt->result_metadata();
	$questionDetails = array();
		while($stmt->fetch()) { 
				$bcm=new stdClass();
				//getting question detail and assign view
				$bcm->ques_detail_arr=getQuestiondetail($ques_id);
				$bcm->ques_detail=$ques_detail_arr[0];
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
		 print_r($questionDetails);
	
	
	}
	
	

}
?>