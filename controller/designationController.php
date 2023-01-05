<?php
class designationController {

    public $dbConn;
    public function __construct() {
		$this->dbConn = DBConnection::createConn();
		
    }
	
	
	
   Public function getDesignationDetail($designation_id){
	   //// Select user id  to user-center-map 
	    $sql ="Select * from tblx_designation WHERE id=:designation_id";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':designation_id', $designation_id, PDO::PARAM_INT);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		//echo "<pre>";print_r($RESULT);exit;
		$id = $RESULT[0]['id'];
		$desination_short_code = $RESULT[0]['desination_short_code'];
		$designation = $RESULT[0]['designation'];
		$description = $RESULT[0]['description'];

		
		
		
		$obj = new stdclass();
		$obj->id = $id;
		$obj->desination_short_code = $desination_short_code;
		$obj->designation = $designation;
		$obj->description = $description;

		return $obj;
	
	}

	//get tehsil list
	public function getDesignationList($cond_arr=array(), $start = 0, $limit = 10,$order,$dir){
		
		$whr="where 1=1 ";
		 
		 if($cond_arr['designation']!=""){
		$whr.= " AND (td.designation LIKE '%".$cond_arr['designation']."%' or td.desination_short_code LIKE '%".$cond_arr['designation']."%')";
		}  
		
		

		 $sql = "Select count(DISTINCT id) as 'cnt' from tblx_designation td $whr";
		$stmt = $this->dbConn->prepare($sql);	
		$stmt->execute();
		$RESULT_CNT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$row_cnt = array_shift( $RESULT_CNT ); 
		


		$limit_sql = '';
		if( !empty($limit) ){
		$limit_sql .= " LIMIT $start, $limit";
		}
		
		$sql = "Select td.* from tblx_designation td $whr ORDER BY ".$order." ".$dir." $limit_sql";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();

		return array('total' =>$row_cnt['cnt'] , 'result' => $RESULT);

	}
	
	
	//create tehsil
	public function createDesignation($res){
		try{
			$sql= "INSERT INTO tblx_designation SET desination_short_code = :desination_short_code, designation = :designation, description = :description,created_date = NOW()";
			$stmt = $this->dbConn->prepare($sql); 	
			$stmt->bindValue('desination_short_code', $res->desination_short_code, PDO::PARAM_STR);		
			$stmt->bindValue('designation', $res->designation, PDO::PARAM_STR);
			$stmt->bindValue('description', $res->description, PDO::PARAM_STR);		
			$stmt->execute();
			$district_id_new =$this->dbConn->lastInsertId();
			$stmt->closeCursor(); 
			$obj = new stdclass();
			$obj->designation = $res->designation;
			
			return $obj;
	  }//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();exit;
		}
	}
	
	//update tehsil
	public function updateDesignation($dataArr,$did){
		  $desination_short_code = $dataArr->desination_short_code;
		  $designation = $dataArr->designation;
		  $description = $dataArr->description;
		
		 try{
			  $sql = "UPDATE tblx_designation SET  desination_short_code = :desination_short_code,designation = :designation,description = :description where id = :designation_id";
			  $stmt = $this->dbConn->prepare($sql);	
			  $stmt->bindValue('desination_short_code', $desination_short_code, PDO::PARAM_STR);		  
			  $stmt->bindValue('designation', $designation, PDO::PARAM_STR);		  
			  $stmt->bindValue('description', $description, PDO::PARAM_STR);		  
			  $stmt->bindValue('designation_id', $did, PDO::PARAM_INT);		  
			  $stmt->execute();
			  $stmt->closeCursor();
			   return true;
			  }//catch exception
				   catch(Exception $e) {
				  echo 'Message: ' .$e->getMessage();exit;
				}
    }
	
	public function chkdesignationByName($designation){
	    $sql ="Select * from tblx_designation WHERE designation=:designation_name";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue('designation_name', $designation, PDO::PARAM_STR);		  
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$designation_id = $RESULT[0]['id'];
		if($designation_id!="" && $designation_id!== NULL){
			return $designation_id;
		}else{
			return false;
		}
	}
	public function searchDesignation($name){ 
		
			
			
			
			
			if($name!=""){
				$whr.= " WHERE (td.designation LIKE '%".$name."%' or td.desination_short_code LIKE '%".$name."%')";
				//$whr.= " AND tc.country = '$country'";
			}
				$order = "DESC";
			
				$sql = "Select * from tblx_designation td  $whr "; 
				  // print_r($sql); exit();
			// $sql = "Select td.* from tblx_designation td $whr ORDER BY ".$order." ".$dir." $limit_sql";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			
		
			// $sql = "Select td.* from tblx_designation td $whr ORDER BY ".$order; 
			// print_r($sql); exit();
			// // $sql = "Select tc.* from tblx_center AS tc LEFT JOIN tblx_region_country_map AS trcm ON tc.country=trcm.country_name $whr group by tc.center_id order by tc.center_id DESC"; 
			// $stmt = $this->dbConn->prepare($sql);
			
			// if($name!=""){
			// 	$stmt->bindValue(':designation', '%'.$name.'%', PDO::PARAM_STR);
			// }
			
			// $stmt->execute();
			// $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			// $stmt->closeCursor();
			// $centerList = array();
		 //   while($row = array_shift( $RESULT )) {
			// 	$bcm = new stdClass();
				
			// 	$bcm->name = $row['designation'];
			// 	array_push($centerList,$bcm);
			
			// }

		return $RESULT;
}

	public function getClassByCenterFromDesignationMap($center_id){
	    $sql ="Select count('*') as cnt from tblx_batch  WHERE center_id=:center_id AND is_default= '1'";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue('center_id', $center_id, PDO::PARAM_INT);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		$designation_id = $RESULT[0]['cnt'];
		if($designation_id!="" && $designation_id>0){
			return true;
		}else{
			return false;
		}
	}

}

?>