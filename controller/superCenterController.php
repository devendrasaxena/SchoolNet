<?php
class centerController {

    public $dbConn;
    public function __construct() {
		$this->dbConn = DBConnection::createConn();
		
    }
	
public function getLastCenter(){
     $sql="SELECT COUNT(*) center_id FROM tblx_center";
	 $stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->fetch();
		$stmt->closeCursor();
	//echo "<pre>";print_r($RESULT);exit;
	 return $RESULT;
  }
  public function getCenterLicense($client_id,$license){
   
	 $sql = "Select * from tblx_center where license_key=:license";
      //update Center Online live server Database
	 $stmt = $this->dbConn->prepare($sql);
	 $stmt->bindValue(':license', $license, PDO::PARAM_INT);	 
	 $stmt->execute();
	 $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
	 $stmt->closeCursor();
	//echo "<pre>";print_r($RESULT);exit;
	 return $RESULT;
  }
  
   public function getCenterByClient($client_id,$cond_arr = array()){ 
		
		if($cond_arr['region_id']!=""){
			$whr = " AND tr.id = '".$cond_arr['region_id']."' ";
		}
		
		// $sql = "Select tc.* from tblx_center AS tc LEFT JOIN tblx_region_country_map AS trcm ON tc.country=trcm.country_name where tc.client_id = '$client_id' $whr and tc.status=1 group by tc.center_id order by tc.center_id DESC";
		$sql = "Select tc.* from tblx_center AS tc LEFT JOIN tblx_region AS tr ON tc.region=tr.id where tc.client_id = :client_id $whr and tc.status=1 group by tc.center_id order by tc.center_type,tc.name";
		
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);	 		
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		
		if(count($RESULT) > 0 ){
				return $RESULT;
			}else{
				return false;
			} 
    
	}

   
    public function getCenterListByClient($cond_arr = array(), $start = 0, $limit = 10,$order,$dir){
 
		$whr="where 1=1 and tc.status='1'";
		 
		if($cond_arr['client_id']!=""){
		$whr.= " AND tc.client_id = '".$cond_arr['client_id']."'";
		}

		if($cond_arr['country']!="" && $cond_arr['country']!='All' && $cond_arr['center_id']!='0'){
		$whr.= " AND tc.country = '".$cond_arr['country']."'";
		}

		if($cond_arr['center_id']!="" && $cond_arr['center_id']!='All' && $cond_arr['center_id']!='0'){
		$whr.= " AND tc.center_id = '".$cond_arr['center_id']."'";
		}
		
		if($cond_arr['region_id']!="" && $cond_arr['region_id']!='All' && $cond_arr['region_id']!='0'){
			$whr.= " AND tr.id = '".$cond_arr['region_id']."' ";
		}
		if($cond_arr['center_txt']!=""){
			$whr.= " AND tc.name LIKE '%".$cond_arr['center_txt']."%' ";
		}
		
		 $sql = "Select count(DISTINCT tc.center_id) as 'cnt' from tblx_center AS tc LEFT JOIN tblx_region AS tr ON tc.region=tr.id $whr";
		$stmt = $this->dbConn->prepare($sql);	
		$stmt->execute();
		$RESULT_CNT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$row_cnt = array_shift( $RESULT_CNT );;
		


		$limit_sql = '';
		if( !empty($limit) ){
		$limit_sql .= " LIMIT $start, $limit";
		}
		
		
		$sql = "Select tc.* from tblx_center AS tc LEFT JOIN tblx_region AS tr ON tc.region=tr.id $whr group by tc.center_id ORDER BY ".$order." ".$dir." $limit_sql";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();

		return array('total' =>$row_cnt['cnt'] , 'result' => $RESULT);
   
   }
     
	 public function getCenterListCount($cond_arr = array()){
 
		$whr="where 1=1 and tc.license_key!='1EAA401523' and tc.status='1'";
		 
		if($cond_arr['client_id']!=""){
		$whr.= " AND tc.client_id = '".$cond_arr['client_id']."'";
		}

		if($cond_arr['country']!="" && $cond_arr['country']!='All' && $cond_arr['center_id']!='0'){
		$whr.= " AND tc.country = '".$cond_arr['country']."'";
		}

		if($cond_arr['center_id']!="" && $cond_arr['center_id']!='All' && $cond_arr['center_id']!='0'){
		$whr.= " AND tc.center_id = '".$cond_arr['center_id']."'";
		}
		
		if($cond_arr['region_id']!="" && $cond_arr['region_id']!='All' && $cond_arr['region_id']!='0'){
			$whr.= " AND trcm.region_id = '".$cond_arr['region_id']."' ";
		}
		
		 $sql = "Select count(DISTINCT tc.center_id) as 'cnt' from tblx_center AS tc LEFT JOIN tblx_region_country_map AS trcm ON tc.country=trcm.country_name $whr";
		$stmt = $this->dbConn->prepare($sql);	
		$stmt->execute();
		$RESULT_CNT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$row_cnt = array_shift( $RESULT_CNT );;
		


	

		return array('total' =>$row_cnt['cnt']);
   
   }
    
	public function getCenterListCountByRegion($cond_arr = array()){

		$whr="where 1=1 and tc.license_key!='1EAA401523' and tc.status='1'";
		 
		if($cond_arr['client_id']!=""){
		$whr.= " AND tc.client_id = '".$cond_arr['client_id']."'";
		}

		if($cond_arr['region_id']!="" && $cond_arr['region_id']!='All' && $cond_arr['region_id']!='0'){
		$whr.= " AND tc.region = '".$cond_arr['region_id']."'";
		}

		
		
		$sql = "Select count(DISTINCT tc.center_id) as 'cnt' from tblx_center AS tc $whr";
		$stmt = $this->dbConn->prepare($sql);	
		$stmt->execute();
		$RESULT_CNT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$row_cnt = array_shift( $RESULT_CNT );;
		


	

		return array('total' =>$row_cnt['cnt']);
   
   }
   
    public function getCenterOnline($center_id){
 
	$sql = "Select * from tblx_center where center_id = :center_id";
	//update Center Online live server Database
	$stmt = $this->dbConn->prepare($sql);
	$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);	 	
	$stmt->execute();
	$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt->closeCursor();
	//echo "<pre>";print_r($RESULT);exit;
	if($RESULT!="" && count($RESULT)>1){
		return $RESULT[0];
	}else if($RESULT!="" && count($RESULT)==1){

		return $RESULT[0];

	}else{
		return $RESULT;}
   }
   
 
 

 public function createCenterOnServer($res){
	 // echo "<pre>";print_r($res);exit;
	   $client_id=$res->client_id;
	   $email_id=$res->email_id;
	   $password=$res->password;
	   $license=$res->license_key;
	   $learning_mode=$res->learning_mode;
	  
	  $centerDetails = $this->getCenterLicense($client_id,$license);
	  $exitCenter= $centerDetails[0]['center_id'];
	 //echo "<pre>";print_r($exitCenter);exit;
	   $lastCenter = $this->getLastCenter();
	   $center_id=$lastCenter[0]['center_id'];
	//echo "<pre>";print_r($center_id);exit;
	
		//echo $exitCenter."==".$center_id;exit;
	try{
         $nextId =  $center_id + 1;			
         $code = "CN-".$nextId;		
		if($res->expiry_days !=0)
	    {
         $res->expiry_date=NULL;
		 
		}
		//echo "<pre>";print_r($code);exit;
		
		////Now Adding  Center in tblx center 
		//file_put_contents("new.txt",$res->license_key);
		
		
		$sql= "INSERT INTO tblx_center SET client_id = :client_id,
		client_name =:client_name, product =:product, license_issue_date = :license_issue_date,
		code = :code, license_key = :license_key, expiry_date = :expiry_date, expiry_days = :expiry_days,
		trainer_limit = :trainer_limit, student_limit = :student_limit, sync_days = :sync_days,
		name = :name,center_description = :center_description,description = :user_full_name
		,mac_address = :mac_address, mobile = :user_mobile, phone = :center_phone, address1 = :address,region =:region,country =:country, state =:state, city = :city, pincode =:postal_code, 
		email_id =:email_id, learning_mode =:learning_mode,password =:password,org_short_code =:shortcode,
		created_date = NOW()";
		
		
	    $stmt = $this->dbConn->prepare($sql); 
		$stmt->bindValue(':client_id', $res->client_id, PDO::PARAM_INT);
		$stmt->bindValue(':client_name', $res->client_name, PDO::PARAM_STR);
		$stmt->bindValue(':product', $res->product, PDO::PARAM_STR);
		$stmt->bindValue(':license_issue_date', $res->license_issue_date, PDO::PARAM_STR);
		$stmt->bindValue(':code', $code, PDO::PARAM_STR);	 	
		$stmt->bindValue(':license_key', $res->license_key, PDO::PARAM_STR);	 	
		$stmt->bindValue(':expiry_date', $res->expiry_date, PDO::PARAM_STR);
		$stmt->bindValue(':expiry_days', $res->expiry_days, PDO::PARAM_INT);
		$stmt->bindValue(':trainer_limit', $res->trainer_limit, PDO::PARAM_INT);
		$stmt->bindValue(':student_limit', $res->student_limit, PDO::PARAM_INT);
		$stmt->bindValue(':sync_days', $res->sync_days, PDO::PARAM_INT);	
		$stmt->bindValue(':name', $res->name, PDO::PARAM_STR);	
		$stmt->bindValue(':center_description', $res->center_description, PDO::PARAM_STR);	
		
		$stmt->bindValue(':user_full_name', $res->user_full_name, PDO::PARAM_STR);
		$stmt->bindValue(':mac_address', $res->mac_address, PDO::PARAM_STR);			
		$stmt->bindValue(':user_mobile', $res->user_mobile, PDO::PARAM_STR);			
		$stmt->bindValue(':center_phone', $res->center_phone, PDO::PARAM_STR);	
		$stmt->bindValue(':address', $res->address, PDO::PARAM_STR);	
		$stmt->bindValue(':region', $res->region, PDO::PARAM_INT);	
		$stmt->bindValue(':country', $res->country, PDO::PARAM_STR);
		$stmt->bindValue(':state', $res->state, PDO::PARAM_STR);	
		$stmt->bindValue(':city', $res->city, PDO::PARAM_STR);	
		$stmt->bindValue(':postal_code', $res->postal_code, PDO::PARAM_STR);
		$stmt->bindValue(':email_id', $email_id, PDO::PARAM_STR);	
		$stmt->bindValue(':learning_mode', $res->learning_mode, PDO::PARAM_STR);	
		$stmt->bindValue(':password', $res->password, PDO::PARAM_STR);	
		$stmt->bindValue(':shortcode', $res->shortcode, PDO::PARAM_STR);
		$stmt->execute();
		$center_id_new =$this->dbConn->lastInsertId();
		$stmt->closeCursor();  


		$nextId =  $center_id_new;			
        $code = "CN-".$center_id_new;
		$sql = "UPDATE tblx_center SET code = :code where center_id = :center_id_new";
		  $stmt = $this->dbConn->prepare($sql);	
         // $stmt->bindValue('center_id', $cid, PDO::PARAM_INT);
		  $stmt->bindValue(':code', $code, PDO::PARAM_STR);
		  $stmt->bindValue(':center_id_new', $center_id_new, PDO::PARAM_INT);
		  $stmt->execute();
		  $stmt->closeCursor();
		
		$sql = "UPDATE tbl_client_licenses SET license_status='4', license_used_by = :code,license_used_by_name = :name, license_used_by_email = :email_id, used_date = NOW(), issued_date = :license_issue_date where license_value = :license_key";
		  $stmt = $this->dbConn->prepare($sql);	
         // $stmt->bindValue('center_id', $cid, PDO::PARAM_INT);
		  $stmt->bindValue(':code', $code, PDO::PARAM_STR);
		  $stmt->bindValue(':name', $res->name, PDO::PARAM_STR);
		  $stmt->bindValue(':email_id', $email_id, PDO::PARAM_STR);
		  $stmt->bindValue(':license_issue_date', $res->license_issue_date, PDO::PARAM_STR);
		  $stmt->bindValue(':license_key', $res->license_key, PDO::PARAM_STR);		 
		  $stmt->execute();
		  $stmt->closeCursor();

		//// Now Adding  Admin address 
		$stmt = $this->dbConn->prepare("INSERT INTO address_master(address_line1, city, state, country, postal_code, phone, landline_no, updated_by,created_date) VALUES(:address,:city, :state,:country,:postal_code,:user_mobile,:center_phone,".$_SESSION['user_id'].",NOW())");
		$stmt->bindValue(':address', $res->address, PDO::PARAM_STR);
		$stmt->bindValue(':city', $res->city, PDO::PARAM_STR);
		$stmt->bindValue(':state', $res->state, PDO::PARAM_STR);
		$stmt->bindValue(':country', $res->country, PDO::PARAM_STR);
		$stmt->bindValue(':postal_code', $res->postal_code, PDO::PARAM_STR);
		$stmt->bindValue(':user_mobile', $res->user_mobile, PDO::PARAM_STR);
		$stmt->bindValue(':center_phone', $res->center_phone, PDO::PARAM_STR);
		
		$stmt->execute();
		$address_id =$this->dbConn->lastInsertId();
		$stmt->closeCursor(); 
         //echo "<pre>";print_r($address_id);exit;
		 
	      //// Now Adding  Assest 
		$stmt = $this->dbConn->prepare("INSERT INTO asset(updated_by,created_date) VALUES('1',NOW())");
		$stmt->execute();
		$asset_id = $this->dbConn->lastInsertId();
		$stmt->closeCursor(); 
		
		//// Now Adding  Admin Login 
		$stmt= $this->dbConn->prepare("insert into user(first_name,email_id,address_id,profile_pic,updated_by,created_date,user_client_id) values(:user_full_name,:email_id,:address_id,:asset_id,".$_SESSION['user_id'].", NOW(),:client_id)");
		//echo "<pre>";print_r($stmt);exit;
		$stmt->bindValue(':user_full_name', $res->user_full_name, PDO::PARAM_STR);
		$stmt->bindValue(':email_id', $email_id, PDO::PARAM_STR);
		$stmt->bindValue(':address_id', $address_id, PDO::PARAM_INT);
		$stmt->bindValue(':asset_id', $asset_id, PDO::PARAM_INT);
		$stmt->bindValue(':client_id', $res->client_id, PDO::PARAM_INT);
		$stmt->execute();
		$user_id =$this->dbConn->lastInsertId();
		$stmt->closeCursor(); 
		
	    //// Adding user and center map 
		$stmt = $this->dbConn->prepare("insert into user_center_map(user_id,center_id,client_id,created_date) values(:user_id,:nextId,:client_id,NOW())");
			//echo "<pre>";print_r($stmt);exit;
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->bindValue(':nextId', $nextId, PDO::PARAM_INT);
		$stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
		$stmt->execute();
		$stmt->closeCursor();
		

		
		//// Adding Admin Credentials 
		$stmt= $this->dbConn->prepare("insert into user_credential(user_id,loginid,password,updated_by,created_date) values(:user_id,:email_id,:password,1,NOW())");
	    //echo "<pre>";print_r($stmt);exit;
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->bindValue(':email_id', $email_id, PDO::PARAM_STR);
		$stmt->bindValue(':password', $password, PDO::PARAM_STR);
		$stmt->execute();
		$stmt->closeCursor(); 
		
		////Select the client to user group id */
		$stmt = $this->dbConn->prepare("Select user_group_id from client WHERE client_id=:client_id");
		$stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
			//echo "<pre>";print_r($stmt);exit;
		$stmt->execute();
	    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		//echo "<pre>";print_r($RESULT[0]['user_group_id']);exit;
		$client_group_id = $RESULT[0]['user_group_id'];

		
		//// Adding Admin into role map group 
		$role_type="4";//center Admin
		$stmt = $this->dbConn->prepare("insert into user_role_map(user_id,role_definition_id,user_group_id,is_active,updated_by,created_date) values(:user_id,:role_type,:client_group_id,1,1,NOW())");
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->bindValue(':role_type', $role_type, PDO::PARAM_INT);
		$stmt->bindValue(':client_group_id', $client_group_id, PDO::PARAM_INT);
			//echo "<pre>";print_r($stmt);exit;
		$stmt->execute();
		$stmt->closeCursor(); 
		
		
		$obj = new stdclass();
		$obj->address_id = $address_id;
		$obj->user_id = $user_id;
		$obj->user_group_id = $client_group_id;
		$obj->center_id = $nextId;
		$obj->client_id = $res->client_id;
		$obj->code = $code;
		$obj->license_key = $res->license_key;
		$obj->expiry_date = $res->expiry_date;
		$obj->expiry_days = $res->expiry_days;
		$obj->sync_days = $res->sync_days;
		$obj->trainer_limit = $res->trainer_limit;
		$obj->student_limit = $res->student_limit;
		$obj->name = $res->name;
		$obj->description = $res->user_full_name;
		$obj->email_id = $res->email_id;
		$obj->mobile = $res->user_mobile;
		$obj->phone = $res->center_phone;
		$obj->password = $res->password;
		$obj->learning_mode = $res->learning_mode;
        $obj->mac_address = $res->mac_address;
        $obj->address1 = $res->address;
		$obj->city = $res->city;
		$obj->state = $res->state;
		$obj->country = $res->country;
		$obj->pincode = $res->postal_code;

       // echo "<pre>";print_r($obj);exit;
		return $obj;
	  }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
  }

///////////////////function added to generate access codes////////
public function random_strings(){

    $length_of_string='6';
	$str_result = '123456789ABCDEFGHIJKLMNPQRSTUVWXYZ'; 
    return substr(str_shuffle($str_result), 0, $length_of_string); 
} 
///////////////////function added to generate access codes////////

///////////////////function added to short center codes////////
public function shortcode_strings(){
    $length_of_minstring='3';
    $length_of_string='8';
	$str_result = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz'; 
    return substr(str_shuffle(strtolower($str_result)), 0, $length_of_string);
} 
///////////////////function added to short center codes////////

