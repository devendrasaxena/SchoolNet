<?php
include_once 'commonController.php'; 
class reportController extends commonController {

    public $dbConn;
    public function __construct() {
		$this->dbConn = DBConnection::createConn();
		
    }

    private function getDataBySql($qry,$whr = null, $one = false, $type = 'obj'){
		$pdo = $this->dbConn;
		$stmt = $pdo->prepare($qry);
		if($whr != null){
			foreach ($whr as $key => $w) {
				$stmt->bindValue(":$key", $w);
			}
	    	
		}

	    $stmt->execute();
	    if($type == 'obj'){
	    if($one)
	    	$data = $stmt->fetch(PDO::FETCH_OBJ);
	    else 
	    	$data = $stmt->fetchAll(PDO::FETCH_OBJ);
	    
	   }else{
	   		 if($one)
	    	$data = $stmt->fetch(PDO::FETCH_ASSOC);
	    else 
	    	$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

	   }
	    return $data;
	}

    public function fetch_rows($qry,$arr=false,$whrValue=array()) {
		$pre = $this->dbConn->prepare($qry);
		foreach ($whrValue  as $key => $val) {
			$val = ltrim($val," ");
			$pre->bindValue(":$key", $val);
		}
		$pre->execute();
		if($arr == false)
			$res = $pre->fetchAll(PDO::FETCH_OBJ);
		else
			$res = $pre->fetchAll(PDO::FETCH_ASSOC);
        $pre->closeCursor();
        return $res;
    }

    public function fetch($qry,$arr=false,$whrValue=array()) {
		$pre = $this->dbConn->prepare($qry);
		foreach ($whrValue  as $key => $val) {
			$val = ltrim($val," ");
			$pre->bindValue(":$key", $val);
		}
		$pre->execute();
		if($arr == false)
			return $pre->fetch(PDO::FETCH_OBJ);
		else
			return $pre->fetch(PDO::FETCH_ASSOC);
    }

    public function dd($data,$clean = 0){
    	if($clean)
    		ob_clean();
    	echo "<pre>";
    	print_r($data);
    	exit;
	}
	
	public function d($data){
    	echo "<pre>";
    	print_r($data);
    
    }

	//$commonObj = new commonController();
	//============= Get user detail by  user id and role id 
   public function getUserReportByID($uid){
        $roleID=2;
		$sql = "SELECT bum.batch_id, uld.* FROM tblx_batch_user_map bum join user_role_map uld ON bum.user_id = uld.user_id "
                . " WHERE bum.status = 1 AND uld.user_id = :user_id AND uld.role_definition_id =:role_definition_id AND bum.center_id = :center_id";
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':user_id', $uid, PDO::PARAM_INT);
		$stmt->bindValue(':role_definition_id', $roleID, PDO::PARAM_INT);
		$stmt->bindValue(':role_definition_id', $roleID, PDO::PARAM_INT);
		$stmt->bindValue(':center_id', $_SESSION['center_id'], PDO::PARAM_INT); 
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
		$gender=$this->getDatabyId('tblx_gender','name',$gender);
		$gender=$gender['description'];
		$age_range=$RESULT[0]['age_range'];
		$age_range=$this->getDatabyId('tblx_age_range','id',$age_range);
		$age_range=$age_range['age_range'];
		$marital_status=$RESULT[0]['marital_status'];
		$marital_status=$this->getDatabyId('tblx_marital_status','id',$marital_status);
		$marital_status=$marital_status['name'];
		$mother_tongue=$RESULT[0]['mother_tongue'];
		$mother_tongue=$this->getDatabyId('tblx_mother_tongue','id',$mother_tongue);
		$mother_tongue=$mother_tongue['name'];
		$education=$RESULT[0]['education'];
		$education=$this->getDatabyId('tblx_education','id',$education);
		$education=$education['name'];
		$employment_status=$RESULT[0]['employment_status'];
		$employment_status=$this->getDatabyId('tblx_employment_status','id',$employment_status);
		$employment_status=$employment_status['name'];
		$joining_purpose=$RESULT[0]['joining_purpose'];
		$joining_purpose=$this->getDatabyId('tblx_joining_purpose','id',$joining_purpose);
		$joining_purpose=$joining_purpose['name'];
		$app_discovered	=$RESULT[0]['app_discovered'];
		$app_discovered=$this->getDatabyId('tblx_app_discovered','id',$app_discovered);
		$app_discovered=$app_discovered['name'];
         //echo "<pre>"; print_r($app_discovered); die;
        //echo "<pre>"; print_r($RESULT); die;
		
		$sql = "SELECT * FROM user_credential WHERE user_id=:user_id order by user_id DESC";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$password=$RESULT[0]['password'];
		
	    //echo "<pre>"; print_r($RESULT); die;
		
		$sql = "SELECT * FROM address_master WHERE address_id=:address_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':address_id', $address_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$phone=$RESULT[0]['phone'];
		$country=$RESULT[0]['country'];
		$country=$this->getDatabyId('country','id',$country);
		$country=$country['country_name'];
		$state=$RESULT[0]['state'];
		$state=$this->getDatabyId('state','id',$state);
		$state=$state['state_name'];
		$city=$RESULT[0]['city'];
        $city=$this->getDatabyId('city','id',$city);
		$city=$city['city_name'];
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
		/* $sql = "SELECT * FROM tblx_user_company WHERE user_id=:user_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$company=$RESULT[0]['company_id']; */
		$obj = new stdclass();
		$obj->batch_id = $list_batch;
		$obj->first_name = $first_name;
		$obj->last_name = $last_name;
		$obj->email_id = $email_id;
		$obj->password = $password;
		$obj->phone = $phone;
		$obj->profile_id = $profile_id;
		$obj->system_name = $system_name;
		$obj->gender = $gender;
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
		
		//$obj->company = $company;
		
