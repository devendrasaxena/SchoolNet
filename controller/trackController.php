<?php 
//error_reporting(E_ALL);
//ini_set('display_errors',1);

require_once __DIR__ . '/serviceController.php';
require_once __DIR__ . '/assessmentController.php';
class trackController {
    
    public $dbConn;
    //private $trackType = array();
    public function __construct() {
		$this->dbConn = DBConnection::createConn();
    }

    public function trackConcept($arr){
       //echo "<pre>";print_r($arr);exit;
          try{	
		  	
			$cEdgeID=$arr['cEdgeID'];
			$conceptEdgeId=$arr['concept_edge_id'];
			$token =$arr['token'];
			$package_code =$arr['package_code'];
			$course_code=$arr['course_code'];
			$st = $arr['start_date_ms'];
			$et = $arr['end_date_ms'];
			
			$paramArr=array();
			$serviceObj = new serviceController();
			
			if( is_numeric($cEdgeID) && is_numeric($st) && is_numeric($et) ){
					$params1 = new stdClass();
					$params1->edge_id = $cEdgeID;
					$params1->start_date_ms = $st;
					$params1->end_date_ms = $et;
					$params1->package_code = $package_code;
					$params1->course_code = $course_code;
					
					$params1->client = CLIENT_NAME;// $client name;
					$params1->class_name = CLIENT_NAME;// $client name;
					$params1->platform = WEB_SERVICE_PLATFORM;
					$params1->deviceId = WEB_SERVICE_DEVICE_ID;
					$params1->appVersion = WEB_SERVICE_APP_VERSION;
					array_push($paramArr,$params1);
				 } 
				if( is_numeric($conceptEdgeId) && is_numeric($st) && is_numeric($et) ){
					$params2 = new stdClass();				
					$params2->edge_id = $conceptEdgeId;
					$params2->start_date_ms = $st;
					$params2->end_date_ms = $et;
					$params2->package_code = $package_code;
					$params2->course_code = $course_code;
					
					$params2->client = CLIENT_NAME;// $client name;
					$params2->class_name = CLIENT_NAME;// $client name;
					$params2->platform = WEB_SERVICE_PLATFORM;
					$params2->deviceId = WEB_SERVICE_DEVICE_ID;
					$params2->appVersion = WEB_SERVICE_APP_VERSION;
					array_push($paramArr,$params2); 
			   } 

		       // echo "<pre>";print_r($paramArr);exit;
				$res = $serviceObj->processRequest($token, 'track', $paramArr);
				$res_json = json_encode($res);
				$res = json_decode($res_json, true);
				//echo "<pre>";print_r($res);
        }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
    }

  
     public function trackQuiz($arr){
        // echo "<pre>";print_r($arr);exit;
          try{	
				$cEdgeID=$arr['cEdgeID'];
				$quizEdgeId=$arr['quiz_edge_id'];
				$token =$arr['token'];
				$package_code =$arr['package_code'];
				$course_code=$arr['course_code'];
				$st = $arr['start_date_ms'];
				$et = $arr['end_date_ms'];
				
				$paramArr=array();
				$serviceObj = new serviceController();
				if( is_numeric($cEdgeID) && is_numeric($st) && is_numeric($et) ){
					$params1 = new stdClass();
					$params1->edge_id = $cEdgeID;
					$params1->start_date_ms = $st;
					$params1->end_date_ms = $et;
					$params1->package_code = $package_code;
					$params1->course_code = $course_code;
					
					$params1->client = CLIENT_NAME;// $client name;
					$params1->class_name = CLIENT_NAME;// $client name;
					$params1->platform = WEB_SERVICE_PLATFORM;
					$params1->deviceId = WEB_SERVICE_DEVICE_ID;
					$params1->appVersion = WEB_SERVICE_APP_VERSION;
					array_push($paramArr,$params1);
				 } 
				if( is_numeric($quizEdgeId) && is_numeric($st) && is_numeric($et) ){
					$params2 = new stdClass();				
					$params2->edge_id = $quizEdgeId;
					$params2->start_date_ms = $st;
					$params2->end_date_ms = $et;
					$params2->package_code = $package_code;
					$params2->course_code = $course_code;
					
					$params2->client = CLIENT_NAME;// $client name;
					$params2->class_name = CLIENT_NAME;// $client name;
					$params2->platform = WEB_SERVICE_PLATFORM;
					$params2->deviceId = WEB_SERVICE_DEVICE_ID;
					$params2->appVersion = WEB_SERVICE_APP_VERSION;
					array_push($paramArr,$params2); 
			   } 
				// echo "<pre>";print_r($paramArr);exit;
				$res = $serviceObj->processRequest($token, 'track', $paramArr);
				$res_json = json_encode($res);
				$res = json_decode($res_json, true);
				//echo "<pre>";print_r($res);

        }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
    }
	