///////////////////function added to check  existence of short codes////////
public function chk_exist_shortcode($org_short_code){
	
		$sql = "SELECT COUNT(*) as cnt FROM tblx_center where org_short_code =:org_short_code";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':org_short_code', $org_short_code, PDO::PARAM_STR);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->fetch();
		$stmt->closeCursor();
		$count_chk = $RESULT[0]['cnt'];
		if($count_chk=="" || $count_chk==0){
			return true;
		}
		return false;
} 
///////////////////function added to short center codes////////

  
public function getAllCenterList(){
 
	 $sql = "Select * from tblx_center";
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
  
 Public function getUserCenterDetail($center_id){
	   //// Select user id  to user-center-map 
	    $sql ="Select * from user_center_map WHERE center_id=:center_id";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		//echo "<pre>";print_r($RESULT);exit;
		$user_id = $RESULT[0]['user_id'];
		$client_id = $RESULT[0]['client_id'];

		//// Select address id to user 
	    $sql ="Select address_id,education,business_unit from user WHERE user_id=:user_id";
		$stmt= $this->dbConn->prepare($sql);
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT); 
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		//echo "<pre>";print_r($RESULT);exit;
		$qualification =$RESULT[0]['education'];
		$business_unit =$RESULT[0]['business_unit'];
	    $address_id =$RESULT[0]['address_id'];
	   
		//// Select user_group_id to client 
		$stmt = $this->dbConn->prepare("Select user_group_id from client WHERE client_id=:client_id");
		$stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
		$stmt->execute();
	    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
      // echo "<pre>";print_r($RESULT);exit;
		$user_group_id = $RESULT[0]['user_group_id'];
		
		
		$obj = new stdclass();
		$obj->address_id = $address_id;
		$obj->user_id = $user_id;
		$obj->user_group_id = $user_group_id;
		$obj->qualification = $qualification;
		$obj->business_unit = $business_unit;
		
         //echo "<pre>";print_r($obj);exit;
		return $obj;
	
 }	 
  
  public function updateCenterOnline($dataArr,$cid){
	     //echo "<pre>";print_r($dataArr);echo $cid;exit;
	      $user_id =$dataArr->user_id;
		  $address_id =$dataArr->address_id;
		  $user_group_id =$dataArr->user_group_id;
		  $email_id = $dataArr->email_id;
		  $shortcode = $dataArr->shortcode;
		  $password = $dataArr->password;
	 try{
		 
	     //// Update Center 
		//  $sql = "UPDATE tblx_center SET  name = '$dataArr->name',description = '$dataArr->user_full_name', mobile = '$dataArr->user_mobile', phone = '$dataArr->center_phone', address1 = '$dataArr->address', country = '$dataArr->country', state = '$dataArr->state', city = '$dataArr->city', pincode = '$dataArr->postal_code',email_id = '$dataArr->email_id', modified_date = NOW() where center_id = '$cid'";
		/* Devendra */
		  $sql = "UPDATE tblx_center SET  name = :name,description = :user_full_name, mobile = :user_mobile, phone = :center_phone, address1 = :address, region = :region,country =:country, state = :state, city = :city, pincode = :postal_code,learning_mode=:learning_mode, modified_date = NOW() where center_id = :center_id";
		  /* Devendra end*/
		  
		  $stmt = $this->dbConn->prepare($sql);	
          $stmt->bindValue(':center_id', $cid, PDO::PARAM_INT);
		  $stmt->bindValue(':name', $dataArr->name, PDO::PARAM_STR);
		  $stmt->bindValue(':user_full_name', $dataArr->user_full_name, PDO::PARAM_STR);
		  $stmt->bindValue(':user_mobile', $dataArr->user_mobile, PDO::PARAM_STR);
		  $stmt->bindValue(':center_phone', $dataArr->center_phone, PDO::PARAM_STR);
		  $stmt->bindValue(':address', $dataArr->address, PDO::PARAM_STR);
		  $stmt->bindValue(':region', $dataArr->region, PDO::PARAM_INT);
		  $stmt->bindValue(':country', $dataArr->country, PDO::PARAM_STR);
		  $stmt->bindValue(':state', $dataArr->state, PDO::PARAM_STR);
		  $stmt->bindValue(':city', $dataArr->city, PDO::PARAM_STR);
		  $stmt->bindValue(':postal_code', $dataArr->postal_code, PDO::PARAM_STR);
		  $stmt->bindValue(':learning_mode', $dataArr->learning_mode, PDO::PARAM_STR);
		  $stmt->execute();
		  $stmt->closeCursor();
		  
		  //Update for shortcode
		  if($shortcode!=""){
			$sql = "UPDATE tblx_center SET  org_short_code = :shortcode where center_id = :center_id";
			$stmt = $this->dbConn->prepare($sql);	
			$stmt->bindValue(':center_id', $cid, PDO::PARAM_INT);
			$stmt->bindValue(':shortcode', $shortcode, PDO::PARAM_STR);			
			$stmt->execute();
			$stmt->closeCursor();
		  }
		  
		  
		  
		  //// Update User
		     $sql = "UPDATE user SET first_name=:user_full_name, modified_date = NOW()  where user_id = '$user_id'";
			  $stmt = $this->dbConn->prepare($sql);
			  $stmt->bindValue(':user_full_name', $dataArr->user_full_name, PDO::PARAM_STR);
			  $stmt->execute();
			  $stmt->closeCursor();
			  
			//// update login Credentials 
		   if($password!=''){
				$stmt= $this->dbConn->prepare("UPDATE user_credential SET password=:password ,modified_date=NOW() WHERE user_id=:user_id");
			  // echo "<pre>";print_r($stmt);exit;
				 $stmt->bindValue(':password',$password, PDO::PARAM_STR);
				 $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
				 $stmt->execute();
				 $stmt->closeCursor();
		   }
			  
		     //// Update Address, Phone
		   $sql = "UPDATE address_master SET phone = :user_mobile, landline_no = :center_phone, address_line1 = :address, country = :country, state = :state, city = :city, postal_code = :postal_code, modified_date = NOW()  where address_id = :address_id";
			  $stmt = $this->dbConn->prepare($sql);	
			  $stmt->bindValue(':user_mobile', $dataArr->user_mobile, PDO::PARAM_STR);
			  $stmt->bindValue(':center_phone', $dataArr->center_phone, PDO::PARAM_STR);
			  $stmt->bindValue(':address', $dataArr->address, PDO::PARAM_STR);
			  $stmt->bindValue(':country', $dataArr->country, PDO::PARAM_STR);
			  $stmt->bindValue(':state', $dataArr->state, PDO::PARAM_STR);
			  $stmt->bindValue(':city', $dataArr->city, PDO::PARAM_STR);
			  $stmt->bindValue(':postal_code', $dataArr->postal_code, PDO::PARAM_STR);
			  $stmt->bindValue(':address_id', $dataArr->address_id, PDO::PARAM_INT);
			  $stmt->execute();
			  $stmt->closeCursor();

			  return true;
		  }//catch exception
			   catch(Exception $e) {
			  echo 'Message: ' .$e->getMessage();exit;
			}
         
		
        
  }

	public function getLastUpdatedDate(){

        $sql = "SELECT MAX(createDate) as last_updated FROM rpt_summary_new";
		
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		if( count($RESULT) ){
            $row = array_shift($RESULT);
            return $row['last_updated'];
        }
        
        return '';
		 
		
     }
	/* public function updateUserServer($request){
        $user_id = $request['userIdVal'];
		//echo "<pre>"; print_r($request); die;	
		$objUser = new User();
        $user_data = $objUser->getUserLogData($user_id);
		//echo $user_data['loginid'];
		//echo "<pre>"; print_r($user_data); die;	
		$loginid=$user_data['loginid'];
		$user_server_id=$user_data['user_server_id'];
		
		
		if( empty($user_data) ){
            return false;
        }

		$first_name = isset($request['fld_first_name']) ? $request['fld_first_name'] : "";
        $last_name = isset($request['fld_last_name']) ? trim($request['fld_last_name']) : "";
        $email = isset($request['fld_email']) ? trim($request['fld_email']) : ""; 
		$password = isset($request['fld_password']) ? trim($request['fld_password']) : "";

		 $sql = "UPDATE user SET first_name='".$first_name."', last_name='".$last_name."', email_id='".$email."' WHERE user_id=".$user_server_id." limit 1";
		 
		 $stmt = $this->dbConn->prepare($sql);
         //$stmt->bindValue('user_id', $user_server_id, PDO::PARAM_INT);
         $stmt->execute();
        $stmt->closeCursor();
		
	     //echo "<pre>"; print_r($sql);
	     $sql2 = "UPDATE user_credential SET  password='".$password."' WHERE user_id=".$user_server_id." limit 1";
		
	   // echo "<pre>"; print_r($sql2); die;
		
         $stmt = $this->dbConn->prepare($sql2);
         //$stmt->bindValue('user_id', $user_server_id, PDO::PARAM_INT);
         $stmt->execute();
        $stmt->closeCursor();
         
        return true;
	}
	 */

	
		//============= ADD company in company master====
 function addCompany($res){
	  // echo "<pre>";print_r($res);exit;
            $company_name=$res['company_name'];
			$company_address=$res['company_address'];
			if($company_name!=""){
				$sql = "INSERT INTO tblx_company(company_name, company_address) VALUES(:company_name,:company_address)";
			
				$stmt = $this->dbConn->prepare($sql);                                  
				$stmt->bindParam(':company_name', $company_name, PDO::PARAM_STR);
				$stmt->bindParam(':company_address', $company_address, PDO::PARAM_STR);   
				$RESULT=$stmt->execute(); 
				$stmt->closeCursor();			
				return $RESULT;
			}
			else{ return false;}
   }
	
