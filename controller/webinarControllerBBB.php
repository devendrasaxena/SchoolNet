<?php
//error_reporting(E_ALL);
//ini_set('display_errors',1);


require_once __DIR__ . '/serviceController.php';

function insert($table, $data) {
		$con = DBConnection::createConn();

        ksort($data);
        $fieldNames = implode('`, `', array_keys($data));
        $fieldValues = ':' . implode(', :', array_keys($data));
        $sth = $con->prepare("INSERT INTO `$table` (`$fieldNames`) VALUES ($fieldValues)");

        foreach ($data as $key => $value) {
            $sth->bindValue(":$key", $value);
        }
       print_r( $data);
        $s = $sth->execute();
        return $s;
    }


function getWebinarBBBListByClient($client_id){
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


function getWebinarBBBList($client_id,$center_id){
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
  
 function getWebinarBBBListByUserId($client_id,$center_id,$userId){
 
		try{	
			$con = createConnection();
			$stmt = $con->prepare("SELECT id,class_id, provider_id,teacher_user_id, event_date, event_timezone, duration_minutes, num_seats_total, num_seats_avail, title, trainer_url,recording_url FROM live_class_aduro_bbb WHERE client_id=? AND center_id=? AND teacher_user_id=? order by id DESC");
			$stmt->bind_param("iii",$client_id,$center_id,$userId);
			$stmt->execute();
			$stmt->bind_result($id,$class_id,$provider_id,$teacher_user_id,$event_date,$event_timezone, $duration_minutes,$num_seats_total,$num_seats_avail,$title,$trainer_url,$recording_url);
			
			$webList = array();
			while($stmt->fetch()) {
				$bcm = new stdClass();
				$bcm->id = $id;
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


 function getWebinarBBBListByEventId($id){
 
		try{	
			$con = createConnection();
			$stmt = $con->prepare("SELECT class_id, provider_id,teacher_user_id, event_date, event_timezone, duration_minutes, num_seats_total, num_seats_avail, title, trainer_url,recording_url,batch_code,description FROM live_class_aduro_bbb WHERE id=?");
			$stmt->bind_param("i",$id);
			$stmt->execute();
			$stmt->bind_result($class_id,$provider_id,$teacher_user_id,$event_date,$event_timezone, $duration_minutes,$num_seats_total,$num_seats_avail,$title,$trainer_url,$recording_url,$batch_code,$description);
			$stmt->fetch();
			
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
				$bcm->batch_code = $batch_code;
				$bcm->description = $description;
			
			$stmt->close();
			
			return $bcm;
			
	 }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}	
  } 





function addWebinarBBB($course,$topic,$chapter,$title,$description,$txtDate,$txtSetTime,$duration,$textMaxSeat,$event_timezone,$trainer_url,$txtBatch,$meetingID) {
	
	try{	
		$con = createConnection();

		$client_id = $_SESSION['client_id'];
		$user_id = $_SESSION['user_id'];
		$center_id = $_SESSION['center_id'];
		$class_id = $_POST['class_id'];
		
        
        $data = array(
        	'class_id'=>$class_id,
        	'client_id'=>$client_id,
        	'provider_id'=>$meetingID,
        	'teacher_user_id'=>$user_id,
        	'event_timezone'=>$event_timezone,
        	'event_date'=>date('Y-m-d H:i:s',strtotime($txtDate.' '.$txtSetTime)),
        	'duration_minutes'=>$duration,
        	'topic_id'=>$topic,
        	'user_group_id'=>0,
        	'num_seats_total'=>$textMaxSeat,
        	'num_seats_avail'=>$textMaxSeat,
        	'title'=>$title,
        	'description'=>$description,
        	'recording_url'=>'',
        	'trainer_url'=>$trainer_url,
        	'join_url'=>'',
        	'center_id'=>$center_id,
        	'batch_code'=>$txtBatch

        );
		insert('live_class_aduro_bbb', $data);

	
	}//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
		
	}

function viewWebinarByIdBBB($web_id){
	try{	
		 $con = createConnection();
		//$stmt = $con->prepare("SELECT provider_id, teacher_user_id, event_date, duration_minutes,num_seats_total, num_seats_avail,title,description,recording_url,trainer_url,course_code,course_edge_id,topic_edge_id,chapter_edge_id FROM live_class_aduro WHERE class_id=?");
		$stmt = $con->prepare("SELECT provider_id, teacher_user_id, event_timezone, event_date, duration_minutes,num_seats_total, num_seats_avail,title,description,recording_url,trainer_url,center_id,batch_code FROM live_class_aduro_bbb WHERE class_id=?");
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



function getEventListBBB($userToken,$client_id,$center_id){
		
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
				  $eventObj->join_url= $retVal['join_url'];
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
function joinEventBBB($userToken,$client_id,$center_id,$class_id){
		
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

function cancelEventBBB($arr){
		
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