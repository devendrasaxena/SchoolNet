<?php
//error_reporting(E_ALL);
//ini_set('display_errors',1);
require_once __DIR__ . '/serviceController.php';

function getWebinarListByClient($client_id){
		try{	
			$con = createConnection();
			$stmt = $con->prepare("SELECT class_id, provider_id, teacher_user_id, event_date, duration_minutes, num_seats_total, num_seats_avail, title, trainer_url,recording_url FROM live_class_aduro WHERE client_id=? order by event_date desc");
			$stmt->bind_param("i",$client_id);
			$stmt->execute();
			//echo "<pre>";print_r($stmt);//exit;
			$stmt->bind_result($class_id,$provider_id,$teacher_user_id, $event_date,$duration_minutes,$num_seats_total,$num_seats_avail,$title,$trainer_url,$recording_url);
			
			$webList = array();
			while($stmt->fetch()) {
				$bcm = new stdClass();
				$bcm->class_id = $class_id;
				$bcm->provider_id = $provider_id;
				$bcm->teacher_user_id = $teacher_user_id;
				$bcm->event_date = $event_date;
				$bcm->duration_minutes = $duration_minutes;
				$bcm->num_seats_total = $num_seats_total;
				$bcm->num_seats_avail = $num_seats_avail;
				$bcm->title = $title;
				$bcm->recording_url = $recording_url;
				$bcm->trainer_url = $trainer_url;
				//$bcm->course_code = $course_code;
				array_push($webList,$bcm);
			}
			$stmt->close();
			
			return $webList;
			
	 }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}	
  }


function getWebinarList($client_id,$center_id){
		try{	
			$con = createConnection();
			$stmt = $con->prepare("SELECT class_id, provider_id,teacher_user_id, event_date, event_timezone, duration_minutes, num_seats_total, num_seats_avail, title, trainer_url,recording_url FROM live_class_aduro WHERE client_id=? AND center_id=? order by event_date desc");
			$stmt->bind_param("ii",$client_id,$center_id);
			$stmt->execute();
			$stmt->bind_result($class_id,$provider_id,$teacher_user_id,$event_date,$event_timezone, $duration_minutes,$num_seats_total,$num_seats_avail,$title,$trainer_url,$recording_url);
			
			$webList = array();
			while($stmt->fetch()) {
				$bcm = new stdClass();
				$bcm->class_id = $class_id;
				$bcm->provider_id = $provider_id;
				$bcm->teacher_user_id = $teacher_user_id;
				$bcm->event_date = $event_date;
				$bcm->event_timezone = $event_timezone;
				$bcm->duration_minutes = $duration_minutes;
				$bcm->num_seats_total = $num_seats_total;
				$bcm->num_seats_avail = $num_seats_avail;
				$bcm->title = $title;
				$bcm->recording_url = $recording_url;
				$bcm->trainer_url = $trainer_url;
				//$bcm->course_code = $course_code;
				array_push($webList,$bcm);
			}
			$stmt->close();
			
			return $webList;
			
	 }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}	
  }
  
 function getWebinarListByUserId($client_id,$center_id,$userId){
		try{	
			$con = createConnection();
			$stmt = $con->prepare("SELECT class_id, provider_id,teacher_user_id, event_date, event_timezone, duration_minutes, num_seats_total, num_seats_avail, title, trainer_url,recording_url FROM live_class_aduro WHERE client_id=? AND center_id=? AND teacher_user_id=? order by event_date desc");
			$stmt->bind_param("iii",$client_id,$center_id,$userId);
			$stmt->execute();
			$stmt->bind_result($class_id,$provider_id,$teacher_user_id,$event_date,$event_timezone, $duration_minutes,$num_seats_total,$num_seats_avail,$title,$trainer_url,$recording_url);
			
			$webList = array();
			while($stmt->fetch()) {
				$bcm = new stdClass();
				$bcm->class_id = $class_id;
				$bcm->provider_id = $provider_id;
				$bcm->teacher_user_id = $teacher_user_id;
				$bcm->event_date = $event_date;
				$bcm->event_timezone = $event_timezone;
				$bcm->duration_minutes = $duration_minutes;
				$bcm->num_seats_total = $num_seats_total;
				$bcm->num_seats_avail = $num_seats_avail;
				$bcm->title = $title;
				$bcm->recording_url = $recording_url;
				$bcm->trainer_url = $trainer_url;
				//$bcm->course_code = $course_code;
				array_push($webList,$bcm);
			}
			$stmt->close();
			
			return $webList;
			
	 }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}	
  } 