// Get test report
	public function getTestsReport($roleID=2, $centerID){
	  
	  /*  $sql = "SELECT uld.* FROM user_role_map uld	JOIN tblx_batch_user_map bum ON bum.user_id = uld.user_id AND bum.status = 1 WHERE uld.role_definition_id = :roleID  AND bum.center_id=:center_id"; */
	   $sql = "SELECT uld.* FROM user_role_map uld	JOIN tblx_batch_user_map bum ON bum.user_id = uld.user_id AND bum.status = 1 WHERE uld.role_definition_id = :roleID  AND bum.center_id=:center_id order by uld.user_id DESC";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':roleID', $roleID, PDO::PARAM_INT);
		$stmt->bindValue(':center_id',$centerID, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$userArr = array();
		while($row = array_shift($RESULT)) {
			array_push($userArr,$row);
		}
        return $userArr;
	
	}
	
	public function getSignedUpUserCountByCenter($client_id,$centerId){
		$sql = "SELECT count(*) teacherReg FROM  user_role_map  uld join user_credential uc on uc.user_id = uld.user_id inner join user_center_map ucm ON uld.user_id =  ucm.user_id and uld.role_definition_id =1 and uc.is_active =1 where ucm.center_id=:center_id AND ucm.client_id=:client_id";
		
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':client_id',$client_id, PDO::PARAM_INT);
		$stmt->bindValue(':center_id',$centerId, PDO::PARAM_INT);
		$stmt->execute();
		$tRESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if($tRESULT[0]['teacherReg']){
			$totalTeacher = $tRESULT[0]['teacherReg'];
		}else{
	    	$totalTeacher = 0;
		}
        $stmt->closeCursor();
		//echo "<pre>";print_r($tRESULT[0]['teacherReg']);exit;
		
		$sql="SELECT count(*) studentReg FROM  user_role_map  uld join user_credential uc on uc.user_id = uld.user_id  inner join user_center_map ucm ON uld.user_id =  ucm.user_id and uld.role_definition_id =2 and uc.is_active =1 where ucm.center_id=:center_id AND ucm.client_id=:client_id";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':client_id',$client_id, PDO::PARAM_INT);
		$stmt->bindValue(':center_id',$centerId, PDO::PARAM_INT);
		$stmt->execute();																		
		$sRESULT =$stmt->fetchAll(PDO::FETCH_ASSOC);
		if($sRESULT[0]['studentReg']){
			$totalStudent = $sRESULT[0]['studentReg'];
		}else{
			$totalStudent = 0;
		}
		$stmt->closeCursor();
		
		
		$obj = new stdClass();
		
		$obj->totalCenterTeacher = $totalTeacher;
		$obj->totalCenterStudent = $totalStudent;
		
		return $obj; 
	}
	
	//for region
	public function getSignedUpUserCountByRegion($client_id,$regionId){
		$sql = "SELECT count(*) teacherReg FROM  user_role_map  uld join user_credential uc on uc.user_id = uld.user_id inner join user_center_map ucm ON uld.user_id =  ucm.user_id inner join tblx_center tc on ucm.center_id=tc.center_id and uld.role_definition_id =1 and  uc.is_active =1 where  ucm.client_id=:client_id and tc.region=:region_id";
		
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':client_id',$client_id, PDO::PARAM_INT);
		$stmt->bindValue(':region_id',$regionId, PDO::PARAM_INT);
		$stmt->execute();
		$tRESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if($tRESULT[0]['teacherReg']){
			$totalTeacher = $tRESULT[0]['teacherReg'];
		}else{
	    	$totalTeacher = 0;
		}
        $stmt->closeCursor();
		//echo "<pre>";print_r($tRESULT[0]['teacherReg']);exit;
		
		$sql="SELECT count(*) studentReg FROM  user_role_map  uld join user_credential uc on uc.user_id = uld.user_id inner join user_center_map ucm ON uld.user_id =  ucm.user_id inner join tblx_center tc on ucm.center_id=tc.center_id and uld.role_definition_id =2  and uc.is_active =1 where tc.region=:region_id AND ucm.client_id=:client_id";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':client_id',$client_id, PDO::PARAM_INT);
		$stmt->bindValue(':region_id',$regionId, PDO::PARAM_INT);
		$stmt->execute();																		
		$sRESULT =$stmt->fetchAll(PDO::FETCH_ASSOC);
		if($sRESULT[0]['studentReg']){
			$totalStudent = $sRESULT[0]['studentReg'];
		}else{
			$totalStudent = 0;
		}
		$stmt->closeCursor();
		
		
		$obj = new stdClass();
		
		$obj->totalCenterTeacher = $totalTeacher;
		$obj->totalCenterStudent = $totalStudent;
		
		return $obj; 
		
		
		
	}
	
		//============= Get center details  methods
	public function getCenterDetailsById($center_id){
		$sql = "SELECT c.* FROM tblx_center c where c.center_id=:center_id";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
        $stmt->execute();
        $centerResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		if(count($centerResult) > 0 ){
				return $centerResult;
			}else{
				return false;
			}		
    }
	
	// Get max registration limit
	public function getUserLimit($centerId,$userRole){
		$sql = "select count('*') as cnt from user_center_map INNER JOIN user_role_map ON user_center_map.user_id=user_role_map.user_id and user_center_map.center_id=:center_id and user_role_map.role_definition_id=:role_definition_id";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':center_id',$centerId, PDO::PARAM_INT);
		$stmt->bindValue(':role_definition_id',$userRole, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$cnt=$RESULT[0]['cnt'];
        return $cnt;
	}
	
	
	//============= Create batch methods
	 public function createCenterBatch($batchName,$batchType,$centerId,$lmode){
		 //echo "<pre>";print_r($batchType);exit;

		$cCode = 'CN-'.$centerId;
		$bcode = $cCode;
		
		$sql = "SELECT MAX(batch_id) as maxBatchId from tblx_batch where center_id=:center_id";
		//echo "<pre>";print_r($sql);exit;
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue('center_id', $centerId, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$section=$RESULT[0]['maxBatchId'];
		$section=$section+1;
		 //echo "<pre>";print($section);exit;
        //// Now Adding  Batch 
        $sql = "INSERT INTO tblx_batch(center_id,batch_id, batch_code, batch_name,date_created,status,batch_type,learning_mode,is_default) VALUES(:center_id, :section,:bcode,:batchName,NOW(),1,:batchType,:lmode,'1')";
		//echo "<pre>";print_r($sql);exit;
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':center_id', $centerId, PDO::PARAM_INT);
		$stmt->bindValue(':section', $section, PDO::PARAM_STR);
		$stmt->bindValue(':bcode', $bcode, PDO::PARAM_STR);
		$stmt->bindValue(':batchName', $batchName, PDO::PARAM_STR);
		$stmt->bindValue(':batchType', $batchType, PDO::PARAM_STR);
		$stmt->bindValue(':lmode', $lmode, PDO::PARAM_STR);
		$stmt->execute();
		$batchID =$section;//$this->dbConn->lastInsertId();
		$stmt->closeCursor(); 
		 //echo "<pre>";print($batchID);exit;
		 
        $batch_code = $bcode.'-B'.$batchID;
		$stmt = $this->dbConn->prepare("UPDATE `tblx_batch` SET `batch_code`= :batch_code WHERE `batch_id`=:batchID AND center_id=:centerId");
		$stmt->bindValue(':batchID', $batchID, PDO::PARAM_INT);
		$stmt->bindValue(':batch_code', $batch_code, PDO::PARAM_STR);
		$stmt->bindValue(':centerId', $centerId, PDO::PARAM_INT);
		$stmt->execute();
        $stmt->closeCursor();
		
		$obj = new stdclass();
		$obj->batchID = $batchID;
		 return $obj;//array('$batchID' => $batchID);
		
		
    }
		//Get Batch Course map
	public function getBatchCourseMapDetails($batch_id,$courseid,$centerId){

		$sql = "SELECT COUNT(*) as 'cnt' FROM tblx_batch_course_map WHERE center_id=:center_id and batch_id=:batch_id and course_id=:course_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':center_id', $centerId, PDO::PARAM_INT);
        $stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
        $stmt->bindValue(':course_id', $courseid, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		return $RESULT;
	
	}
	//=============  Update Batch Course Map
	
	public function updateBatchCourseMap($batch_id,$courseid,$centerId){
		try{
			$batchCourseMapDetails = $this->getBatchCourseMapDetails($batch_id,$courseid,$centerId);
			if($batchCourseMapDetails[0]['cnt']>0)
			{
				$sql = "UPDATE tblx_batch_course_map SET date_created=NOW() WHERE center_id=:center_id AND batch_id = :batch_id AND course_id=:course_id";

				$stmt = $this->dbConn->prepare($sql);
				$stmt->bindValue(':center_id', $centerId, PDO::PARAM_INT);
				$stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
				$stmt->bindValue(':course_id', $courseid, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor();  
			}
			else{
				$sql = "INSERT INTO tblx_batch_course_map (center_id,batch_id,course_id,date_created) values (:center_id, :batch_id, :course_id,NOW())";
				$stmt = $this->dbConn->prepare($sql);
				$stmt->bindValue(':center_id', $centerId, PDO::PARAM_INT);
				$stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
				$stmt->bindValue(':course_id', $courseid, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor(); 
			}
			return true;	
		 }//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
	}
	
	//=============  Update batch name methods by batch Id
	
	public function updateBatchDataByID($batch_id,$batchName,$batchType,$centerId,$lmode){
		try{
			$sql = "UPDATE tblx_batch SET batch_name=:batchName,batch_type=:batchType,learning_mode=:learning_mode WHERE batch_id = :batchID AND center_id=:center_id";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':batchID', $batch_id, PDO::PARAM_INT);
			$stmt->bindValue(':center_id', $centerId, PDO::PARAM_INT);
			$stmt->bindValue(':batchName', $batchName, PDO::PARAM_STR);
			$stmt->bindValue(':batchType', $batchType, PDO::PARAM_STR);
			$stmt->bindValue(':learning_mode', $lmode, PDO::PARAM_STR);
			$stmt->execute();
			$stmt->closeCursor();
			return true;	
		}//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
	}
	
	
   	//=============  Get batch data  methods by batch Id
	
	public function getBatchDataByID($batch_id,$centerId){
		try{ 
			$sql = "Select * from tblx_batch WHERE batch_id = :batchID AND center_id = :centerID";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':batchID', $batch_id, PDO::PARAM_INT);
			$stmt->bindValue(':centerID', $centerId, PDO::PARAM_INT);
			
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			if(count($RESULT) > 0 ){
					return $RESULT;
				}else{
					return false;
				} 
		}//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
	}
	//Get Batch Course map data
	public function getBatchCourseMapList($batch_id,$centerId){

		$sql = "SELECT course_id FROM tblx_batch_course_map WHERE center_id=:center_id and batch_id=:batch_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':center_id', $centerId, PDO::PARAM_INT);
        $stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$courseArr = array();
		while($row = array_shift( $RESULT )) {
			array_push($courseArr,$row['course_id']);
		}
        return $courseArr;
	
	}
	//Delete Batch Course map data
	public function deleteBatchCourseMapDetails($batch_id,$centerId){

		$sql = "DELETE  FROM tblx_batch_course_map WHERE center_id=:center_id and batch_id=:batch_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':center_id', $centerId, PDO::PARAM_INT);
        $stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();	
	
	}
		//============= Create Student and teacher methods


		//////////////////////////IES register///////////////////////

	
//////////////////////////////////////////////

	public function registerUser( array $request){

		// echo "<pre>"; print_r($request); die;
		
		$center_id = isset($request['center_id']) ? trim($request['center_id']) : "";		
		$client_id = isset($request['client_id']) ? trim($request['client_id']) : "";

		if(empty($center_id)|| empty($client_id)){
			return false;
		}
		$roleID = ( $request['uSignUp'] == 'studentReg' ) ? 2 : 1;		
		$fName=filter_string($request['name']);
		$lName=filter_string($request['lastname']);
		
		/* echo '<pre>';print_r($request);exit;  */
		$first_name = isset($fName) ? $fName : "";
		$last_name = isset($lName) ? $lName : "";
		$email1=filter_string($request['email']);
        $email = isset($email1) ? trim($email1) : "";
        $is_email_verified = 0;
		$mobile1=filter_string($request['mobile']);
        $phone = isset($mobile1) ? trim($mobile1) : "";
        $is_phone_verified = 0;
		$password1=filter_string($request['password']);
		$password = isset($password1) ? trim($password1) : "";
		$batch = isset($request['batch']) ? trim($request['batch']) : "";
		$gender = isset($request['gender']) ? trim($request['gender']) : "";
		//$marital_id = isset($request['maritalStatus']) ? trim($request['maritalStatus']) : "";
		$age = isset($request['age']) ? trim($request['age']) : "";
		//$dob= isset($request['age']) ? trim($request['age']) : "";
		$country_id = isset($request['country']) ? trim($request['country']) : "";
		$mother_tongue_id = isset($request['motherTongue']) ? trim($request['motherTongue']) : "";
		$education_id = isset($request['education']) ? trim($request['education']) : "";
		$emp_status_id = isset($request['empStatus']) ? trim($request['empStatus']) : "";
		$perpose_join_id = isset($request['purJoining']) ? trim($request['purJoining']) : "";
		$english_exp_id = isset($request['englishExp']) ? trim($request['englishExp']) : "";
		
		$profile_id = isset($request['profile_id'])? trim($_POST["profile_id"]) : "";
		$fileImgNamePro = isset($request['fileImgNamePro'])?trim($_POST["fileImgNamePro"]) : "";
		$status = isset($request['status']) ? $request['status'] : "";
		
		$centerDetails=$this->getCenterDetailsById($center_id);

		$trainer_limit = $centerDetails[0]['trainer_limit'];
		$student_limit = $centerDetails[0]['student_limit'];
		
		//Check for registration limit
		$chkLimit= $this->getUserLimit($center_id,$roleID);
		
		if((($roleID==1) && ($trainer_limit>$chkLimit)) || ($roleID==2) && ($student_limit>$chkLimit)){
			


			$stmt = $this->dbConn->prepare("select c.user_id,loginid, u.address_id from user u, user_credential c where email_id=:email and c.user_id=u.user_id");
			$stmt->bindValue(":email", $email, PDO::PARAM_STR);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			if(count($RESULT) > 0 ){
				 //echo "<pre>";print_r($RESULT);exit;
					return $RESULT[0];
			}else{
				$stmt = $this->dbConn->prepare("INSERT INTO address_master(country,phone,is_phone_verified,updated_by,created_date) VALUES(:country,:phone,:is_phone_verified,:user_id,NOW())");
				$stmt->bindValue(':phone',$phone, PDO::PARAM_STR);
				$stmt->bindValue(':country',$country_id, PDO::PARAM_STR);
				$stmt->bindValue(':is_phone_verified',$is_phone_verified, PDO::PARAM_STR);
				$stmt->bindValue(':user_id',$_SESSION['user_id'], PDO::PARAM_INT);
				$stmt->execute();
				$address_id =$this->dbConn->lastInsertId();
				$stmt->closeCursor(); 
				 
				  //// Now Adding  Assest 
				$stmt = $this->dbConn->prepare("INSERT INTO asset(updated_by,created_date) VALUES('1',NOW())");
				$stmt->execute();
				$asset_id = $this->dbConn->lastInsertId();
				$stmt->closeCursor(); 
				
				
				
				$stmt= $this->dbConn->prepare("insert into user(first_name,last_name,email_id,is_email_verified,address_id,profile_pic,updated_by,gender,user_client_id,age_range,mother_tongue,education,employment_status,joining_purpose,years_eng_edu,created_date) values(:first_name,:last_name,:email_id,:is_email_verified,:address_id,:profile_pic,:updated_by,:gender,:user_client_id,:age_range,:mother_tongue,:education,:employment_status,:joining_purpose,:years_eng_edu,NOW())");
				
				$stmt->bindValue(':first_name', $first_name, PDO::PARAM_STR);
				$stmt->bindValue(':last_name', $last_name, PDO::PARAM_STR);
				$stmt->bindValue(':email_id', $email, PDO::PARAM_STR);
				$stmt->bindValue(':is_email_verified',$is_email_verified, PDO::PARAM_STR);
				$stmt->bindValue(':address_id', $address_id, PDO::PARAM_INT);
				$stmt->bindValue(':profile_pic', $asset_id, PDO::PARAM_INT);
				$stmt->bindValue(':updated_by',$_SESSION['user_id'], PDO::PARAM_INT);
				$stmt->bindValue(':gender', $gender, PDO::PARAM_STR);
				$stmt->bindValue(':user_client_id', $client_id, PDO::PARAM_INT);
				//$stmt->bindValue(':date_of_birth', $dob, PDO::PARAM_STR);
				$stmt->bindValue(':age_range', $age, PDO::PARAM_STR);
				//$stmt->bindValue(':marital_status', $marital_id, PDO::PARAM_INT);
				$stmt->bindValue(':mother_tongue', $mother_tongue_id, PDO::PARAM_INT);
				$stmt->bindValue(':education', $education_id, PDO::PARAM_INT);
				$stmt->bindValue(':employment_status', $emp_status_id, PDO::PARAM_INT);
				$stmt->bindValue(':joining_purpose', $perpose_join_id, PDO::PARAM_INT);
				$stmt->bindValue(':years_eng_edu', $english_exp_id, PDO::PARAM_INT);
				
				$stmt->execute();
				
				$user_id =$this->dbConn->lastInsertId();
				$stmt->closeCursor(); 
				
				//// update profile pic  
				$stmt= $this->dbConn->prepare("UPDATE `asset` SET `system_name`=:system_name WHERE `asset_id`=:asset_id");
				$stmt->bindValue(':system_name', $fileImgNamePro, PDO::PARAM_STR);
				$stmt->bindValue(':asset_id', $asset_id, PDO::PARAM_INT);
			   //echo "<pre>";print_r($stmt);exit;
				$stmt->execute();
				$stmt->closeCursor();
				
				//// Adding user and center map

				$stmt = $this->dbConn->prepare("insert into user_center_map(user_id,center_id,client_id,created_date) values(:user_id,:center_id,:client_id,NOW())");
				// echo "<pre>";print_r($stmt);exit;
				$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
				$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
				$stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor(); 
				
				//// Adding Admin Credentials 
				$stmt= $this->dbConn->prepare("insert into user_credential(user_id,loginid,password,updated_by,created_date) values(:user_id,:loginid,:password,1,NOW())");
				$stmt->bindValue(':user_id',$user_id, PDO::PARAM_INT);
				$stmt->bindValue(':loginid', $email, PDO::PARAM_STR);
				$stmt->bindValue(':password',$password, PDO::PARAM_STR);
				$stmt->execute();
				$stmt->closeCursor(); 
				//echo "<pre>";print_r($stmt);exit;
			
				
				////Select the client to user group id */
				$stmt = $this->dbConn->prepare("Select user_group_id from client WHERE client_id=:client_id");
					//echo "<pre>";print_r($stmt);exit;
				$stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
				$stmt->execute();
				$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();
				//echo "<pre>";print_r($RESULT[0]['user_group_id']);exit;
				$client_group_id = $RESULT[0]['user_group_id'];

				if( $roleID == 2 ){
					
					$role_type="2";//student/learner
				}
				else{
					$role_type="1";//teacher
					
				}
				//// Adding user into role map group 
				$stmt = $this->dbConn->prepare("insert into user_role_map(user_id,role_definition_id,user_group_id,is_active,updated_by,created_date) values(:user_id,:role_type,:client_group_id,1,1,NOW())");
					//echo "<pre>";print_r($stmt);exit;
				$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
				$stmt->bindValue(':role_type', $role_type, PDO::PARAM_INT);
				$stmt->bindValue(':client_group_id', $client_group_id, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor(); 
				
				// For batch_user_map table insert
				if( $roleID == 2 ){	
				  
				  $default_batch_id = isset($request['default_batch_id']) ? trim($request['default_batch_id']) : "";
					
					if(count($request['batch']) > 0){
					  $batch = isset($request['batch']) ? $request['batch'] : 0;
					}	
				    
				  if($batch==$default_batch_id){	
							$stmt = $this->dbConn->prepare("update user_credential set is_active= :status  WHERE user_id=:user_id");
							$stmt->bindValue(':status', $status, PDO::PARAM_STR);
							$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
							$stmt->execute();
							$stmt->closeCursor();
					}else{
						$stmt = $this->dbConn->prepare("update user_credential set is_active= :status WHERE user_id=:user_id");
						$stmt->bindValue(':status', $status, PDO::PARAM_STR);
						$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
						$stmt->execute();
						$stmt->closeCursor();
					}
				 	

							
					$btUserMap_sql = "insert into tblx_batch_user_map (user_id, batch_id, center_id,status,user_server_id) values (:userID, :batchID, :center_id,1,:userServerID)";
					
					$btUserMap = $this->dbConn->prepare( $btUserMap_sql );
					$btUserMap->bindValue(':userID', $user_id, PDO::PARAM_INT);
					$btUserMap->bindValue(':userServerID', $user_id, PDO::PARAM_INT);
					$btUserMap->bindValue(':batchID', $batch, PDO::PARAM_INT);
					$btUserMap->bindValue(':center_id', $center_id, PDO::PARAM_INT);
					//echo "<pre>";print_r($btUserMap_sql);exit;
					$btUserMap->execute();
					$btUserMap->closeCursor();
					
				}else{
					for( $i=0; $i<count($request['batch']); $i++){
						$btUserMap_sql = "insert into tblx_batch_user_map (user_id, batch_id, center_id) values (:userID, :batchID, :center_id) ";
						$btUserMap = $this->dbConn->prepare( $btUserMap_sql );
						$btUserMap->bindValue(':userID', $user_id, PDO::PARAM_INT);
						$btUserMap->bindValue(':batchID', $request['batch'][$i], PDO::PARAM_INT);
						$btUserMap->bindValue(':center_id', $center_id, PDO::PARAM_INT);
						$btUserMap->execute();
						$btUserMap->closeCursor();
					}
						
				}
				$btUserMap->closeCursor();
				
				////Code for MixPanel/////
				
				$post_data = array();
				$parentObj = new stdClass();
				$parentObj->eventName = 'Signup';
				$parentObj->clientCode = 'CommonApp';
				
				$data = new stdClass();
				$data->user_id = $user_id;
				$data->first_name = $first_name;
				$data->last_name = $last_name;
				$data->email_id = $email;
				$data->phone = $phone;
				$data->loginid = $email;

				array_push($post_data,$data);
				$parentObj->userProps=$post_data; 
				//$MTResponse=$this->sendToMixPanel($parentObj);

				
				////Code for MixPanel/////
				return array('roleID' => $roleID, 'loginid' => $loginid, 'password' => $password);
					
			
		  }
		
		}else{
				return false;
		}
  }
	
	//============= Upadate Student and teacher methods

	public function updateUser( array $request){
		// echo "<pre>";print_r($request);exit;
		$center_id1=filter_query($request['center_id']);
		$center_id = isset($center_id1) ? trim($center_id1) : "";
		$client_id=filter_query($request['client_id']);
		$client_id = isset($client_id) ? trim($client_id) : "";
		if(empty($center_id)|| empty($client_id)){
				return false;
		}
	
		$userIdVal=filter_query($request['userIdVal']);
		$user_id = isset($userIdVal)? $userIdVal : '';		
		$roleID = ( $request['uSignUp'] == 'studentReg' ) ? 2 : 1;		// 2 student and 1 trainer
		$fName=filter_string($request['name']);
		$lName=filter_string($request['lastname']);
		$first_name = isset($fName) ? $fName : "";
		$last_name = isset($lName) ? $lName : "";
		$email1=filter_string($request['email']);
        $email = isset($email1) ? trim($email1) : "";
        $is_email_verified = 0;
		$mobile1=filter_string($request['mobile']);
        $phone = isset($mobile1) ? trim($mobile1) : "";
        $is_phone_verified = 0;
		$password1=filter_string($request['fld_password']);
		$password = isset($password1) ? trim($password1) : "";
		$batch1 = filter_query($request['batch']);
		$batch = isset($batch1) ? trim($batch1) : "";
		$gender = isset($request['gender']) ? trim($request['gender']) : "";
		//$marital_id = isset($request['maritalStatus']) ? trim($request['maritalStatus']) : "";
		$age = isset($request['age']) ? trim($request['age']) : "";
		//$dob= isset($request['age']) ? trim($request['age']) : "";
		$country_id = isset($request['country']) ? trim($request['country']) : "";
		//$state_id = isset($request['state_dropdown']) ? trim($request['state_dropdown']) : "";
		//$city_id = isset($request['city_dropdown']) ? trim($request['city_dropdown']) : "";
		//$pincode = isset($request['pincode']) ? trim($request['pincode']) : "";
		//$nationality = isset($request['nationality']) ? trim($request['nationality']): "";
		$mother_tongue_id = isset($request['motherTongue']) ? trim($request['motherTongue']) : "";
		$education_id = isset($request['education']) ? trim($request['education']) : "";
		$emp_status_id = isset($request['empStatus']) ? trim($request['empStatus']) : "";
		$perpose_join_id = isset($request['purJoining']) ? trim($request['purJoining']) : "";
		$english_exp_id = isset($request['englishExp']) ? trim($request['englishExp']) : "";
		
		//$discover_app_id = isset($request['usersDicover']) ? trim($request['usersDicover']) : "";
		$profile_id1=filter_query($request['profile_id']);
		$profile_id = isset($profile_id1)? trim(filter_query($profile_id1) ): "";
		$fileImgNamePro = isset($request['fileImgNamePro'])?trim($request["fileImgNamePro"]) : "";
		$status = isset($request['status']) ? $request['status'] : "";
		$userType = isset($request['userType']) ? $request['userType'] : "b2b";

        try{
			  //// for phone get address id by user id
			$stmt = $this->dbConn->prepare("Select address_id from user  WHERE user_id=:user_id");
			//echo "<pre>";print_r($stmt);exit;
			$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			//echo "<pre>";print_r($RESULT);exit;
			$stmt->closeCursor();
			$address_id = $RESULT[0]['address_id'];
			
			 //// update phone in address master
			 //$stmt = $this->dbConn->prepare("UPDATE `address_master` SET `phone`=:phone,`city`=:city,`state`=:state,`country`=:country,`postal_code`=:postal_code,`is_phone_verified`=:is_phone_verified,`modified_date`= NOW(),`nationality`=:nationality WHERE `address_id`=:address_id");
			 
			$stmt = $this->dbConn->prepare("UPDATE address_master SET phone=:phone,country=:country,is_phone_verified=:is_phone_verified,modified_date= NOW() WHERE address_id=:address_id");
			//echo "<pre>";print_r($stmt);exit;
			//$stmt->bindValue(':nationality',$nationality, PDO::PARAM_STR);
			$stmt->bindValue(':phone',$phone, PDO::PARAM_STR);
			//$stmt->bindValue(':city',$city_id, PDO::PARAM_STR);
			//$stmt->bindValue(':state',$state_id, PDO::PARAM_STR);
			$stmt->bindValue(':country',$country_id, PDO::PARAM_STR);
			//$stmt->bindValue(':postal_code',$pincode, PDO::PARAM_STR);
			$stmt->bindValue(':is_phone_verified',$is_phone_verified, PDO::PARAM_STR);
			$stmt->bindValue(':address_id',$address_id, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();

	  
		   $stmt = $this->dbConn->prepare("UPDATE user SET first_name=:first_name ,last_name= :last_name,gender=:gender,age_range=:age_range,mother_tongue=:mother_tongue,education=:education,employment_status=:employment_status,joining_purpose=:joining_purpose,years_eng_edu=:years_eng_edu,modified_date= Now() WHERE user_id=:user_id");
			//echo "<pre>";print_r($stmt);exit;
			$stmt->bindValue(':first_name', $first_name, PDO::PARAM_STR);
			$stmt->bindValue(':last_name', $last_name, PDO::PARAM_STR);
			$stmt->bindValue(':gender', $gender, PDO::PARAM_STR);
			//$stmt->bindValue(':date_of_birth', $dob, PDO::PARAM_STR);
			$stmt->bindValue(':age_range', $age, PDO::PARAM_STR);
			//$stmt->bindValue(':marital_status', $marital_id, PDO::PARAM_INT);
			$stmt->bindValue(':mother_tongue', $mother_tongue_id, PDO::PARAM_INT);
			$stmt->bindValue(':education', $education_id, PDO::PARAM_INT);
			$stmt->bindValue(':employment_status', $emp_status_id, PDO::PARAM_INT);
			$stmt->bindValue(':joining_purpose', $perpose_join_id, PDO::PARAM_INT);
			$stmt->bindValue(':years_eng_edu', $english_exp_id, PDO::PARAM_INT);
			$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor(); 
			
			//// update profile pic  
			$stmt= $this->dbConn->prepare("UPDATE asset SET system_name=:system_name,modified_date=NOW() WHERE asset_id=:profile_id");
		   //echo "<pre>";print_r($stmt);exit;
			$stmt->bindValue(':system_name', $fileImgNamePro, PDO::PARAM_STR);
			$stmt->bindValue(':profile_id', $profile_id, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();

			 
          //// update login Credentials 
		   if($password!=''){
				$stmt= $this->dbConn->prepare("UPDATE user_credential SET password=:password ,modified_date=NOW() WHERE user_id=:user_id");
			  // echo "<pre>";print_r($stmt);exit;
				 $stmt->bindValue(':password',$password, PDO::PARAM_STR);
				 $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
				 $stmt->execute();
				 $stmt->closeCursor();
		   }				 
			// For batch_user_map table UPDATE for trainer only
			if( $roleID == 1 ){
				 //echo "<pre>";print_r($request['batch']);exit;
				$update_sql = "Update tblx_batch_user_map set status = 0 WHERE user_id = :user_id AND center_id = :center_id ";
				$update_stmt = $this->dbConn->prepare( $update_sql );
				$update_stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
				$update_stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
				$update_stmt->execute();
				$update_stmt->closeCursor();
				
				for( $i=0; $i<count($request['batch']); $i++){
					
					$btUserMap_sql = "insert into tblx_batch_user_map (user_id, batch_id, center_id) values (:user_id, :batch_id, :center_id) "
							. "ON DUPLICATE KEY UPDATE status = 1";
					$btUserMap = $this->dbConn->prepare( $btUserMap_sql );
					
					$btUserMap->bindValue(':user_id', $user_id, PDO::PARAM_INT);
					$btUserMap->bindValue(':batch_id', $request['batch'][$i], PDO::PARAM_INT);
					$btUserMap->bindValue(':center_id', $center_id, PDO::PARAM_INT);
					$btUserMap->execute();
					$btUserMap->closeCursor();
					
				}
				
				$stmt1 = $this->dbConn->prepare("update user_center_map set center_id= :center_id WHERE user_id=:user_id");
				$stmt1 ->bindValue(':center_id', $center_id, PDO::PARAM_INT);
				$stmt1 ->bindValue(':user_id', $user_id, PDO::PARAM_INT);
				$stmt1->execute();
				$stmt1->closeCursor(); 
					
			}else{
				if($userType!='b2b'){
					$expiry = isset($request['expiry']) ? trim($request['expiry']) : '';
					if($expiry!=''){
						$expiryDate = date('Y-m-d',strtotime($expiry));
					}else{
						$expiryDate = NULL;
					}
					
					$stmt= $this->dbConn->prepare("UPDATE user_credential SET expiry_date=:expiry ,modified_date=NOW() WHERE user_id=:user_id");
				  // echo "<pre>";print_r($stmt);exit;
					 $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
					 $stmt->bindValue(':expiry', $expiryDate, PDO::PARAM_STR);
					 $stmt->execute();
					 $stmt->closeCursor();  
				}
				
				$default_batch_id = isset($request['default_batch_id']) ? trim($request['default_batch_id']) : "";
				 if(count($request['batch']) > 0){
						$batch = isset($request['batch'][0]) ? $request['batch'][0] : 0;
					}
					if(count($request['cCenter']) > 0){
						$cCenter = isset($request['cCenter'][0]) ? $request['cCenter'][0] : 0;
					}
					if(count($request['cBatch']) > 0){
						$cBatch = isset($request['cBatch'][0]) ? $request['cBatch'][0] : 0;
					}

				  if($batch==$default_batch_id){	
							$stmt = $this->dbConn->prepare("update user_credential set is_active= :status WHERE user_id=:user_id");
							$stmt->bindValue(':status', $status, PDO::PARAM_STR);
							$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
							$stmt->execute();
							$stmt->closeCursor();
					}else{
						$stmt = $this->dbConn->prepare("update user_credential set is_active= :status WHERE user_id=:user_id");
						$stmt->bindValue(':status', $status, PDO::PARAM_STR);
						$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
						$stmt->execute();
						$stmt->closeCursor();
					}
						
				   if($last_name!=''){
						  $userName=$first_name." ".$last_name;
						 }else{
							$userName=$first_name; 
						 }
					
					//// update user access code
						$stmt1 = $this->dbConn->prepare("UPDATE tbl_access_codes SET code_used_by_name=:userName WHERE access_code_status='1' AND code_used_by_id=:user_id");	
						$stmt1->bindValue(':userName', $userName, PDO::PARAM_STR);
						$stmt1->bindValue(':user_id', $user_id, PDO::PARAM_INT);
						$stmt1->execute();
						$stmt1->closeCursor();
						
						//// Adding user and center map 
						
						$stmt1 = $this->dbConn->prepare("update user_center_map set center_id= :center_id WHERE user_id=$user_id");
						$stmt1->bindValue(':center_id', $center_id, PDO::PARAM_INT);
						$stmt1->execute();
						$stmt1->closeCursor(); 	
				  
					if($cCenter!=$center_id){

						$sql = "insert into tblx_user_center_batch_migrate(user_id,current_center,move_center,current_batch,move_batch,updated_by,updated_date) values(:user_id,:cCenter,:center_id,:cBatch,:batch,".$_SESSION['user_id'].", NOW())";
						$stmt = $this->dbConn->prepare( $sql );
						$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
						$stmt->bindValue(':cCenter', $cCenter, PDO::PARAM_INT);
						$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
						$stmt->bindValue(':cBatch', $cBatch, PDO::PARAM_INT);
						$stmt->bindValue(':batch', $batch, PDO::PARAM_INT);
						$stmt->execute();
						$stmt->closeCursor(); 

					

					
							 
							
						$btUserMap_sql = "Update tblx_batch_user_map set batch_id =:batchID,center_id=:center_id WHERE user_id=:userID AND status ='1'";
						$btUserMap = $this->dbConn->prepare( $btUserMap_sql );
						$btUserMap->bindValue(':userID', $user_id, PDO::PARAM_INT);
						$btUserMap->bindValue(':batchID', $batch, PDO::PARAM_INT);
						$btUserMap->bindValue(':center_id', $center_id, PDO::PARAM_INT);
						$btUserMap->execute();
						$btUserMap->closeCursor();
							

					}else{
							if($cBatch!=$batch){
								$sql = "insert into tblx_user_center_batch_migrate(user_id,current_center,move_center,current_batch,move_batch,updated_by,updated_date) values(:user_id,:cCenter,:center_id,:cBatch,:batch,".$_SESSION['user_id'].", NOW())";
								$stmt = $this->dbConn->prepare( $sql );
								$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
								$stmt->bindValue(':cCenter', $cCenter, PDO::PARAM_INT);
								$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
								$stmt->bindValue(':cBatch', $cBatch, PDO::PARAM_INT);
								$stmt->bindValue(':batch', $batch, PDO::PARAM_INT);
								$stmt->execute();
								$stmt->closeCursor(); 
								
								$btUserMap_sql = "Update tblx_batch_user_map set batch_id =:batchID WHERE user_id=:userID AND center_id=:center_id AND status ='1'";
								$btUserMap = $this->dbConn->prepare( $btUserMap_sql );
								$btUserMap->bindValue(':userID', $user_id, PDO::PARAM_INT);
								$btUserMap->bindValue(':batchID', $batch, PDO::PARAM_INT);
								$btUserMap->bindValue(':center_id', $center_id, PDO::PARAM_INT);
								$btUserMap->execute();
								$btUserMap->closeCursor();  
		
						}		
						
				   }	 
				   /* if(count($request['batch']) > 0){
					  $batch = isset($request['batch'][0]) ? $request['batch'][0] : 0;
					}		
					$btUserMap_sql = "Update tblx_batch_user_map set batch_id =:batchID WHERE user_id=:userID AND center_id=:center_id AND status ='1'";

					$btUserMap = $this->dbConn->prepare( $btUserMap_sql );
					$btUserMap->bindValue(':userID', $user_id, PDO::PARAM_INT);
					$btUserMap->bindValue(':batchID', $batch, PDO::PARAM_INT);
					$btUserMap->bindValue(':center_id', $center_id, PDO::PARAM_INT);
					//echo "<pre>";print_r($btUserMap_sql);exit;
					$btUserMap->execute();
					$btUserMap->closeCursor(); */
				
				
				
			}

			return array('roleID' => $roleID);
		}//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
}

		 //============= Get user role and  batch based
	public function getUserList($roleID, $batchID,$center_id){
	  
		$sql = "SELECT uld.* FROM user_role_map uld	JOIN tblx_batch_user_map bum ON bum.user_id = uld.user_id AND bum.status = 1 WHERE uld.role_definition_id = :roleID AND bum.batch_id = :batch_id AND bum.center_id=:center_id order by uld.user_id DESC";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':roleID', $roleID, PDO::PARAM_INT);
        $stmt->bindValue(':batch_id', $batchID, PDO::PARAM_INT);
		$stmt->bindValue(':center_id',$center_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		//$user_id=$RESULT[0]['user_id'];
		//echo "<pre>"; print_r($RESULT); die;
		$userArr = array();
		while($row = array_shift( $RESULT ) ) {
			array_push($userArr,$row);
		}
        return $userArr; 
   
	}
	
	
	 //============= Get Global teacher for all batch 
	public function getAllUserDetails($roleID,$client_id,$institute_id=''){
	    if($institute_id != ''){
	  	  $add = "AND ucm.center_id=:institute_id";
	  	}else{
	  	  $add = "";
	  	}
		$sql = "SELECT uld.* FROM user_role_map uld	JOIN user_center_map ucm ON ucm.user_id = uld.user_id JOIN user u ON u.user_id = uld.user_id 
		WHERE uld.role_definition_id = :roleID  AND u.user_client_id=:client_id $add order by uld.user_id DESC";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':roleID', $roleID, PDO::PARAM_INT);
		$stmt->bindValue(':client_id',$client_id, PDO::PARAM_INT);
		if($institute_id != ''){
	  	$stmt->bindValue(':institute_id',$institute_id, PDO::PARAM_INT);
	  	}
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
	
	//============= Get Global teacher for all batch 
	public function getUsersByCenterAndCountry($cond_arr = array(), $start = 0, $limit = 10,$order,$dir){
		
			$whr = "where 1=1";
			$whr.= " and urm.role_definition_id='".$cond_arr['role_id']."' and u1.user_client_id='".$cond_arr['client_id']."'";  

			if($cond_arr['country']!="" && $cond_arr['country']!='All'){
				$whr.= " AND tc.country = '".$cond_arr['country']."'";
			}
			if($cond_arr['center_id']!="" && $cond_arr['center_id']!='All'){
				$whr.= " AND ubm.center_id = '".$cond_arr['center_id']."'";
			}
			
			if($cond_arr['batch_id']!="" && $cond_arr['batch_id']!='All'){
				$whr.= " AND ubm.batch_id = '".$cond_arr['batch_id']."'";
			}
			if($cond_arr['student_id']!=""){
				$whr.= " AND u1.user_id = '".$cond_arr['student_id']."'";
			}
			if($cond_arr['status']!="" || $cond_arr['status']=='0'){
				$whr.= " AND uc1.is_active = '".$cond_arr['status']."'";
			}
			
			if($cond_arr['region_id']!="" && $cond_arr['region_id']!='All'){
				$whr.= " AND tr.id = '".$cond_arr['region_id']."'";
			}
			
			 if($cond_arr['student_txt']!="" && $cond_arr['student_id']==""){
				$whr.= " AND ((u1.first_name LIKE '%".$cond_arr['student_txt']."%' or u1.last_name LIKE '%".$cond_arr['student_txt']."%'  or CONCAT(u1.first_name,' ',u1.last_name ) LIKE  '%".$cond_arr['student_txt']."%' or CONCAT(u1.first_name,'',u1.last_name) LIKE  '%".$cond_arr['student_txt']."%') OR (u1.email_id LIKE '%".$cond_arr['student_txt']."%') OR (uc1.loginid LIKE '%".$cond_arr['student_txt']."%'))";
			} 

			$limit_sql = '';
			if( !empty($limit) ){
				$limit_sql .= " LIMIT $start, $limit";
			}

			$sql = "Select count(DISTINCT u1.user_id) as 'cnt' FROM user u1 JOIN user_credential uc1 ON u1.user_id=uc1.user_id JOIN user_center_map ucm on u1.user_id=ucm.user_id JOIN user_role_map urm on u1.user_id=urm.user_id JOIN address_master am on u1.address_id=am.address_id JOIN tblx_batch_user_map ubm on u1.user_id=ubm.user_id JOIN tblx_center tc on ubm.center_id=tc.center_id LEFT JOIN tblx_region tr ON tc.region=tr.id $whr "; 
			$stmt = $this->dbConn->prepare($sql);	
			$stmt->execute();
			$RESULT_CNT = $stmt->fetchAll(PDO::FETCH_ASSOC); 
			$stmt->closeCursor();
			$row_cnt = array_shift( $RESULT_CNT );

			 $sql = "SELECT DISTINCT u1.user_id, u1.address_id, uc1.loginid, uc1.created_date, uc1.expiry_date, u1.first_name, u1.last_name, u1.email_id, uc1.is_active,tc.name,u1.gender,am.phone,am.country,u1.mother_tongue,u1.user_from,ucm.center_id FROM user u1 JOIN user_credential uc1 ON 
			 u1.user_id=uc1.user_id JOIN user_center_map ucm on u1.user_id=ucm.user_id JOIN user_role_map urm on u1.user_id=urm.user_id JOIN address_master am on u1.address_id=am.address_id JOIN tblx_batch_user_map ubm on u1.user_id=ubm.user_id JOIN tblx_center tc on ubm.center_id=tc.center_id LEFT JOIN tblx_region tr ON tc.region=tr.id $whr  ORDER BY ".$order." ".$dir." $limit_sql";
			//echo $sql;
			$stmt = $this->dbConn->prepare($sql);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			$userList = array();
			while($row = array_shift( $RESULT )) {
				$bcm = new stdClass();
				$bcm->first_name = $row['first_name'];
				$bcm->last_name = $row['last_name'];
				$bcm->email_id = $row['email_id'];
				$bcm->user_id = $row['user_id'];
				$bcm->loginid = $row['loginid'];
				$bcm->is_active = $row['is_active'];
				$bcm->expiry_date = $row['expiry_date'];
				$bcm->address_id = $row['address_id'];
				$bcm->created_date = $row['created_date'];
				$bcm->country = $row['country'];
				$bcm->mother_tongue = $row['mother_tongue'];
				$bcm->user_from = $row['user_from'];
				$bcm->gender = $row['gender'];
				$bcm->phone = $row['phone'];
				$bcm->center_name = $row['name'];
				$bcm->center_id = $row['center_id'];
				
				array_push($userList,$bcm);
			
			}

		
		return array('total' =>$row_cnt['cnt'] , 'result' => $userList);

	}
	//============= Get Learner all batch wise for teacher  
	public function getUsersByBatchCenterAndCountry($cond_arr = array(), $start = 0, $limit = 10,$order,$dir){
		
			$whr = "where 1=1";
			$whr.= " and urm.role_definition_id='".$cond_arr['role_id']."' and u1.user_client_id='".$cond_arr['client_id']."'";  

			if($cond_arr['country']!="" && $cond_arr['country']!='All'){
				$whr.= " AND tc.country = '".$cond_arr['country']."'";
			}
			if($cond_arr['center_id']!="" && $cond_arr['center_id']!='All'){
				$whr.= " AND ubm.center_id = '".$cond_arr['center_id']."'";
			}
			 if($cond_arr['batch_id']!="" && $cond_arr['batch_id']!='All'){
			  $whr.= ' and ubm.batch_id IN('.$cond_arr['batch_id'].')';
			  
			 }
			/* if($cond_arr['batch_id']!="" && $cond_arr['batch_id']!='All'){
				$whr.= " AND ubm.batch_id = '".$cond_arr['batch_id']."'";
			} */
			if($cond_arr['student_id']!=""){
				$whr.= " AND u1.user_id = '".$cond_arr['student_id']."'";
			}
			if($cond_arr['status']!="" || $cond_arr['status']=='0'){
				$whr.= " AND uc1.is_active = '".$cond_arr['status']."'";
			}
			
			if($cond_arr['region_id']!="" && $cond_arr['region_id']!='All'){
				$whr.= " AND trcm.region_id = '".$cond_arr['region_id']."'";
			}
			
			 if($cond_arr['student_txt']!="" && $cond_arr['student_id']==""){
				$whr.= " AND ((u1.first_name LIKE '%".$cond_arr['student_txt']."%' or u1.last_name LIKE '%".$cond_arr['student_txt']."%'  or CONCAT(u1.first_name,' ',u1.last_name ) LIKE  '%".$cond_arr['student_txt']."%' or CONCAT(u1.first_name,'',u1.last_name) LIKE  '%".$cond_arr['student_txt']."%') OR (u1.email_id LIKE '%".$cond_arr['student_txt']."%') OR (uc1.loginid LIKE '%".$cond_arr['student_txt']."%'))";
			} 

			$limit_sql = '';
			if( !empty($limit) ){
				$limit_sql .= " LIMIT $start, $limit";
			}

			$sql = "Select count(DISTINCT u1.user_id) as 'cnt' FROM user u1 JOIN user_credential uc1 ON u1.user_id=uc1.user_id JOIN user_center_map ucm on u1.user_id=ucm.user_id JOIN user_role_map urm on u1.user_id=urm.user_id JOIN address_master am on u1.address_id=am.address_id JOIN tblx_batch_user_map ubm on u1.user_id=ubm.user_id JOIN tblx_center tc on ubm.center_id=tc.center_id LEFT JOIN tblx_region_country_map trcm ON tc.country=trcm.country_name $whr "; 
			$stmt = $this->dbConn->prepare($sql);	
			$stmt->execute();
			$RESULT_CNT = $stmt->fetchAll(PDO::FETCH_ASSOC); 
			$stmt->closeCursor();
			$row_cnt = array_shift( $RESULT_CNT );

			 $sql = "SELECT DISTINCT u1.user_id, u1.address_id, uc1.loginid, uc1.created_date, uc1.expiry_date, u1.first_name, u1.last_name, u1.email_id, uc1.is_active,tc.name,u1.gender,am.phone,am.country,u1.mother_tongue,u1.user_from,ucm.center_id FROM user u1 JOIN user_credential uc1 ON 
			 u1.user_id=uc1.user_id JOIN user_center_map ucm on u1.user_id=ucm.user_id JOIN user_role_map urm on u1.user_id=urm.user_id JOIN address_master am on u1.address_id=am.address_id JOIN tblx_batch_user_map ubm on u1.user_id=ubm.user_id JOIN tblx_center tc on ubm.center_id=tc.center_id LEFT JOIN tblx_region_country_map trcm ON tc.country=trcm.country_name $whr  ORDER BY ".$order." ".$dir." $limit_sql";
			//echo $sql;
			$stmt = $this->dbConn->prepare($sql);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			$userList = array();
			while($row = array_shift( $RESULT )) {
				$bcm = new stdClass();
				$bcm->first_name = $row['first_name'];
				$bcm->last_name = $row['last_name'];
				$bcm->email_id = $row['email_id'];
				$bcm->user_id = $row['user_id'];
				$bcm->loginid = $row['loginid'];
				$bcm->is_active = $row['is_active'];
				$bcm->expiry_date = $row['expiry_date'];
				$bcm->address_id = $row['address_id'];
				$bcm->created_date = $row['created_date'];
				$bcm->country = $row['country'];
				$bcm->mother_tongue = $row['mother_tongue'];
				$bcm->user_from = $row['user_from'];
				$bcm->gender = $row['gender'];
				$bcm->phone = $row['phone'];
				$bcm->center_name = $row['name'];
				$bcm->center_id = $row['center_id'];
				
				array_push($userList,$bcm);
			
			}

		
		return array('total' =>$row_cnt['cnt'] , 'result' => $userList);

	}

//============= Get Global teacher for all batch 
	public function getUserDetailsById($userID,$roleID){
	  
		$sql = "SELECT uld.* FROM user_role_map uld	JOIN user_center_map ucm ON ucm.user_id = uld.user_id WHERE uld.role_definition_id = :roleID AND uld.user_id=:userID";
        $stmt = $this->dbConn->prepare($sql);
		 $stmt->bindValue(':userID', $userID, PDO::PARAM_INT);
        $stmt->bindValue(':roleID', $roleID, PDO::PARAM_INT);
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

	//============= updateStudentStatus activ and dective login
	public function updateStudentStatus($userID,$status){

		$sql = "Update user_credential set is_active=:status WHERE user_id=:userID";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':userID', $userID, PDO::PARAM_INT);
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        $stmt->execute();
		$stmt->closeCursor();
		return true;
			
			
	}
//============= Get user detail by  user id and role id 
     public function getUserDataByID($uid,$roleID){

		$sql = "SELECT bum.batch_id, uld.* FROM tblx_batch_user_map bum join user_role_map uld ON bum.user_id = uld.user_id "
                . " WHERE bum.status = 1 AND uld.user_id = ".$uid." AND uld.role_definition_id = ".$roleID;
        $stmt = $this->dbConn->prepare($sql);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$user_id=$RESULT[0]['user_id'];
		$list_batch = array();
		while($row = array_shift( $RESULT ) ) {
			array_push($list_batch,$row);
		}
		
		//echo "<pre>"; print_r($list_batch); die;

		$sql = "SELECT * FROM user WHERE user_id=:user_id order by user_id DESC";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$address_id=$RESULT[0]['address_id'];
		$first_name=$RESULT[0]['first_name'];
		$last_name=$RESULT[0]['last_name'];
		$email_id=$RESULT[0]['email_id'];
		$profile_id=$RESULT[0]['profile_pic'];
		$gender=$RESULT[0]['gender'];
		$dob=$RESULT[0]['date_of_birth'];
		if($dob=='0000-00-00'){
           $dob='';
		}else{
		  $dob= date('d-m-Y',strtotime($dob));
		}
		$age_range=$RESULT[0]['age_range'];
		$marital_status=$RESULT[0]['marital_status'];
		$mother_tongue=$RESULT[0]['mother_tongue'];
		$education=$RESULT[0]['education'];
		$employment_status=$RESULT[0]['employment_status'];
		$joining_purpose=$RESULT[0]['joining_purpose'];
		$app_discovered	=$RESULT[0]['app_discovered'];
		$course_id	=$RESULT[0]['course_id'];
		$career	=$RESULT[0]['career'];
		$areaofinterest	=$RESULT[0]['area_of_interest'];
		$userType	=$RESULT[0]['user_from'];
		$englishexp=$RESULT[0]['years_eng_edu'];
		
		$sql = "SELECT * FROM user_center_map WHERE user_id=:user_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$centerId=$RESULT[0]['center_id'];
		$district_id=$RESULT[0]['district_id'];
		$tehsil_id=$RESULT[0]['tehsil_id'];
		
		$sql = "SELECT * FROM user_credential WHERE user_id=:user_id order by user_id DESC";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$loginid=$RESULT[0]['loginid'];
		$password=$RESULT[0]['password'];
		$is_active=$RESULT[0]['is_active'];
		$expiry_date=$RESULT[0]['expiry_date'];

		$sql = "SELECT default_batch_id from tblx_center WHERE center_id=:center_id ";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':center_id', $centerId, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$default_batch_id=$RESULT[0]['default_batch_id'];
		
		
	    //echo "<pre>"; print_r($RESULT); die;
		
		$sql = "SELECT * FROM address_master WHERE address_id=:address_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':address_id', $address_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$country_code=$RESULT[0]['country_code'];
		$phone=$RESULT[0]['phone'];
		$city=$RESULT[0]['city'];
		$state=$RESULT[0]['state'];
		$country=$RESULT[0]['country'];
		$postal_code=$RESULT[0]['postal_code'];
		$nationality=$RESULT[0]['nationality'];
		
		$sql = "SELECT * FROM asset WHERE asset_id=:asset_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':asset_id', $profile_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$profileName=$RESULT[0]['display_name']; 
		$profilePath=$RESULT[0]['path']; 
		$system_name=$RESULT[0]['system_name']; 
		
		$obj = new stdclass();
		$obj->batch_id = $list_batch;
		$obj->first_name = $first_name;
		$obj->last_name = $last_name;
		$obj->email_id = $email_id;
		$obj->password = $password;
		$obj->country_code = $country_code;
		$obj->phone = $phone;
		$obj->profile_id = $profile_id;
		$obj->system_name = $system_name;
		$obj->gender = $gender;
		$obj->dob = $dob;
		$obj->age_range = $age_range;
		$obj->marital_status = $marital_status;
		$obj->mother_tongue = $mother_tongue;
		$obj->education = $education;
		$obj->employment_status = $employment_status;
		$obj->joining_purpose = $joining_purpose;
		$obj->app_discovered = $app_discovered;
		$obj->city = $city;
		$obj->state = $state;
		$obj->country = $country;
		$obj->postal_code = $postal_code;
		$obj->nationality = $nationality;
		$obj->center_id = $centerId;
		$obj->district_id = $district_id;
		$obj->tehsil_id = $tehsil_id;
		$obj->education_label = $education_label;;
		
		$obj->course_id = $course_id;
		$obj->career = $career;
		$obj->area_of_interest = $areaofinterest;
		$obj->is_active = $is_active;
		$obj->expiry_date = $expiry_date;
		$obj->default_batch_id = $default_batch_id;
		$obj->loginid = $loginid;
		$obj->userType = $userType;
		$obj->englishexp = $englishexp; 
		
        //echo "<pre>";print_r($obj);exit;
		return $obj;
	}

//============= Get center admin detail by  user id and role id 
  public function getCenterAdminDataByID($uid,$roleID,$center_id){

		$sql = "SELECT uld.user_id FROM user_center_map ucm join user_role_map uld ON ucm.user_id = uld.user_id "
                . " WHERE  uld.user_id = ".$uid." AND uld.role_definition_id = ".$roleID." AND ucm.center_id = ".$center_id;
        $stmt = $this->dbConn->prepare($sql);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$user_id=$RESULT[0]['user_id'];
		

		$sql = "SELECT * FROM user WHERE user_id=:user_id order by user_id DESC";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$address_id=$RESULT[0]['address_id'];
		$first_name=$RESULT[0]['first_name'];
		$email_id=$RESULT[0]['email_id'];
		$qualification=$RESULT[0]['education'];
		$business_unit=$RESULT[0]['business_unit'];
		$profile_id=$RESULT[0]['profile_pic'];
        //echo "<pre>"; print_r($RESULT); die;
		
		$sql = "SELECT * FROM user_credential WHERE user_id=:user_id order by user_id DESC";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$password=$RESULT[0]['password'];
		$is_active=$RESULT[0]['is_active'];
		$loginid=$RESULT[0]['loginid'];
	    //echo "<pre>"; print_r($RESULT); die;
		
		$sql = "SELECT * FROM address_master WHERE address_id=:address_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':address_id', $address_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$phone=$RESULT[0]['phone'];
		
		$sql = "SELECT * FROM asset WHERE asset_id=:asset_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':asset_id', $profile_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$profileName=$RESULT[0]['display_name']; 
		$profilePath=$RESULT[0]['path']; 
		$system_name=$RESULT[0]['system_name']; 
		
		$obj = new stdclass();
		$obj->first_name = $first_name;
		$obj->email_id = $email_id;
		$obj->qualification = $qualification;
		$obj->business_unit = $business_unit;
		
		$obj->password = $password;
		$obj->phone = $phone;
		$obj->profile_id = $profile_id;
		$obj->system_name = $system_name;
		$obj->is_active = $is_active;
		$obj->expiry_date = $expiry_date;
		$obj->loginid = $loginid;
        //echo "<pre>";print_r($obj);exit;
		return $obj;
	}
			//============= bulk uplaod xml
	public function bulkDataInsert(array $request){
		$roleID = 2;
	//	echo "<pre>"; print_r($request); die;
		$center_id = isset($request['center_id']) ? trim($request['center_id']) : ""; 
		$district_id = isset($request['district_id']) ? trim($request['district_id']) : ""; 
		$tehsil_id = isset($request['tehsil_id']) ? trim($request['tehsil_id']) : ""; 
		$client_id = isset($request['client_id']) ? trim($request['client_id']) : "";
		
		if(empty($center_id)|| empty($client_id)){
			return false;
		}
		
		$first_name = isset($request['first_name']) ? $request['first_name'] : "";
		$last_name = isset($request['last_name']) ? $request['last_name'] : "";
        $email_id = isset($request['email_id']) ? trim($request['email_id']) : "";
        $is_email_verified = isset($request['is_email_verified']) ? trim($request['is_email_verified']) : "";
        $phone = isset($request['mobile']) ? trim($request['mobile']) : "";
        $is_phone_verified = isset($request['is_phone_verified']) ? trim($request['is_phone_verified']) : "";
		$password = isset($request['password']) ? trim($request['password']) : "";
		$batch = isset($request['batch']) ? trim($request['batch']) : "";
		$country_id = isset($request['country']) ? trim($request['country']) : "";
		$mother_tongue_id = isset($request['motherTongue']) ? trim($request['motherTongue']) : "";
		
		/* $gender = isset($request['gender']) ? trim($request['gender']) : "";
		$age_id = isset($request['age_id']) ? trim($request['age_id']) : "";
		$marital_id = isset($request['marital_id']) ? trim($request['marital_id']) : "";
		$country_id = isset($request['country_id']) ? trim($request['country_id']) : "";
		$state_id = isset($request['state_id']) ? trim($request['state_id']) : "";
		$city_id = isset($request['city_id']) ? trim($request['city_id']) : "";
		$pincode = isset($request['pincode']) ? trim($request['pincode']) : "";
		$nationality = isset($request['nationality']) ? trim($request['nationality']) : "";
		$mother_tongue_id = isset($request['mother_tongue_id']) ? trim($request['mother_tongue_id']) : "";
		$education_id = isset($request['education_id']) ? trim($request['education_id']) : "";
		$emp_status_id = isset($request['emp_status_id']) ? trim($request['emp_status_id']) : "";
		$perpose_join_id = isset($request['perpose_join_id']) ? trim($request['perpose_join_id']) : "";
		$discover_app_id = isset($request['discover_app_id']) ? trim($request['discover_app_id']) : ""; */
		

		
         $resEmail = $this->checkEmailExits($email_id);
		if(isset($resEmail['user_id']) && $resEmail['user_id']!='' && $resEmail['loginid']!=''){
		
		  return false;
			
		}else{
			//// Now Adding  user address 
			$stmt = $this->dbConn->prepare("INSERT INTO address_master(country, phone,is_phone_verified,updated_by,created_date) VALUES(:country,:phone,:is_phone_verified,".$_SESSION['user_id'].",NOW())");
			$stmt->bindValue(':phone', $phone, PDO::PARAM_STR);
			$stmt->bindValue(':is_phone_verified', $is_phone_verified, PDO::PARAM_STR);
			$stmt->bindValue(':country',$country_id, PDO::PARAM_STR);
			$stmt->execute();
			$address_id =$this->dbConn->lastInsertId();
			$stmt->closeCursor(); 
		  //  echo "<pre>";print_r($address_id);exit;
			 
			  //// Now Adding  Assest 
			$stmt = $this->dbConn->prepare("INSERT INTO asset(updated_by,created_date) VALUES('1',NOW())");
			$stmt->execute();
			$asset_id = $this->dbConn->lastInsertId();
			$stmt->closeCursor(); 
			// echo "<pre>";print_r($asset_id);exit;
			
		 //// Adding user 
			$stmt= $this->dbConn->prepare("insert into user(first_name,last_name,email_id,is_email_verified,address_id,profile_pic,mother_tongue,updated_by,created_date,user_client_id) values(:first_name,:last_name,:email_id,:is_email_verified,:address_id,:asset_id,:mother_tongue,".$_SESSION['user_id'].", NOW(),:client_id)");
			//echo "<pre>";print_r($stmt);exit;
			$stmt->bindValue(':first_name', $first_name, PDO::PARAM_STR);
			$stmt->bindValue(':last_name', $last_name, PDO::PARAM_STR);
			$stmt->bindValue(':email_id', $email_id, PDO::PARAM_STR);
			$stmt->bindValue(':is_email_verified', $is_email_verified, PDO::PARAM_INT);
			$stmt->bindValue(':address_id', $address_id, PDO::PARAM_INT);
			$stmt->bindValue(':asset_id', $asset_id, PDO::PARAM_INT);
			$stmt->bindValue(':mother_tongue', $mother_tongue_id, PDO::PARAM_INT);	
			$stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
			$stmt->execute();
			$user_id =$this->dbConn->lastInsertId();
			$stmt->closeCursor(); 
			// echo "<pre>";print_r($user_id);exit;
			//// Adding user and center map 
			$stmt = $this->dbConn->prepare("insert into user_center_map(user_id,center_id,client_id,created_date) values(:user_id,:center_id,:district_id,:tehsil_id,:client_id,NOW())");
			//echo "<pre>";print_r($stmt);exit;
			$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
			$stmt->bindValue(':district_id', $district_id, PDO::PARAM_INT);
			$stmt->bindValue(':tehsil_id', $tehsil_id, PDO::PARAM_INT);
			$stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor(); 
			
			//// Adding Admin Credentials 
			$stmt= $this->dbConn->prepare("insert into user_credential(user_id,loginid,password,updated_by,created_date) values(:user_id,:email_id,:password,1,NOW())");
			//echo "<pre>";print_r($stmt);exit;
			$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			$stmt->bindValue(':email_id', $email_id, PDO::PARAM_STR);
			$stmt->bindValue(':password', $password, PDO::PARAM_STR);
			
			$stmt->execute();
			$stmt->closeCursor(); 
			
			////Select the client to user group id */
			$stmt = $this->dbConn->prepare("Select user_group_id from client WHERE client_id='$client_id'");
				//echo "<pre>";print_r($stmt);exit;
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			//echo "<pre>";print_r($RESULT[0]['user_group_id']);exit;
			$client_group_id = $RESULT[0]['user_group_id'];
			$role_type="2";//student/learner
			
			//// Adding user into role map group 
			$stmt = $this->dbConn->prepare("insert into user_role_map(user_id,role_definition_id,user_group_id,is_active,updated_by,created_date) values(:user_id,:role_type,:client_group_id,1,1,NOW())");
			//echo "<pre>";print_r($stmt);exit;
			$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			$stmt->bindValue(':role_type', $role_type, PDO::PARAM_INT);
			$stmt->bindValue(':client_group_id', $client_group_id, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor(); 
			
			// For batch_user_map table insert
					
				$btUserMap_sql = "insert into tblx_batch_user_map (user_id, batch_id, center_id,status) values (:userID, :batchID, :center_id,1) ";
				
				$btUserMap = $this->dbConn->prepare( $btUserMap_sql );
				$btUserMap->bindValue(':userID', $user_id, PDO::PARAM_INT);
				$btUserMap->bindValue(':batchID', $batch, PDO::PARAM_INT);
				$btUserMap->bindValue(':center_id', $center_id, PDO::PARAM_INT);
				//echo "<pre>";print_r($btUserMap_sql);exit;
				$btUserMap->execute();
				$btUserMap->closeCursor();
			   // echo "<pre>";print_r($btUserMap);exit;
				return true;
         }
	}
   
	
	//=============  Get Acc data  methods by organization/center Id
	
	public function getAcessDataByID($center_id,$accessCode=""){
		 
		$sql = "Select * from tbl_access_codes WHERE organization_id = :centerID";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':centerID', $center_id, PDO::PARAM_INT);
		
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		return $RESULT;
			
		
	}
	//=============  Get Acc data  methods by organization/center Id with limit
	 public function getAcessDataByCenterIdAndAccessCode($cond_arr = array(), $start = 0, $limit = 10,$order,$dir){ 
 
		$whr="where 1=1";
 
		if($cond_arr['center_id']!=""){
			$whr.= " AND organization_id = '".$cond_arr['center_id']."'";
		}

		$sql = "Select count(DISTINCT access_code_id) as 'cnt' from tbl_access_codes $whr";
		$stmt = $this->dbConn->prepare($sql);	
		$stmt->execute();
		$RESULT_CNT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$row_cnt = array_shift( $RESULT_CNT );
		

		$limit_sql = '';
		if( !empty($limit) ){
			$limit_sql .= " LIMIT $start, $limit";
		}	

		$sql = "Select * from tbl_access_codes $whr ORDER BY ".$order." ".$dir." $limit_sql";
		$stmt = $this->dbConn->prepare($sql);	
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		
		return array('total' =>$row_cnt['cnt'] , 'result' => $RESULT);
   
   }
   
   
	public function getAcessCodeByCenter($center_id){
		 
		$sql = "Select access_code from tbl_access_codes WHERE organization_id = :centerID AND access_code_status='0' LIMIT 1";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':centerID', $center_id, PDO::PARAM_INT);
		
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		if(count($RESULT) > 0 ){
				return $RESULT[0]['access_code'];
			}else{
				return 0;
			} 
		
	 
	}
	public function getAccCountByCenter($center_id){
	
		$sql = "Select * from tbl_access_codes WHERE organization_id = :centerID";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':centerID', $center_id, PDO::PARAM_INT);
		
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		return $RESULT;
		
	}
	public function generateAccessCode($res){
		
	 // echo "<pre>";print_r($res);exit;
	   $client_id=$res->client_id;
	   $center_id=$res->center_id;
		
		////////////////////////Generating access codes for institue//////////////
		 for($i=1;$i<=$res->student_limit;$i++){
			$access_code=$this->random_strings();
			$stmt = $this->dbConn->prepare("INSERT INTO tbl_access_codes(access_code, client_id, organization_id, code_created_by, code_created_date, code_modified_date) VALUES(:access_code,:client_id,:center_id,1,NOW(),NOW())");
			$stmt->bindValue(':access_code', $access_code, PDO::PARAM_STR);
			$stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
			$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor(); 	
		  }
		return true;
		
	}
	
	
		//============= Create Region methods
	 public function createRegion($dataArr){
		  $regionName =$dataArr->regionName;
		  $regionDescription =$dataArr->regionDescription;
		  $region_logo =$dataArr->region_logo;
		  $tandc = $dataArr->tandc;
		  $policy = $dataArr->policy;
		  $faq = $dataArr->faq;
          //// Now Adding  Region 
        $sql = "INSERT INTO tblx_region(region_name,is_active,updated_by,created_date,region_description,region_logo,tandc_link,policy_link,faq_link) VALUES(:regionName, '1','1',NOW(),:regionDescription,:region_logo,:tandc,:policy,:faq)";
        // echo $sql; die;
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':regionName', $regionName, PDO::PARAM_STR);
		$stmt->bindValue(':regionDescription', $regionDescription, PDO::PARAM_STR);
		$stmt->bindValue(':region_logo', $region_logo, PDO::PARAM_STR);
		$stmt->bindValue(':tandc', $tandc, PDO::PARAM_STR);
		$stmt->bindValue(':policy', $policy, PDO::PARAM_STR);
		$stmt->bindValue(':faq', $faq, PDO::PARAM_STR);
		$stmt->execute();
		$regionID =$this->dbConn->lastInsertId();
		$stmt->closeCursor(); 
		$obj = new stdclass();
		$obj->regionID = $regionID;
		 return $obj;
		
		
    }
    
		
	//=============  Get region details methods
	public function getRegionDetails(){
		$sql = "SELECT * FROM tblx_region order by region_name";
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
	
		//Get Region Country map
	public function getRegionCountryMapDetails($region_id){

		$sql = "SELECT COUNT(*) as 'cnt' FROM tblx_region_country_map WHERE region_id=:region_id ";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':region_id', $region_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		return $RESULT;
	
	}
	public function getRegionCountryMapById($region_id){

		$sql = "SELECT * FROM tblx_region_country_map WHERE region_id=:region_id ";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':region_id', $region_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		return $RESULT;
	
	}
		//Delete Region Country map data
	public function deleteRegionCountryMapDetails($region_id){

		$sql = "DELETE  FROM tblx_region_country_map WHERE region_id=:region_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':region_id', $region_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();	
	
	}
	
	//=============  ADD  Region Country Map
	
	public function addRegionCountryMap($region_id,$country){
		try{
				$sql = "INSERT INTO tblx_region_country_map (region_id,country_name,created_date) values ( :region_id, :country,NOW())";
				$stmt = $this->dbConn->prepare($sql);
				$stmt->bindValue(':region_id', $region_id, PDO::PARAM_INT);
				$stmt->bindValue(':country', $country, PDO::PARAM_STR);
				$stmt->execute();
				$stmt->closeCursor(); 
			
			return true;	
		 }//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
	}
	
	
	public function getRegionProductMapById($region_id){

		$sql = "SELECT * FROM tblx_region_product_map WHERE region_id=:region_id ";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':region_id', $region_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		return $RESULT;
	
	}
		//Delete Region Product map data
	public function deleteRegionProductMapDetails($region_id){

		$sql = "DELETE  FROM tblx_region_product_map WHERE region_id=:region_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':region_id', $region_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();	
	
	}
	
//============= ADD  Region Product Map
	
	public function addRegionProductMap($region_id,$product){
		try{
				$sql = "INSERT INTO tblx_region_product_map (region_id,product_id,created_date) values (:region_id, :product_id,NOW())";
				$stmt = $this->dbConn->prepare($sql);
				$stmt->bindValue(':region_id', $region_id, PDO::PARAM_INT);
				$stmt->bindValue(':product_id', $product, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor(); 
			
			return true;	
		 }//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
	}
	
	//=============  Update region name methods by region Id

	
	public function updateRegionDataByID($dataArr){
		  $region_id =$dataArr->rId;
		  $regionName =$dataArr->regionName;
		  $regionDescription =$dataArr->regionDescription;
		  $region_logo =$dataArr->region_logo;
		  $tandc = $dataArr->tandc;
		  $policy = $dataArr->policy;
		  $faq = $dataArr->faq;
		try{
			if($region_logo != ''){
				$sql = "UPDATE tblx_region SET region_name=:regionName,region_description=:regionDescription,region_logo=:region_logo,tandc_link=:tandc,policy_link=:policy,faq_link=:faq WHERE id = :regionID";
			} else {
				$sql = "UPDATE tblx_region SET region_name=:regionName,region_description=:regionDescription,tandc_link=:tandc,policy_link=:policy,faq_link=:faq WHERE id = :regionID";
			}
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':regionID', $region_id, PDO::PARAM_INT);
			$stmt->bindValue(':regionName', $regionName, PDO::PARAM_STR);
			$stmt->bindValue(':regionDescription', $regionDescription, PDO::PARAM_STR);
			if($region_logo != ''){
			$stmt->bindValue(':region_logo', $region_logo, PDO::PARAM_STR);
			}
			$stmt->bindValue(':tandc', $tandc, PDO::PARAM_STR);
			$stmt->bindValue(':policy', $policy, PDO::PARAM_STR);
			$stmt->bindValue(':faq', $faq, PDO::PARAM_STR);
			$stmt->execute();
			$stmt->closeCursor();
			return true;	
		}//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
	}
	
   	//=============  Get region data  methods by region Id
	
	public function getRegionDataByID($region_id){
		try{ 
			$sql = "Select * from tblx_region WHERE id = :regionID";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':regionID', $region_id, PDO::PARAM_INT);
			
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			if(count($RESULT) > 0 ){
					return $RESULT;
				}else{
					return false;
				} 
		}//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
	}
	//=============  Get Acc data  methods by organization/center Id with limit
	 public function getRegionList($cond_arr = array(), $start = 0, $limit = 10,$order,$dir){  
 
		if($cond_arr['region_id']!=""){
		$whr.= "and region_id = '".$cond_arr['region_id']."'";
		}

		$sql = "Select count(*) as 'cnt' from tblx_region where is_active='1' $whr";
		//echo $sql;
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT_CNT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$row_cnt = array_shift( $RESULT_CNT );


		$limit_sql = '';
		if( !empty($limit) ){
		$limit_sql .= " LIMIT $start, $limit";
		}

		$sql = "Select * from  tblx_region where is_active='1' $whr   ORDER BY ".$order." ".$dir." $limit_sql";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();

		return array('total' =>$row_cnt['cnt'] , 'result' => $RESULT);
   
   }
   
  
 public function createRegionAdminOnline($res){
	   $client_id=$res->client_id;
	   $email_id=$res->email_id;
	   $password=$res->password;
       $region_id=$res->region;
	try{

	
		//// Now Adding  Admin address 
		$stmt = $this->dbConn->prepare("INSERT INTO address_master(address_line1, city, state, country, postal_code, phone, landline_no, updated_by,created_date) VALUES(:address,:city, :state,:country,:postal_code,:user_mobile,:center_phone,".$_SESSION['user_id'].",NOW())");
		$stmt->bindValue(':address', $res->address, PDO::PARAM_STR);
		$stmt->bindValue(':city', $res->city, PDO::PARAM_STR);
		$stmt->bindValue(':state', $res->state, PDO::PARAM_STR);
		$stmt->bindValue(':country', $res->country, PDO::PARAM_STR);
		$stmt->bindValue(':postal_code', $res->postal_code, PDO::PARAM_STR);
		$stmt->bindValue(':user_mobile', $res->user_mobile, PDO::PARAM_STR);
		$stmt->bindValue(':center_phone', $res->center_phone, PDO::PARAM_STR);
		$stmt->execute();
		$address_id =$this->dbConn->lastInsertId();
		$stmt->closeCursor(); 
         //echo "<pre>";print_r($address_id);exit;
		 
	      //// Now Adding  Assest 
		$stmt = $this->dbConn->prepare("INSERT INTO asset(updated_by,created_date) VALUES('1',NOW())");
		$stmt->execute();
		$asset_id = $this->dbConn->lastInsertId();
		$stmt->closeCursor(); 
		
		//// Now Adding  Admin Login 
		$stmt= $this->dbConn->prepare("insert into user(first_name,email_id,address_id,profile_pic,updated_by,created_date,user_client_id) values(:user_full_name,:email_id,:address_id,:asset_id,".$_SESSION['user_id'].", NOW(),:client_id)");
		//echo "<pre>";print_r($stmt);exit;
		$stmt->bindValue(':user_full_name', $res->user_full_name, PDO::PARAM_STR);
		$stmt->bindValue(':email_id', $email_id, PDO::PARAM_STR);
		$stmt->bindValue(':address_id', $address_id, PDO::PARAM_STR);
		$stmt->bindValue(':asset_id', $asset_id, PDO::PARAM_INT);
		$stmt->bindValue(':client_id', $res->client_id, PDO::PARAM_INT);
		$stmt->execute();
		$user_id =$this->dbConn->lastInsertId();
		$stmt->closeCursor(); 
		
	    //// Adding  region and user map 
		$stmt = $this->dbConn->prepare("insert into tblx_region_user_map(region_id,user_id,client_id,created_date) values(:region_id,:user_id,:client_id,NOW())");
			//echo "<pre>";print_r($stmt);exit;
		$stmt->bindValue(':region_id', $region_id, PDO::PARAM_INT);
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->bindValue(':client_id', $res->client_id, PDO::PARAM_INT);
		$stmt->execute();
		$stmt->closeCursor();
		
		//// Adding Admin Credentials 
		$stmt= $this->dbConn->prepare("insert into user_credential(user_id,loginid,password,updated_by,created_date) values(:user_id,:email_id,:password,1,NOW())");
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->bindValue(':email_id', $res->email_id, PDO::PARAM_STR);
		$stmt->bindValue(':password', $res->password, PDO::PARAM_STR);
		$stmt->execute();
		$stmt->closeCursor(); 
		
		////Select the client to user group id */
		$stmt = $this->dbConn->prepare("Select user_group_id from client WHERE client_id=:client_id");
			//echo "<pre>";print_r($stmt);exit;
		$stmt->bindValue(':client_id', $res->client_id, PDO::PARAM_INT);
		$stmt->execute();
	    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$client_group_id = $RESULT[0]['user_group_id'];

		
		//// Adding Region Admin into role map group 
		$role_type="7";//region  Admin
		$stmt = $this->dbConn->prepare("insert into user_role_map(user_id,role_definition_id,user_group_id,is_active,updated_by,created_date) values(:user_id,:role_type,:client_group_id,1,1,NOW())");
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->bindValue(':role_type', $role_type, PDO::PARAM_INT);
		$stmt->bindValue(':client_group_id', $client_group_id, PDO::PARAM_INT);
		$stmt->execute();
		$stmt->closeCursor(); 

		$obj = new stdclass();
		$obj->address_id = $address_id;
		$obj->user_id = $user_id;
		$obj->user_group_id = $client_group_id;
		$obj->region_id = $region_id;
		$obj->client_id = $res->client_id;
		$obj->name = $res->name;
		$obj->description = $res->user_full_name;
		$obj->email_id = $res->email_id;
		$obj->mobile = $res->user_mobile;
		$obj->phone = $res->center_phone;
		$obj->password = $res->password;

        $obj->address1 = $res->address;
		$obj->city = $res->city;
		$obj->state = $res->state;
		$obj->country = $res->country;
		$obj->pincode = $res->postal_code;
       // echo "<pre>";print_r($obj);exit;
		return $obj;
	  }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
  }
 
  //============= Get regionadmin detail 
  public function getRegionAdminDetails($cond_arr = array(), $start = 0, $limit = 10,$order,$dir){ 

		$whr = "where 1=1 AND urm.role_definition_id =7 ";
			

			if($cond_arr['region_id']!="" && $cond_arr['region_id']!='All'){
				$whr.= " AND trum.region_id = '".$cond_arr['region_id']."'";
			}
			
			if($cond_arr['student_id']!=""){
			$whr.= " AND us.user_id = '".$cond_arr['student_id']."'";
				//$whr.= " AND tc.country = '$country'";
			}
			
			if($cond_arr['status']!=""  || $cond_arr['status']=='0'){
				//$whr.= " AND uc1.is_active = '$status'";
				//$whr.= " AND tc.country = '$country'";
			}

			if($cond_arr['student_txt']!="" && $cond_arr['student_id']==""){
				$whr.= " AND ((us.first_name LIKE '%".$cond_arr['student_txt']."%' or us.last_name LIKE '%".$cond_arr['student_txt']."%'  or CONCAT(us.first_name,'',us.last_name ) LIKE  '%".$cond_arr['student_txt']."%') OR (us.email_id LIKE '%".$cond_arr['student_txt']."%') OR (uc.loginid LIKE '%".$cond_arr['student_txt']."%'))";
			}
		
		
		$limit_sql = '';
		if( !empty($limit) ){
		$limit_sql .= " LIMIT $start, $limit";
		}
		//get count 
		 $sql = "SELECT count(DISTINCT us.user_id) as 'cnt' FROM user_credential uc JOIN user_role_map urm ON urm.user_id = uc.user_id JOIN user us ON us.user_id = uc.user_id JOIN tblx_region_user_map trum ON us.user_id=trum.user_id JOIN tblx_region tr ON trum.region_id=tr.id  $whr ";
		// echo "<pre>"; print_r($sql); die;
		$stmt = $this->dbConn->prepare($sql);
		//$stmt->bindValue(':regionAdminRole', $regionAdminRole, PDO::PARAM_INT);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$cnt = $RESULT[0]['cnt'];
		
		$sql = "SELECT uc.user_id, role_definition_id, uc.is_active, uc.expiry_date, urm.user_group_id, us.user_client_id, us.firstTime_login , tr.region_name FROM user_credential uc JOIN user_role_map urm ON urm.user_id = uc.user_id JOIN user us ON us.user_id = uc.user_id JOIN tblx_region_user_map trum ON us.user_id=trum.user_id JOIN tblx_region tr ON trum.region_id=tr.id $whr ORDER BY ".$order." ".$dir." $limit_sql";
		// echo "<pre>"; print_r($sql); die;
		$stmt = $this->dbConn->prepare($sql);
		//$stmt->bindValue(':regionAdminRole', $regionAdminRole, PDO::PARAM_INT);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();	
		$user_id=$RESULT[0]['user_id'];
		
		
		$regionadmin_arr = array();
		foreach($RESULT as $key=>$val) {

			$bcm = new stdClass();
			$bcm->user_id = $val['user_id'];
			array_push($regionadmin_arr,$bcm);
		}
		 
		$admin_List_arr=array();
		foreach($regionadmin_arr as $key=>$val){
			
			$user_id = $val->user_id;
			$sql = "SELECT * FROM user WHERE user_id=:user_id order by user_id DESC";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':user_id', $val->user_id, PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			$address_id=$RESULT[0]['address_id'];
			$first_name=$RESULT[0]['first_name'];
			$last_name=$RESULT[0]['last_name'];
			$email_id=$RESULT[0]['email_id'];
			$profile_id=$RESULT[0]['profile_pic'];
			//echo "<pre>"; print_r($RESULT); die;
			
			$sql = "SELECT * FROM user_credential WHERE user_id=:user_id order by user_id DESC";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			$password=$RESULT[0]['password'];
			$loginid=$RESULT[0]['loginid'];
			$is_active=$RESULT[0]['is_active'];
			
			//echo "<pre>"; print_r($RESULT); die;
			
			$sql = "SELECT * FROM address_master WHERE address_id=:address_id";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':address_id', $address_id, PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			$phone=$RESULT[0]['phone'];
			$gender=$RESULT[0]['gender'];
			
			$sql = "SELECT * FROM asset WHERE asset_id=:asset_id";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':asset_id', $profile_id, PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			$profileName=$RESULT[0]['display_name']; 
			$profilePath=$RESULT[0]['path']; 
			$system_name=$RESULT[0]['system_name']; 

			$sql = "SELECT * FROM tblx_region_user_map WHERE user_id=:user_id";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			$region_id=$RESULT[0]['region_id']; 
			
			
			$obj = new stdclass();
			$obj->user_id = $user_id;
			$obj->first_name = $first_name;
			$obj->email_id = $email_id;
			$obj->password = $password;
			$obj->phone = $phone;
			$obj->profile_id = $profile_id;
			$obj->system_name = $system_name;
			$obj->region_id = $region_id; 
			$obj->loginid = $loginid; 
			$admin_List_arr[]=$obj;
			//$obj->company = $company;
			//$obj1 = (object) $obj;
			//echo "<pre>";print_r($obj);exit;
		
		}
	
		return array('total' =>$cnt , 'result' => $admin_List_arr);
	
	
	
	
	}

public function getRegionAdminCount($cond_arr = array()){ 

		$whr = "where 1=1 AND urm.role_definition_id =7 ";
			

			if($cond_arr['region_id']!="" && $cond_arr['region_id']!='All'){
				$whr.= " AND trum.region_id = '".$cond_arr['region_id']."'";
			}
			
			if($cond_arr['student_id']!=""){
			$whr.= " AND us.user_id = '".$cond_arr['student_id']."'";
				//$whr.= " AND tc.country = '$country'";
			}
			
			if($cond_arr['status']!=""  || $cond_arr['status']=='0'){
				//$whr.= " AND uc1.is_active = '$status'";
				//$whr.= " AND tc.country = '$country'";
			}

		
		
		//get count 
		 $sql = "SELECT count(DISTINCT us.user_id) as 'cnt' FROM user_credential uc JOIN user_role_map urm ON urm.user_id = uc.user_id JOIN user us ON us.user_id = uc.user_id JOIN tblx_region_user_map trum ON us.user_id=trum.user_id JOIN tblx_region tr ON trum.region_id=tr.id  $whr ";
		// echo "<pre>"; print_r($sql); die;
		$stmt = $this->dbConn->prepare($sql);
		//$stmt->bindValue(':regionAdminRole', $regionAdminRole, PDO::PARAM_INT);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$cnt = $RESULT[0]['cnt'];
		
		return array('total' =>$cnt);
	
	
	
	
	}
	
	public function getRegionUserDetail($user_id){
       $sql = "SELECT uc.user_id, role_definition_id, uc.is_active, uc.expiry_date, urm.user_group_id, us.user_client_id, us.firstTime_login FROM user_credential uc JOIN user_role_map urm ON urm.user_id = uc.user_id JOIN user us ON us.user_id = uc.user_id WHERE uc.user_id=:user_id and urm.role_definition_id =7";
		// echo "<pre>"; print_r($sql); die;
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();	
		$user_group_id=$RESULT[0]['user_group_id'];
       
	   
	   
		$sql = "SELECT * FROM user WHERE user_id=:user_id order by user_id DESC";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$address_id=$RESULT[0]['address_id'];
		$first_name=$RESULT[0]['first_name'];
		$email_id=$RESULT[0]['email_id'];
		$profile_id=$RESULT[0]['profile_pic'];
        //echo "<pre>"; print_r($RESULT); die;
		
		$sql = "SELECT * FROM user_credential WHERE user_id=:user_id order by user_id DESC";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$password=$RESULT[0]['password'];
		$loginid=$RESULT[0]['loginid'];
		$is_active=$RESULT[0]['is_active'];
	    //echo "<pre>"; print_r($RESULT); die;
		
		$sql = "SELECT * FROM address_master WHERE address_id=:address_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':address_id', $address_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$phone=$RESULT[0]['phone'];
		$landline_no=$RESULT[0]['landline_no'];
		$country=$RESULT[0]['country'];
		$state=$RESULT[0]['state'];
		$city=$RESULT[0]['city'];
		$pincode=$RESULT[0]['postal_code'];
		$address1=$RESULT[0]['address_line1'];
		
		$sql = "SELECT * FROM asset WHERE asset_id=:asset_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':asset_id', $profile_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$profileName=$RESULT[0]['display_name']; 
		$profilePath=$RESULT[0]['path']; 
		$system_name=$RESULT[0]['system_name']; 

		$sql = "SELECT * FROM tblx_region_user_map WHERE user_id=:user_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$region_id=$RESULT[0]['region_id']; 
		
		$obj = new stdclass();
		$obj->user_id = $user_id;
		$obj->first_name = $first_name;
		$obj->last_name = $last_name;
		$obj->email_id = $email_id;
		$obj->password = $password;
		$obj->profile_id = $profile_id;
		$obj->system_name = $system_name;
		$obj->date_of_birth = $date_of_birth;
		$obj->roleName = $roleName;
		$obj->address_id = $address_id;
		$obj->profile_pic = $profile_pic;
		$obj->user_group_id = $user_group_id;
		$obj->system_name = $system_name;
		$obj->user_client_id = $user_client_id;
		$obj->firstTime_login = $firstTime_login;
		$obj->is_active = $is_active;
		$obj->country=$country;
		$obj->state=$state;
		$obj->city=$city;
		$obj->pincode=$pincode;
		$obj->address1=$address1; 
		$obj->region_id = $region_id; 
		$obj->loginid = $loginid; 
		$obj->phone = $phone; 
		$obj->landline_no = $landline_no; 
		
		return $obj;
	}
	
 public function updateRegionAdminOnline($dataArr,$user_id){
      
		$user_id =$dataArr->user_id;
		$address_id =$dataArr->address_id;
		$user_group_id =$dataArr->user_group_id;
		$email_id = $dataArr->email_id;
		$password = $dataArr->password;
		
	 try{

		  //// Update User
		     $sql = "UPDATE user SET first_name=:user_full_name, modified_date = NOW()  where user_id = :user_id";
			  $stmt = $this->dbConn->prepare($sql);	
			  $stmt->bindValue(':user_full_name', $dataArr->user_full_name, PDO::PARAM_STR);
			  $stmt->bindValue(':user_id', $dataArr->user_id, PDO::PARAM_INT);			  
			  $stmt->execute();
			  $stmt->closeCursor();
			  
			  //// Update region
			  $sql = "UPDATE tblx_region_user_map SET region_id=:region, modified_date = NOW()  where user_id = :user_id";
			  $stmt = $this->dbConn->prepare($sql);	
			  $stmt->bindValue(':region', $dataArr->region, PDO::PARAM_INT);	
			  $stmt->bindValue(':user_id', $dataArr->user_id, PDO::PARAM_INT);				  
			  $stmt->execute();
			  $stmt->closeCursor(); 
			  
			//// update login Credentials 
		   if($password!=''){
				$stmt= $this->dbConn->prepare("UPDATE user_credential SET password=:password ,modified_date=NOW() WHERE user_id=:user_id");
			  // echo "<pre>";print_r($stmt);exit;
				 $stmt->bindValue(':password',$password, PDO::PARAM_STR);
				 $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
				 $stmt->execute();
				 $stmt->closeCursor();
		   }
			  
		     //// Update Address, Phone
		   $sql = "UPDATE address_master SET phone = :user_mobile, landline_no = :center_phone, address_line1 = :address, country = :country, state = :state, city = :city, postal_code = :postal_code, modified_date = NOW()  where address_id = :address_id";
			  $stmt = $this->dbConn->prepare($sql);	
			  $stmt->bindValue(':user_mobile', $dataArr->user_mobile, PDO::PARAM_STR);	
			  $stmt->bindValue(':center_phone', $dataArr->center_phone, PDO::PARAM_STR);	
			  $stmt->bindValue(':address', $dataArr->address, PDO::PARAM_STR);	
			  $stmt->bindValue(':country', $dataArr->country, PDO::PARAM_STR);	
			  $stmt->bindValue(':state', $dataArr->state, PDO::PARAM_STR);	
			  $stmt->bindValue(':city', $dataArr->city, PDO::PARAM_STR);	
			  $stmt->bindValue(':postal_code', $dataArr->postal_code, PDO::PARAM_STR);	
			  $stmt->bindValue(':address_id', $address_id, PDO::PARAM_INT);	
			  $stmt->execute();
			  $stmt->closeCursor(); 
			  return true;
		  }//catch exception
			   catch(Exception $e) {
			  echo 'Message: ' .$e->getMessage();exit;
			}
         
	}
	
		//=============  Get region details methods
	public function getRegionDetailsByUserId($user_id){
		$sql = "SELECT * FROM tblx_region_user_map where user_id=:user_id";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		if(count($RESULT) > 0 ){
				return $RESULT;
			}else{
				return false;
			} 
	}
	  
   public function getCenterByClientCountry($client_id,$country=''){ 
    if($country!=""){
		$whr.= " AND country LIKE '%$country%' ";
	}
	$sql = "Select * from tblx_center where client_id = :client_id $whr and status=1 order by center_id DESC";
	  //select Center Online live server Database
	 $stmt = $this->dbConn->prepare($sql);
	 $stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);		 
     $stmt->execute();
     $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
     $stmt->closeCursor();
	//echo "<pre>";print_r($RESULT);
	 if(count($RESULT) > 0 ){
				return $RESULT;
			}else{
				return false;
			} 
   }

  /* Function search AcessData By CenterId And AccessCode*/
  	 public function searchAcessDataByCenterIdAndAccessCode($cond_arr = array(), $start = 0, $limit = 10,$order,$dir){ 
 
		$whr="where 1=1";
 		if($cond_arr['region_id']!="" && $cond_arr['region_id']!="All"){
			$whr.= " AND trcm.region_id = '".$cond_arr['region_id']."'";
		}
		if($cond_arr['center_id']!="" && $cond_arr['center_id']!="All"){
			$whr.= " AND ta.organization_id = '".$cond_arr['center_id']."'";
		}
		if($cond_arr['accessCode']!=""){
		$whr.= " AND ta.access_code LIKE '%".$cond_arr['accessCode']."%'";
		}

		if($cond_arr['status']!=""){
		$whr.= " AND ta.access_code_status ='".$cond_arr['status']."'";
		}

		$sql = "Select count(DISTINCT ta.access_code_id) as 'cnt' from tbl_access_codes AS ta  
		JOIN tblx_center AS tc ON tc.center_id = ta.organization_id
		LEFT JOIN tblx_region_country_map AS trcm ON trcm.country_name = tc.country
		$whr";
		$stmt = $this->dbConn->prepare($sql);	
		$stmt->execute();
		$RESULT_CNT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$row_cnt = array_shift( $RESULT_CNT );
		

		$limit_sql = '';
		if( !empty($limit) ){
			$limit_sql .= " LIMIT $start, $limit";
		}	

		$sql = "Select DISTINCT(ta.access_code),ta.* from tbl_access_codes AS ta  
		JOIN tblx_center AS tc ON tc.center_id = ta.organization_id
		LEFT JOIN tblx_region_country_map AS trcm ON trcm.country_name = tc.country $whr  ORDER BY ".$order." ".$dir." $limit_sql";
		$stmt = $this->dbConn->prepare($sql);	
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		
		return array('total' =>$row_cnt['cnt'] , 'result' => $RESULT);
   
   } 


 public function getAccessCodeBySearchkey($center_id='', $searchKey ='', $region_id='',$status =''){
 		$whr="where 1=1";
 		if($center_id !='' && $center_id!="All"){
 			$whr.= " AND ta.organization_id = '".$center_id."'";
 		}
 		if($searchKey !=''){
 		$whr.= " AND ta.access_code LIKE '%".$searchKey."%'";
 		}
		if($region_id!="" && $region_id!="All"){
			$whr.= " AND trcm.region_id = '".$region_id."'";
		}
		if($status!=""){
		$whr.= " AND ta.access_code_status ='".$status."'";
		}

		
		
		
		
		$sql = "Select ta.* from tbl_access_codes AS ta  
		JOIN tblx_center AS tc ON tc.center_id = ta.organization_id
		LEFT JOIN tblx_region_country_map AS trcm ON trcm.country_name = tc.country  $whr GROUP BY ta.access_code";
		$stmt = $this->dbConn->prepare($sql);	
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$accessCodeArr = array();
		 while($row = array_shift( $RESULT )) {
				$code = new stdClass();
				$code->access_code = $row['access_code'];
				array_push($accessCodeArr,$code);
			
		}
		return $accessCodeArr;
	 } 
       /* access Code count inActive Student*/
    public function getInactiveStudentsDetail($center_id)
    {
    	//$sql = "SELECT count(*) as total_inactive FROM user_credential as u join user_center_map as ucm on ucm.user_id = u.user_id where u.is_active=:status and ucm.center_id = :center_id";
		$sql = "SELECT count(*) as total_inactive FROM user_credential as u join user_center_map as ucm on ucm.user_id = u.user_id join tbl_access_codes as uac on uac.code_used_by_id = u.user_id where u.is_active=:status and ucm.center_id = :center_id";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':status', 0, PDO::PARAM_INT);
		$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
        $stmt->execute();
        $centerResult = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		if(count($centerResult) > 0 ){
			return $centerResult;
		} else {
			return false;
		}
    }
    /* access Code count inActive Student Ends code*/
	