      public function trackRolePlay($arr){
      // echo "<pre>";print_r($arr);exit;
          try{	
		  	$cEdgeID=$arr['cEdgeID'];
			$roleplayEdgeId=$arr['rp_edge_id'];
			$watchEdgeId=$arr['tab_edge_id'];
			$token =$arr['token'];
			$package_code =$arr['package_code'];
			$course_code=$arr['course_code'];
			$st = $arr['start_date_ms'];
			$et = $arr['end_date_ms'];
			
			$paramArr=array();
			$serviceObj = new serviceController();
			if( is_numeric($cEdgeID) && is_numeric($st) && is_numeric($et) ){
					$params1 = new stdClass();
					$params1->edge_id = $cEdgeID;
					$params1->start_date_ms = $st;
					$params1->end_date_ms = $et;
					$params1->package_code = $package_code;
					$params1->course_code = $course_code;
					
					$params1->client = CLIENT_NAME;// $client name;
					$params1->class_name = CLIENT_NAME;// $client name;
					$params1->platform = WEB_SERVICE_PLATFORM;
					$params1->deviceId = WEB_SERVICE_DEVICE_ID;
					$params1->appVersion = WEB_SERVICE_APP_VERSION;
					array_push($paramArr,$params1);
				 } 
			if( is_numeric($roleplayEdgeId) && is_numeric($st) && is_numeric($et) ){
					$params2 = new stdClass();				
					$params2->edge_id = $roleplayEdgeId;
					$params2->start_date_ms = $st;
					$params2->end_date_ms = $et;
					$params2->package_code = $package_code;
					$params2->course_code = $course_code;
					
					$params2->client = CLIENT_NAME;// $client name;
					$params2->class_name = CLIENT_NAME;// $client name;
					$params2->platform = WEB_SERVICE_PLATFORM;
					$params2->deviceId = WEB_SERVICE_DEVICE_ID;
					$params2->appVersion = WEB_SERVICE_APP_VERSION;
					array_push($paramArr,$params2); 
			   } 
			   if( is_numeric($watchEdgeId) && is_numeric($st) && is_numeric($et) ){
					$params3 = new stdClass();				
					$params3->edge_id = $watchEdgeId;
					$params3->start_date_ms = $st;
					$params3->end_date_ms = $et;
					$params3->package_code = $package_code;
					$params3->course_code = $course_code;
					
					$params3->client = CLIENT_NAME;// $client name;
					$params3->class_name = CLIENT_NAME;// $client name;
					$params3->platform = WEB_SERVICE_PLATFORM;
					$params3->deviceId = WEB_SERVICE_DEVICE_ID;
					$params3->appVersion = WEB_SERVICE_APP_VERSION;
					array_push($paramArr,$params3); 
			   }
			   /* if( is_numeric($inactEdgeId) && is_numeric($st) && is_numeric($et) ){
					$params4 = new stdClass();				
					$params4->edge_id = $inactEdgeId;
					$params4->start_date_ms = $st;
					$params4->end_date_ms = $et;
					$params4->package_code = $package_code;
					$params4->course_code = $course_code;
					
					$params4->client = CLIENT_NAME;// $client name;
					$params4->class_name = CLIENT_NAME;// $client name;
					$params4->platform = WEB_SERVICE_PLATFORM;
					$params4->deviceId = WEB_SERVICE_DEVICE_ID;
					$params4->appVersion = WEB_SERVICE_APP_VERSION;
					array_push($paramArr,$params4); 
			   }
			   
			   if( is_numeric($reviewEdgeId) && is_numeric($st) && is_numeric($et) ){
					$params5 = new stdClass();				
					$params5->edge_id = $reviewEdgeId;
					$params5->start_date_ms = $st;
					$params5->end_date_ms = $et;
					$params5->package_code = $package_code;
					$params5->course_code = $course_code;
					
					$params5->client = CLIENT_NAME;// $client name;
					$params5->class_name = CLIENT_NAME;// $client name;
					$params5->platform = WEB_SERVICE_PLATFORM;
					$params5->deviceId = WEB_SERVICE_DEVICE_ID;
					$params5->appVersion = WEB_SERVICE_APP_VERSION;
					array_push($paramArr,$params5); 
			   } */
				 //echo "<pre>";print_r($paramArr);exit;
				$res = $serviceObj->processRequest($token, 'track', $paramArr);
				$res_json = json_encode($res);
				$res = json_decode($res_json, true);
				//echo "<pre>";print_r($res);

        }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
    } 
	
	     public function trackVocab($arr){
        // echo "<pre>";print_r($arr);exit;
          try{	
				$cEdgeID=$arr['cEdgeID'];
				$vocabEdgeId=$arr['vocab_edge_id'];
				$token =$arr['token'];
				$package_code =$arr['package_code'];
				$course_code=$arr['course_code'];
				$st = $arr['start_date_ms'];
				$et = $arr['end_date_ms'];
				
				$paramArr=array();
				$serviceObj = new serviceController();
				if( is_numeric($cEdgeID) && is_numeric($st) && is_numeric($et) ){
					$params1 = new stdClass();
					$params1->edge_id = $cEdgeID;
					$params1->start_date_ms = $st;
					$params1->end_date_ms = $et;
					$params1->package_code = $package_code;
					$params1->course_code = $course_code;
					
					$params1->client = CLIENT_NAME;// $client name;
					$params1->class_name = CLIENT_NAME;// $client name;
					$params1->platform = WEB_SERVICE_PLATFORM;
					$params1->deviceId = WEB_SERVICE_DEVICE_ID;
					$params1->appVersion = WEB_SERVICE_APP_VERSION;
					array_push($paramArr,$params1);
				 } 
				if( is_numeric($vocabEdgeId) && is_numeric($st) && is_numeric($et) ){
					$params2 = new stdClass();				
					$params2->edge_id = $vocabEdgeId;
					$params2->start_date_ms = $st;
					$params2->end_date_ms = $et;
					$params2->package_code = $package_code;
					$params2->course_code = $course_code;
					
					$params2->client = CLIENT_NAME;// $client name;
					$params2->class_name = CLIENT_NAME;// $client name;
					$params2->platform = WEB_SERVICE_PLATFORM;
					$params2->deviceId = WEB_SERVICE_DEVICE_ID;
					$params2->appVersion = WEB_SERVICE_APP_VERSION;
					array_push($paramArr,$params2); 
			   } 
				// echo "<pre>";print_r($paramArr);//exit;
				$res = $serviceObj->processRequest($token, 'track', $paramArr);
				$res_json = json_encode($res);
				$res = json_decode($res_json, true);
				echo "<pre>";print_r($res);

        }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
    }
	