        //echo "<pre>";print_r($obj);exit;
		return $obj;
	}
	
	
	/* public function getUsersByCenter($center_id){ 
 
	//$sql = "SELECT u1.address_id, am.phone, uc1.loginid, uc1.created_date, u1.first_name, u1.last_name, u1.user_id, u1.email_id FROM user u";
	$sql = "SELECT u1.address_id, uc1.loginid, uc1.created_date, u1.first_name, u1.last_name, u1.user_id, u1.email_id, u1.is_active FROM user u1, user_credential uc1 where u1.user_id=uc1.user_id and u1.user_client_id=2 order by u1.created_date desc";
	  //select Center Online live server Database
	 $stmt = $this->dbConn->prepare($sql);	
     $stmt->execute();
     $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
     $stmt->closeCursor();
	//echo "<pre>";print_r($RESULT);exit;
	 return $RESULT;
   }*/

	public function getCenterListByClient($client_id,$center='',$country='',$region_id=''){ 
	
		$whr = " AND tc.center_id!='32'";
		
		if($center!="" && $center!='All'){
			$whr.= " AND tc.center_id =:center_id";
		}
		if($country!="" && $country!='All'){
			$whr.= " AND tc.country =:country";
		}
		
		if($region_id!="" && $region_id!='All' && $region_id!=0){
				$whr.= " AND tc.region =:region_id ";
				//$whr.= " AND trcm.region_id =:region_id ";
			}
			
		$sql = "Select tc.* from tblx_center AS tc LEFT JOIN tblx_region_country_map AS trcm ON tc.country=trcm.country_name where status=1 and tc.client_id=:client_id $whr group by tc.center_id order by tc.center_type,tc.name asc";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
		if($center!="" && $center!='All'){
		$stmt->bindValue(':center_id', $center, PDO::PARAM_INT); 
		}
		if($country!="" && $country!='All'){
		$stmt->bindValue(':country', $country, PDO::PARAM_INT);
		}
		if($region_id!="" && $region_id!='All' && $region_id!=0){
		$stmt->bindValue(':region_id', $region_id, PDO::PARAM_INT);
		}
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		//echo "<pre>";print_r($RESULT);exit;
		return $RESULT;
   }

	
	public function getAllCenterListByClient($client_id, $region_id=''){ 
 		$whr = "";
 		if($region_id != ''){
 		$whr.= " AND trcm.region_id =:region_id";
 		}
		$sql = "Select tc.* from tblx_center AS tc LEFT JOIN tblx_region_country_map AS trcm ON tc.country=trcm.country_name  where tc.client_id =:client_id and tc.status=1 $whr group by tc.center_id order by tc.name asc";
		$stmt = $this->dbConn->prepare($sql);	
		$stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
		if($region_id != ''){
		$stmt->bindValue(':region_id', $region_id, PDO::PARAM_INT);
		}
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		//echo "<pre>";print_r($RESULT);exit;
		return $RESULT;
   } 
   
   	//=============  Get region methods
	public function getRegionByUserId($user_id){
		$sql = "SELECT * FROM tblx_region_user_map";
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
   
   public function getAllCountryList(){ 
		 $sql = "Select * from tbl_countries order by country_name asc";
		  //select Center Online live server Database
		 $stmt = $this->dbConn->prepare($sql);	
		 $stmt->execute();
		 $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		 $stmt->closeCursor();
		//echo "<pre>";print_r($RESULT);exit;
		 return $RESULT;
	
   }
   
   public function getCountryList($region_id=''){ 
		
		$whr = " where 1=1";
 		if(isset($_SESSION['region_id']) &&  $_SESSION['region_id']!= ''){ 
			$region_id = $_SESSION['region_id'];
			$whr.= " AND trcm.region_id = :region_id";
 		}elseif($region_id!= '' && $region_id!= 'All' && $region_id!= '0'){
			$whr.= " AND trcm.region_id = :region_id";
		}
		 $sql = "Select tc.id,tc.country_code,tc.country_name,tc.is_active from tbl_countries tc LEFT JOIN tblx_region_country_map trcm ON tc.country_name=trcm.country_name $whr group by tc.country_name order by tc.country_name asc";
		  //select Center Online live server Database
		 $stmt = $this->dbConn->prepare($sql);	
		 if(isset($_SESSION['region_id']) &&  $_SESSION['region_id']!= ''){ 
		     $stmt->bindValue(':region_id', $region_id, PDO::PARAM_INT);
		 }elseif($region_id!= '' && $region_id!= 'All' && $region_id!= '0'){
			 $stmt->bindValue(':region_id', $region_id, PDO::PARAM_INT);
		 }
		 $stmt->execute();
		 $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		 $stmt->closeCursor();
		//echo "<pre>";print_r($RESULT);exit;
		 return $RESULT;
	
 
   }


	//========= Get country by country name
	public function searchCountryByCountryName($name='',$region_id=''){ 
		
			$whr = "where 1=1 ";
			
			
			if($name!=""){
				$whr.= " AND tc.country_name LIKE :country_name";
			}
			if($region_id!="" && $region_id!="All"){
				$whr.= " AND trcm.region_id LIKE :region_id";
			}
			
			$sql = "Select tc.* from tbl_countries AS tc LEFT JOIN tblx_region_country_map AS trcm ON tc.country_name=trcm.country_name $whr group by tc.id order by tc.country_name asc";
			$stmt = $this->dbConn->prepare($sql);
			if($name != ''){
				$stmt->bindValue(':country_name', '%'.$name.'%', PDO::PARAM_STR);
			}
			if($region_id!="" && $region_id!="All"){
				$stmt->bindValue(':region_id', $region_id, PDO::PARAM_INT);
			}
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			$countryList = array();
		   while($row = array_shift( $RESULT )) {
				$bcm = new stdClass();
				$bcm->id = $row['id'];
				$bcm->country_name = $row['country_name'];
				array_push($countryList,$bcm);
			
			}

		return $countryList;
	}
	
   public function getUsersByCenter($center,$role_id,$country='',$user_id=''){
		
			$whr = "where 1=1";
			$whr.= " AND u1.user_id=uc1.user_id and u1.user_id=urm.user_id and u1.address_id=am.address_id and u1.user_id=ucm.user_id and ucm.center_id=tc.center_id and u1.user_client_id=2";

			if($role_id!=""){
				$whr.= " and urm.role_definition_id=:role_id";
			}
			if($center!="" && $center!='All'){
				$whr.= " AND ucm.center_id =:center_id";
			}
			if($country!="" && $country!='All'){
				$whr.= " AND am.country =:country";
				//$whr.= " AND tc.country = '$country'";
			}
			if($user_id!="" && $user_id!='All'){
				$whr.= " AND u1.user_id =:user_id";
			}

			$sql = "SELECT DISTINCT u1.user_id, u1.address_id, uc1.loginid, uc1.created_date, u1.first_name, u1.last_name, u1.email_id, u1.is_active,am.country,u1.mother_tongue FROM user u1, user_credential uc1, user_role_map urm , address_master am , user_center_map ucm, tblx_center tc $whr order by u1.created_date desc";
			$stmt = $this->dbConn->prepare($sql);
			if($role_id!=""){
				$stmt->bindValue(':role_id', $role_id, PDO::PARAM_INT);
			}
			if($center!="" && $center!='All'){
				$stmt->bindValue(':center_id', $center, PDO::PARAM_INT);
				}
			if($country!="" && $country!='All'){
				$stmt->bindValue(':country', $country, PDO::PARAM_STR);
			}
			if($user_id!="" && $user_id!='All'){
				$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
			}
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			$userList = array();
		   while($row = array_shift( $RESULT )) {
				$bcm = new stdClass();
				$bcm->first_name = $row['first_name'];
				$bcm->last_name = $row['last_name'];
				$bcm->email_id = $row['email_id'];
				$bcm->loginid = $row['loginid'];
				$bcm->user_id = $row['user_id'];
				$bcm->is_active = $row['is_active'];
				$bcm->address_id = $row['address_id'];
				$bcm->created_date = $row['created_date'];
				$bcm->country = $row['country'];
				$bcm->mother_tongue = $row['mother_tongue'];
				array_push($userList,$bcm);
			
			}

		$userArr= array();
		foreach($userList as $key => $value){
			
			$stmt = $this->dbConn->prepare("select name from tblx_mother_tongue where id='".$value->mother_tongue."'");
			$stmt->execute();
			$RESULT1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$row = array_shift( $RESULT1 );
			$mother_tongue = isset($row['name'])?$row['name']:'-';
			
			$bcm = new stdClass();
			$bcm->first_name = $value->first_name;
			$bcm->last_name = $value->last_name;
			$bcm->email_id = $value->email_id;
			$bcm->loginid = $value->loginid;
			$bcm->user_id = $value->user_id;
			$bcm->is_active = $value->is_active;
			$bcm->address_id = $value->address_id;
			$bcm->created_date = $value->created_date;
			$bcm->country = $value->country;
			$bcm->mother_tongue = $mother_tongue;
			array_push($userArr,$bcm);
			
			$stmt->closeCursor();
		}
			
		return $userArr;
}

	
	 public function getCenterListByClientAndCountry($cond_arr = array(), $start = 0, $limit = 50,$order="",$dir=""){ 
	
		$columnArr = array('name','license_key','created_date','expiry_days','student_limit','trainer_limit');
		$ascdscArr = array('asc','desc','ASC','DESC');
		
		$whr = "where 1=1 and tc.status=1";
		if(!empty($cond_arr['region_id'])){

			$whr.= " AND tc.region =:region_id ";
		}
		if(!empty($cond_arr['client_id'])){
			
			$whr.= " AND tc.client_id =:client_id and tc.status=1";
		}
		if($cond_arr['center_id']!="" && $cond_arr['center_id']!='All' && $cond_arr['center_id']!='0'){
			$whr.= " AND center_id =:center_id";
		}

		if(in_array($order,$columnArr) && in_array($dir,$ascdscArr)){
			$order= " ORDER BY ".$order." ".$dir."";
		}else{
			$order= "";
		}

		$limit_sql = '';
		if( !empty($limit) && is_numeric($start) && is_numeric($limit)){
			$limit_sql .= " LIMIT $start, $limit";
		}
		
		 $sql = "Select count(DISTINCT tc.center_id) as 'cnt' from tblx_center AS tc 
		LEFT JOIN tblx_region_country_map AS trcm ON tc.country=trcm.country_name
		 LEFT JOIN tblx_region as r ON  tc.region = r.id
		 $whr";
		$stmt = $this->dbConn->prepare($sql);	
		
		if(!empty($cond_arr['region_id'])){
			
			$stmt->bindValue(':region_id',$cond_arr['region_id'],PDO::PARAM_INT);
		}
		if(!empty($cond_arr['client_id'])){
			$stmt->bindValue(':client_id',$cond_arr['client_id'],PDO::PARAM_INT);
		}
		if($cond_arr['center_id']!="" && $cond_arr['center_id']!='All' && $cond_arr['center_id']!='0'){
			$stmt->bindValue(':center_id',$cond_arr['center_id'],PDO::PARAM_INT);
		}

		
		$stmt->execute();
		$RESULT_CNT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$row_cnt = array_shift( $RESULT_CNT );
		
		
		 $sql = "Select tc.*,r.region_name from tblx_center AS tc 
		LEFT JOIN tblx_region_country_map AS trcm ON tc.country=trcm.country_name
		LEFT JOIN tblx_region as r ON  tc.region = r.id
		$whr GROUP BY tc.center_id $order  $limit_sql";
		$stmt = $this->dbConn->prepare($sql);	
		if(!empty($cond_arr['region_id'])){
			$stmt->bindValue(':region_id',$cond_arr['region_id'],PDO::PARAM_INT);
		}
		if(!empty($cond_arr['client_id'])){
			$stmt->bindValue(':client_id',$cond_arr['client_id'],PDO::PARAM_INT);
		}
		if($cond_arr['center_id']!="" && $cond_arr['center_id']!='All' && $cond_arr['center_id']!='0'){
			$stmt->bindValue(':center_id',$cond_arr['center_id'],PDO::PARAM_INT);
		}

		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();

		return array('total' =>$row_cnt['cnt'] , 'result' => $RESULT);
   
   
   }





   public function getCenterListByClientAndCountryExport($cond_arr = array(), $start = 0, $limit = 50,$order="",$dir=""){ 

	$columnArr = array('name','license_key','created_date','expiry_days','student_limit','trainer_limit');
		$ascdscArr = array('asc','desc','ASC','DESC');
		
		$whr = "where 1=1 and tc.status=1";
		
		if(!empty($cond_arr['region_id'])){

			$whr.= " AND region_id =:region_id ";
		}
		if(!empty($cond_arr['client_id'])){
			
			$whr.= " AND client_id =:client_id and tc.status=1";
		}
		if($cond_arr['center_id']!="" && $cond_arr['center_id']!='All' && $cond_arr['center_id']!='0'){
			$whr.= " AND tc.center_id =:center_id";
		}


		if($cond_arr['district_id']!="" && $cond_arr['district_id']!='All' && $cond_arr['district_id']!='0'){
			$whr.= " AND district_id =:district_id";
		}
		
		if(in_array($order,$columnArr) && in_array($dir,$ascdscArr)){
			$order= " ORDER BY ".$order." ".$dir."";
		}else{
			$order= "";
		}

		$limit_sql = '';
		if( !empty($limit) && is_numeric($start) && is_numeric($limit)){
			$limit_sql .= " LIMIT $start, $limit";
		}

		if($cond_arr['tehsil_id']!="" && $cond_arr['tehsil_id']!='All' && $cond_arr['tehsil_id']!='0'){
			$whr.= " AND tt.tehsil_id = :tehsil_id";
			$tehsilWhr = "AND tehsil_id = ".$cond_arr['tehsil_id'];
		}else{
			$tehsilWhr = '';
		}

	

	 $sql = "Select tc.*,tr.region_name,tb.batch_name from tblx_center AS tc 
	LEFT JOIN tblx_region_country_map AS trcm ON tc.country=trcm.country_name
	LEFT JOIN tblx_region as tr ON  tc.region = tr.id
	LEFT JOIN tblx_batch as tb ON  tb.center_id = tc.center_id
	LEFT JOIN tblx_district AS td ON tc.center_id=td.state_id
	LEFT JOIN tblx_tehsil as tt ON  tt.district_id = td.district_id $whr  
	GROUP BY tc.center_id $order  $limit_sql";
	$stmt = $this->dbConn->prepare($sql);
	if(!empty($cond_arr['region_id'])){
			$stmt->bindValue(':region_id',$cond_arr['region_id'],PDO::PARAM_INT);
		}
	if(!empty($cond_arr['client_id'])){
		$stmt->bindValue(':client_id',$cond_arr['client_id'],PDO::PARAM_INT);
	}
	if($cond_arr['center_id']!="" && $cond_arr['center_id']!='All' && $cond_arr['center_id']!='0'){
		$stmt->bindValue(':center_id',$cond_arr['center_id'],PDO::PARAM_INT);
	}
	if($cond_arr['district_id']!="" && $cond_arr['district_id']!='All' && $cond_arr['district_id']!='0'){
		$stmt->bindValue(':district_id',$cond_arr['district_id'],PDO::PARAM_INT);

	}
	if($cond_arr['tehsil_id']!="" && $cond_arr['tehsil_id']!='All' && $cond_arr['district_id']!='0'){
		$stmt->bindValue(':tehsil_id',$cond_arr['tehsil_id'],PDO::PARAM_INT);

	}
	$stmt->execute();
	$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt->closeCursor();
	$res = array();


	foreach($RESULT as $k =>$v){
		 $dist = $this->fetch_rows('Select td.* from  tblx_district as td  
		WHERE td.state_id = :center_id',1,array('center_id'=>$v['center_id']));

		foreach($dist as $l=>$d){
			$tehsil = $this->fetch_rows('Select tt.* from  tblx_tehsil as tt 
		WHERE tt.district_id = '.$d['district_id'].' '.$tehsilWhr,1);
				$district_id = $d['district_id']; 
			foreach ($tehsil as $m => $teh) {
				$tid = $teh['tehsil_id'];
				$tmpRes1 = $this->fetch("SELECT count(*) teacherReg FROM user_role_map uld 
			inner join user_center_map uc ON uld.user_id = uc.user_id 
			and uld.role_definition_id =1 and uld.is_active =1 
			where uc.tehsil_id=$tid AND uc.district_id = $district_id AND uc.client_id=2");

			$tmpRes2 = $this->fetch("SELECT count(*) studentReg FROM user_role_map uld 
			inner join user_center_map uc ON uld.user_id = uc.user_id 
			and uld.role_definition_id =2 and uld.is_active =1 
			where uc.tehsil_id=$tid AND uc.district_id = $district_id AND uc.client_id=2");


				$res[] = array( 
					'center_id'=>$v['center_id'],
					'region'=>$v['region_name'],
					'state'=>$v['name'],
					'district'=>$d['district_name'],
					'tehsil'=>$teh['tehsil_name'],
					'batch_name'=>$v['batch_name'],
					'license_key'=>$v['license_key'],
					'expiry_days'=>$v['expiry_days'],
					'created_date'=>$v['created_date'],
					'expiry_date'=>$v['expiry_date'],
					'trainer_limit'=>$v['trainer_limit'],
					'student_limit'=>$v['student_limit'],
					'teacherReg'=>$tmpRes1->teacherReg,
					'studentReg'=>$tmpRes2->studentReg,
				);
			}
		
		}


		
	}

	//$RESULT[] = $res;

	return array('total' =>0, 'result' => $res);


}

   public function getCenterListByClientAndCountryExportSuperadmin($cond_arr = array(), $start = 0, $limit = 50,$order="",$dir=""){ 

   	
	$columnArr = array('name','license_key','created_date','expiry_days','student_limit','trainer_limit');
		$ascdscArr = array('asc','desc','ASC','DESC');
		
		$whr = "where 1=1 and tc.status=1";
		
		if(!empty($cond_arr['region_id'])){

			$whr.= " AND region_id =:region_id ";
		}
		if(!empty($cond_arr['client_id'])){
			
			$whr.= " AND tc.client_id =:client_id and tc.status=1";
		}
		if($cond_arr['center_id']!="" && $cond_arr['center_id']!='All' && $cond_arr['center_id']!='0'){
			$whr.= " AND tc.center_id =:center_id";
		}

		if(in_array($order,$columnArr) && in_array($dir,$ascdscArr)){
			$order= " ORDER BY ".$order." ".$dir."";
		}else{
			$order= "";
		}

		$limit_sql = '';
		if( !empty($limit) && is_numeric($start) && is_numeric($limit)){
			$limit_sql .= " LIMIT $start, $limit";
		}


	 $sql = "Select tc.*,tr.region_name,tb.batch_name from tblx_center AS tc 
	LEFT JOIN tblx_region_country_map AS trcm ON tc.country=trcm.country_name
	LEFT JOIN tblx_region as tr ON  tc.region = tr.id
	LEFT JOIN tblx_batch as tb ON  tb.center_id = tc.center_id $whr  
	GROUP BY tc.center_id $order  $limit_sql";
	$stmt = $this->dbConn->prepare($sql);
	if(!empty($cond_arr['region_id'])){
			$stmt->bindValue(':region_id',$cond_arr['region_id'],PDO::PARAM_INT);
		}
	if(!empty($cond_arr['client_id'])){
		$stmt->bindValue(':client_id',$cond_arr['client_id'],PDO::PARAM_INT);
	}
	if($cond_arr['center_id']!="" && $cond_arr['center_id']!='All' && $cond_arr['center_id']!='0'){
		$stmt->bindValue(':center_id',$cond_arr['center_id'],PDO::PARAM_INT);
	}
	
	$stmt->execute();
	$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt->closeCursor();
	$res = array();


	foreach($RESULT as $k =>$v){
		
		$res[] = array( 
					'center_id'=>$v['center_id'],
					'region'=>$v['region_name'],
					'state'=>$v['name'],
					
					'batch_name'=>$v['batch_name'],
					'license_key'=>$v['license_key'],
					'expiry_days'=>$v['expiry_days'],
					'created_date'=>$v['created_date'],
					'expiry_date'=>$v['expiry_date'],
					'trainer_limit'=>$v['trainer_limit'],
					'student_limit'=>$v['student_limit'],
					'teacherReg'=>0,
					'studentReg'=>0,
				);
	


		
	}

	//$RESULT[] = $res;

	return array('total' =>0, 'result' => $res);


}




   public function getUsersByCenterAndCountry($cond_arr = array(), $start = 0, $limit = 10,$order,$dir){
			$columnArr = array('u.first_name','u.email_id','u.created_date','state','last_visit','u.is_active');
		    $ascdscArr = array('asc','desc','ASC','DESC');

		
			$regionWhr = 'where 1=1';

			if($cond_arr['role_id']!="" && $cond_arr['role_id']!='All'){
				$regionWhr.= " AND urm.role_definition_id = :role_definition_id";
			
			}else if($cond_arr['role_id']=='All' && $cond_arr['center_id']!="" && $cond_arr['center_id']!='All'){
				$regionWhr .= " AND urm.role_definition_id IN(1,2)";

			}



			$client_id = $_SESSION['client_id'];
			
			if($cond_arr['region_id']!="" && $cond_arr['region_id']!='All'){
				$regionWhr.= " AND ( tr.id  = :region_id";
				$regionWhr.= " OR rum.region_id  = :region_id )";
				} 

			if($cond_arr['center_id']!="" && $cond_arr['center_id']!='All'){
				$regionWhr .= " AND tc.center_id = :center_id1";
				$regionWhr.= " AND ucm.center_id  = :center_id2";
			}
			
			if($cond_arr['student_txt']!="" && $cond_arr['user_id']==""){
				$regionWhr.= " AND ((u.first_name LIKE '%".$cond_arr['student_txt']."%' or u.last_name LIKE '%".$cond_arr['student_txt']."%'  or CONCAT(u.first_name,'',u.last_name ) LIKE  '%".$cond_arr['student_txt']."%') OR (u.email_id LIKE '%".$cond_arr['student_txt']."%') OR (uc.loginid LIKE '%".$cond_arr['student_txt']."%'))";
			} 	
			
			if($cond_arr['user_id']!="" && $cond_arr['user_id']!='All'){
				
				$regionWhr.= " AND u.user_id = :user_id";
			}

			if(in_array($order,$columnArr) && in_array($dir,$ascdscArr)){
				$order= " ORDER BY ".$order." ".$dir."";
			}else{
				$order= "";
			}

			
			$sql ="SELECT Count(*) as 'cnt' from  (SELECT u.*,tc.name AS centerName, MAX(vu.date_with_time) AS last_visit from user AS u JOIN user_credential uc on u.user_id=uc.user_id JOIN user_role_map as urm ON u.user_id = urm.user_id LEFT JOIN tblx_region_user_map as rum ON rum.user_id = u.user_id LEFT JOIN user_center_map as ucm ON ucm.user_id = u.user_id LEFT JOIN tblx_center as tc ON tc.center_id = ucm.center_id LEFT JOIN tblx_region as tr ON tc.region = tr.id LEFT JOIN visiting_user as vu ON vu.user_id = u.user_id $regionWhr GROUP BY u.user_id) as def";
			   
			$stmt = $this->dbConn->prepare($sql);
			if($cond_arr['role_id']!="" && $cond_arr['role_id']!='All'){
					
					$stmt->bindValue(':role_definition_id',$cond_arr['role_id'],PDO::PARAM_INT);
				}
			if($cond_arr['region_id']!="" && $cond_arr['region_id']!='All'){
					
					$stmt->bindValue(':region_id',$cond_arr['region_id'],PDO::PARAM_INT);
					$stmt->bindValue(':region_id1',$cond_arr['region_id'],PDO::PARAM_INT);
					
				} 

			if($cond_arr['center_id']!="" && $cond_arr['center_id']!='All'){
				$stmt->bindValue(':center_id1',$cond_arr['center_id'],PDO::PARAM_INT);
				$stmt->bindValue(':center_id2',$cond_arr['center_id'],PDO::PARAM_INT);
			}
			
			if($cond_arr['user_id']!="" && $cond_arr['user_id']!='All'){
				
				$stmt->bindValue(':user_id',$cond_arr['user_id'],PDO::PARAM_INT);
			}
		
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$total = $RESULT[0]['cnt'];



			$limit_sql = '';
				if( !empty($limit) && is_numeric($start) && is_numeric($limit)){
					$limit_sql .= " LIMIT $start, $limit";
				}



			 $sql ="SELECT u.*,uc.is_active,tc.center_id,tc.name AS centerName, MAX(vu.date_with_time) AS last_visit from user AS u JOIN user_credential uc on u.user_id=uc.user_id JOIN user_role_map as urm ON u.user_id = urm.user_id LEFT JOIN tblx_region_user_map as rum ON rum.user_id = u.user_id LEFT JOIN user_center_map as ucm ON ucm.user_id = u.user_id LEFT JOIN tblx_center as tc ON tc.center_id = ucm.center_id LEFT JOIN tblx_region as tr ON tc.region = tr.id LEFT JOIN visiting_user as vu ON vu.user_id = u.user_id $regionWhr GROUP BY u.user_id $order  $limit_sql";
			   
			$stmt = $this->dbConn->prepare($sql);
			if($cond_arr['role_id']!="" && $cond_arr['role_id']!='All'){
					
					$stmt->bindValue(':role_definition_id',$cond_arr['role_id'],PDO::PARAM_INT);
				}
			if($cond_arr['region_id']!="" && $cond_arr['region_id']!='All'){
					
					$stmt->bindValue(':region_id',$cond_arr['region_id'],PDO::PARAM_INT);
					$stmt->bindValue(':region_id1',$cond_arr['region_id'],PDO::PARAM_INT);
					
				} 

			if($cond_arr['center_id']!="" && $cond_arr['center_id']!='All'){
				$stmt->bindValue(':center_id1',$cond_arr['center_id'],PDO::PARAM_INT);
				$stmt->bindValue(':center_id2',$cond_arr['center_id'],PDO::PARAM_INT);
			}
				
			
			if($cond_arr['user_id']!="" && $cond_arr['user_id']!='All'){
				
				$stmt->bindValue(':user_id',$cond_arr['user_id'],PDO::PARAM_INT);
			}
		
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
	
			
		
		return array('total' =>$total, 'result' => $RESULT);

	}
   
   
   public function getUsersByCenterAndCountryAndBatch($cond_arr = array(), $start = 0, $limit = 10,$order,$dir){
			$columnArr = array('u.first_name','u.email_id','u.created_date','state','last_visit','u.is_active');
		    $ascdscArr = array('asc','desc','ASC','DESC');
		
			$regionWhr = 'where 1=1';

			if($cond_arr['role_id']!="" && $cond_arr['role_id']!='All'){
				$regionWhr.= " AND urm.role_definition_id = :role_definition_id";
			
			}
			if($cond_arr['batch_id']!="" && $cond_arr['batch_id']!='All'){
				$regionWhr.= " AND tbum.batch_id = :batch_id";
			
			}

			$client_id = $_SESSION['client_id'];
			
			if($cond_arr['region_id']!="" && $cond_arr['region_id']!='All'){
				$regionWhr.= " AND ( tr.id  = :region_id";
				$regionWhr.= " OR rum.region_id  = :region_id )";
				} 

			if($cond_arr['center_id']!="" && $cond_arr['center_id']!='All'){
				$regionWhr .= " AND tc.center_id = :center_id1";
				$regionWhr.= " AND ucm.center_id  = :center_id2";
			}
			
			if($cond_arr['student_txt']!="" && $cond_arr['user_id']==""){
				$regionWhr.= " AND ((u.first_name LIKE '%".$cond_arr['student_txt']."%' or u.last_name LIKE '%".$cond_arr['student_txt']."%'  or CONCAT(u.first_name,'',u.last_name ) LIKE  '%".$cond_arr['student_txt']."%') OR (u.email_id LIKE '%".$cond_arr['student_txt']."%') OR (uc.loginid LIKE '%".$cond_arr['student_txt']."%'))";
			} 	
			
			if($cond_arr['user_id']!="" && $cond_arr['user_id']!='All'){
				
				$regionWhr.= " AND u.user_id = :user_id";
			}

			if(in_array($order,$columnArr) && in_array($dir,$ascdscArr)){
				$order= " ORDER BY ".$order." ".$dir."";
			}else{
				$order= "";
			}

			
			$sql ="SELECT Count(*) as 'cnt' from  (SELECT u.*,tc.name AS state, MAX(vu.date_with_time) AS last_visit from user AS u JOIN user_credential uc on u.user_id=uc.user_id JOIN user_role_map as urm ON u.user_id = urm.user_id LEFT JOIN tblx_region_user_map as rum ON rum.user_id = u.user_id LEFT JOIN user_center_map as ucm ON ucm.user_id = u.user_id LEFT JOIN tblx_center as tc ON tc.center_id = ucm.center_id LEFT JOIN tblx_region as tr ON tc.region = tr.id LEFT JOIN visiting_user as vu ON vu.user_id = u.user_id Left JOIN tblx_batch_user_map tbum on u.user_id = tbum.user_id $regionWhr GROUP BY u.user_id) as def";
			   
			$stmt = $this->dbConn->prepare($sql);
			if($cond_arr['role_id']!="" && $cond_arr['role_id']!='All'){
					
					$stmt->bindValue(':role_definition_id',$cond_arr['role_id'],PDO::PARAM_INT);
			}
			if($cond_arr['batch_id']!="" && $cond_arr['batch_id']!='All'){
					
					$stmt->bindValue(':batch_id',$cond_arr['batch_id'],PDO::PARAM_INT);
			}
			if($cond_arr['region_id']!="" && $cond_arr['region_id']!='All'){
					
					$stmt->bindValue(':region_id',$cond_arr['region_id'],PDO::PARAM_INT);
					$stmt->bindValue(':region_id1',$cond_arr['region_id'],PDO::PARAM_INT);
					
				} 

			if($cond_arr['center_id']!="" && $cond_arr['center_id']!='All'){
				$stmt->bindValue(':center_id1',$cond_arr['center_id'],PDO::PARAM_INT);
				$stmt->bindValue(':center_id2',$cond_arr['center_id'],PDO::PARAM_INT);
			}
			
			if($cond_arr['user_id']!="" && $cond_arr['user_id']!='All'){
				
				$stmt->bindValue(':user_id',$cond_arr['user_id'],PDO::PARAM_INT);
			}
		
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$total = $RESULT[0]['cnt'];



			$limit_sql = '';
				if( !empty($limit) && is_numeric($start) && is_numeric($limit)){
					$limit_sql .= " LIMIT $start, $limit";
				}


			

		 $sql ="SELECT u.*,uc.is_active,tc.name AS state, MAX(vu.date_with_time) AS last_visit from user AS u JOIN user_credential uc on u.user_id=uc.user_id JOIN user_role_map as urm ON u.user_id = urm.user_id LEFT JOIN tblx_region_user_map as rum ON rum.user_id = u.user_id LEFT JOIN user_center_map as ucm ON ucm.user_id = u.user_id LEFT JOIN tblx_center as tc ON tc.center_id = ucm.center_id LEFT JOIN tblx_region as tr ON tc.region = tr.id LEFT JOIN visiting_user as vu ON vu.user_id = u.user_id Left JOIN tblx_batch_user_map tbum on u.user_id = tbum.user_id $regionWhr GROUP BY u.user_id $order  $limit_sql";

			   
			$stmt = $this->dbConn->prepare($sql);
			if($cond_arr['role_id']!="" && $cond_arr['role_id']!='All'){
					
					$stmt->bindValue(':role_definition_id',$cond_arr['role_id'],PDO::PARAM_INT);
				}
			if($cond_arr['batch_id']!="" && $cond_arr['batch_id']!='All'){
					
					$stmt->bindValue(':batch_id',$cond_arr['batch_id'],PDO::PARAM_INT);
			}
			if($cond_arr['region_id']!="" && $cond_arr['region_id']!='All'){
					
					$stmt->bindValue(':region_id',$cond_arr['region_id'],PDO::PARAM_INT);
					$stmt->bindValue(':region_id1',$cond_arr['region_id'],PDO::PARAM_INT);
					
				} 

			if($cond_arr['center_id']!="" && $cond_arr['center_id']!='All'){
				$stmt->bindValue(':center_id1',$cond_arr['center_id'],PDO::PARAM_INT);
				$stmt->bindValue(':center_id2',$cond_arr['center_id'],PDO::PARAM_INT);
			}
				
			
			if($cond_arr['user_id']!="" && $cond_arr['user_id']!='All'){
				
				$stmt->bindValue(':user_id',$cond_arr['user_id'],PDO::PARAM_INT);
			}
		
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
	
		
		return array('total' =>$total, 'result' => $RESULT);

	}

	public function getCourseByClientId($client_id,$order,$dir){

		$sql="SELECT course_id,code,title,CAST(title as SIGNED) AS casted_column FROM course where client_id=:client_id and course_type=0 and status = 'active' and is_active =1 ORDER BY ".$order." ".$dir."";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':client_id',$client_id,PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $RESULT;
	
	}	
	
	
	public function getCourseDetailByCourseId($course_id){

		$sql="SELECT c.tree_node_id, c.code, c.title, c.description, c.duration, c.published_version, c.thumnailImg, c.level_id, gmt.edge_id, gmt.is_active FROM generic_mpre_tree gmt
							JOIN course c ON c.tree_node_id = gmt.tree_node_id 
							WHERE  c.course_id =:course_id";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':course_id',$course_id,PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        $row = array_shift( $RESULT);
		
		return $row;
		
	
	}
	
	public function getTopicOrAssessmentByCourseId($course_id,$order,$dir){

		$course_edge_id = $this->getCourseEdgeIdByCourseId($course_id);
		$course_detail = $this->getCourseDetailByCourseId($course_id);
		
		$topicArr = array();

		$stmt =  $this->dbConn->prepare("SELECT cm.tree_node_id, cm.name,cm.description,cm.assessment_type, gmt.edge_id 
								FROM generic_mpre_tree gmt
								JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
								JOIN cap_module cm ON cm.tree_node_id = gmt.tree_node_id
								WHERE gmt.is_active = 1 AND tree_node_super_root = :course_edge_id AND tnd.tree_node_category_id = 5 AND cm.assessment_type = 'pre'");
		$stmt->bindValue(':course_edge_id',$course_edge_id,PDO::PARAM_INT);
        $stmt->execute();
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		if(count($RESULT>0)){
			foreach ($RESULT as $key=>$val) {
				$topic1 = new stdClass();
				$topic1->tree_node_id = $val['tree_node_id'];
				$topic1->name = $val['name'];
				$topic1->description = $val['description'];
				$topic1->assessment_type = $val['assessment_type'];
				$topic1->edge_id = $val['edge_id'];
				$topic1->course_title = $course_detail['title'];
				$topic1->course_id = $course_id;
				array_push($topicArr,$topic1);
			}
		}
		

		$stmt = $this->dbConn->prepare("SELECT cm.tree_node_id, cm.name,cm.description,cm.assessment_type, gmt.edge_id 
								FROM generic_mpre_tree gmt
								JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
								JOIN cap_module cm ON cm.tree_node_id = gmt.tree_node_id
								WHERE gmt.is_active = 1 AND tree_node_super_root = :course_edge_id AND tnd.tree_node_category_id IN(3,5) AND (cm.assessment_type = 'mid' OR cm.assessment_type IS NULL) ORDER BY sequence_id");
		$stmt->bindValue(':course_edge_id',$course_edge_id,PDO::PARAM_INT);
        $stmt->execute();
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        
		if(count($RESULT>0)){
			foreach ($RESULT as $key=>$val) {
				$topic1 = new stdClass();
				$topic1->tree_node_id = $val['tree_node_id'];
				$topic1->name = $val['name'];
				$topic1->description = $val['description'];
				$topic1->assessment_type = $val['assessment_type'];
				$topic1->edge_id = $val['edge_id'];
				$topic1->course_title = $course_detail['title'];
				$topic1->course_id = $course_id;
				array_push($topicArr,$topic1);
			}
		}
		

		$stmt = $this->dbConn->prepare("SELECT cm.tree_node_id, cm.name,cm.description,cm.assessment_type, gmt.edge_id 
								FROM generic_mpre_tree gmt
								JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
								JOIN cap_module cm ON cm.tree_node_id = gmt.tree_node_id
								WHERE gmt.is_active = 1 AND tree_node_super_root = :course_edge_id AND tnd.tree_node_category_id = 5 AND cm.assessment_type = 'post'");
		$stmt->bindValue(':course_edge_id',$course_edge_id,PDO::PARAM_INT);
        $stmt->execute();
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		
		if(count($RESULT>0)){
			foreach ($RESULT as $key=>$val) {
				$topic1 = new stdClass();
				$topic1->tree_node_id = $val['tree_node_id'];
				$topic1->name = $val['name'];
				$topic1->description = $val['description'];
				$topic1->assessment_type = $val['assessment_type'];
				$topic1->edge_id = $val['edge_id'];
				$topic1->course_title = $course_detail['title'];
				$topic1->course_id = $course_id;
				array_push($topicArr,$topic1);
			}
		}
		
		
		//echo "<pre>";print_r($topicArr);exit;
		
		return $topicArr;

}



public function getAllTopicOrAssessment($client_id,$order,$dir){

		$course_list_arr = $this->getCourseByClientId($client_id,$order,$dir);		
		
		$topicArr = array();
		
		foreach ($course_list_arr as $key => $value) {	
						
			$course_id= $course_list_arr[$key]['course_id']; 
			$course_edge_id = $this->getCourseEdgeIdByCourseId($course_id);
			$course_detail = $this->getCourseDetailByCourseId($course_id);
	
				
			$stmt =  $this->dbConn->prepare("SELECT cm.tree_node_id, cm.name,cm.description,cm.assessment_type, gmt.edge_id 
									FROM generic_mpre_tree gmt
									JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
									JOIN cap_module cm ON cm.tree_node_id = gmt.tree_node_id
									WHERE gmt.is_active = 1 AND tree_node_super_root = :course_edge_id AND tnd.tree_node_category_id = 5 AND cm.assessment_type = 'pre'");
			$stmt->bindValue(':course_edge_id',$course_edge_id,PDO::PARAM_INT);
			$stmt->execute();
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			if(count($RESULT>0)){
				foreach ($RESULT as $key=>$val) {
					$topic1 = new stdClass();
					$topic1->tree_node_id = $val['tree_node_id'];
					$topic1->name = $val['name'];
					$topic1->description = $val['description'];
					$topic1->assessment_type = $val['assessment_type'];
					$topic1->edge_id = $val['edge_id'];
					$topic1->course_title = $course_detail['title'];
					$topic1->course_id = $course_id;
					array_push($topicArr,$topic1);
				}
			}
			

			$stmt = $this->dbConn->prepare("SELECT cm.tree_node_id, cm.name,cm.description,cm.assessment_type, gmt.edge_id 
									FROM generic_mpre_tree gmt
									JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
									JOIN cap_module cm ON cm.tree_node_id = gmt.tree_node_id
									WHERE gmt.is_active = 1 AND tree_node_super_root = :course_edge_id AND tnd.tree_node_category_id IN(3,5) AND (cm.assessment_type = 'mid' OR cm.assessment_type IS NULL) ORDER BY sequence_id");
			$stmt->bindValue(':course_edge_id',$course_edge_id,PDO::PARAM_INT);
			$stmt->execute();
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			
			if(count($RESULT>0)){
				foreach ($RESULT as $key=>$val) {
					$topic1 = new stdClass();
					$topic1->tree_node_id = $val['tree_node_id'];
					$topic1->name = $val['name'];
					$topic1->description = $val['description'];
					$topic1->assessment_type = $val['assessment_type'];
					$topic1->edge_id = $val['edge_id'];
					$topic1->course_title = $course_detail['title'];
					$topic1->course_id = $course_id;
					array_push($topicArr,$topic1);
				}
			}
			

			$stmt = $this->dbConn->prepare("SELECT cm.tree_node_id, cm.name,cm.description,cm.assessment_type, gmt.edge_id 
									FROM generic_mpre_tree gmt
									JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
									JOIN cap_module cm ON cm.tree_node_id = gmt.tree_node_id
									WHERE gmt.is_active = 1 AND tree_node_super_root = :course_edge_id AND tnd.tree_node_category_id = 5 AND cm.assessment_type = 'post'");
			$stmt->bindValue(':course_edge_id',$course_edge_id,PDO::PARAM_INT);
			$stmt->execute();
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			
			if(count($RESULT>0)){
				foreach ($RESULT as $key=>$val) {
					$topic1 = new stdClass();
					$topic1->tree_node_id = $val['tree_node_id'];
					$topic1->name = $val['name'];
					$topic1->description = $val['description'];
					$topic1->assessment_type = $val['assessment_type'];
					$topic1->edge_id = $val['edge_id'];
					$topic1->course_title = $course_detail['title'];
					$topic1->course_id = $course_id;
					array_push($topicArr,$topic1);
				}
			}
		}
		
		return $topicArr;

}

	
	public function getCourseEdgeIdByCourseId($course_id){
		
		$sql = "SELECT gmt.edge_id FROM generic_mpre_tree gmt
								JOIN course c ON c.tree_node_id = gmt.tree_node_id
								WHERE  c.course_id=:course_id";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':course_id',$course_id,PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$row = array_shift( $RESULT );
        return $row['edge_id'];
	
	}
	
	
	
	public function getChapterSkillByTopicEdgeId($topic_edge_id){
		
		$stmt = $this->dbConn->prepare("SELECT DISTINCT cm.chapterSkill,trc.competency FROM generic_mpre_tree gmt
								JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
								JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id
								JOIN tbl_rubric_competency trc ON cm.chapterSkill = trc.id
								WHERE gmt.is_active = 1 AND tree_node_parent = :topic_edge_id AND tnd.tree_node_category_id=2 order by cm.chapterSkill");
		$stmt->bindValue(':topic_edge_id',$topic_edge_id,PDO::PARAM_INT);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		
		$skillArr = array();
		
		foreach ($RESULT as $key=>$val) {
			$topic1 = new stdClass();
			$topic1->skill_id = $val['chapterSkill'];
			$topic1->competency = $val['competency'];
			array_push($skillArr,$topic1);
		}

		return $skillArr;
		
	}
	
	public function getTotalAndCompletedLesson($topic_edge_id,$chapter_skill,$user_id){
		
		$stmt = $this->dbConn->prepare("SELECT gmt.edge_id,gmt.tree_node_id  FROM 		generic_mpre_tree gmt
		JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
		JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id
		WHERE gmt.is_active = 1 AND tree_node_parent = :topic_edge_id  AND cm.chapterSkill = :chapter_skill AND tnd.tree_node_category_id=2");
		$stmt->bindValue(':topic_edge_id',$topic_edge_id,PDO::PARAM_INT);
		$stmt->bindValue(':chapter_skill',$chapter_skill,PDO::PARAM_INT);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		
		$ttlChapter = count($RESULT);
		
		$stmt = $this->dbConn->prepare("SELECT cm.* FROM generic_mpre_tree gmt
		JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
		JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id
		JOIN tblx_component_completion tcc ON tcc.component_edge_id = gmt.edge_id
		WHERE gmt.is_active = 1 AND tree_node_parent = :topic_edge_id AND cm.chapterSkill = :chapter_skill AND tcc.user_id = :user_id AND tcc.completion = 'c' AND tnd.tree_node_category_id=2");
		$stmt->bindValue(':topic_edge_id',$topic_edge_id,PDO::PARAM_INT);
		$stmt->bindValue(':chapter_skill',$chapter_skill,PDO::PARAM_INT);
		$stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
		$stmt->execute();
		$RESULT2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$ttlCompletedChapter = count($RESULT2);
		return json_encode(array('cnt'=>$ttlChapter,'cmplt'=>$ttlCompletedChapter,'cmplt_chapter_list'=>$RESULT2,'chapter_list'=>$RESULT));
	
	}	
	
	public function getSkillScoreByChapterEdgeId($chapterArr,$user_id){
		
		$arrTtlSkillQues = $arrTtlSkillCrrct = array();
		foreach($chapterArr as $key=>$chapter) {
				
				$ttlCorrect = 0;  $ttlChapQuesCount = 0; 
				$component_edge_id_arr  = $qCorrect =  array();
				
				$chapter_edge_id = $chapter->edge_id;
				$chapter_tree_node_id = $chapter->tree_node_id;
				
				
				
				
				$stmt = $this->dbConn->prepare("SELECT tc.component_edge_id from tbl_component tc where tc.parent_edge_id=:chapter_edge_id and tc.scenario_subtype='Quiz' AND tc.topic_type = 1 AND tc.status = 1");
				$stmt->bindValue(':chapter_edge_id',$chapter_edge_id,PDO::PARAM_INT);
				$stmt->execute();
				$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();
				foreach ($RESULT as $key=>$val) {
					$bcm = new stdClass();
					$bcm->component_edge_id = $val['component_edge_id'];
					array_push($component_edge_id_arr,$bcm);
				}
				
				
				
				foreach($component_edge_id_arr as $cmpKey=>	$cmpVal){
					$ttl_correct = 0; 
					$stmt = $this->dbConn->prepare("select ttl_correct from temp_test_attempt where user_id=:user_id and test_id=:test_id order by attempt_no desc limit 1");
					$stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
					$stmt->bindValue(':test_id',$cmpVal->component_edge_id,PDO::PARAM_INT);
					$stmt->execute();
					$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$stmt->closeCursor();
					$RESULT = array_shift($RESULT);
					$qCorrect[] = $RESULT['ttl_correct'];

					
				}

				$ttlCorrect = array_sum($qCorrect);
				$arrTtlSkillCrrct[] = $ttlCorrect;
				

				$stmt = $this->dbConn->prepare("SELECT COUNT(tq.id) as ttlCount from generic_mpre_tree gmt 
				JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id 
				JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id 
				JOIN tbl_component tc ON gmt.edge_id= tc.parent_edge_id 
				JOIN tbl_questions tq ON tq.parent_edge_id= tc.component_id 
				where cm.tree_node_id=:chapter_tree_node_id and tc.scenario_subtype='Quiz' AND tc.topic_type = 1 AND cm.topic_type = 1 AND tc.status = 1");
				$stmt->bindValue(':chapter_tree_node_id',$chapter_tree_node_id,PDO::PARAM_INT);
				$stmt->execute();
				$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$stmt->closeCursor();
				$RESULT = array_shift($RESULT);
				$ttlChapQuesCount= $RESULT['ttlCount'];
				$arrTtlSkillQues[] = $ttlChapQuesCount;
				
				
			}
			
			$arrTtlSkillQues = array_sum($arrTtlSkillQues);
			
			$arrTtlSkillCrrct = array_sum($arrTtlSkillCrrct);
			
			$skillPer =	 round(($arrTtlSkillCrrct/$arrTtlSkillQues)*100);
			$skillPer =	 !empty($skillPer)?$skillPer:0;
			
			return $skillPer;
	
	}
 	
	 public function getUsersByBatchAndCountry($cond_arr = array(), $start = 0, $limit = 10,$order,$dir){
		
			$columnArr = array('user_id', 'address_id', 'loginid', 'created_date', 'first_name', 'last_name', 'email_id', 'is_active','country','mother_tongue');
			$ascdscArr = array('asc','desc','ASC','DESC');
			
			$whr = "where 1=1";
			$whr.= " AND u1.user_id=uc1.user_id and u1.user_id=urm.user_id and u1.address_id=am.address_id and u1.user_id=ubm.user_id and ubm.center_id=tc.center_id and urm.role_definition_id=:role_definition_id and u1.user_client_id=:client_id";

			if($cond_arr['center_id']!=""){
				$whr.= " AND ubm.center_id =:center_id";
			}
			if($cond_arr['batch_id']!="" && $cond_arr['batch_id']!='All'){
				$whr.= " AND ubm.batch_id =:batch_id";
			}
			if($cond_arr['country']!="" && $cond_arr['country']!='All'){
				$whr.= " AND am.country =:country";
			}
			
			if(in_array($order,$columnArr) && in_array($dir,$ascdscArr)){
				$order= " ORDER BY ".$order." ".$dir."";
			}else{
				$order= "";
			}

			$limit_sql = '';
			if( !empty($limit) && is_numeric($start) && is_numeric($limit)){
				$limit_sql .= " LIMIT $start, $limit";
			}


			$sql = "Select count(DISTINCT u1.user_id) as 'cnt' FROM user u1, user_credential uc1, user_role_map urm , address_master am ,tblx_batch_user_map ubm, tblx_center tc $whr ";
			$stmt = $this->dbConn->prepare($sql);	
			$stmt->bindValue(':role_definition_id',$cond_arr['role_id'],PDO::PARAM_INT);
			$stmt->bindValue(':client_id',$cond_arr['client_id'],PDO::PARAM_INT);
			if($cond_arr['center_id']!=""){
			$stmt->bindValue(':center_id',$cond_arr['center_id'],PDO::PARAM_INT);
			}
			if($cond_arr['batch_id']!="" && $cond_arr['batch_id']!='All'){
			$stmt->bindValue(':batch_id',$cond_arr['batch_id'],PDO::PARAM_INT);
			}
			if($cond_arr['country']!="" && $cond_arr['country']!='All'){
			$stmt->bindValue(':country',$cond_arr['country'],PDO::PARAM_STR);
			}

			$stmt->execute();
			$RESULT_CNT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			$row_cnt = array_shift( $RESULT_CNT );



			$sql = "SELECT DISTINCT u1.user_id, u1.address_id, uc1.loginid, uc1.created_date, u1.first_name, u1.last_name, u1.email_id, u1.is_active,am.country,u1.mother_tongue FROM user u1, user_credential uc1, user_role_map urm , address_master am , tblx_batch_user_map ubm, tblx_center tc $whr AND uc1.is_active='1' $order $limit_sql";
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
				$bcm->loginid = $row['loginid'];
				$bcm->is_active = $row['is_active'];
				$bcm->address_id = $row['address_id'];
				$bcm->created_date = $row['created_date'];
				$bcm->country = $row['country'];
				$bcm->mother_tongue = $row['mother_tongue'];
				array_push($userList,$bcm);
			
			}

		$userArr= array();
		foreach($userList as $key => $value){
			
			$stmt = $this->dbConn->prepare("select name from tblx_mother_tongue where id='".$value->mother_tongue."'");
			$stmt->execute();
			$RESULT1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$row = array_shift( $RESULT1 );
			$mother_tongue = isset($row['name'])?$row['name']:'-';
			
			$bcm = new stdClass();
			$bcm->first_name = $value->first_name;
			$bcm->last_name = $value->last_name;
			$bcm->email_id = $value->email_id;
			$bcm->loginid = $value->loginid;
			$bcm->is_active = $value->is_active;
			$bcm->address_id = $value->address_id;
			$bcm->created_date = $value->created_date;
			$bcm->country = $value->country;
			$bcm->mother_tongue = $mother_tongue;
			array_push($userArr,$bcm);
			
			$stmt->closeCursor();
		}
		
		return array('total' =>$row_cnt['cnt'] , 'result' => $userArr);

   }
	
	//=============  Get batch details methods
	public function getBatchDeatils($center_id,$country='',$region_id=''){
		
		$whr = "where 1=1 AND tc.status=1 AND tb.is_default= '1'";
		
		if($center_id!="" && $center_id!='All'){
				$whr.= " AND tc.center_id =:center_id";
			}
		if($country!="" && $country!='All'){
			$whr.= " AND tc.country =:country";
		}
		if($region_id!="" && $region_id!="All" && $region_id!="0"){
			$whr.= " AND trcm.region_id =:region_id";
		}
			
		 $sql = "Select tb.* from tblx_batch tb JOIN tblx_center tc 
		ON tb.center_id = tc.center_id LEFT JOIN tblx_region_country_map AS trcm ON tc.country=trcm.country_name $whr GROUP BY tb.batch_id,tb.center_id";
		$stmt = $this->dbConn->prepare($sql);
		if($center_id!="" && $center_id!='All'){
			$stmt->bindValue(':center_id',$center_id,PDO::PARAM_INT);
		}
		if($country!="" && $country!='All'){
			$stmt->bindValue(':country',$country,PDO::PARAM_STR);
		}
		if($region_id!="" && $region_id!='All'){
			$stmt->bindValue(':region_id',$region_id,PDO::PARAM_STR);
		}
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();	
  
	return $RESULT;
			
	}
	
	//========= Get user by center and batch
	public function getUsersByCenterAndBatch($center,$batch_id,$role_id,$country='',$user_id=''){
		
			$whr = "where 1=1";
			$whr.= " AND u1.user_id=uc1.user_id and u1.user_id=urm.user_id and u1.address_id=am.address_id and u1.user_id=ubm.user_id and ubm.center_id=tc.center_id and urm.role_definition_id=:role_definition_id and u1.user_client_id=2";
			
			if($center!="" && $center!='All'){
				$whr.= " AND ubm.center_id =:center_id";
			}
			if($country!="" && $country!='All'){
				$whr.= " AND am.country =:country";
			}
			
			if($user_id!="" && $user_id!='All'){
				$whr.= " AND u1.user_id =:user_id";
			}
			
			if($batch_id!="" && $batch_id!='All'  && $batch_id!=0){
				$whr.= " AND ubm.batch_id =:batch_id";
			}

			$sql = "SELECT DISTINCT u1.user_id, u1.address_id, uc1.loginid, uc1.created_date, u1.first_name, u1.last_name, u1.email_id, u1.is_active,am.country,u1.mother_tongue FROM user u1, user_credential uc1, user_role_map urm , address_master am,  tblx_center tc, tblx_batch_user_map ubm $whr order by u1.created_date desc";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':role_definition_id',$role_id,PDO::PARAM_INT);
			
			if($center!="" && $center!='All'){
			$stmt->bindValue(':center_id',$center_id,PDO::PARAM_INT);
			}
			if($country!="" && $country!='All'){
				$stmt->bindValue(':country',$country,PDO::PARAM_STR);
			}
			if($user_id!="" && $user_id!='All'){
				$stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
			}
			if($batch_id!="" && $batch_id!='All'  && $batch_id!=0){
				$stmt->bindValue(':batch_id',$batch_id,PDO::PARAM_INT);
			}
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			$userList = array();
		   while($row = array_shift( $RESULT )) {
				$bcm = new stdClass();
				$bcm->first_name = $row['first_name'];
				$bcm->last_name = $row['last_name'];
				$bcm->email_id = $row['email_id'];
				$bcm->loginid = $row['loginid'];
				$bcm->user_id = $row['user_id'];
				$bcm->is_active = $row['is_active'];
				$bcm->address_id = $row['address_id'];
				$bcm->created_date = $row['created_date'];
				$bcm->country = $row['country'];
				$bcm->mother_tongue = $row['mother_tongue'];
				array_push($userList,$bcm);
			
			}

		$userArr= array();
		foreach($userList as $key => $value){
			
			$stmt = $this->dbConn->prepare("select name from tblx_mother_tongue where id='".$value->mother_tongue."'");
			$stmt->execute();
			$RESULT1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$row = array_shift( $RESULT1 );
			$mother_tongue = isset($row['name'])?$row['name']:'-';
			
			$bcm = new stdClass();
			$bcm->first_name = $value->first_name;
			$bcm->last_name = $value->last_name;
			$bcm->email_id = $value->email_id;
			$bcm->loginid = $value->loginid;
			$bcm->user_id = $value->user_id;
			$bcm->is_active = $value->is_active;
			$bcm->address_id = $value->address_id;
			$bcm->created_date = $value->created_date;
			$bcm->country = $value->country;
			$bcm->mother_tongue = $mother_tongue;
			array_push($userArr,$bcm);
			
			$stmt->closeCursor();
		}
			
		return $userArr;
}

	//========= Get user by center and batch
	public function searchUsersByCenterAndBatchAndName($center_id,$batch_id,$role_id,$country='',$user_id='',$name='',$status='',$region_id=''){ 
		
		 //echo "<pre>";print_r($_POST);exit;
			$regionWhr = 'where 1=1';
		
			if($name!=""){
			$regionWhr.= " AND ((u.first_name LIKE :first_name or u.last_name LIKE :last_name  or CONCAT(u.first_name,' ',u.last_name ) LIKE  :first_last_name or CONCAT(u.first_name,'',u.last_name ) LIKE  :first_last_name1) OR (u.email_id LIKE :email_id) OR (uc.loginid LIKE :loginid))";
			
			}
			if($batch_id!="" && $batch_id!='All'){
			  $regionWhr.= ' AND ubm.batch_id IN('.$batch_id.')';
			  
			 }
			
			if($role_id!="" && $role_id!='All'){
				$regionWhr.= " AND urm.role_definition_id = :role_definition_id";
			
			}

			
			if($region_id!="" && $region_id!='All'){
				$regionWhr.= " AND ( tr.id  = :region_id";
				$regionWhr.= " OR rum.region_id  = :region_id1 )";

				} 

			if($center_id!="" && $center_id!='All'){
				$regionWhr .= " AND tc.center_id = :center_id1";
				$regionWhr.= " AND ucm.center_id  = :center_id2";
			}
			
			if($user_id!="" && $user_id!='All'){
				
				$regionWhr.= " AND u.user_id = :user_id";
			}

			$limit_sql = ' limit 100';
			
			$total = 0;
			if($batch_id!="" && $batch_id!='All'){
			  
			 $sql ="SELECT u.*,tc.name AS state, MAX(vu.date_with_time) AS last_visit from user AS u JOIN user_role_map as urm ON u.user_id = urm.user_id 
			JOIN user_credential uc ON u.user_id=uc.user_id
			LEFT JOIN tblx_region_user_map as rum ON rum.user_id = u.user_id LEFT JOIN user_center_map as ucm ON ucm.user_id = u.user_id JOIN tblx_batch_user_map ubm on u.user_id=ubm.user_id JOIN tblx_center tc on ubm.center_id=tc.center_id LEFT JOIN tblx_region as tr ON tc.region = tr.id LEFT JOIN visiting_user as vu ON vu.user_id = u.user_id $regionWhr GROUP BY u.user_id $order  $limit_sql";
			
			 }else{
				  $sql ="SELECT u.*,tc.name AS state, MAX(vu.date_with_time) AS last_visit from user AS u JOIN user_role_map as urm ON u.user_id = urm.user_id 
			JOIN user_credential uc ON u.user_id=uc.user_id
			LEFT JOIN tblx_region_user_map as rum ON rum.user_id = u.user_id LEFT JOIN user_center_map as ucm ON ucm.user_id = u.user_id LEFT JOIN tblx_center as tc ON tc.center_id = ucm.center_id LEFT JOIN tblx_region as tr ON tc.region = tr.id LEFT JOIN visiting_user as vu ON vu.user_id = u.user_id $regionWhr GROUP BY u.user_id $order  $limit_sql";
			
			 }
			
			$stmt = $this->dbConn->prepare($sql);
		
		if($name!=""){
			
			
			$stmt->bindValue(':first_name','%'.$name.'%', PDO::PARAM_STR);
			$stmt->bindValue(':last_name','%'.$name.'%', PDO::PARAM_STR);
			$stmt->bindValue(':first_last_name','%'.$name.'%', PDO::PARAM_STR);
			$stmt->bindValue(':first_last_name1','%'.$name.'%', PDO::PARAM_STR);
			$stmt->bindValue(':email_id','%'.$name.'%', PDO::PARAM_STR);
			$stmt->bindValue(':loginid','%'.$name.'%', PDO::PARAM_STR);
			
			}
			
		
			if($role_id!="" && $role_id!='All'){
					
				$stmt->bindValue(':role_definition_id',$role_id,PDO::PARAM_INT);
			}
			if($region_id!="" && $region_id!='All'){
					
					$stmt->bindValue(':region_id',$region_id,PDO::PARAM_INT);
					$stmt->bindValue(':region_id1',$region_id,PDO::PARAM_INT);
					
				} 

			if($center_id!="" && $center_id!='All'){
				$stmt->bindValue(':center_id1',$center_id,PDO::PARAM_INT);
				$stmt->bindValue(':center_id2',$center_id,PDO::PARAM_INT);
			}
			
			if($user_id!="" && $user_id!='All'){
				
				$stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
			}
	
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
	
		
		return $RESULT;
}

//========= Get user by center and district
	public function searchUsersByCenterAndDistrictAndName($center='',$district_id='',$role_id,$country='',$user_id='',$name='',$status='',$region_id=''){ 
		
			$whr = "where 1=1";
			$whr.= " AND u1.user_id=uc1.user_id and u1.user_id=urm.user_id and u1.address_id=am.address_id and u1.user_id=ucm.user_id and ucm.center_id=tc.center_id and tc.country=trcm.country_name and urm.role_definition_id=:role_definition_id and u1.user_client_id=2";

			if($center!="" && $center!='All'){
				$whr.= " AND ucm.center_id =:center_id";
			}
			if($district_id!="" && $district_id!='All'){
				$whr.= " AND ucm.district_id =:district_id";
			}
			if($country!="" && $country!='All'){
				$whr.= " AND tc.country =:country";
			}
			if($user_id!="" && $user_id!='All'){
				$whr.= " AND u1.user_id =:user_id";
			}
			if($name!=""){
			$whr.= " AND ((u1.first_name LIKE :first_name or u1.last_name LIKE :last_name  or CONCAT(u1.first_name,'',u1.last_name ) LIKE  :first_last_name  or CONCAT(u1.first_name,' ',u1.last_name ) LIKE  :first_last_name1) OR (u1.email_id LIKE :email_id) OR (uc1.loginid LIKE :loginid))";
			}
		
			if($status!=""  || $status=='0'){
				$whr.= " AND uc1.is_active =:status";
			}
			if($region_id!="" && $region_id!='All'){
				$whr.= " AND trcm.region_id =:region_id";
			}
			$sql = "SELECT DISTINCT u1.user_id, u1.address_id, uc1.loginid, uc1.created_date, u1.first_name, u1.last_name, u1.email_id, u1.is_active,am.country,u1.mother_tongue FROM user u1, user_credential uc1, user_role_map urm , address_master am,  tblx_center tc, user_center_map ucm,  tblx_region_country_map  trcm  $whr order by u1.created_date desc LIMIT 100";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':role_definition_id',$role_id,PDO::PARAM_INT);
			if($name!=""){
				$stmt->bindValue(':first_name','%'.$name.'%',PDO::PARAM_STR);
				$stmt->bindValue(':last_name','%'.$name.'%',PDO::PARAM_STR);
				$stmt->bindValue(':first_last_name','%'.$name.'%',PDO::PARAM_STR);
				$stmt->bindValue(':first_last_name1','%'.$name.'%',PDO::PARAM_STR);
				$stmt->bindValue(':email_id','%'.$name.'%',PDO::PARAM_STR);
				$stmt->bindValue(':loginid','%'.$name.'%',PDO::PARAM_STR);
			}
			if($center!="" && $center!='All'){
			$stmt->bindValue(':center_id',$center,PDO::PARAM_INT);
			}
			if($district_id!="" && $district_id!='All'){
			$stmt->bindValue(':district_id',$district_id,PDO::PARAM_INT);
			}
			if($country!="" && $country!='All'){
				$stmt->bindValue(':country',$country,PDO::PARAM_STR);
			}
			if($user_id!="" && $user_id!='All'){
				$stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
			}
			
			if($status!=""  || $status=='0'){
				$stmt->bindValue(':status',$status,PDO::PARAM_INT);
			}
			if($region_id!="" && $region_id!='All'){
				$stmt->bindValue(':region_id',$region_id,PDO::PARAM_INT);
			}
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor(); 
			$userList = array();
		   while($row = array_shift( $RESULT )) {
				$bcm = new stdClass();
				$bcm->first_name = $row['first_name'];
				$bcm->last_name = $row['last_name'];
				$bcm->email_id = $row['email_id'];
				$bcm->loginid = $row['loginid'];
				$bcm->user_id = $row['user_id'];
				$bcm->is_active = $row['is_active'];
				$bcm->address_id = $row['address_id'];
				$bcm->created_date = $row['created_date'];
				$bcm->country = $row['country'];
				$bcm->mother_tongue = $row['mother_tongue'];
				array_push($userList,$bcm);
			
			}

		$userArr= array();
		foreach($userList as $key => $value){
			
			$stmt = $this->dbConn->prepare("select name from tblx_mother_tongue where id='".$value->mother_tongue."'");
			$stmt->execute();
			$RESULT1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$row = array_shift( $RESULT1 );
			$mother_tongue = isset($row['name'])?$row['name']:'-';
			
			$bcm = new stdClass();
			$bcm->first_name = $value->first_name;
			$bcm->last_name = $value->last_name;
			$bcm->email_id = $value->email_id;
			$bcm->loginid = $value->loginid;
			$bcm->user_id = $value->user_id;
			$bcm->is_active = $value->is_active;
			$bcm->address_id = $value->address_id;
			$bcm->created_date = $value->created_date;
			$bcm->country = $value->country;
			$bcm->mother_tongue = $mother_tongue;
			array_push($userArr,$bcm);
			
			$stmt->closeCursor();
		}
			
		return $userArr;
}




//========= Get center by center name
public function searchCenterByCenterName($client_id,$name='',$region_id='',$hide_b2c=''){ 
		
			$whr = "where 1=1 AND tc.status=1";
			
			if($client_id!=""){
				$whr.= " AND tc.client_id LIKE :client_id ";
				//$whr.= " AND tc.country = '$country'";
			}
			
			if($name!=""){
				$whr.= " AND tc.name LIKE :name";
				//$whr.= " AND tc.country = '$country'";
			}
			
			if($region_id!="" && $region_id!='All'){
				$whr.= " AND trcm.region_id =:region_id";
			}
			if($hide_b2c!=""){
				$whr.= " AND tc.center_id !=:hide_b2c";
			}
		
			$sql = "Select tc.* from tblx_center AS tc LEFT JOIN tblx_region_country_map AS trcm ON tc.country=trcm.country_name $whr group by tc.center_id order by tc.center_id DESC"; 
			$stmt = $this->dbConn->prepare($sql);
			if($client_id!=""){
				$stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
			}
			if($name!=""){
				$stmt->bindValue(':name', '%'.$name.'%', PDO::PARAM_STR);
			}
			if($region_id!=""){
				$stmt->bindValue(':region_id', $region_id, PDO::PARAM_INT);
			}
			if($hide_b2c!=""){
				$stmt->bindValue(':hide_b2c', $hide_b2c, PDO::PARAM_INT);
			}
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			$centerList = array();
		   while($row = array_shift( $RESULT )) {
				$bcm = new stdClass();
				$bcm->center_id = $row['center_id'];
				$bcm->name = $row['name'];
				array_push($centerList,$bcm);
			
			}

		return $centerList;
}


//Get skill report data
 public function getUsersSkillData($cond_arr = array(), $start = 0, $limit = 10,$order,$dir){
		
			$whr = "where 1=1";
			$columnArr = array();
			if($cond_arr['country']!="" && $cond_arr['country']!='All'){
				$whr.= " AND country_name = '".$cond_arr['country']."'";
				$whr.= " AND country_name =:country";
			}
			if($cond_arr['center_id']!="" && $cond_arr['center_id']!='All'){
				$whr.= " AND center_id = '".$cond_arr['center_id']."'";
				$whr.= " AND center_id =:center_id";
			}
			if($cond_arr['batch_id']!="" && $cond_arr['batch_id']!='All'){
				$whr.= " AND batch_id = '".$cond_arr['batch_id']."'";
				$whr.= " AND batch_id =:batch_id";
			}
			if($cond_arr['user_id']!="" && $cond_arr['user_id']!='All'){
				$whr.= " AND user_id =:user_id";
			}
			
			if(in_array($order,$columnArr) && in_array($dir,$ascdscArr)){
				$order= " ORDER BY ".$order." ".$dir."";
			}else{
				$order= "";
			}

			$limit_sql = '';
			if( !empty($limit) && is_numeric($start) && is_numeric($limit)){
				$limit_sql .= " LIMIT $start, $limit";
			}

			$sql = "Select count(DISTINCT rpt_id) as 'cnt' FROM rpt_user_skill_tracking $whr ";
			$stmt = $this->dbConn->prepare($sql);	
			$stmt->execute();
			$RESULT_CNT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			$row_cnt = array_shift( $RESULT_CNT );



			$sql = "SELECT * FROM rpt_user_skill_tracking $whr ORDER BY $order $limit_sql";
			//echo $sql;
			$stmt = $this->dbConn->prepare($sql);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			
			return array('total' =>$row_cnt['cnt'] , 'result' => $RESULT);

	}
	
	
	//Get License Used date
	public function getLicenseUsedDate($license_value,$client_id=''){ 

		$whr = "where 1=1";
		if(!empty($client_id)){
			
			$whr.= " AND lic_req_client_id = '".$client_id."'";
		}
		
		$whr.= " AND license_value = '".$license_value."'";
		
		
		
		$sql = "Select used_date from tbl_client_licenses $whr";
		$stmt = $this->dbConn->prepare($sql);	
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$row_used_date = array_shift( $RESULT );
		
		if(isset($row_used_date['used_date']) && $row_used_date['used_date']!=""){
			return $row_used_date['used_date'];
		}

		return;


	}
	
	public function displayText($str){
		$str=stripslashes($str);

		$str=str_replace("&quot;","'",$str);
		//$str=str_replace('"','',$str);
		//$str=str_replace('&lt;','<',$str);
		//$str=str_replace('&gt;','>',$str);
		$str=str_replace('<','&lt;',$str);
		$str=str_replace('>','&gt;',$str);
		//$str=str_replace('  ',' ',$str);
		//$str=str_replace('"',"'",$str);
		$str=str_replace("&#38;#39;","'",$str);
		$str=str_replace("&#39;","'",$str);

		$str=str_replace("â€‹Ã¢â‚¬â€¹","",$str);
		$str=str_replace("&#38;",'&#38;',$str);
		$str= mb_convert_encoding($str, 'UTF-8', 'UTF-8');
		
		if($str==""){
			$str="";
		}
		return $str;
	}

function getSkillnameById($skill){
	
	$stmt = $this->dbConn->prepare("SELECT competency FROM tbl_rubric_competency WHERE id='".$skill."'");
	$stmt->execute();
	$RESULT1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$row = array_shift( $RESULT1 );
	$competency = isset($row['competency'])?$row['competency']:'-';
	if($competency!="" && $competency!=NULL && !empty($competency)){
		return $competency;
	}
	return '';
}

function publishText($str){
	$str=stripslashes($str);
	
	////check: $str=str_replace('"','&quot;',$str);
	$str=str_replace('&quot;',"'",$str);
	$str=str_replace("&#39;","'",$str);
	$str=str_replace("&#38;#39;","'",$str);
	//$str=str_replace("&#38;",'&',$str);
	$str=str_replace("&",'&#38;',$str);
	$str=str_replace('<','&lt;',$str);
	$str=str_replace('>','&gt;',$str);
	$str=str_replace('&#38;#38;','&#38;',$str);
	
	
	
	//$str= mb_convert_encoding($str, 'UTF-8', 'UTF-8');
	
	if($str==""){
		$str="";
	}
	return $str;
}


# Function is written by Prabhat Kumar Tiwari
public function getCountryByRegion($region_id=''){
	$whr = 'WHERE 1=1';
	if($region_id!="" && $region_id!="All"){
	$whr.= " AND trcm.region_id = :region_id";
	}

	$sql = "Select tc.* from tbl_countries AS tc LEFT JOIN tblx_region_country_map AS trcm ON tc.country_name=trcm.country_name $whr group by tc.id order by tc.country_name asc";
	$stmt = $this->dbConn->prepare($sql);
	if($region_id!="" && $region_id!="All"){
		$stmt->bindValue(':region_id', $region_id, PDO::PARAM_INT);
	}
	$stmt->execute();
	$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt->closeCursor();
	$countryList = array();
	while($row = array_shift( $RESULT )) {
	$bcm = new stdClass();
	$bcm->id = $row['id'];
	$bcm->country_name = $row['country_name'];
	array_push($countryList,$bcm);

	}
  return $countryList;
}
	//========= Search region admin by region id and name
	public function searchRegionAdminByRegionIdAndName($region_id='',$role_id,$name='',$status=''){ 
		
			$whr = "where 1=1";
			$whr.= " AND u1.user_id=uc1.user_id and u1.user_id=urm.user_id and u1.address_id=am.address_id and u1.user_id=trum.user_id and trum.region_id=tr.id and urm.role_definition_id=$role_id and u1.user_client_id=2";

			if($region_id!="" && $region_id!='All'){
				$whr.= " AND trum.region_id =:region_id";
			}
			
			if($name!=""){
			$whr.= " AND ((u1.first_name LIKE :first_name or u1.last_name LIKE :last_name  or CONCAT(u1.first_name,'',u1.last_name ) LIKE  :first_last_name) OR (u1.email_id LIKE :email_id) OR (uc1.loginid LIKE :loginid))";
			}
			
			if($status!=""  || $status=='0'){
				$whr.= " AND uc1.is_active =:status";
			}

			$sql = "SELECT DISTINCT u1.user_id, u1.address_id, uc1.loginid, uc1.created_date, u1.first_name, u1.last_name, u1.email_id, u1.is_active,am.country,tr.region_name FROM user u1, user_credential uc1, user_role_map urm , address_master am, tblx_region_user_map trum, tblx_region tr $whr order by u1.created_date desc LIMIT 100";
			$stmt = $this->dbConn->prepare($sql);
			if($region_id!="" && $region_id!='All'){
				$stmt->bindValue(':region_id',$region_id,PDO::PARAM_INT);
			}
			if($name!=""){
				$stmt->bindValue(':first_name','%'.$name.'%',PDO::PARAM_STR);
				$stmt->bindValue(':last_name','%'.$name.'%',PDO::PARAM_STR);
				$stmt->bindValue(':first_last_name','%'.$name.'%',PDO::PARAM_STR);
				$stmt->bindValue(':email_id','%'.$name.'%',PDO::PARAM_STR);
				$stmt->bindValue(':loginid','%'.$name.'%',PDO::PARAM_STR);
			}
			if($status!=""  || $status=='0'){
				$stmt->bindValue(':status',$status,PDO::PARAM_INT);
			}
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			$userList = array();
		   while($row = array_shift( $RESULT )) {
				$bcm = new stdClass();
				$bcm->first_name = $row['first_name'];
				$bcm->last_name = $row['last_name'];
				$bcm->email_id = $row['email_id'];
				$bcm->loginid = $row['loginid'];
				$bcm->user_id = $row['user_id'];
				$bcm->is_active = $row['is_active'];
				$bcm->address_id = $row['address_id'];
				$bcm->created_date = $row['created_date'];
				$bcm->country = $row['country'];
				$bcm->region_name = $row['region_name'];
				array_push($userList,$bcm);
			
			}

		
			
		return $userList;
}
	#To get total users by user_from
	public function getTotalUserByUserFrom($user_from,$start_date,$end_date){
		
		$sql = "Select COUNT(user_id) as 'cnt' from user where DATE(created_date)>= '$start_date' and DATE(created_date)<= '$end_date' and user_from = '$user_from'";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$row_cnt = array_shift( $RESULT );
		
		
		return $row_cnt['cnt'];
	}
	
	#To get total visited users by user_from
	public function getVisitedUserByUserFrom($user_from,$start_date,$end_date){
		
		$sql = "Select COUNT(DISTINCT u.user_id) as 'cnt' from user u JOIN visiting_user vu ON u.user_id = vu.user_id where DATE(u.created_date)>= '$start_date' and DATE(u.created_date)<= '$end_date' and u.user_from = '$user_from'";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$row_cnt = array_shift( $RESULT );
		
		
		return $row_cnt['cnt'];
	}
	
	#To user who attempted pretest by user_from
	public function getUserAttemptedPretestByUserFrom($user_from,$start_date,$end_date){
		
		$sql = "Select COUNT(DISTINCT u.user_id) as 'cnt' from user u JOIN tbl_user_lti_score tuls ON u.user_id = tuls.user_id where DATE(u.created_date)>= '$start_date' and DATE(u.created_date)<= '$end_date' and u.user_from = '$user_from'";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$row_cnt = array_shift( $RESULT );
		
		
		return $row_cnt['cnt'];
	}
	#To user who completed pretest by user_from
	public function getUserCompletedPretestByUserFrom($user_from,$start_date,$end_date){
		
		$sql = "Select COUNT(DISTINCT u.user_id) as 'cnt' from user u JOIN tbl_user_lti_score tuls ON u.user_id = tuls.user_id where DATE(u.created_date)>= '$start_date' and DATE(u.created_date)<= '$end_date' and u.user_from = '$user_from' and tuls.imsx_messageIdentifier <> '0'";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$row_cnt = array_shift( $RESULT );
		
		
		return $row_cnt['cnt'];
	}
	
	#Get users who break level by user_from
	public function getBreakLevelUserByUserFrom($user_from,$start_date,$end_date){
		
		$sql = "Select COUNT(DISTINCT u.user_id) as 'cnt' from user u JOIN tbl_user_lti_score tuls ON u.user_id = tuls.user_id where DATE(u.created_date)>= '$start_date' and DATE(u.created_date)<= '$end_date' and u.user_from = '$user_from' and tuls.user_visiting_level > tuls.user_current_level";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$row_cnt = array_shift( $RESULT );
		
		
		return $row_cnt['cnt'];
	}
	
	#Get users who move  from one level to other by user_from
	public function getMoveLevelUserByUserFrom($user_from,$start_date,$end_date){
		
		$sql = "Select COUNT(DISTINCT u.user_id) as 'cnt' from user u JOIN tbl_user_lti_score tuls ON u.user_id = tuls.user_id where DATE(u.created_date)>= '$start_date' and DATE(u.created_date)<= '$end_date' and u.user_from = '$user_from' and tuls.user_current_level > tuls.user_start_level";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$row_cnt = array_shift( $RESULT );
		
		return $row_cnt['cnt'];
	
	}
	#Get all pearson modules
	public function getAllModule(){
		
		$sql = "SELECT c.code,cm.name,cm.description,gmt2.edge_id,gmt2.tree_node_id FROM generic_mpre_tree gmt JOIN course c ON c.tree_node_id = gmt.tree_node_id JOIN generic_mpre_tree gmt2 ON gmt2.tree_node_super_root = gmt.edge_id JOIN tree_node_def tnd ON tnd.tree_node_id = gmt2.tree_node_id JOIN cap_module cm ON cm.tree_node_id = gmt2.tree_node_id WHERE gmt2.is_active = 1 and tnd.tree_node_category_id IN (5,3) and cm.assessment_type IS NULL and cm.topic_type=1 and c.client_id=2";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$moduleList = array();
	    while($row = array_shift( $RESULT )) {
			
			array_push($moduleList,$row['edge_id']);
		
		}

		$moduleList = implode(',',$moduleList);
			
		return $moduleList;
	}
	#Get time spent on Modules
	public function getModuleTimeSpent($user_from,$start_date,$end_date){
		

		$moduleList = $this->getAllModule();
		$sql = "SELECT COALESCE(SUM(ust.actual_seconds),0) as ttlCompTimeSp from generic_mpre_tree gmt 
							JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id 
							JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id 
							JOIN tbl_component tc ON gmt.edge_id= tc.parent_edge_id 
							JOIN user_session_tracking ust ON ust.session_id= tc.component_edge_id 
							JOIN user u ON u.user_id= ust.user_id 
							where DATE(u.created_date)>= '$start_date' AND DATE(u.created_date)<= '$end_date' AND u.user_from = '$user_from' AND  gmt.tree_node_parent IN($moduleList) AND ust.session_type = 'CM'  AND LENGTH(ust.unique_code) >= 10 AND tc.topic_type = 1 AND cm.topic_type = 1 AND tc.status = 1";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$row = array_shift( $RESULT );
		
		return $row['ttlCompTimeSp'];
			
		
	}
	
	#Get time spent on skills
	public function getSkillTimeSpent($user_from,$start_date,$end_date){
		
			$sql = "SELECT COALESCE(SUM(ust.actual_seconds),0) as ttlCompTimeSp from generic_mpre_tree gmt 
							JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id 
							JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id 
							JOIN tbl_component tc ON gmt.edge_id= tc.parent_edge_id 
							JOIN user_session_tracking ust ON ust.session_id= tc.component_edge_id 
							JOIN user u ON u.user_id= ust.user_id 
							where DATE(u.created_date)>= '$start_date' AND DATE(u.created_date)<= '$end_date' AND u.user_from = '$user_from' AND cm.chapterSkill IN(1,2,3,4,5,6) AND ust.session_type = 'CM' AND LENGTH(ust.unique_code) >= 10 AND tc.topic_type = 1 AND cm.topic_type = 1 AND tc.status = 1";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$row = array_shift( $RESULT );
		
		return $row['ttlCompTimeSp'];
			
		
	}
	
	
	#To user who attempted pretest by user_from and level
	public function getTotalUserByUserFromAndByLevel($user_from,$start_date,$end_date,$level){
		
		$sql = "Select COUNT(DISTINCT u.user_id) as 'cnt' from user u JOIN tbl_user_lti_score tuls ON u.user_id = tuls.user_id where DATE(u.created_date)>= '$start_date' and DATE(u.created_date)<= '$end_date' and u.user_from = '$user_from' and tuls.user_current_level='$level'";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$row_cnt = array_shift( $RESULT );
		
		
		return $row_cnt['cnt'];
	}
		/* Ankesh Start*/
	//========= Search region admin by region id and name
	public function searchCenterAdminByCenterIdAndName($center_id='',$role_id,$name='',$status=''){ 
		
		$whr = "where 1=1";
		$whr.= " AND u1.user_id=uc1.user_id and u1.user_id=urm.user_id and u1.address_id=am.address_id and u1.user_id=ucm.user_id and ucm.center_id=tc.center_id and urm.role_definition_id=:role_definition_id and u1.user_client_id=2";

		if($center_id!="" && $center_id!='All'){
			$whr.= " AND ucm.center_id =:center_id";
			//$whr.= " AND ucm.center_id = $center_id";
		}
		
		if($name!=""){
		$whr.= " AND ((u1.first_name LIKE :first_name or u1.last_name LIKE :last_name  or CONCAT(u1.first_name,'',u1.last_name ) LIKE  :first_last_name) OR (u1.email_id LIKE :email_id) OR (uc1.loginid LIKE :loginid))";
		//$whr.= " AND ((u1.first_name LIKE '%$name%' or u1.last_name LIKE '%$name%'  or CONCAT(u1.first_name,'',u1.last_name ) LIKE  '%$name%') OR (u1.email_id LIKE '%$name%') OR (uc1.loginid LIKE '%$name%'))";
			//$whr.= " AND tc.country = '$country'";
		}
		
		if($status!=""  || $status=='0'){
			$whr.= " AND uc1.is_active =:is_active";
			//$whr.= " AND tc.country = '$country'";
		}

		$sql = "SELECT DISTINCT u1.user_id, u1.address_id, uc1.loginid, uc1.created_date, u1.first_name, u1.last_name, u1.email_id, u1.is_active,am.country,tc.name FROM user u1, user_credential uc1, user_role_map urm , address_master am, user_center_map ucm, tblx_center tc $whr order by u1.created_date desc LIMIT 100";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':role_definition_id',$role_id,PDO::PARAM_INT);
		if($center_id!="" && $center_id!='All'){
			$stmt->bindValue(':center_id',$center_id,PDO::PARAM_INT);
		}
		if($name!=""){
			$stmt->bindValue(':first_name','%'.$name.'%',PDO::PARAM_STR);
			$stmt->bindValue(':last_name','%'.$name.'%',PDO::PARAM_STR);
			$stmt->bindValue(':first_last_name','%'.$name.'%',PDO::PARAM_STR);
			$stmt->bindValue(':email_id','%'.$name.'%',PDO::PARAM_STR);
			$stmt->bindValue(':loginid','%'.$name.'%',PDO::PARAM_STR);
		}
		if($status!=""  || $status=='0'){
			$stmt->bindValue(':status',$status,PDO::PARAM_INT);
		}
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$userList = array();
	   while($row = array_shift( $RESULT )) {
			$bcm = new stdClass();
			$bcm->first_name = $row['first_name'];
			$bcm->last_name = $row['last_name'];
			$bcm->email_id = $row['email_id'];
			$bcm->loginid = $row['loginid'];
			$bcm->user_id = $row['user_id'];
			$bcm->is_active = $row['is_active'];
			$bcm->address_id = $row['address_id'];
			$bcm->created_date = $row['created_date'];
			$bcm->country = $row['country'];
			$bcm->region_name = $row['region_name'];
			array_push($userList,$bcm);
		
		}

	
		
	return $userList;
}





public function getCentresList($region_id = '')
{	
	
	if($region_id != '' && $region_id != 0 && $region_id>0){
		$id = intval($region_id);
		$whrData = array('id'=>$id);
		$sql = 'SELECT id,region_name as centre from  tblx_region where id = :id';
		$res = $this->fetch_rows($sql,0,$whrData);
	}else{
		$sql = 'SELECT id,region_name as centre from  tblx_region';
		$res = $this->fetch_rows($sql);
	
	}

	return $res;
}
 




public function getDistrictList($center='',$country='',$region_id=''){ 

		$whr = "where 1=1";
		//$whr.= " AND tc.client_id = '$client_id' and status=1";
		
		if($center!="" && $center!='All'){
			$whr.= " AND td.state_id = :state";
		}

		if($country!="" && $country!='All'){
			//$whr.= " AND td.country = '$country'";
		}
		if($region_id!="" && $region_id!='All' && $region_id!=0){
				//$whr.= " AND trcm.region_id = '".$region_id."' ";
			}
			
		//$sql = "Select tc.* from tblx_center AS tc LEFT JOIN tblx_region_country_map AS trcm ON tc.country=trcm.country_name $whr group by tc.center_id order by tc.name asc";
		
		$sql = "Select td.* from tblx_district AS td  $whr  order by td.district_name asc";
		//echo "<pre>";print_r($sql);exit;	
		//select Center Online live server Database
		$stmt = $this->dbConn->prepare($sql);	
		if($center!="" && $center!='All'){
			$stmt->bindValue(':state',$center,PDO::PARAM_INT);
		}
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		return $RESULT;
   }


public function getTehsilList($center='',$district_id='',$country='',$region_id=''){ 

		$whr="where 1=1 and tt.status=1 and td.status=1";
		 
		if($district_id!=""){
		$whr.= " AND tt.district_id = :district_id";
		}

		if($center!=""){
		$whr.= " AND td.state_id = :state_id";
		}

		 $sql = "Select tt.*,td.district_name from tblx_tehsil tt 
		 JOIN tblx_district td ON tt.district_id=td.district_id $whr";
		$stmt = $this->dbConn->prepare($sql);	
		if($district_id!=""){
		$stmt->bindValue(':district_id', $district_id, PDO::PARAM_INT);
		}
		if($center!=""){
		$stmt->bindValue(':state_id', $center, PDO::PARAM_INT);
		}

		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		return $RESULT;
   }








// start for report 




public function sbGetCenterListByClient($client_id,$region_id=''){ 
	
	$whr = "where 1=1";
	$whr.= " AND tc.client_id =:client_id and status=1";
	
	if($center!="" && $center!='All'){
		$whr.= " AND tc.center_id =:center";
	}
	
	
	if($region_id!="" && $region_id!='All' && $region_id!=0){
			$whr.= " AND tc.region =:region";
		}
		
	 $sql = "Select tc.* from tblx_center AS tc LEFT JOIN tblx_region_country_map AS trcm ON tc.country=trcm.country_name $whr group by tc.center_id order by tc.center_type,tc.name asc";
	//select Center Online live server Database
	
	$stmt = $this->dbConn->prepare($sql);	
	$stmt->bindValue(':client_id',$client_id,PDO::PARAM_INT);
	if($center!="" && $center!='All'){
			$stmt->bindValue(':center_id',$center,PDO::PARAM_INT);
		}
	if($region_id!="" && $region_id!='All' && $region_id!=0){
			$stmt->bindValue(':region',$region_id,PDO::PARAM_INT);
		}
	$stmt->execute();
	$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt->closeCursor();

	$centerObj = new centerController(); 

	foreach($RESULT  as $key => $value){
			$RESULT[$key]['created_date'] = date('d-m-Y',strtotime($value['created_date']));

			$expiry_date = $value['expiry_date'];
			$expiry_days = $value['expiry_days'];
		if($expiry_date!="" && $expiry_date != '0000-00-00 00:00:00'){
			$RESULT[$key]['expiry_date']= date('d-m-Y H:i',strtotime($expiry_date));
		
		}else{
			$res_used_date = $this->getLicenseUsedDate($license_key);
			
			$expiry_date = date('d-m-Y H:i',strtotime($res_used_date . "+".$expiry_days." days"));
			$RESULT[$key]['expiry_date'] = date('d-m-Y H:i',strtotime($expiry_date));
			
		}

		$res = $centerObj->getSignedUpUserCountByCenter($value['client_id'],$value['center_id']);
				$res = (object) $res;
				$totalCenterTeacher = $res->totalCenterTeacher;
				$totalCenterStudent = $res->totalCenterStudent;
				$totalCenterTeacher = !empty($totalCenterTeacher)?$totalCenterTeacher:0;
				$totalCenterStudent = !empty($totalCenterStudent)?$totalCenterStudent:0;

				$RESULT[$key]['student_limit'] = $totalCenterStudent.'/'.$RESULT[$key]['student_limit'];
				$RESULT[$key]['trainer_limit'] = $totalCenterTeacher.'/'.$RESULT[$key]['trainer_limit'];
	}	

	return  $RESULT;


}

   
   public function sbGetStateList($region_id){
		$client_id =  $_SESSION['client_id'];
		
		$res = $this->sbGetCenterListByClient($client_id,$region_id);
		$this->session('states',$res);
		return $res;
   }
   
   public function sbGetDistrictList($post){ 
	   $center = $post['input'];
		$whr =  " where 1=1 ";
		$whrData = array();
		
		if($post['input'] != "" && $post['input'] != 'All' && $post['input'] != '0'){
			$whr.= " AND td.state_id = :state_id";
			$whrData['state_id']=$post['input'];
		}

		if($post['district'] != "" && $post['district'] != 'All' && $post['district'] != '0'){
			$whr.= " AND td.district_id = :district_id";
			$whrData['district_id'] = $post['district'];
		}
	
			

		$sql = "Select td.* from tblx_district AS td  $whr order by td.district_name asc";
	
		$RESULT = $this->fetch_rows($sql,1,$whrData);

		foreach ($RESULT as $key => $value) {
			$did = $value['district_id'] ;
			$tmpRes1 = $this->fetch("SELECT count(*) teacherReg FROM user_role_map uld 
			inner join user_center_map uc ON uld.user_id = uc.user_id 
			and uld.role_definition_id =1 and uld.is_active =1 
			where uc.center_id=:center_id AND uc.district_id = $did AND uc.client_id=2",0,
			array('center_id'=>$center));

			$tmpRes2 = $this->fetch("SELECT count(*) studentReg FROM user_role_map uld 
			inner join user_center_map uc ON uld.user_id = uc.user_id 
			and uld.role_definition_id =2 and uld.is_active =1 
			where uc.center_id=:center_id AND uc.district_id = $did AND uc.client_id=2",0,
			array('center_id'=>$center));



			$RESULT[$key]['teacherReg'] = $tmpRes1->teacherReg;
			$RESULT[$key]['studentReg'] = $tmpRes2->studentReg;
		}

		$this->session('districts',$RESULT);
		return $RESULT;
   }
   
   public function sbGetTehsilList($post){ 
		$district = $post['input'];
		$whr =  " where 1=1 ";
		$whrData = array();
	
		if($district  != "" && $district != 'All' && $district != '0'){
			$whr.= " AND tt.district_id = :district_id";
			$whrData['district_id'] = $district;
		}

		if($post['tehsil'] != "" && $post['tehsil'] != 'All' && $post['tehsil'] != '0'){
			$whr.= " AND tt.tehsil_id = :tehsil_id";
			$whrData['tehsil_id'] = $post['tehsil'];
		}
		
		if($post['center_id'] != "" && $post['center_id'] != 'All' && $post['center_id'] != '0'){
			$whr.= " AND td.state_id = :center_id";
			$whrData['center_id'] = $post['center_id'];
		}
			
	
		 $sql = "Select tt.* from tblx_tehsil AS tt JOIN tblx_district td ON tt.district_id=td.district_id $whr order by tt.tehsil_name ASC";
	
		 $RESULT = $this->fetch_rows($sql,1,$whrData);	
	
		foreach ($RESULT as $key => $value) {
			$tid = $value['tehsil_id'] ;
			$tmpRes1 = $this->fetch("SELECT count(*) teacherReg FROM user_role_map uld 
			inner join user_center_map uc ON uld.user_id = uc.user_id 
			and uld.role_definition_id =1 and uld.is_active =1 
			where uc.tehsil_id=$tid AND uc.district_id = :district_id AND uc.client_id=2",0,
			array('district_id'=>$district));

			$tmpRes2 = $this->fetch("SELECT count(*) studentReg FROM user_role_map uld 
			inner join user_center_map uc ON uld.user_id = uc.user_id 
			and uld.role_definition_id =2 and uld.is_active =1 
			where uc.tehsil_id=$tid AND uc.district_id = :district_id AND uc.client_id=2",0,
			array('district_id'=>$district));

			$RESULT[$key]['teacherReg'] = $tmpRes1->teacherReg;
			$RESULT[$key]['studentReg'] = $tmpRes2->studentReg;
		}
		$this->session('tehsils',$RESULT);
		return $RESULT;
   }




   public function sbGetClassList($tehsil='',$country='',$region_id='',$center_id=''){ 

		return array();
   }



   public function sbGetSignedUpUserCount($payload=array()){
	$whr = "where 1=1";
	
	$client_id=$_SESSION['client_id'];
	 $sql="SELECT count(*) studentReg FROM  user_role_map  uld 
	inner join user_center_map uc ON uld.user_id =  uc.user_id 
	and uld.role_definition_id =2 and uld.is_active =1 
	where uc.center_id=1 AND uc.client_id=$client_id";

	$stmt = $this->dbConn->prepare($sql);
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





   public function session($key,$value = null){
	   if($value == null)
		   return isset($_SESSION[$key])?$_SESSION[$key]:[];
		else  
		   $_SESSION[$key] = $value; 
   }



   public function getUsersByStateRanking($cond_arr = array(), $start = 0, $limit = 10,$order,$dir){
	
	
	$whr =  " where 1=1 ";
	$whrData =  array();
	$region = isset($cond_arr['region_id']) ? $cond_arr['region_id']:"";
		

	if($region  != "" && $region != 'All' && $region != '0'){
		$whr.= " AND tc.region = :region";
		$whrData['region'] = $region;
	 
			
			$sql = "SELECT SUM(score) as score,tc.center_id,tc.name,COUNT(ucm.user_id) AS users, AVG(score) AS total_avg  FROM tblx_user_score AS tus 
			JOIN user_center_map AS ucm ON ucm.user_id = tus.user_id
			JOIN tblx_center AS tc ON tc.center_id = ucm.center_id
			$whr GROUP BY tc.name";
			$users_arr = $this->fetch_rows($sql,1,$whrData);
			foreach($users_arr  as $key => $value){
				$users_arr[$key]['weightage_20'] = ($value['total_avg']*20)/100 ;
				$weightage = $users_arr[$key]['weightage_20'];
					$center_id = $value['center_id'];
					$name = $value['name'];
					$score = $value['score'];
					$users = $value['users'];
					$average = $value['total_avg'];
				$ckSql = "SELECT COUNT(*) as cnt FROM rpt_state_ranking WHERE center_id =  ".$value['center_id'];
				$ckarr = $this->fetch($ckSql,1);
				if($ckarr['cnt'] > 0 ){
					$sql = "UPDATE rpt_state_ranking SET users=?,score=?,average = ?, weightage_1=?,total=? WHERE center_id=?";
					$this->dbConn->prepare($sql)->execute([$users,$score,$average,$weightage,$weightage,$center_id]);
				}else{
					
					$sql = "INSERT INTO rpt_state_ranking (center_id, name,users,score,average, weightage_1,total) VALUES (?,?,?,?,?,?,?)";
					$this->dbConn->prepare($sql)
					->execute([ $center_id,$name,$users,$score,$average, $weightage,$weightage]);
					
				}
				
				

			}
			$return = array('total'=>count($users_arr),'result'=>$users_arr);

			

		}else{
			$return = array('total'=>0,'result'=>array());
		}

		return $return; 

	}  


   public function getUsersByStateCountRanking($cond_arr = array(), $start = 0, $limit = 10,$order,$dir){
	$topic_edge_ids = array(9457, 9596, 9601, 9606, 9611, 9616, 9621, 9626, 9631, 9636);
	$total_modules = count($topic_edge_ids);
	$whr =  " where 1=1 AND tcc.completion = 'c' AND urm.role_definition_id = 2 ";
	$whrData = array();
	$region = isset($cond_arr['region_id']) ? $cond_arr['region_id']:"";


	if($region  != "" && $region != 'All' && $region != '0'){
		$whrData['region'] = $region;
		
			$state_sql  = "SELECT tc.center_id,tc.name FROM 
			tblx_center AS tc where  tc.region = :region";
			
			$all_states = $this->fetch_rows($state_sql,1,$whrData);
	
			


			foreach ($all_states as $key => $state) {
				$center_id = $state['center_id'];

			

			
				$users_sql  = "SELECT u.user_id FROM user  AS u 
			JOIN user_center_map as ucm ON ucm.user_id = u.user_id 
			JOIN tblx_center AS tc ON tc.center_id = ucm.center_id 
			JOIN user_role_map as urm ON urm.user_id = u.user_id 
			where  urm.role_definition_id = 2 AND tc.center_id = $center_id AND tc.region = $region";
			
				$all_states[$key]['users'] =   $this->fetch_rows($users_sql,1);
				$total_completed = 0;
				$partial_completed = 0;
				foreach($all_states[$key]['users'] as $u => $user):
					
									
					$module_complete = "SELECT COUNT(*) AS total FROM tblx_component_completion  
					WHERE user_id = ".$user['user_id']." AND completion = 'c' 
					AND component_edge_id IN (".implode(',',$topic_edge_ids).")";
					$completed = $this->fetch($module_complete,1);

						$all_states[$key]['users'][$u]['completed'] =  $completed['total'];
						if($total_modules == $completed['total'])
							$total_completed++;
						else
							$partial_completed += $completed['total']>0? $completed['total']/$total_modules:0;
						
				endforeach;

				$totalUsers = count($all_states[$key]['users']);
				unset($all_states[$key]['users']);
				$total_users_percentage = ($total_completed*$totalUsers)/100;
				$all_states[$key]['total_completed_module'] = $total_completed;
				$all_states[$key]['total__partial_completed_module'] = $partial_completed;
				$all_states[$key]['total_module'] = $total_modules;
				$all_states[$key]['total_users'] = 
				$all_states[$key]['total_users_percentage'] = $total_users_percentage;
				$all_states[$key]['total_users_weightage_40'] = (40*$total_users_percentage)/100;

				$weightage = $all_states[$key]['total_users_weightage_40'] ;
				$center_id = $state['center_id'];
				$name = $state['name'];
						$ckSql = "SELECT COUNT(*) as cnt FROM rpt_state_ranking WHERE center_id =  ".$center_id;
						$ckarr = $this->fetch($ckSql,1);
						if($ckarr['cnt'] > 0 ){
							$sql = "UPDATE rpt_state_ranking SET weightage_2=$weightage,total=total+$weightage WHERE center_id=?";
							$this->dbConn->prepare($sql)->execute([$center_id]);
						}else{
							
							$sql = "INSERT INTO rpt_state_ranking (center_id, name, weightage_2,total) VALUES (?,?,?,total+$weightage)";
							$this->dbConn->prepare($sql)->execute([ $center_id,$name, $weightage]);
							
						}


				}
		
			
			$return = array('total'=>count( $all_states),'result'=> $all_states);

		
		}else{
			$return = array('total'=>0,'result'=>array());
		}
		return $return; 

	}  


	public function getStateRankingMandatoryModules($cond_arr = array(), $start = 0, $limit = 10,$order,$dir){
		$mmd = $this->fetch_rows("SELECT DISTINCT tmm.module_edge_id FROM 
		`tblx_mandatory_module` as tmm
		WHERE tmm.is_mandatory = 'yes'");

		$topic_edge_ids = array();
		foreach ($mmd  as $k => $m) {
			array_push($topic_edge_ids,$m->module_edge_id);
		}
		
		$total_modules = count($topic_edge_ids);
		$whr =  " where 1=1 AND tcc.completion = 'c' AND urm.role_definition_id = 2 ";
		$whrData = array();
		$region = isset($cond_arr['region_id']) ? $cond_arr['region_id']:"";
	
	
		if($region  != "" && $region != 'All' && $region != '0'){
			$whrData['region'] = $region;
			
				$state_sql  = "SELECT tc.center_id,tc.name FROM tblx_center AS 
				tc where  tc.region = :region";
				
				$all_states = $this->fetch_rows($state_sql,1,$whrData);
		
				
	
	
				foreach ($all_states as $key => $state) {
					$center_id = $state['center_id'];
					$users_sql  = "SELECT u.user_id FROM user  AS u 
				JOIN user_center_map as ucm ON ucm.user_id = u.user_id 
				JOIN tblx_center AS tc ON tc.center_id = ucm.center_id 
				JOIN user_role_map as urm ON urm.user_id = u.user_id 
				where  urm.role_definition_id = 2 AND tc.center_id = $center_id AND tc.region = $region";
				
					$all_states[$key]['users'] =   $this->fetch_rows($users_sql,1);
					$total_completed = 0;
					$partial_completed = 0;
					foreach($all_states[$key]['users'] as $u => $user):
						
										
						$module_complete = "SELECT COUNT(*) AS total FROM tblx_component_completion  
						WHERE user_id = ".$user['user_id']." AND completion = 'c' 
						AND component_edge_id IN (".implode(',',$topic_edge_ids).")";
						$completed = $this->fetch($module_complete,1);
	
							$all_states[$key]['users'][$u]['completed'] =  $completed['total'];
							if($total_modules == $completed['total'])
								$total_completed++;
							else
								$partial_completed += $completed['total']>0? $completed['total']/$total_modules:0;
							
					endforeach;
	
					$totalUsers = count($all_states[$key]['users']);
					unset($all_states[$key]['users']);
					$total_users_percentage = ($total_completed*$totalUsers)/100;
					$all_states[$key]['total_completed_module'] = $total_completed;
					$all_states[$key]['total__partial_completed_module'] = $partial_completed;
					$all_states[$key]['total_module'] = $total_modules;
					$all_states[$key]['total_users'] = 
					$all_states[$key]['total_users_percentage'] = $total_users_percentage;
					$all_states[$key]['total_users_weightage_40'] = (40*$total_users_percentage)/100;

					$weightage = $all_states[$key]['total_users_weightage_40'] ;
				$center_id = $state['center_id'];
				$name = $state['name'];
						$ckSql = "SELECT COUNT(*) as cnt FROM rpt_state_ranking WHERE center_id =  ".$center_id;
						$ckarr = $this->fetch($ckSql,1);
						if($ckarr['cnt'] > 0 ){
							$sql = "UPDATE rpt_state_ranking SET weightage_3=$weightage,total=total+$weightage WHERE center_id=?";
							$this->dbConn->prepare($sql)->execute([$center_id]);
						}else{
							
							$sql = "INSERT INTO rpt_state_ranking (center_id, name, weightage_3,total) VALUES (?,?,?,total+$weightage)";
							$this->dbConn->prepare($sql)->execute([ $center_id,$name, $weightage]);
							
						}
				}
			
				
				$return = array('total'=>count( $all_states),'result'=> $all_states);

			}else{
				$return = array('total'=>0,'result'=>array());
			}
			return $return; 
	
		}  



	public function getFinalStateRanking($cond_arr = array(), $start = 0, $limit = 10,$order,$dir){
		$sql = "SELECT * from  rpt_state_ranking order by total DESC";
		return $this->fetch_rows($sql,1);
	}
			
//========= Check survey response count
	public function getSurveyResponseCount($survey_id){ 
		
			$sql = "SELECT count('*') as 'cnt' from survey_response where survey_id=:survey_id";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':survey_id',$survey_id,PDO::PARAM_INT);
			$stmt->execute();
			$RESULT_CNT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			$row_cnt = array_shift( $RESULT_CNT );
			return $row_cnt['cnt'];  
	}
  public function getBatchByUserCenter($user_id,$center_id){ 
		
			$sql = "SELECT batch_id from tblx_batch_user_map where user_id=:user_id and status =1 AND center_id=:center_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT); 
		$stmt->bindValue(":center_id",  $center_id, PDO::PARAM_INT); 
		//echo "<pre>";print_r($stmt);exit;
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		//echo "<pre>";print_r($RESULT);exit;
        $stmt->closeCursor();
		if(count($RESULT) > 0 ){
				return $RESULT;
			}else{
				return false;
			}
	}
	public function getScorePlacementByUserRegion($user_id,$batch_id,$region_id){ 
		
			$sql = "SELECT * from tblx_placement_result where user_id=:user_id and batch_id =:batch_id AND region_id=:region_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT); 
		 $stmt->bindValue(":batch_id", $batch_id, PDO::PARAM_INT); 
		$stmt->bindValue(":region_id",  $region_id, PDO::PARAM_INT); 
		//echo "<pre>";print_r($stmt);exit;
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		//echo "<pre>";print_r($RESULT);exit;
        $stmt->closeCursor();
		if(count($RESULT) > 0 ){
				return $RESULT;
			}else{
				return false;
			}
	}
	public function getScoreQuizByUserTestId($user_id,$test_id){ 
		$stmt = $this->dbConn->prepare("select MAX(id) as track_id,MAX(attempt_no) as attempt_no from user_test_score where user_id=:user_id and test_id=:test_id");
		$stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT); 
		$stmt->bindValue(":test_id",  $test_id, PDO::PARAM_INT);
		$stmt->execute();
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		//echo "<pre>";print_r($RESULT);//exit;
        $stmt->closeCursor();
		if(count($RESULT) > 0 ){
			
		$attempt_no = $RESULT[0]['attempt_no'];
			
		
		$sql = "SELECT * from user_test_score where user_id=:user_id  AND test_id=:test_id and attempt_no=:attempt_no";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT); 
		$stmt->bindValue(":test_id",  $test_id, PDO::PARAM_INT);
		$stmt->bindValue(":attempt_no",  $attempt_no, PDO::PARAM_INT); 
		//echo "<pre>";print_r($stmt);exit;
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		//echo "<pre>";print_r($RESULT);exit;
        $stmt->closeCursor();
		if(count($RESULT) > 0 ){
				return $RESULT[0];
			}else{
				return false;
			}
		}else{
			return false;
		}
	}


}

?>