//============= Create batch details methods
	 public function createCenterBatchdetails($bid,$centerId,$product_id,$levellist,$modulelist,$chapterlist){
		 //echo "<pre>";print_r($batchType);exit;
		$cCode = 'CN-'.$centerId;
		$bcode = $cCode.'-'.$bid;
		 
		$sql = "INSERT INTO tblx_product_configuration(entity_type,institute_id, batch_id, batch_code, product_id,course,topic,chapter) VALUES('batch',:centerId, :bid,:bcode,:product_id,:levellist,:modulelist,:chapterlist)";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':centerId', $centerId, PDO::PARAM_INT);		
		$stmt->bindValue(':bid', $bid, PDO::PARAM_INT);		
		$stmt->bindValue(':bcode', $bcode, PDO::PARAM_STR);	
		$stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);		
		$stmt->bindValue(':levellist', $levellist, PDO::PARAM_STR);
		$stmt->bindValue(':modulelist', $modulelist, PDO::PARAM_STR);
		$stmt->bindValue(':chapterlist', $chapterlist, PDO::PARAM_STR);				
		$stmt->execute();
		$stmt->closeCursor(); 

		 return true;			 
    } 

//=============  Update batch details 
	
	public function updateBatchDataByDetails($batch_id,$centerId,$product_id,$levellist,$modulelist,$chapterlist){
		try{
			
			$cCode = 'CN-'.$centerId;
			$bcode = $cCode.'-'.$batch_id;
			$whr='';
			if($product_id!=''){
			  $whr.= 'AND product_id IN('.$product_id.')';			  
			}
		  
			$sql = "Select * from tblx_product_configuration WHERE batch_id = :batch_id AND institute_id=:centerId ".$whr."";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);	
			$stmt->bindValue(':centerId', $centerId, PDO::PARAM_INT);						
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();

		   if(count($RESULT) > 0 ){
				$sql = "UPDATE tblx_product_configuration SET course=:levellist,topic=:modulelist,chapter=:chapterlist WHERE batch_id = :batchID AND institute_id=:center_id AND product_id=:product_id";
				$stmt = $this->dbConn->prepare($sql);
				$stmt->bindValue(':batchID', $batch_id, PDO::PARAM_INT);
				$stmt->bindValue(':center_id', $centerId, PDO::PARAM_INT);
				$stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
				$stmt->bindValue(':levellist', $levellist, PDO::PARAM_STR);
				$stmt->bindValue(':modulelist', $modulelist, PDO::PARAM_STR);
				$stmt->bindValue(':chapterlist', $chapterlist, PDO::PARAM_STR);
				$stmt->execute();
				$stmt->closeCursor();	
		    }else{
			
				$sql = "INSERT INTO tblx_product_configuration(entity_type,institute_id, batch_id, batch_code, product_id,course,topic,chapter) VALUES('batch',:centerId, :bid,:bcode,:product_id,:levellist,:modulelist,:chapterlist)";
				$stmt = $this->dbConn->prepare($sql);
				$stmt->bindValue(':centerId', $centerId, PDO::PARAM_INT);		
				$stmt->bindValue(':bid', $batch_id, PDO::PARAM_INT);		
				$stmt->bindValue(':bcode', $bcode, PDO::PARAM_STR);	
				$stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);		
				$stmt->bindValue(':levellist', $levellist, PDO::PARAM_STR);
				$stmt->bindValue(':modulelist', $modulelist, PDO::PARAM_STR);
				$stmt->bindValue(':chapterlist', $chapterlist, PDO::PARAM_STR);				
				$stmt->execute();
				$stmt->closeCursor(); 
			} 
		}//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
	}	
	