   public function componentCompletion($arr){
          try{	
			$score=$arr['score'];
			$cEdgeID=$arr['cEdgeID'];
			$componentEdgeId=$arr['component_edge_id'];
			$token =$arr['token'];
			$package_code =$arr['package_code'];
			$course_code=$arr['course_code'];
			$complete_status = $arr['complete_status'];
            $batch_id = $arr['batch_id'];
			$paramArr=array();
			$serviceObj = new serviceController();
			if(is_numeric($componentEdgeId)){
					$params = new stdClass();
					$params->score = $score;
					$params->edge_id = $componentEdgeId;
					$params->completion = $complete_status;
					$params->package_code = $package_code;
					$params->course_code = $course_code;
					$params->batch_id = $batch_id;
					
					$params->client = CLIENT_NAME;// $client name;
					$params->class_name = CLIENT_NAME;// $client name;
					$params->platform = WEB_SERVICE_PLATFORM;
					$params->deviceId = WEB_SERVICE_DEVICE_ID;
					$params->appVersion = WEB_SERVICE_APP_VERSION;
					array_push($paramArr,$params);
					
				 }
				echo "Component <pre>";print_r($paramArr);		 
				$res = $serviceObj->processRequest($token, 'syncComponentCompletion', $paramArr);
				$res_json = json_encode($res);
				$res = json_decode($res_json, true);
				
			if(strcasecmp($res['retCode'], 'SUCCESS') == 0 && count($res['retVal']) ){
				$retValArr = $res['retVal'];
					//echo "<pre>";print_r($retValArr);
					$completeComponentArr=array();
				foreach($retValArr as $retVal){
					//echo print($retVal['edge_id']);
					if( isset($retVal['edge_id']) &&  $retVal['edge_id']==$componentEdgeId){
						$completeComponentObj = new stdClass();
					    $completeComponentObj->course_code= $retVal['course_code'];
					    $completeComponentObj->edge_id= $retVal['edge_id'];
					    $completeComponentObj->completion= $retVal['completion'];
					    $completeComponentArr[]=$completeComponentObj;
					}
				}
				return $completeComponentArr;
			  }else{
				  return false;
			  }

        }//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
    }

	
    public function vocabWordCompletion($arr){
       //echo "<pre>";print_r($arr);exit;
          try{	
				$cEdgeID=$arr['cEdgeID'];
				$componentEdgeId=$arr['component_edge_id'];
				$word_id=$arr['word_id'];
				$token =$arr['token'];
				$package_code =$arr['package_code'];
				$course_code=$arr['course_code'];
				$complete_word_status = $arr['complete_word_status'];
			    $paramArr=array();
			    $serviceObj = new serviceController();
			    $params = new stdClass();
					// echo "<pre>";print_r($key);
					
					$params->chapter_edge_id = $cEdgeID;
					$params->edge_id = $componentEdgeId;
					$params->word_id = $word_id;
					$params->completion = $complete_word_status;
					$params->package_code = $package_code;
					$params->course_code = $course_code;
					
					$params->client = CLIENT_NAME;// $client name;
					$params->class_name = CLIENT_NAME;// $client name;
					$params->platform = WEB_SERVICE_PLATFORM;
					$params->deviceId = WEB_SERVICE_DEVICE_ID;
					$params->appVersion = WEB_SERVICE_APP_VERSION;
					array_push($paramArr,$params);
				 
				 
			     
		        // echo "<pre>";print_r($paramArr);//exit;
				$res = $serviceObj->processRequest($token, 'syncWordCompletion', $paramArr);
				$res_json = json_encode($res);
				$res = json_decode($res_json, true);
				//echo "<pre>";print_r($res);	
			if(strcasecmp($res['retCode'], 'SUCCESS') == 0 && count($res['retVal']) ){
				$retValArr = $res['retVal'];
				$completeWordArr=array();
				foreach($retValArr as $retVal){
					//echo print($retVal['edge_id']);
					if( isset($retVal['edge_id']) &&  $retVal['edge_id']==$componentEdgeId){
						$completeWordObj = new stdClass();
					    $completeWordObj->course_code= $retVal['course_code'];
					    $completeWordObj->edge_id= $retVal['edge_id'];
						$completeWordObj->word_id= $retVal['word_id'];
					    $completeWordObj->completion= $retVal['completion'];
					    $completeWordArr[]=$completeWordObj;
					}
				}
					//echo "<pre>";print_r($completeWordArr);
					return $completeWordArr;
			  }else{
				  return false;
			  }
			
        }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
    }
public function getCompletion($arrTopic){
      //echo "<pre>";print_r($arrTopic);//exit;
          try{	
				$edge_id=$arrTopic['edge_id'];
			    $token =$arrTopic['userToken'];//$arrTopic['userToken'];
				$package_code =$arrTopic['package_code'];
				$course_code=$arrTopic['course_code'];
				$batch_id=$arrTopic['batch_id'];
				
				$serviceObj = new serviceController();
				if( is_numeric($edge_id) || is_array($edge_id)){
						$params = new stdClass();
						$params->edge_id= $edge_id;
						$params->package_code = $package_code;
						$params->course_code = $course_code;
						$params->batch_id = $batch_id;
						
						$params->client = CLIENT_NAME;// $client name;
						$params->class_name = CLIENT_NAME;// $client name;
						$params->platform = WEB_SERVICE_PLATFORM;
						$params->deviceId = WEB_SERVICE_DEVICE_ID;
						$params->appVersion = WEB_SERVICE_APP_VERSION;
						
						// echo "<pre>";print_r($params);
						$res = $serviceObj->processRequest($token, 'getCompletionAndPer', $params);
						$res_json = json_encode($res);
						$res = json_decode($res_json, true);
						//echo "<pre>";print_r($res);
				
				  if(strcasecmp($res['retCode'], 'SUCCESS') == 0 && count($res['retVal']) ){
						$retValArr = $res['retVal'];
						return $retValArr;
					}else{
						return false;
					}  
		   } 
        }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
    }
	
	
public function getAllCompletion($arrTopic){
      //echo "<pre>";print_r($arrTopic);//exit;
          try{	
				$edge_id=$arrTopic['edge_id'];
			    $token =$arrTopic['userToken'];//$arrTopic['userToken'];
				$package_code =$arrTopic['package_code'];
				$course_code=$arrTopic['course_code'];
				$batch_id=$arrTopic['batch_id'];
				
				$serviceObj = new serviceController();
				if( is_numeric($edge_id) || is_array($edge_id)){
						$params = new stdClass();
						$params->edge_id= $edge_id;
						$params->package_code = $package_code;
						$params->course_code = $course_code;
						$params->batch_id = $batch_id;
						
						$params->client = CLIENT_NAME;// $client name;
						$params->class_name = CLIENT_NAME;// $client name;
						$params->platform = WEB_SERVICE_PLATFORM;
						$params->deviceId = WEB_SERVICE_DEVICE_ID;
						$params->appVersion = WEB_SERVICE_APP_VERSION;
						
					//echo "<pre>";print_r($params);
						$res = $serviceObj->processRequest($token, 'getAllCompletionAndPer', $params);
						$res_json = json_encode($res);
						$res = json_decode($res_json, true);
						//echo "<pre>";print_r($res);
				
				  if(strcasecmp($res['retCode'], 'SUCCESS') == 0 && count($res['retVal']) ){
						$retValArr = $res['retVal'];
						return $retValArr;
					}else{
						return false;
					}  
		   } 
        }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
    }

 	
	
public function chapterCompletion($arrChapter){
     // echo "<pre>";print_r($arrChapter);//exit;
		try{	
			$chapterEdgeId =$arrChapter['chapter_edge_id'];
			$userToken =$arrChapter['userToken'];
			$package_code =$arrChapter['package_code'];
			$course_code = $arrChapter['course_code'];
			$batch_id=$arrChapter['batch_id'];
			
			$completeChapterArr=array();
			$assessmentObj = new assessmentController();
			$compontentArr = $assessmentObj->getScenarioByChapterId($chapterEdgeId);

			$totalComponent=0;
			$completeStatus=0;
			if($compontentArr>0){
			  
				foreach($compontentArr as $comp_loop){
	
					$component_edge_id= $comp_loop->component_edge_id;
					$type=$comp_loop->scenario_type;
					$subType= $comp_loop->scenario_subtype;
					
					if($type=='Practice' &&  $subType=="Quiz"){
						$edgeIdArr[] = $component_edge_id;
						$totalComponent+=1;
					 } 
					if($type=='Practice' &&  $subType=='Conversation Practice'){
						$edgeIdArr[] = $component_edge_id;
						$totalComponent+=1;
					 }
					if($type=='Concept' &&  $subType=="CC Video"){
						$edgeIdArr[] = $component_edge_id;
						$totalComponent+=1;
					  } 
					 if($type=='Practice' &&  $subType=="Role-play"){
						$edgeIdArr[] = $component_edge_id;
						$totalComponent+=1;
					   } 
					if($type=='Practice' &&  $subType=="Speech Role-play"){
						$edgeIdArr[] = $component_edge_id;
						$totalComponent+=1;
					   }    
					   if($type=='Practice' &&  $subType=="Game"){
						$edgeIdArr[] = $component_edge_id;
						$totalComponent+=1;
					   }  
					 
					 if($type=='Practice' &&  $subType=="SpeedReading"){
						$edgeIdArr[] = $component_edge_id;
						$totalComponent+=1;
					   } 
					   
					 if($type=='Practice' &&  $subType=="SpeechRecognition"){
						$edgeIdArr[] = $component_edge_id;
						$totalComponent+=1;
					   } 
					
					if($type=='Practice' &&  $subType=="Resources"){
						$edgeIdArr[] = $component_edge_id;
						$totalComponent+=1;
					   } 						
					
					if($type=='Practice' &&  $subType=="Conversation Video"){
						$edgeIdArr[] = $component_edge_id;
						$totalComponent+=1;
					   } 
					
					}
			}
			
			
			$arrComponent = array();
			$arrComponent['edge_id'] = $edgeIdArr;
			$arrComponent['userToken'] = $userToken;
			$arrComponent['package_code'] = $package_code;
			$arrComponent['course_code'] = $course_code;
			$arrComponent['batch_id'] = $batch_id;
			
			//echo "<pre>";print_r($arrComponent);exit;
			
			
			$completeArrComponent=  $this->getAllCompletion($arrComponent);
			//echo "all<pre>";print_r($completeArrComponent);
			
			foreach($completeArrComponent as $qValue){
				$completion=$qValue['completion_status'];
				if($completion=='c'){
					$completeStatus+=1;
				}else if($completion=='nc'){
					$completeStatus+=1/2;
				}else{
					$completeStatus+=0;
				}						
			}
				
			$completeChapterPer= round(($completeStatus/$totalComponent)*100);
			if($completeChapterPer==100){
				$chapter_complete_status = 'c';
			}else if(($completeChapterPer>0)&&($completeChapterPer<100)){
				$chapter_complete_status = 'nc';
			}else{
				$chapter_complete_status = 'na';
			}
			 
			
			$paramArr=array();
			$serviceObj = new serviceController();
				if(is_numeric($chapterEdgeId)){
					$params = new stdClass();
					$params->edge_id = $chapterEdgeId;
					$params->completion = $chapter_complete_status;
					$params->complete_per = $completeChapterPer;
					$params->package_code = $package_code;
					$params->course_code = $course_code;
					$params->batch_id = $batch_id;

					$params->client = CLIENT_NAME;// $client name;
					$params->class_name = CLIENT_NAME;// $client name;
					$params->platform = WEB_SERVICE_PLATFORM;
					$params->deviceId = WEB_SERVICE_DEVICE_ID;
					$params->appVersion = WEB_SERVICE_APP_VERSION;
					array_push($paramArr,$params);
				} 
				
				 //echo "chapter<pre>";print_r($paramArr);//exit;
				$res = $serviceObj->processRequest($userToken, 'syncComponentCompletion', $paramArr);
				 $res_json = json_encode($res);
				$res = json_decode($res_json, true);
				
		}//catch exception
	catch(Exception $e) {
	  echo 'Message: ' .$e->getMessage();exit;
	}
    }
	
public function topicCompletion($arrtopic){
     // echo "<pre>";print_r($arrChapter);//exit;
          try{	
				$topic_edge_id=$arrtopic['topic_edge_id'];
			    $userToken =$arrtopic['userToken'];
				$package_code =$arrtopic['package_code'];
				$course_code=$arrtopic['course_code'];
				$batch_id=$arrtopic['batch_id'];
				
				$completeChapterArr=array();
				$assessmentObj = new assessmentController();
			    $chaptersArr = $assessmentObj->getChapterByTopicEdgeId($topic_edge_id,$customChapter=null);

				$completeStatus=0;
				$totalChapter=0;
				if(count($chaptersArr>0)){
				   foreach($chaptersArr as $chap_loop){
						$chapter_edge_id= $chap_loop->edge_id;
						$totalChapter+=1;
						$edgeIdArr[] = $chapter_edge_id; 
					}
				}
				
				
				$arrChapter = array();
				$arrChapter['edge_id'] = $edgeIdArr;
				$arrChapter['userToken'] = $userToken;
				$arrChapter['package_code'] = $package_code;
				$arrChapter['course_code'] = $course_code;
				$arrChapter['batch_id'] = $batch_id;
				//echo "<pre>";print_r($arrTopic);exit;
				
				$completeArrChapter=  $this->getAllCompletion($arrChapter);
				
				
				
				//$completeComponent = array_count_values(call_user_func_array('array_merge', $completeArrChapter));
				//print_r($completeComponent);
				
				foreach($completeArrChapter as $qValue){
					$completion=$qValue['completion_status'];
					if($completion=='c'){
						$completeStatus+=1;
					}elseif($completion=='nc'){
						$completeStatus+=1/2;
					}else{
						$completeStatus+=0;
					}						
				}
					
				$completeTopicPer= round(($completeStatus/$totalChapter)*100);
				if($completeTopicPer==100){
					$topic_complete_status = 'c';

				}else if(($completeTopicPer>0)&&($completeTopicPer<100)){
					$topic_complete_status = 'nc';
				}else{
					$topic_complete_status = 'na';
				}
				 
				
				$paramArr=array();
				$serviceObj = new serviceController();
					if(is_numeric($topic_edge_id)){
						$params = new stdClass();
						$params->edge_id = $topic_edge_id;
						$params->completion = $topic_complete_status;
						$params->complete_per = $completeTopicPer;
						$params->package_code = $package_code;
						$params->course_code = $course_code;
						$params->batch_id = $batch_id;
						
						$params->client = CLIENT_NAME;// $client name;
						$params->class_name = CLIENT_NAME;// $client name;
						$params->platform = WEB_SERVICE_PLATFORM;
						$params->deviceId = WEB_SERVICE_DEVICE_ID;
						$params->appVersion = WEB_SERVICE_APP_VERSION;
						array_push($paramArr,$params);
						
					} 
					
					//To return
					$returnCompletion = new stdClass();
					$returnCompletion->completion = $topic_complete_status;
					$returnCompletion->complete_per = $completeTopicPer;
					
					 echo "topic<pre>";print_r($paramArr);//exit;
					$res = $serviceObj->processRequest($userToken, 'syncComponentCompletion', $paramArr);
					$res_json = json_encode($res);
					$res = json_decode($res_json, true);
					//echo "<pre>";print_r($params);print_r($res);exit;
					
					if(strcasecmp($res['retCode'], 'SUCCESS') == 0){
					    $retValArr = $res['retVal'];
						return $returnCompletion;
					}else{
						return false;
					} 
			}//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
    }
		
public function chapterTotalCompletion($arrChapter){
	  // echo "<pre>";print_r($arrChapter);//exit;
          try{	
				$topic_edge_id=$arrChapter['topic_edge_id'];
		        $chapterEdgeId =$arrChapter['chapter_edge_id'];
			    $token =$arrChapter['userToken'];
				$package_code =$arrChapter['package_code'];
				$course_code=$arrChapter['course_code'];
				
				$completeChapterArr=array();
				
				 $paramArr=array();
				 $serviceObj = new serviceController();
					if( is_numeric($chapterEdgeId)){
						$params = new stdClass();
						$params->edge_id = $chapterEdgeId;
						$params->package_code = $package_code;
						$params->course_code = $course_code;
						
						$params->client = CLIENT_NAME;// $client name;
						$params->class_name = CLIENT_NAME;// $client name;
						$params->platform = WEB_SERVICE_PLATFORM;
						$params->deviceId = WEB_SERVICE_DEVICE_ID;
						$params->appVersion = WEB_SERVICE_APP_VERSION;
						array_push($paramArr,$params);
						
					 } 
					 //echo "<pre>";print_r($paramArr);exit;
					$res = $serviceObj->processRequest($token, 'getChapterOrTopicCompletion', $paramArr);
					$res_json = json_encode($res);
					$res = json_decode($res_json, true);
					//echo "<pre>";print_r($res);
				
				  if(strcasecmp($res['retCode'], 'SUCCESS') == 0 && count($res['retVal']) ){
					$retValArr = $res['retVal'];
						//echo "<pre>";print_r($retValArr);
						$completeChapterObj = new stdClass();
					foreach($retValArr as $retVal){
					//echo print($retVal['edge_id']);
					if( isset($retVal['edge_id']) &&  $retVal['edge_id']==$chapterEdgeId &&  $retVal['course_code']==$course_code){
						//$completeChapterObj = new stdClass();
					    $completeChapterObj->course_code= $retVal['course_code'];
					    $completeChapterObj->edge_id= $retVal['edge_id'];
					    $completeChapterObj->completion= $retVal['completion'];
						//$completeChapterObj->percentage= $completeChapterPer;
						//$completeChapterObj->totalComponent= $totalComponent;
					    //$completeChapterArr[]=$completeChapterObj;
					}
				}
				return $completeChapterObj; 
			}else{
			  return false;
		    } 
		 }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}		
 }	 
 

