<?php 
//error_reporting(E_ALL);
//ini_set('display_errors',1);
require_once __DIR__ . '/serviceController.php';
require_once __DIR__ . '/commonController.php';
class componentController {

    public $dbConn;
    public function __construct() {
     	$this->dbConn = DBConnection::createConn();
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
 
 
  public function getQuizData($qid){
      
        $data = array();
        if(!is_numeric($qid)){
            return $data;
        }
        $sql = " select * from tbl_component WHERE component_id = :id ";
        $stmt = $this->dbConn->prepare($sql);   
        
        $stmt->bindValue(':id', $qid, PDO::PARAM_INT);
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

 
	 public function getAssQuestionData($edgeId,$no_of_skill_quesArr){
		$ques_arr=array();	
	    foreach($no_of_skill_quesArr as $key=>$skillObjVal){
		  $skillCount=$skillObjVal->qCount;
		  if($skillCount>0){
				$sql = "SELECT tq.*, trc.competency, tqr.compentency_id FROM tbl_questions AS tq
				JOIN tbl_questions_rubric AS tqr ON tqr.question_id=tq.id 
				JOIN tbl_rubric_competency AS trc ON trc.id=tqr.compentency_id WHERE parent_edge_id = :id AND trc.id=:skill_id LIMIT $skillCount";
				$stmt = $this->dbConn->prepare($sql);   
				$stmt->bindValue(':id', $edgeId, PDO::PARAM_INT);
				$stmt->bindValue(':skill_id',$key, PDO::PARAM_INT);
				$stmt->execute();
				$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();
				
			   if(count($RESULT) > 0 ){
					$ques_arr[]= $RESULT;
				 }
			}
			
		 } 
		 //echo "<pre>";print_r($ques_arr);//exit;  
		return $ques_arr;
		
    }
	 public function getRemidationMapAss($edgeId){//Get remidation edge id 
			//echo "<pre>";print_r($edgeId);exit;  
        $data = array();
        if(!is_numeric($edgeId)){
            return $data;
        }
		$sql = "SELECT topic_tree_node_id FROM remediation_assessment_map 
			 WHERE assessment_tree_node_id = :id ";
    
        $stmt = $this->dbConn->prepare($sql);   
        $stmt->bindValue(':id', $edgeId, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
	    $stmt->closeCursor();
       if(count($RESULT) > 0 ){
			return $RESULT[0];
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
			
			$is_word_sentence=$ans_data['is_word_sentence'];
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
					    //$rarr['feedback'] = !empty($feedback)?$feedback:'';
						$rarr['feedback'] = !empty($feedbackArr[2])?$feedbackArr[2]:'';
                        $rarr['is_correct'] = 0; 
					  //if( strcmp($ans, $correct_ans) === 0){
                        if( $ans == $correct_ans){
                            $rarr['is_correct'] = 1;
							$rarr['feedback'] = !empty($feedback)?$feedback:'';
							$rarr['feedback'] = !empty($feedbackArr[1])?$feedbackArr[1]:'';
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
                            if($is_word_sentence=='sentence'){
							 $content .= ' ';	
							}else{
                              $content .= '';
							}
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
 
 public function saveQuizScoreData($token,$paramArr){
          try{	
		  
		      $serviceObj = new serviceController();
				/*  $paramArr=array();
				 foreach($arr as $key=>$value){
					$params = new stdClass();
					$params->test_uniqid = $value['test_uniqid'];
					$params->ques_uniqid=$value['ques_uniqid'];
					$params->ans_uniqid=$value['ans_uniqid'];
					$params->date_ms=$value['date_ms'];
					$params->essay_answer=$value['essay_answer']; 
					$params->user_response=$value['user_response'];	
					$params->av_media_files=$value['av_media_files'];
					
					$params->correct=$value['correct'];
					$params->package_code=$value['package_code'];
					$params->course_code=$value['course_code'];
					
				   $paramArr[]=$params;
				}	
			     */
				$extra=array();
				$extra['client'] = CLIENT_NAME;// $client name;
				$extra['class_name'] = CLIENT_NAME;// $client name;
				$extra['platform']= WEB_SERVICE_PLATFORM;
				$extra['deviceId'] = WEB_SERVICE_DEVICE_ID;
				$extra['appVersion'] = WEB_SERVICE_APP_VERSION;

		        //echo "<pre>";print_r($paramArr);
				$res = $serviceObj->processRequest($token, 'pushanswerattempt', $paramArr,$extra);
				//$res = $serviceObj->processRequest($token, 'pushanswer', $paramArr,$extra);
				$res_json = json_encode($res);
				$res = json_decode($res_json, true);
				//echo "<pre>";print_r($res);

        }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
    }
	

   	
 public function saveQuizScore($userToken,$quiz_edge_id,$paramsArr,$extra ){
	try{	
	    $user_id=$_SESSION['user_id'];

		//echo print_r($paramsArr);exit;
        if($user_id >=0) {
			   $userEdgeIdArr = array();
		       if(isset($extra['appVersion']) && $extra['appVersion'] == 2){
			
			   foreach($paramsArr as $obj){
				  // echo print_r($obj->test_uniqid);
					if(!in_array($obj->test_uniqid, $userEdgeIdArr)){			
						$userEdgeIdArr[] = $obj->test_uniqid;
						$del=$this->aduroDetetePreAnsCL($user_id, $obj->test_uniqid, $obj->package_code,$obj->course_code);
					} 
					$objRet  = $this->aduroCentralLicensingTrackAnswer($user_id,$obj->test_uniqid,$obj->ques_uniqid,$obj->ans_uniqid,$obj->date_ms,$extra['platform'], $obj->package_code,$obj->course_code, $obj->essay_answer, $obj->av_media_files,$obj->user_response,$obj->correct);
					$userEdgeIdArr[]=$obj->test_uniqid;
				  // echo print_r($objRet);
				   // echo print_r($userEdgeIdArr);
				}
			 //  echo print_r($userEdgeIdArr);
				//`echo print_r($objRet);exit;

				return "Success";
			}
		  }else {
			  return "fail";
			//$sr->setCode("TOKEN_EXPIRED");
			//$sr->setStat(0);
		} 
			 /*  $serviceObj = new serviceController(); 
           //echo "<pre>";print_r($paramsArr);exit;
			$res = $serviceObj->processRequest($uToken, 'pushanswer', $paramsArr, $extra);
			$res_json = json_encode($res);
			$res = json_decode($res_json, true);
			echo "<pre>";print_r($res);exit;
            */
	   }//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		} 
		 
	}	
	
	public function aduroCentralLicensingTrackAnswer($user_id,$test_uniqid, $ques_uniqid,$ans_uniqid,$date_ms, $platform, $unique_code, $course_code, $essay_answer, $av_media_files,$user_response,$correct) {
	  try{	
			$con = createConnection();
			$stmt = $con->prepare("insert INTO temp_ans_push(user_id,test_id,ques_id,ans_id,time_sp,fld_datetime,platform,unique_code,course_code,essay_answer, av_media_files,user_response,correct) values(?,?,?,?,?,NOW(),?,?,?,?,?,?,?)");
			$stmt->bind_param("isssissssssi",$user_id,$test_uniqid, $ques_uniqid,$ans_uniqid,$date_ms,$platform,$unique_code, $course_code,$essay_answer,$av_media_files,$user_response,$correct);
			//echo "<pre>";print_r($stmt);
			$stmt->execute();
			$stmt->close();
		}//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		} 
	} 
	public function aduroDetetePreAnsCL($user_id, $test_uniqid, $package_code,$course_code){
		try{	
			 $con = createConnection();
			/*$stmt = $con->prepare("DELETE FROM temp_ans_push WHERE user_id = ? AND test_id = ? AND course_code = ? AND unique_code = ?");
			$stmt->bind_param("isss",$user_id,$test_uniqid, $course_code,$package_code);*/
			//echo "<pre>";print_r($user_id);exit;
			$stmt =$con->prepare("DELETE FROM temp_ans_push WHERE user_id = ? AND test_id = ? AND course_code = ?");
			$stmt->bind_param("iss",$user_id,$test_uniqid,$course_code); 
			$stmt->execute();
			$stmt->close();
		}//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		} 
	} 

	
 
	
    public function getVocabWordDataFromXml($course_code, $word){
        $file_path = $course_data_url.'/CRS-'.$course_code .'/course/vocabulary/words/'.$word.'.xml';
        $xml = getXmlFromLocal($file_path);
        $data = parseXML($xml);
        return $data;
    } 
	public function getVocabWordData($vocab_id){
        
        //$vocab_sql = "Select * from tbl_vocabulary WHERE vocb_id = :vid ";
		 $vocab_sql = "Select * from tbl_vocabulary WHERE id = :vid ";
        $vocab_stmt = $this->dbConn->prepare($vocab_sql);
        
        $vocab_stmt->bindValue(':vid', $vocab_id, PDO::PARAM_STR);
        $vocab_stmt->execute();
        $RESULT = $vocab_stmt->fetchAll(PDO::FETCH_ASSOC);
        $vocab_stmt->closeCursor();
        $data = array();
        if( count($RESULT) ){
            $data = array_shift($RESULT);
        }
		//print_r($data);//exit;  
        return $data;
        
    }

	
	public function getScorePer($test_id,$user_id){
        
        $sql = "SELECT count(*) AS total, sum(case when ans_id = 1 then 1 else 0 end) AS CrrctCount
			FROM temp_ans_push WHERE user_id=:user_id AND test_id=:test_id ";
        $sql_stmt = $this->dbConn->prepare($sql);
        
        $sql_stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $sql_stmt->bindValue(':test_id', $test_id, PDO::PARAM_STR);
        $sql_stmt->execute();
        $RESULT = $sql_stmt->fetchAll(PDO::FETCH_ASSOC);
        $sql_stmt->closeCursor();
        
		
        $RESULT = $RESULT[0];
		$totalQues = $RESULT['total'];
		$totalCrrct = $RESULT['CrrctCount'];
		$scorePer1 = ($totalCrrct*100)/$totalQues;
		$scorePer=  round($scorePer1);
		$scorePer=($scorePer>0)?$scorePer:0;
		return $scorePer;
        
    }
    public function getAssessmentData($asmt_edge_id){

        $data = $this->getQuizData($asmt_edge_id, 2);
        return $data;

    }
    
    
    public function checkMcqScore($ass_edge_id,$ans,$q_time_taken){
 
      //  $ques_data = $this->getAssQuestionData($ass_edge_id);
		//print_r($ques_data);//exit; 
        //$total_ques = count($ques_data);
        $ques_attempted = count($ans);
        $correct_ans = 0;
        $user_score = 0;
        $wrong_ans = 0;
        $sub_ques = 0; 
		/* $ques_format =trim($ques_data['question_type']);
		 
        foreach($ques_data as $ques_arr){
          if( $ques_format == 'AV_TT_AU' || $ques_format == 'EW-TT-AU' ){
                $sub_ques++;
            } 
        }
 */
			$ques_result_arr = array();
			$skill_arr = array();
			$qid_arr = array();
			foreach($ans as $qid => $ans_id){
				
				$ques_result_arr[$qid] = 0;
			  //  $check_arr = $objQuiz->checkQuizAns($qid, $ans_id);
				$check_arr = $this->checkQuizAns($qid,$ans_id,$q_time_taken[$qid]);
			  //echo "<pre>";print_r($check_arr);	
				if( $check_arr['is_correct'] == 1){
					$correct_ans++;
					$ques_result_arr[$qid] = 1;
				}
				$qid_arr[]=$qid;
			}
			$wrong_ans = $ques_attempted - $correct_ans;
			/* return array('asmt_edge_id' => $ass_edge_id,'total_ques' => $total_ques - $sub_ques, 'sub_ques' => $sub_ques ,
				'ques_attempted' => $ques_attempted, 'correct_ans' => $correct_ans, 'wrong_ans' => $wrong_ans, 'ques_result_arr' => $ques_result_arr); */
		   return array('asmt_edge_id' => $ass_edge_id,
				'ques_attempted' => $ques_attempted, 'correct_ans' => $correct_ans, 'wrong_ans' => $wrong_ans, 'ques_result_arr' => $ques_result_arr);
	
   }
   
   public function setRemidationScore($setScoreArr,$rem_edge_id,$userid){
		//echo "<pre>";print_r($setScoreArr); 
		$lScore;
		$sScore;
		$rScore;
		$wScore;
		foreach($setScoreArr as $key=>$val){
			
			if($key=='Listening'){
			  $lScore=$setScoreArr[$key];
			}
			if($key=='Speaking'){
			  $sScore=$setScoreArr[$key];
			}
			if($key=='Reading'){
			  $rScore=$setScoreArr[$key];
			}
			if($key=='Writing'){
			  $wScore=$setScoreArr[$key];
			}
			
	   }
	   $listening_score=(!empty($lScore))?$lScore:0;
	   $speaking_score=(!empty($sScore))?$sScore:0;
	   $reading_score=(!empty($rScore))?$rScore:0;
	   $writing_score=(!empty($wScore))?$wScore:0;
	   
		$sql ="Select * from tblx_user_remediation_scores where user_id=:user_id AND remediation_quiz_id=:edgeId";
	    $stmt = $this->dbConn->prepare($sql);   
        $stmt->bindValue(':edgeId', $rem_edge_id, PDO::PARAM_INT);
		$stmt->bindValue(':user_id', $userid, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
	    $stmt->closeCursor();
		
		$RESULT = $RESULT[0];
		$id = $RESULT['id'];
		$user_id = $RESULT['user_id'];
		$remediation_quiz_id = $RESULT['remediation_quiz_id'];
	 if(empty($user_id)){
			$sql="INSERT INTO tblx_user_remediation_scores(user_id,remediation_quiz_id,	listening_score,speaking_score,reading_score,writing_score,date_attempted) values($userid,$rem_edge_id,:listening_score,:speaking_score,:reading_score,:writing_score,NOW())";
			//echo "<pre>";print_r($sql); 
			$stmt = $this->dbConn->prepare($sql); 
			//$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);			
			//$stmt->bindValue(':edgeId', $rem_edge_id, PDO::PARAM_INT);
			$stmt->bindValue(':listening_score', $listening_score, PDO::PARAM_INT);
			$stmt->bindValue(':speaking_score', $speaking_score, PDO::PARAM_INT);
			$stmt->bindValue(':reading_score', $reading_score, PDO::PARAM_INT);
			$stmt->bindValue(':writing_score', $writing_score, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();
			return true;
		}else{
				$sql= "update tblx_user_remediation_scores set listening_score=:listening_score,speaking_score=:speaking_score,reading_score=:reading_score,writing_score=:writing_score,date_attempted =NOW() where user_id=:user_id AND remediation_quiz_id=:edgeId";
				$stmt = $this->dbConn->prepare($sql);
				$stmt->bindValue(':user_id', $userid, PDO::PARAM_INT);				
				$stmt->bindValue(':edgeId', $rem_edge_id, PDO::PARAM_INT);
				$stmt->bindValue(':listening_score', $listening_score, PDO::PARAM_INT);
				$stmt->bindValue(':speaking_score', $speaking_score, PDO::PARAM_INT);
				$stmt->bindValue(':reading_score', $reading_score, PDO::PARAM_INT);
				$stmt->bindValue(':writing_score', $writing_score, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor();
				return true; 
			
		
		}
		 
    }
   public function getRemidationScore($rem_edge_id,$userid){
		$skill_score_arr=array();
		$sql ="Select * from tblx_user_remediation_scores where user_id=:user_id AND remediation_quiz_id=:edgeId";
	    $stmt = $this->dbConn->prepare($sql);   
        $stmt->bindValue(':edgeId', $rem_edge_id, PDO::PARAM_INT);
		$stmt->bindValue(':user_id', $userid, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
	    $stmt->closeCursor();
		/*  if(count($RESULT) > 0 ){
				$skill_score_arr[]= $RESULT[0];
			}
		return $skill_score_arr; */
		return $RESULT[0];
		 
    }
    
}