public function getBatchDataByIDDetails($batch_id,$centerId,$product_id){
		try{ 
		
		
		    $whr='';
			if($product_id!=''){
			  $whr.= 'AND product_id IN('.$product_id.')';			  
			}
		 
			$sql = "Select * from tblx_product_configuration WHERE batch_id = :batchID AND institute_id = :centerID ".$whr."";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':batchID', $batch_id, PDO::PARAM_INT);
			$stmt->bindValue(':centerID', $centerId, PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			if(count($RESULT) > 0 ){
					return $RESULT;
				}else{
					return false;
				} 
		}//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
	}
	public function deleteBatchDataByDetails($batch_id,$centerId,$product_id){
		try{ 
		//echo "<pre>";print_r($centerId);exit;
		    $whr='';
			if($product_id!=''){
			  $whr.= 'AND product_id IN('.$product_id.')';			  
			}
		 
			$sql = "delete from tblx_product_configuration WHERE batch_id = :batchID AND institute_id = :centerID ".$whr."";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':batchID', $batch_id, PDO::PARAM_INT);
			$stmt->bindValue(':centerID', $centerId, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();
			return true;
				
		}//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
	}
	
	
   	//=============  Get batch data  methods by batch Id

	public function createCenterAdminOnline($res){
		// echo "<pre>";print_r($res);exit;
		  $client_id=$res->client_id;
		  $email_id=$res->email_id;
		  $password=$res->password;
		  $center_id=$res->center;
	   try{
   
		   //// Now Adding  Admin address 
		   $stmt = $this->dbConn->prepare("INSERT INTO address_master(address_line1, city, state, country, postal_code, phone, landline_no, updated_by,created_date) VALUES(:address,:city, :state,:country,:postal_code,:user_mobile,:center_phone,".$_SESSION['user_id'].",NOW())");
		   $stmt->bindValue(':address', $res->address, PDO::PARAM_STR);	
		   $stmt->bindValue(':city', $res->city, PDO::PARAM_STR);	
		   $stmt->bindValue(':state', $res->state, PDO::PARAM_STR);	
		   $stmt->bindValue(':country', $res->country, PDO::PARAM_STR);	
		   $stmt->bindValue(':postal_code', $res->postal_code, PDO::PARAM_STR);	
		   $stmt->bindValue(':user_mobile', $res->user_mobile, PDO::PARAM_STR);	
		   $stmt->bindValue(':center_phone', $res->center_phone, PDO::PARAM_STR);
		   $stmt->execute();
		   $address_id =$this->dbConn->lastInsertId();
		   $stmt->closeCursor(); 
			//echo "<pre>";print_r($address_id);exit;
			
			 //// Now Adding  Assest 
		   $stmt = $this->dbConn->prepare("INSERT INTO asset(updated_by,created_date) VALUES('1',NOW())");
		   $stmt->execute();
		   $asset_id = $this->dbConn->lastInsertId();
		   $stmt->closeCursor(); 
		   
		   //// Now Adding  Admin Login 
		   $stmt= $this->dbConn->prepare("insert into user(first_name,email_id,address_id,profile_pic,updated_by,created_date,user_client_id) values(:user_full_name,:email_id,:address_id,:asset_id,".$_SESSION['user_id'].", NOW(),:client_id)");
		   //echo "<pre>";print_r($stmt);exit;
		   $stmt->bindValue(':user_full_name', $res->user_full_name, PDO::PARAM_STR);
		   $stmt->bindValue(':email_id', $email_id, PDO::PARAM_STR);
		   $stmt->bindValue(':address_id', $address_id, PDO::PARAM_INT);
		   $stmt->bindValue(':asset_id', $asset_id, PDO::PARAM_INT);
		   $stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
		   $stmt->execute();
		   $user_id =$this->dbConn->lastInsertId();
		   $stmt->closeCursor(); 
		   
		   //// Adding  center and user map 
		   $stmt = $this->dbConn->prepare("insert into user_center_map(center_id,user_id,client_id,created_date) values(:center_id,:user_id,:client_id,NOW())");
			   //echo "<pre>";print_r($stmt);exit;
		   $stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
		   $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		   $stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
		   $stmt->execute();
		   $stmt->closeCursor();
		   
		   //// Adding Admin Credentials 
		   $stmt= $this->dbConn->prepare("insert into user_credential(user_id,loginid,password,updated_by,created_date) values(:user_id,:email_id,:password,1,NOW())");
		   $stmt->bindValue(':email_id', $email_id, PDO::PARAM_STR);
		   $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		   $stmt->bindValue(':password', $password, PDO::PARAM_STR);
		   $stmt->execute();
		   $stmt->closeCursor(); 
		   
		   ////Select the client to user group id */
		   $stmt = $this->dbConn->prepare("Select user_group_id from client WHERE client_id='$client_id'");
			   //echo "<pre>";print_r($stmt);exit;
		   $stmt->execute();
		   $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		   $stmt->closeCursor();
		   $client_group_id = $RESULT[0]['user_group_id'];
   
		   
		   //// Adding Region Admin into role map group 
		   $role_type="4";//region  Admin
		   $stmt = $this->dbConn->prepare("insert into user_role_map(user_id,role_definition_id,user_group_id,is_active,updated_by,created_date) values(:user_id,:role_type,:client_group_id,1,1,NOW())");
		   $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		   $stmt->bindValue(':role_type', $role_type, PDO::PARAM_INT);
		   $stmt->bindValue(':client_group_id', $client_group_id, PDO::PARAM_INT);
		   $stmt->execute();
		   $stmt->closeCursor(); 
   
		   $obj = new stdclass();
		   $obj->address_id = $address_id;
		   $obj->user_id = $user_id;
		   $obj->user_group_id = $client_group_id;
		   $obj->center_id = $center_id;
		   $obj->client_id = $res->client_id;
		   $obj->name = $res->name;
		   $obj->description = $res->user_full_name;
		   $obj->email_id = $res->email_id;
		   $obj->mobile = $res->user_mobile;
		   $obj->phone = $res->center_phone;
		   $obj->password = $res->password;
   
		   $obj->address1 = $res->address;
		   $obj->city = $res->city;
		   $obj->state = $res->state;
		   $obj->country = $res->country;
		   $obj->pincode = $res->postal_code;
		   //echo "<pre>";print_r($obj);exit;
		   return $obj;
		 }//catch exception
		   catch(Exception $e) {
			 echo 'Message: ' .$e->getMessage();exit;
		   }
	 }

	 //============= Get center admin detail 
	 public function getCenterAdminDetails($cond_arr = array(), $start = 0, $limit = 10,$order,$dir){ 

		$whr = "where 1=1";
			$whr.= " and urm.role_definition_id='".$cond_arr['role_id']."' and u1.user_client_id='".$cond_arr['client_id']."'";  

			if($cond_arr['country']!="" && $cond_arr['country']!='All'){
				$whr.= " AND tc.country = '".$cond_arr['country']."'";
			}
			if($cond_arr['center_id']!="" && $cond_arr['center_id']!='All'){
				$whr.= " AND ucm.center_id = '".$cond_arr['center_id']."'";
			}
			
			if(isset($cond_arr['cadmin_id'])){
				$whr.= " AND u1.user_id = '".$cond_arr['cadmin_id']."'";
			}
						
			if($cond_arr['region_id']!="" && $cond_arr['region_id']!='All'){
				$whr.= " AND tr.id = '".$cond_arr['region_id']."'";
			}
			
			if($cond_arr['cadmin_txt']!=""){
				$whr.= " AND ((u1.first_name LIKE '%".$cond_arr['cadmin_txt']."%' or u1.last_name LIKE '%".$cond_arr['cadmin_txt']."%'  or CONCAT(u1.first_name,'',u1.last_name ) LIKE  '%".$cond_arr['cadmin_txt']."%') OR (u1.email_id LIKE '%".$cond_arr['cadmin_txt']."%') OR (uc1.loginid LIKE '%".$cond_arr['cadmin_txt']."%'))";
			}

			$limit_sql = '';
			if( !empty($limit) ){
				$limit_sql .= " LIMIT $start, $limit";
			}

			$sql = "Select count(DISTINCT u1.user_id) as 'cnt' FROM user u1 JOIN user_credential uc1 ON u1.user_id=uc1.user_id JOIN user_role_map urm on u1.user_id=urm.user_id JOIN address_master am on u1.address_id=am.address_id JOIN user_center_map ucm on u1.user_id=ucm.user_id JOIN tblx_center tc on ucm.center_id=tc.center_id LEFT JOIN tblx_region tr ON tc.region=tr.id $whr "; 
			//echo $sql;
			$stmt = $this->dbConn->prepare($sql);	
			$stmt->execute();
			$RESULT_CNT = $stmt->fetchAll(PDO::FETCH_ASSOC); 
			$stmt->closeCursor();
			$row_cnt = array_shift( $RESULT_CNT );

			 $sql = "SELECT DISTINCT u1.user_id, u1.address_id, uc1.loginid, uc1.created_date, uc1.expiry_date, u1.first_name, u1.last_name, u1.email_id, uc1.is_active,tc.center_id,tc.name,u1.gender,am.phone,am.country,u1.mother_tongue,u1.user_from FROM user u1 JOIN user_credential uc1 ON 
			 u1.user_id=uc1.user_id JOIN user_center_map ucm on u1.user_id=ucm.user_id JOIN user_role_map urm on u1.user_id=urm.user_id JOIN address_master am on u1.address_id=am.address_id JOIN tblx_center tc on ucm.center_id=tc.center_id LEFT JOIN tblx_region tr ON tc.region=tr.id $whr  ORDER BY ".$order." ".$dir." $limit_sql";
			//echo $sql;
			$stmt = $this->dbConn->prepare($sql);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			$centerAdminList = array();
			while($row = array_shift( $RESULT )) {
				$bcm = new stdClass();
				$bcm->first_name = $row['first_name'];
				$bcm->last_name = $row['last_name'];
				$bcm->email_id = $row['email_id'];
				$bcm->user_id = $row['user_id'];
				$bcm->loginid = $row['loginid'];
				$bcm->is_active = $row['is_active'];
				$bcm->expiry_date = $row['expiry_date'];
				$bcm->address_id = $row['address_id'];
				$bcm->created_date = $row['created_date'];
				$bcm->country = $row['country'];
				$bcm->user_from = $row['user_from'];
				$bcm->gender = $row['gender'];
				$bcm->phone = $row['phone'];
				$bcm->center_id = $row['center_id'];
				$bcm->center_name = $row['name'];
				array_push($centerAdminList,$bcm);
			
			}
			//echo "<pre>";print_r($bcm);exit;
		
		return array('total' =>$row_cnt['cnt'] , 'result' => $centerAdminList);
	
	
	
	}

	public function updateCenterAdminOnline($dataArr,$user_id){
      
		//$user_id = $dataArr->user_id;
		$address_id =$dataArr->address_id;
		//$user_group_id =$dataArr->user_group_id;
		$email_id = $dataArr->email_id;
		$password = $dataArr->password;
		
	 try{

		  //// Update User
		     $sql = "UPDATE user SET first_name=:user_full_name, modified_date = NOW()  where user_id = :user_id";
			 $stmt = $this->dbConn->prepare($sql);
			  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			  $stmt->bindValue(':user_full_name', $dataArr->user_full_name, PDO::PARAM_STR);
			  $stmt->execute();
			  $stmt->closeCursor();
			  
			  //// Update center
			  $sql = "UPDATE user_center_map SET center_id=:center, modified_date = NOW()  where user_id = :user_id";
			  $stmt = $this->dbConn->prepare($sql);
			  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			  $stmt->bindValue(':center',$dataArr->center, PDO::PARAM_STR);
			  $stmt->execute();
			  $stmt->closeCursor(); 
			  
			//// update login Credentials 
		   if($password!=''){
				$stmt= $this->dbConn->prepare("UPDATE user_credential SET password=:password ,modified_date=NOW() WHERE user_id=:user_id");
			  // echo "<pre>";print_r($stmt);exit;
				 $stmt->bindValue(':password',$password, PDO::PARAM_STR);
				 $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
				 $stmt->execute();
				 $stmt->closeCursor();
		   }
			  
		     //// Update Address, Phone
		   $sql = "UPDATE address_master SET phone = :user_mobile, landline_no = :center_phone, address_line1 = :address, country = :country, state = :state, city = :city, postal_code = :postal_code, modified_date = NOW()  where address_id = :address_id";
		   //echo $sql; exit; 	  
		   $stmt = $this->dbConn->prepare($sql);
			  $stmt->bindValue(':user_mobile', $dataArr->user_mobile, PDO::PARAM_STR);
			  $stmt->bindValue(':center_phone',$dataArr->center_phone, PDO::PARAM_STR);
			  $stmt->bindValue(':address', $dataArr->address, PDO::PARAM_STR);
			  $stmt->bindValue(':country',$dataArr->country, PDO::PARAM_STR);
			  $stmt->bindValue(':state',$dataArr->state, PDO::PARAM_STR);
			  $stmt->bindValue(':city',$dataArr->city, PDO::PARAM_STR);
			  $stmt->bindValue(':postal_code',$dataArr->postal_code, PDO::PARAM_STR);
			  $stmt->bindValue(':address_id', $address_id, PDO::PARAM_INT);

			  $stmt->execute();
			  $stmt->closeCursor();

			  return true;
		  }//catch exception
			   catch(Exception $e) {
			  echo 'Message: ' .$e->getMessage();exit;
			}
         
	}
	function get_master_address($id){
		$sql = "SELECT * FROM address_master WHERE address_id=:address_id";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':address_id', $id, PDO::PARAM_INT);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		return $RESULT;
	 }
	 

	public function getAllDistrictByState($state_id){	
  		$sql= "SELECT * FROM `tblx_district` WHERE `state_id` = :state_id order by district_name asc";
	    $stmt = $this->dbConn->prepare($sql); 
		$stmt->bindValue(':state_id', $state_id, PDO::PARAM_INT);	
		$stmt->execute();
	    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC); 	
		$stmt->closeCursor(); 

		return $RESULT;
	}
	public function getAllTehsilByDistrict($dist_id){	
  		$sql= "SELECT * FROM `tblx_tehsil` WHERE `district_id` =  :dist_id order by tehsil_name asc";
	    $stmt = $this->dbConn->prepare($sql); 
		$stmt->bindValue(':dist_id', $dist_id, PDO::PARAM_INT);	
		$stmt->execute();
	    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC); 	
		$stmt->closeCursor(); 

		return $RESULT;
    }

	
	//delete Batch ManadatoryModule map
	public function deleteBatchManadatoryModuleMap($batch_id,$center_id){

		$sql = "delete  FROM tblx_mandatory_module WHERE batch_id=:batch_id AND center_id=:center_id";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
        $stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
		return true;
	
	}
	
	//=============  insert ManadatoryModule Map
	
	public function insertManadatoryModuleMap($center_id,$batch_id,$module_edge_id,$mandatory_module,$deadline_date){
		 $module_edge_id1=explode(",",$module_edge_id);
		 $mandatory_module1=explode(",",$mandatory_module);
		 $deadline_date1=explode(",",$deadline_date);
		 
		try{
			//  echo "<pre>insert";print_r($deadline_date1);exit;
				//$mandatory_module2=array();
				$mandatory_module2='';
			for( $i=0; $i<count($module_edge_id1); $i++){
				
				//echo "<pre>insert";print_r($mandatory_module1[$i]);
				if (in_array($module_edge_id1[$i],$mandatory_module1)) { 
					  $mandatory_module2='yes';
					} else { 
					   $mandatory_module2='no';					   
					}
					
				if($deadline_date1[$i]!=''){
					$sql = "INSERT INTO tblx_mandatory_module (center_id,batch_id,module_edge_id,is_mandatory,deadline_date) values (:center_id,:batch_id, :module_edge_id, :mandatoryModule,:deadline_date)";
					$stmt = $this->dbConn->prepare($sql);
					$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
					$stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
					$stmt->bindValue(':module_edge_id', $module_edge_id1[$i], PDO::PARAM_INT);
					$stmt->bindValue(':mandatoryModule', $mandatory_module2, PDO::PARAM_STR);
					$stmt->bindValue(':deadline_date', date('Y-m-d',strtotime($deadline_date1[$i])), PDO::PARAM_STR);
				}else{
					$sql = "INSERT INTO tblx_mandatory_module (center_id,batch_id,module_edge_id,is_mandatory) values (:center_id,:batch_id, :module_edge_id, :mandatoryModule)";
					$stmt = $this->dbConn->prepare($sql);
					$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
					$stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
					$stmt->bindValue(':module_edge_id', $module_edge_id1[$i], PDO::PARAM_INT);
					$stmt->bindValue(':mandatoryModule', $mandatory_module2, PDO::PARAM_STR);
					} 
				$stmt->execute();
				$stmt->closeCursor();
		
			}
	//echo "<pre>insert";print_r($mandatory_module2);exit;
			return true;	
		 }//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
	}

		//Get  Batch ManadatoryModule map
	public function getBatchManadatoryModuleMap($batch_id,$center_id){

		$sql = "Select *  FROM tblx_mandatory_module WHERE batch_id=:batch_id AND center_id=:center_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
		$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC); 	
		$stmt->closeCursor(); 

		return $RESULT;
	
	}
	
	
	public function updateCenterStatus($cid){ 
	     
		$cid = base64_decode($cid);
		try{
		 
		  $sql = "UPDATE tblx_center SET  status = 0, modified_date = NOW() where center_id = :center_id";
		  
		  $stmt = $this->dbConn->prepare($sql);	
          $stmt->bindValue(':center_id', $cid, PDO::PARAM_INT);
		  $stmt->execute();
		  $stmt->closeCursor();
		  
			 return true;
		  }//catch exception
			   catch(Exception $e) {
			  echo 'Message: ' .$e->getMessage();exit;
			}
         
	}
	
	
	public function getDistrictByIdAndState($state_id,$district_id){	
  		try{
			$sql= "SELECT * FROM `tblx_district` WHERE `state_id` = :state_id and `district_id` = :district_id order by district_name asc";
			$stmt = $this->dbConn->prepare($sql); 
			$stmt->bindValue(':state_id', $state_id, PDO::PARAM_INT);
			$stmt->bindValue(':district_id', $district_id, PDO::PARAM_INT);			
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC); 	
			$stmt->closeCursor(); 
			if(count($RESULT) > 0 ){
				return $RESULT[0]['district_name'];
			}else{
				return false;
			} 
		}//catch exception
			   catch(Exception $e) {
			  echo 'Message: ' .$e->getMessage();exit;
			}
	}
	 
	function deleteUserByUserId($uid){
		try{
		 
			if($uid!=""){
				 $stmt = $this->dbConn->prepare("DELETE FROM user_session_tracking WHERE  user_id = :user_id"); 
				$stmt->bindValue(':user_id', $uid, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor();
				$stmt = $this->dbConn->prepare("DELETE FROM visiting_user WHERE  user_id = :user_id"); 
				$stmt->bindValue(':user_id', $uid, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor();
				$stmt = $this->dbConn->prepare("DELETE FROM web_api_session WHERE  user_id = :user_id"); 
				$stmt->bindValue(':user_id', $uid, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor();
				
				$stmt = $this->dbConn->prepare("DELETE FROM tblx_batch_user_map WHERE  user_id = :user_id"); 
				$stmt->bindValue(':user_id', $uid, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor();
				$stmt = $this->dbConn->prepare("DELETE FROM user_center_map WHERE  user_id = :user_id"); 
				$stmt->bindValue(':user_id', $uid, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor();
				$stmt = $this->dbConn->prepare("DELETE FROM user_credential WHERE  user_id = :user_id"); 
				$stmt->bindValue(':user_id', $uid, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor();
				$stmt = $this->dbConn->prepare("DELETE FROM user WHERE  user_id = :user_id"); 
				$stmt->bindValue(':user_id', $uid, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor();  
				
				return true;
			}else{ return false;}
			
		  }//catch exception
			   catch(Exception $e) {
			  echo 'Message: ' .$e->getMessage();exit;
			}
         
	}
	
	//=============  Get user batch data  
	
	public function getStudentByBatchUserMap($centerId,$batch_id){
			$sql = "Select count('*') as 'cnt' from tblx_batch_user_map WHERE batch_id = :batchID AND center_id=:centerID";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':batchID', $batch_id, PDO::PARAM_INT);
			$stmt->bindValue(':centerID', $centerId, PDO::PARAM_INT);
			
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			$cnt = $RESULT[0]['cnt'];
			if($cnt!="" && $cnt>0){
				return true;
			}else{
				return false;
			}
	}
	//=============  Get user batch data  
	
	public function chkDistrictUser($district_id){
			$sql = "Select count('*') as 'cnt' from user_center_map WHERE district_id = :district_id";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':district_id', $district_id, PDO::PARAM_INT);
			
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			$cnt = $RESULT[0]['cnt'];
			if($cnt!="" && $cnt>0){
				return true;
			}else{
				return false;
			}
	}
	
	//=============  Get user list data  
	
	public function chkUserListByTehsil($tehsil_id){
			$sql = "Select count('*') as 'cnt' from user_center_map WHERE tehsil_id = :tehsil_id";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':tehsil_id', $tehsil_id, PDO::PARAM_INT);
			
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			$cnt = $RESULT[0]['cnt'];
			if($cnt!="" && $cnt>0){
				return true;
			}else{
				return false;
			}
	}
	
	function mapProductBatchMap($product,$center_id,$batch_id){
		 $productData=explode(',',$product);

		for( $i=0; $i<count($productData); $i++){
			$sql="SELECT * FROM tblx_batch_product_map WHERE product_id=:product_id AND center_id=:center_id AND batch_id=:batch_id";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':product_id', $productData[$i], PDO::PARAM_INT);
			$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
			$stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			
			if(count($RESULT) > 0 ){
		    }else{
				$sql = "insert into tblx_batch_product_map (product_id, center_id,batch_id,date_created)
				values (:product_id, :center_id,:batch_id,NOW()) ";
				$stmt = $this->dbConn->prepare( $sql );
				$stmt->bindValue(':product_id', $productData[$i], PDO::PARAM_INT);
				$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
				$stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor();  
		  }
	   }
	   return true;
		
	}
	function getProductBatchMap($center_id,$batch_id){
		 	$sql="SELECT product_id FROM tblx_batch_product_map WHERE center_id=:center_id AND batch_id=:batch_id";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
			$stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			
	     return $RESULT;
		
	}
	
	
	public function getCenterProductMapById($region_id,$center_id){

		$sql = "SELECT * FROM tblx_center_product_map WHERE region_id=:region_id AND center_id=:center_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':region_id', $region_id, PDO::PARAM_INT);
		$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		return $RESULT;
	
	}
		//Delete Center Product map data
	public function deleteCenterProductMapDetails($region_id,$center_id){

		$sql = "DELETE  FROM tblx_center_product_map WHERE region_id=:region_id AND center_id=:center_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':region_id', $region_id, PDO::PARAM_INT);
		$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();	
	
	}
	
//============= ADD  Center  Product Map
	
	public function addCenterProductMap($region_id,$center_id,$product){
		try{
				$sql = "INSERT INTO tblx_center_product_map (region_id,center_id,product_id,created_date) values (:region_id,:center_id, :product_id,NOW())";
				$stmt = $this->dbConn->prepare($sql);
				$stmt->bindValue(':region_id', $region_id, PDO::PARAM_INT);
				$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
				$stmt->bindValue(':product_id', $product, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor(); 
			
			return true;	
		 }//catch exception
		  catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
	}
	
	//=============  Update region name methods by region Id

	
	public function sendToMixPanel($data)
	{
		$curl = curl_init();

		$jsonData=json_encode($data);

		//print_r($jsonData);exit;
		curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://65.2.86.61:6000/pushMixPanelData',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS =>$jsonData,
		CURLOPT_HTTPHEADER => array(
		'Content-Type: application/json'
		),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return $response; 
	}
}

?>