public function getchapterQuizAvgCompletion($arrTopic,$arrChapter=array()){
     // echo "<pre>";print_r($arrTopic);//exit;
          try{	
				$edge_id=$arrTopic['edge_id'];
			    $token =$arrTopic['userToken'];//$arrTopic['userToken'];
				$package_code =$arrTopic['package_code'];
				$course_code=$arrTopic['course_code'];
				
				
				$serviceObj = new serviceController();
				if( is_numeric($edge_id) || is_array($edge_id)){
						$params = new stdClass();
						$params->edge_id= $edge_id;
						$params->package_code = $package_code;
						$params->course_code = $course_code;
						
						$params->client = CLIENT_NAME;// $client name;
						$params->class_name = CLIENT_NAME;// $client name;
						$params->platform = WEB_SERVICE_PLATFORM;
						$params->deviceId = WEB_SERVICE_DEVICE_ID;
						$params->appVersion = WEB_SERVICE_APP_VERSION;
						
						 //echo "<pre>";print_r($params);
						$res = $serviceObj->processRequest($token, 'get_avg_quiz_score', $params);
						$res_json = json_encode($res);
						$res = json_decode($res_json, true);
						//echo "<pre>";print_r($res);
				
				  if(strcasecmp($res['retCode'], 'SUCCESS') == 0 && count($res['retVal']) ){
						$retValArr = $res['retVal'];
						return $retValArr;
					}else{
						return false;
					}  
		   } 
        }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
    }
	

	