function getClientCourseListToChoose(){
	 try{	
		 $con = createConnection();
		 $stmt = $con->prepare("SELECT c.title, c.code, gmt.edge_id FROM course c 
								JOIN generic_mpre_tree gmt ON gmt.tree_node_id = c.tree_node_id
								ORDER BY modified_date DESC");
		$stmt->execute();
		$stmt->bind_result($title, $code, $edge_id);
		$course = array();
		while($stmt->fetch()) {
			$bcm = new stdClass();
			$bcm->title = $title;
			$bcm->code = $code;
			$bcm->edge_id = $edge_id;
			array_push($course,$bcm);
		}
	   return $course;
	  	
	}//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
}

function getTeacherList(){
	try{	
		 $con = createConnection();
		$stmt = $con->prepare("SELECT id, teacher_email, teacher_name FROM live_class_teacher 
								where teacher_status='active'
								ORDER BY teacher_name DESC");
								
		$stmt->execute();
		$stmt->bind_result($id, $teacher_email, $teacher_name);
		$teachers = array();
		while($stmt->fetch()) {
			$bcm = new stdClass();
			$bcm->id = $id;
			$bcm->teacher_email = $teacher_email;
			$bcm->teacher_name = $teacher_name;
			//$bcm->edge_id = $edge_id;
			array_push($teachers,$bcm);
		}
	
	  return $teachers;
	
	}//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
}

function addWebinar($course,$topic,$chapter,$title,$description,$txtDate,$txtSetTime,$duration,$textMaxSeat) {
	
	try{	
		$con = createConnection();
		$user = userdetails($_SESSION['user_id']);
		$client_id = $_SESSION['user_id'];
		//$client_id = $user->client_id;
		
		return $event_id;
	}//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
		
	}

function viewWebinarById($web_id){
	try{	
		 $con = createConnection();
		//$stmt = $con->prepare("SELECT provider_id, teacher_user_id, event_date, duration_minutes,num_seats_total, num_seats_avail,title,description,recording_url,trainer_url,course_code,course_edge_id,topic_edge_id,chapter_edge_id FROM live_class_aduro WHERE class_id=?");
		$stmt = $con->prepare("SELECT provider_id, teacher_user_id, event_timezone, event_date, duration_minutes,num_seats_total, num_seats_avail,title,description,recording_url,trainer_url,center_id,batch_code FROM live_class_aduro WHERE class_id=?");
		$stmt->bind_param("i",$web_id);
		$stmt->bind_result($provider_id,$teacher_user_id,$event_timezone,$event_date,$duration_minutes,$num_seats_total,$num_seats_avail,$title,$description,$recording_url,$trainer_url,$center_id,$batch_code);
		//$stmt->bind_result($provider_id,$teacher_user_id,$event_date,$duration_minutes,$num_seats_total,$num_seats_avail,$title,$description,$recording_url,$trainer_url,$course_code,$course_edge_id,$topic_edge_id,$chapter_edge_id);
	
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		$obj = new stdclass();
		$obj->provider_id = $provider_id;
		$obj->teacher_user_id = $teacher_user_id;
		$obj->event_timezone = $event_timezone;
		$obj->event_date = $event_date;
		$obj->duration_minutes = $duration_minutes;
		$obj->num_seats_total = $num_seats_total;
		$obj->num_seats_avail = $num_seats_avail;
		$obj->title = $title;
		$obj->description = $description;
		$obj->recording_url = $recording_url;
		$obj->trainer_url = $trainer_url;
		$obj->center_id = $center_id;
		$obj->batch_code = $batch_code;
		//$obj->course_code = $course_code;
		//$obj->course_edge_id = $course_edge_id;
		//$obj->topic_edge_id = $topic_edge_id;
		//$obj->chapter_edge_id = $chapter_edge_id;
		return $obj;
   }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
		
}

function getTeacherName($teacher_id){
		$con = createConnection();
		$stmt = $con->prepare("SELECT teacher_name FROM live_class_teacher WHERE id=?");
		$stmt->bind_param("i",$teacher_id);
		$stmt->bind_result($teacher_name);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		$obj = new stdclass();
		$obj->teacher_name = $teacher_name;
		return $obj;	
	}

function getEventList($userToken,$client_id,$center_id){
		
     try{	
			$serviceObj = new serviceController();
			$params = new stdClass();
			$params->client_id = $client_id;
			$params->center_id = $center_id;
			$params->client = CLIENT_NAME;// $client name;
			$params->class_name = CLIENT_NAME;// $client name;
			$params->platform = WEB_SERVICE_PLATFORM;
			$params->deviceId = WEB_SERVICE_DEVICE_ID;
			$params->appVersion = WEB_SERVICE_APP_VERSION;
			
			$extra=array();
			$extra['client'] = CLIENT_NAME;// $client name;
			$extra['class_name'] = CLIENT_NAME;// $client name;
			$extra['platform']= WEB_SERVICE_PLATFORM;
			$extra['deviceId'] = WEB_SERVICE_DEVICE_ID;
			$extra['appVersion'] = WEB_SERVICE_APP_VERSION;
			
			//echo "<pre>";print_r($params);//exit;
			$res = $serviceObj->processRequest($userToken, 'eventlist', $params);
			$res_json = json_encode($res);
			$res = json_decode($res_json, true);
			//echo "<pre>";print_r($res);//exit;
			if(strcasecmp($res['retCode'], 'SUCCESS') == 0 && count($res['retVal']) ){
				$eventArr=array();
				 $retArr = $res['retVal'];
				  //echo "<pre>";print_r($retArr);
				foreach($retArr as $retVal){
					 //echo "<pre>";print_r($retVal['class_id']);
				  $eventObj = new stdClass();
				  $eventObj->class_id= $retVal['class_id'];
				  $eventObj->provider_id= $retVal['provider_id'];
				  $eventObj->title= $retVal['title'];
				  $eventObj->description= $retVal['description'];
				  $eventObj->teacher_name= $retVal['teacher_name'];
				  $eventObj->teacher_image= $retVal['teacher_image'];
				  $eventObj->newDate= $retVal['newDate'];
				  $eventObj->newTime= $retVal['newTime'];
				  $eventObj->duration= $retVal['duration'];
				  $eventObj->isRecording= $retVal['isRecording'];
				  $eventObj->bookurl= $retVal['bookurl'];
				  $eventObj->num_total= $retVal['num_total'];
				  $eventObj->num_avail= $retVal['num_avail'];
				  $eventObj->isAvailable= $retVal['isAvailable'];
				  $eventObj->recording_url= $retVal['recording_url'];
				  $eventObj->timezone= $retVal['timezone'];
				  $eventObj->is_booked= $retVal['is_booked']; 
				  $eventArr[]=$eventObj; 
				} 
			  //echo "<pre>";print_r($eventArr);exit;
			  return $eventArr;
			}
			return false;	
			
		  
	 }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
}
function joinEvent($userToken,$client_id,$center_id,$class_id){
		
     try{	
			$serviceObj = new serviceController();
			$params = new stdClass();
			$params->class_id = $class_id;
			$params->client_id = $client_id;
			$params->center_id = $center_id;
			$params->client = CLIENT_NAME;// $client name;
			$params->class_name = CLIENT_NAME;// $client name;
			$params->platform = WEB_SERVICE_PLATFORM;
			$params->deviceId = WEB_SERVICE_DEVICE_ID;
			$params->appVersion = WEB_SERVICE_APP_VERSION;
			
			$extra=array();
			$extra['client'] = CLIENT_NAME;// $client name;
			$extra['class_name'] = CLIENT_NAME;// $client name;
			$extra['platform']= WEB_SERVICE_PLATFORM;
			$extra['deviceId'] = WEB_SERVICE_DEVICE_ID;
			$extra['appVersion'] = WEB_SERVICE_APP_VERSION;
			
			//echo "<pre>";print_r($params);//exit;
			$res = $serviceObj->processRequest($userToken, 'joinevent', $params);
			$res_json = json_encode($res);
			$res = json_decode($res_json, true);
			//echo "<pre>";print_r($res);exit;
			if(strcasecmp($res['retCode'], 'SUCCESS') == 0 && count($res['retVal']) ){
				$eventArr=array();
				 $retArr = $res['retVal'];
				  //echo "<pre>";print_r($retArr);
				foreach($retArr as $retVal){
					 //echo "<pre>";print_r($retVal['class_id']);
				  $eventObj = new stdClass();
				  
				} 
			//echo "<pre>";print_r($eventArr);exit;
			  return $eventArr;
			}
			return false;	
			
		  
	 }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
}

function cancelEvent($arr){
		
     try{	
			$serviceObj = new serviceController();
			
			$token =$arr['token'];
			$class_id =$arr['class_id'];
			$client_id =$arr['client_id'];
			$center_id =$arr['center_id'];
			
			$params = new stdClass();
			$params->class_id = $class_id;
			$params->client_id = $client_id;
			$params->center_id = $center_id;
			$params->client = CLIENT_NAME;// $client name;
			$params->class_name = CLIENT_NAME;// $client name;
			$params->platform = WEB_SERVICE_PLATFORM;
			$params->deviceId = WEB_SERVICE_DEVICE_ID;
			$params->appVersion = WEB_SERVICE_APP_VERSION;
			
			//echo "<pre>";print_r($params);//exit;
			$res = $serviceObj->processRequest($token, 'cancelevent', $params);
			$res_json = json_encode($res);
			$res = json_decode($res_json, true);
			//echo "<pre>";print_r($res);exit;
			 if(strcasecmp($res['retCode'], 'SUCCESS') == 0 && count($res['retVal']) ){
				//return $res['retCode'];
				  $eventObj = new stdClass();
				  $retVal=$res['retVal'];
				  $eventObj->status= $retVal['status'];
				
				 return $eventObj;
				
			}
			return false;	 
			
		  
	 }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
}
 
	
?>