public function setDailGoal($arr){
    //   echo "<pre>";print_r($arr);exit;
          try{	
			$goal_id=$arr['goal_id'];
			$duration_id=$arr['duration_id'];
			$token =$arr['token'];
			$serviceObj = new serviceController();
			
			if( is_numeric($goal_id) && is_numeric($duration_id)){
					$params = new stdClass();
					$params->goal_id = $goal_id;
					$params->duration_id = $duration_id;

					$params->client = CLIENT_NAME;// $client name;
					$params->class_name = CLIENT_NAME;// $client name;
					$params->platform = WEB_SERVICE_PLATFORM;
					$params->deviceId = WEB_SERVICE_DEVICE_ID;
					$params->appVersion = WEB_SERVICE_APP_VERSION;
				 } 
		        //echo "<pre>";print_r($params);exit;
				$res = $serviceObj->processRequest($token, 'set_goal', $params);
				$res_json = json_encode($res);
				$res = json_decode($res_json, true);
				return $res;
				//echo "<pre>";print_r($res);
        }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
    }

	
	public function dailGoalCompletetion($arr){
       //echo "<pre>";print_r($arr);exit;
          try{
			$token =$arr['token'];
			$package_code =$arr['package_code'];
			$course_code=$arr['course_code'];
			$curdate = $arr['date'];

			$paramArr=array();
			$serviceObj = new serviceController();
			
			$params = new stdClass();
			$params->date_time = $curdate;
			$params->unique_code = $package_code;
			$params->course_code = $course_code;
			
			$params->client = CLIENT_NAME;// $client name;
			$params->class_name = CLIENT_NAME;// $client name;
			$params->platform = WEB_SERVICE_PLATFORM;
			$params->deviceId = WEB_SERVICE_DEVICE_ID;
			$params->appVersion = WEB_SERVICE_APP_VERSION;
					
			//echo "<pre>";print_r($params);//exit;
			$res = $serviceObj->processRequest($token, 'get_daily_goal',$params);
			$res_json = json_encode($res);
			$res = json_decode($res_json, true);
			//echo "<pre>";print_r($res);
			
			
			if(strcasecmp($res['retCode'], 'SUCCESS') == 0 && count($res['retVal']) ){
				$retVal = $res['retVal'];
				$goalObj = new stdClass();
				if( isset($retVal['duration_id']) ){
				   //$goalObj->duration_id= $retVal['duration_id'];
				   $goalObj->duration_id= $retVal['duration_id'];
				   $goalObj->duration= $retVal['duration_mnt'];
				   $goalObj->streakCount= $retVal['streakCount'];
				   $goalObj->created_date= $retVal['created_date'];
				   $goalObj->days_completed= $retVal['days_completed'];
				}
				return $goalObj;

				//echo "<pre>";print_r($goalObj->duration_mnt);
			  }else{
				return false;
			  }
			 
        }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
    }
	
	 public function trackPerformance1($arr){
       //echo "<pre>";print_r($arr);//exit;
          try{	
				$token =$arr['token'];
				$package_code =$arr['package_code'];
				$course_code=$arr['course_code'];
				
			    $serviceObj = new serviceController();
			    $params = new stdClass();
				$params->package_code = $package_code;
				$params->course_code = $course_code;
				
				$params->client = CLIENT_NAME;// $client name;
				$params->class_name = CLIENT_NAME;// $client name;
				$params->platform = WEB_SERVICE_PLATFORM;
				$params->deviceId = WEB_SERVICE_DEVICE_ID;
				$params->appVersion = WEB_SERVICE_APP_VERSION;

		        // echo "<pre>";print_r($paramArr);//exit;
				$res = $serviceObj->processRequest($token, 'course_overall_data2', $params);
				$res_json = json_encode($res);
				$res = json_decode($res_json, true);
				//echo "<pre>";print_r($res);	
			if(strcasecmp($res['retCode'], 'SUCCESS') == 0 && count($res['retVal']) ){
					$retVal = $res['retVal'];
					//echo print_r($retValArr['edge_id']);
					$completePerformanceObj = new stdClass();
					  if( isset($retVal['edge_id'])){
						$completePerformanceObj->edge_id= $retVal['edge_id'];
						$completePerformanceObj->course_name = $retVal['course_name'];
						$completePerformanceObj->total_time_spent = $retVal['total_time_spent'];
						$completePerformanceObj->number_of_topics = $retVal['number_of_topics'];
						$completePerformanceObj->number_of_completed_topic = $retVal['number_of_completed_topic'];
						$completePerformanceObj->number_of_chapters = $retVal['number_of_chapters'];
						$completePerformanceObj->number_of_completed_chapter = $retVal['number_of_completed_chapter'];
						$completePerformanceObj->skills = $retVal['skills']; 
							
						}
					   return $completePerformanceObj; 
					
				}else{
				  return false;
			  }
			 
        }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
    }
	


	
	 public function trackPerformance($arr){
       //echo "<pre>";print_r($arr);//exit;
          try{	
				$token =$arr['token'];
				$package_code =$arr['package_code'];
				$course_code=$arr['course_code'];
				
			    $serviceObj = new serviceController();
			    $params = new stdClass();
				$params->package_code = $package_code;
				$params->course_code = $course_code;
				
				$params->client = CLIENT_NAME;// $client name;
				$params->class_name = CLIENT_NAME;// $client name;
				$params->platform = WEB_SERVICE_PLATFORM;
				$params->deviceId = WEB_SERVICE_DEVICE_ID;
				$params->appVersion = WEB_SERVICE_APP_VERSION;

		        //echo "<pre>";print_r($params);//exit;
				$res = $serviceObj->processRequest($token, 'getUserPerformance', $params);
				$res_json = json_encode($res);
				$res = json_decode($res_json, true);
				//echo "<pre>res";print_r($res);	
			if(strcasecmp($res['retCode'], 'SUCCESS') == 0 && count($res['retVal']) ){
					$retVal = $res['retVal'];
					//echo"<pre>"; print_r($retVal['course_id']); 
					$completePerformanceObj = new stdClass();
					  if( isset($retVal['course_id'])){
						$completePerformanceObj->edge_id= $retVal['course_id'];
						//$completePerformanceObj->course_name = $retVal['course_name'];
						$completePerformanceObj->total_time_spent = $retVal['total_time'];
						$completePerformanceObj->number_of_topics = $retVal['topic_count'];
						$completePerformanceObj->number_of_completed_topic = $retVal['topic_complete_count'];
						$completePerformanceObj->number_of_chapters = $retVal['chapter_count'];
						$completePerformanceObj->number_of_completed_chapter = $retVal['chapter_complete_count'];
						$completePerformanceObj->skills = '';//$retVal['skills']; 
							
						}
					   return $completePerformanceObj; 
					
				}else{
				  return false;
			  }
			 
        }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
    }

	
	public function trackSetUserBookmark($arr){
       //echo "<pre>";print_r($arr);//exit;
          try{	
				$token =$arr['token'];
				$package_code =$arr['package_code'];
				$course_code=$arr['course_code'];
				$topic_edge_id=$arr['topic_edge_id'];
				$chapter_edge_id=$arr['chapter_edge_id'];
				$component_edge_id=$arr['component_edge_id'];
				$other=$arr['other'];
				$bookmark_type=$arr['bookmark_type'];
				$serviceObj = new serviceController();
	         
			    $params = new stdClass();
				$params->package_code = $package_code;
				$params->course_code = $course_code;
				$params->topic_edge_id = $topic_edge_id;
				$params->chapter_edge_id = $chapter_edge_id;
				$params->component_edge_id = $component_edge_id;
				$params->other = $other;
				$params->bookmark_type = $bookmark_type;
				$params->client = CLIENT_NAME;// $client name;
				$params->class_name = CLIENT_NAME;// $client name;
				$params->platform = WEB_SERVICE_PLATFORM;
				$params->deviceId = WEB_SERVICE_DEVICE_ID;
				$params->appVersion = WEB_SERVICE_APP_VERSION;
				
		       //echo "<pre>";print_r($params);exit;
				$res = $serviceObj->processRequest($token, 'set_user_bookmark', $params);
				$res_json = json_encode($res);
				$res = json_decode($res_json, true);
				echo "<pre>";print_r($res);	
			  if(strcasecmp($res['retCode'], 'SUCCESS') == 0 && count($res['retVal']) ){
					$retValArr = $res['retVal'];
					//echo print_r($retValArr['skills']);
					//return $res['retVal'];
					/*  $completeSkillPerformanceArr=array();
					  if( isset($retValArr['skills'])){
						  foreach($retValArr['skills'] as $retVal){
							// echo print_r($retVal);
						   $completeSkillPerformanceObj = new stdClass();
						   $completeSkillPerformanceObj->skill_id= $retVal['skill_id'];
						   $completeSkillPerformanceObj->skill_name = $retVal['skill_name'];
						   $completeSkillPerformanceObj->level = $retVal['level'];
						   $completeSkillPerformanceArr[]=$completeSkillPerformanceObj; 
						
						 }
					   return $completeSkillPerformanceArr;
					  } */
				}else{
				  return false;
			  }
			 
        }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
    }
	
	 public function trackGetBookmark($arr){
       //echo "<pre>";print_r($arr);//exit;
          try{	
				$token =$arr['token'];
				$package_code =$arr['package_code'];
				$course_code=$arr['course_code'];
				
				$serviceObj = new serviceController();
	         
			    $params = new stdClass();
				$params->package_code = $package_code;
				$params->course_code = $course_code;
				$params->client = CLIENT_NAME;// $client name;
				$params->class_name = CLIENT_NAME;// $client name;
				$params->platform = WEB_SERVICE_PLATFORM;
				$params->deviceId = WEB_SERVICE_DEVICE_ID;
				$params->appVersion = WEB_SERVICE_APP_VERSION;
				
		       // echo "<pre>";print_r($params);//exit;
				$res = $serviceObj->processRequest($token, 'get_user_bookmark', $params);
				$res_json = json_encode($res);
				$res = json_decode($res_json, true);
				//echo "<pre>";print_r($res);	
			  if(strcasecmp($res['retCode'], 'SUCCESS') == 0 && count($res['retVal']) ){
					$retValArr = $res['retVal'];

					$bookmarkArr=array();
					  if( isset($retValArr)){
						  foreach($retValArr as $retVal){
							// echo print_r($retVal);
						   $bookmarkObj = new stdClass();
						   $bookmarkObj->package_code= $retVal['license_key'];
						   $bookmarkObj->course_code= $retVal['course_code'];
						   $bookmarkObj->topic_edge_id = $retVal['topic_edge_id'];
						   $bookmarkObj->chapter_edge_id = $retVal['chapter_edge_id'];
						   $bookmarkObj->component_edge_id = $retVal['component_edge_id'];
						   $bookmarkObj->other = $retVal['other'];
						   $bookmarkObj->bookmark_type = $retVal['bookmark_type'];
						   
						   $bookmarkArr[]=$bookmarkObj; 
						
						 }
					   return $bookmarkArr;
					  } 
				}else{
				  return false;
			  }
			 
        }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
    }
		 
    public function setVisitLevel($arr){
        //echo "<pre>";print_r($arr);//exit;
          try{	
				$token =$arr['token'];
				$product_id=$arr['product_id'];
				$visiting_level=$arr['visiting_level'];
				$serviceObj = new serviceController();
	         
			    $params = new stdClass();
				$params->product_id=$product_id;
				$params->visiting_level=$visiting_level;
				$params->client = CLIENT_NAME;// $client name;
				$params->class_name = CLIENT_NAME;// $client name;
				$params->platform = WEB_SERVICE_PLATFORM;
				$params->deviceId = WEB_SERVICE_DEVICE_ID;
				$params->appVersion = WEB_SERVICE_APP_VERSION;
				
		       // echo "<pre>";print_r($params);//exit;
				$res = $serviceObj->processRequest($token, 'update_visiting_level', $params);
				$res_json = json_encode($res);
				$res = json_decode($res_json, true);
				//echo "<pre>";print_r($res);	
			  if(strcasecmp($res['retCode'], 'SUCCESS') == 0 && count($res['retVal']) ){
					$retValArr = $res['retVal'];
                    return $retValArr;
				}else{
				  return false;
			  }
			  
        }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
    }
	
	public function getVisitLevel($arr){
          try{	
				$token =$arr['token'];
				$product_id=$arr['product_id'];
				$serviceObj = new serviceController();
	         
			    $params = new stdClass();
				$params->product_id = $product_id;
				$params->client = CLIENT_NAME;// $client name;
				$params->class_name = CLIENT_NAME;// $client name;
				$params->platform = WEB_SERVICE_PLATFORM;
				$params->deviceId = WEB_SERVICE_DEVICE_ID;
				$params->appVersion = WEB_SERVICE_APP_VERSION;
				//echo "<pre>";print_r($params);		
				$res = $serviceObj->processRequest($token, 'get_visiting_level', $params);
				$res_json = json_encode($res);
				$res = json_decode($res_json, true);
				//echo "<pre>";print_r($res);	
			  if(strcasecmp($res['retCode'], 'SUCCESS') == 0 && count($res['retVal']) ){
					$retValArr = $res['retVal'];
                    return $retValArr;
				}else{
				  return false;
			  }
			  
        }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
    }
	
	 	public function trackGame($arr){
        // echo "<pre>";print_r($arr);exit;
          try{	
				$cEdgeID=$arr['cEdgeID'];
				$gameEdgeId=$arr['game_edge_id'];
				$token =$arr['token'];
				$package_code =$arr['package_code'];
				$course_code=$arr['course_code'];
				$st = $arr['start_date_ms'];
				$et = $arr['end_date_ms'];
				
				$paramArr=array();
				$serviceObj = new serviceController();
				if( is_numeric($cEdgeID) && is_numeric($st) && is_numeric($et) ){
					$params1 = new stdClass();
					$params1->edge_id = $cEdgeID;
					$params1->start_date_ms = $st;
					$params1->end_date_ms = $et;
					$params1->package_code = $package_code;
					$params1->course_code = $course_code;
					
					$params1->client = CLIENT_NAME;// $client name;
					$params1->class_name = CLIENT_NAME;// $client name;
					$params1->platform = WEB_SERVICE_PLATFORM;
					$params1->deviceId = WEB_SERVICE_DEVICE_ID;
					$params1->appVersion = WEB_SERVICE_APP_VERSION;
					array_push($paramArr,$params1);
				 } 
				if( is_numeric($gameEdgeId) && is_numeric($st) && is_numeric($et) ){
					$params2 = new stdClass();				
					$params2->edge_id = $gameEdgeId;
					$params2->start_date_ms = $st;
					$params2->end_date_ms = $et;
					$params2->package_code = $package_code;
					$params2->course_code = $course_code;
					
					$params2->client = CLIENT_NAME;// $client name;
					$params2->class_name = CLIENT_NAME;// $client name;
					$params2->platform = WEB_SERVICE_PLATFORM;
					$params2->deviceId = WEB_SERVICE_DEVICE_ID;
					$params2->appVersion = WEB_SERVICE_APP_VERSION;
					array_push($paramArr,$params2); 
			   } 
				// echo "<pre>";print_r($paramArr);exit;
				$res = $serviceObj->processRequest($token, 'track', $paramArr);
				$res_json = json_encode($res);
				$res = json_decode($res_json, true);
				//echo "<pre>";print_r($res);exit;
				if(strcasecmp($res['retCode'], 'SUCCESS') == 0){
					//$retValArr = $res['retVal'];
					//echo "<pre>";print_r($res);	 
                    return 1;
				}else{
				  return false;
			  }
			  
				
        }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
    }
	
	public function trackResource($arr){
         //echo "<pre>";print_r($arr);exit;
          try{	
				$cEdgeID=$arr['cEdgeID'];
				$resourceEdgeId=$arr['resource_edge_id'];
				$token =$arr['token'];
				$package_code =$arr['package_code'];
				$course_code=$arr['course_code'];
				$st = $arr['start_date_ms'];
				$et = $arr['end_date_ms'];
				
				$paramArr=array();
				$serviceObj = new serviceController();
				if( is_numeric($cEdgeID) && is_numeric($st) && is_numeric($et) ){
					$params1 = new stdClass();
					$params1->edge_id = $cEdgeID;
					$params1->start_date_ms = $st;
					$params1->end_date_ms = $et;
					$params1->package_code = $package_code;
					$params1->course_code = $course_code;
					
					$params1->client = CLIENT_NAME;// $client name;
					$params1->class_name = CLIENT_NAME;// $client name;
					$params1->platform = WEB_SERVICE_PLATFORM;
					$params1->deviceId = WEB_SERVICE_DEVICE_ID;
					$params1->appVersion = WEB_SERVICE_APP_VERSION;
					array_push($paramArr,$params1);
				 } 
				if( is_numeric($resourceEdgeId) && is_numeric($st) && is_numeric($et) ){
					$params2 = new stdClass();				
					$params2->edge_id = $resourceEdgeId;
					$params2->start_date_ms = $st;
					$params2->end_date_ms = $et;
					$params2->package_code = $package_code;
					$params2->course_code = $course_code;
					
					$params2->client = CLIENT_NAME;// $client name;
					$params2->class_name = CLIENT_NAME;// $client name;
					$params2->platform = WEB_SERVICE_PLATFORM;
					$params2->deviceId = WEB_SERVICE_DEVICE_ID;
					$params2->appVersion = WEB_SERVICE_APP_VERSION;
					array_push($paramArr,$params2); 
			   } 
				//echo "<pre>";print_r($paramArr);exit;
				$res = $serviceObj->processRequest($token, 'track', $paramArr);
				$res_json = json_encode($res);
				$res = json_decode($res_json, true);
				//echo "<pre>";print_r($res);

        }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
    }
	

public function trackILTChapter($arrChapter){
	  // echo "<pre>";print_r($arrChapter);//exit;
          try{	
				$topic_edge_id=$arrChapter['topic_edge_id'];
		        $chapterEdgeId =$arrChapter['chapter_edge_id'];
			    $token =$arrChapter['userToken'];
				$package_code =$arrChapter['package_code'];
				$course_code=$arrChapter['course_code'];
				$batch_id=$arrChapter['batch_id'];
				$st = $arrChapter['start_date_ms'];
			    $et = $arrChapter['end_date_ms'];
				$completion = $arrChapter['completion'];
				
				// $paramArr=array();
				 $serviceObj = new serviceController();
					if( is_numeric($chapterEdgeId)){
						$params = new stdClass();
						$params->edge_id = $chapterEdgeId;
						$params->batch_id  = $batch_id ;
						$params->start_date_ms = $st;
					    $params->end_date_ms = $et;
						$params->package_code = $package_code;
						$params->course_code = $course_code;
						$params->topic_edge_id = $topic_edge_id;
						$params->completion = $completion;
						
						
						
						$params->client = CLIENT_NAME;// $client name;
						$params->class_name = CLIENT_NAME;// $client name;
						$params->platform = WEB_SERVICE_PLATFORM;
						$params->deviceId = WEB_SERVICE_DEVICE_ID;
						$params->appVersion = WEB_SERVICE_APP_VERSION;
						//array_push($paramArr,$params);
						
					 } 
					// echo "<pre>";print_r($params);//exit;
					$res = $serviceObj->processRequest($token, 'syncComponentCompletionIlt', $params);
					$res_json = json_encode($res);
					$res = json_decode($res_json, true);
					//echo "<pre>";print_r($res);
				
				  if(strcasecmp($res['retCode'], 'SUCCESS') == 0 && count($res['retVal']) ){
					return true; 
				  }else{
					return false;
				  } 
		 }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}		
 }	 
 public function trackRolePlayReview($arr){
       
          try{
			$token=$arr['token'];	
		  	$roleplayEdgeId=$arr['rp_edge_id'];
            
			$serviceObj = new serviceController();
           	
			if( is_numeric($roleplayEdgeId) ){
					$params = new stdClass();		
					$params->rp_edge_id = $roleplayEdgeId;

					$params->client = CLIENT_NAME;// $client name;
					$params->class_name = CLIENT_NAME;// $client name;
					$params->platform = WEB_SERVICE_PLATFORM;
					$params->deviceId = WEB_SERVICE_DEVICE_ID;
					$params->appVersion = WEB_SERVICE_APP_VERSION;
					
			   } 
			     //echo "<pre>";print_r($params);//exit;
				$res = $serviceObj->processRequest($token, 'getUserRecordingReview', $params );
				$res_json = json_encode($res);
				$res = json_decode($res_json, true);
				//echo "<pre>";print_r($res);
				 if(strcasecmp($res['retCode'], 'SUCCESS') == 0 && count($res['retVal']) ){
					return $res['retVal']; 
				  }else{
					return false;
				  } 

        }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
    } 	

 public function trackRolePlayAnalyse($arr){
       
          try{
			$token=$arr['token'];	
		  	$roleplayEdgeId=$arr['rp_edge_id'];
            
			$serviceObj = new serviceController();
           	
			if( is_numeric($roleplayEdgeId) ){
					$params = new stdClass();		
					$params->rp_edge_id = $roleplayEdgeId;

					$params->client = CLIENT_NAME;// $client name;
					$params->class_name = CLIENT_NAME;// $client name;
					$params->platform = WEB_SERVICE_PLATFORM;
					$params->deviceId = WEB_SERVICE_DEVICE_ID;
					$params->appVersion = WEB_SERVICE_APP_VERSION;
					
			   } 
			     //echo "<pre>";print_r($params);//exit;
				$res = $serviceObj->processRequest($token, 'getUserRecordingResponse', $params );
				$res_json = json_encode($res);
				$res = json_decode($res_json, true);
				//echo "<pre>";print_r($res);
				 if(strcasecmp($res['retCode'], 'SUCCESS') == 0 && count($res['retVal']) ){
					return $res['retVal']; 
				  }else{
					return false;
				  } 

        }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
    } 	
	
}