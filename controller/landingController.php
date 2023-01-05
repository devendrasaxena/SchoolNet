<?php

/**
* 
*/
class landingController
{
	
	public $dbConn;
    public function __construct() {
		$this->dbConn = DBConnection::createConn();
		
    }

    public function getAllRegions(){
		$sql = "SELECT id,region_name,region_description,region_logo from  tblx_region WHERE is_active = '1' order by region_name ";
	
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		if(count($RESULT) > 0){
			return $RESULT; 
		} else {
			return false;
		}
    }
    public function getRegionById($id){
		$sql = "SELECT id,region_name,region_description,region_logo from  tblx_region WHERE is_active = '1' and id = :id";
	
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		$stmt->execute();

		$RESULT = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		if(count($RESULT) > 0){
			return $RESULT; 
		} else {
			return false;
		}
    }

    public function getAllUserByRole1($role,$r_id){
		$sql = "SELECT count(*) as total from user_role_map 
				join user_credential on user_credential.user_id = user_role_map.user_id 
				join user_center_map on user_center_map.user_id = user_credential.user_id 
				join tblx_center on user_center_map.center_id = tblx_center.center_id 
				WHERE user_credential.is_active = '1' 
				and   user_role_map.role_definition_id = :role 
				and   tblx_center.region = :region_id";
		// echo $sql; exit;
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':role', $role, PDO::PARAM_INT);
		$stmt->bindValue(':region_id', $r_id, PDO::PARAM_INT);
		$stmt->execute();
		$RESULT = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		if(count($RESULT) > 0){
			return $RESULT; 
		} else {
			return false;
		}
    }    
	
	public function getAllUserByRole($role,$r_id=''){
		if($r_id!=""){
			$rgn_whr = "and tblx_center.region = :region_id";
		}else{
			$rgn_whr = ""; 
		}
		$sql = "SELECT count(*) as total from user_role_map 
				join user_credential on user_credential.user_id = user_role_map.user_id 
				join user_center_map on user_center_map.user_id = user_credential.user_id 
				join tblx_center on user_center_map.center_id = tblx_center.center_id 
				WHERE user_credential.is_active = '1' 
				and   user_role_map.role_definition_id = :role 
				$rgn_whr";
		 //echo $sql; exit;
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':role', $role, PDO::PARAM_INT);
		if($r_id!=""){
			$stmt->bindValue(':region_id', $r_id, PDO::PARAM_INT);
		}
		$stmt->execute();
		$RESULT = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		if(count($RESULT) > 0){
			return $RESULT; 
		} else {
			return false;
		}
    }	
	
	public function getDFPDCenterId(){
		
		$sql = "SELECT center_id from  tblx_center where center_type=0";
		// echo $sql; exit;
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		if(count($RESULT) > 0){
			return $RESULT['center_id']; 
		} else {
			return false;
		}
    }

    public function getAllOrganizations($r_id){
		// $sql = "SELECT count(*) as total from tblx_center join tblx_region_country_map on LOWER(tblx_region_country_map.country_name) = LOWER(tblx_center.country) WHERE tblx_center.status = '1' and tblx_region_country_map.region_id = '$r_id'";
		$sql = "SELECT count(*) as total from tblx_center where region = :region_id and status=1 and center_type=1";
		// echo $sql; exit;
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':region_id', $r_id, PDO::PARAM_INT);
		$stmt->execute();
		$RESULT = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		if(count($RESULT) > 0){
			return $RESULT; 
		} else {
			return false;
		}
    }
	
	public function getDFPDCenterName(){
		$sql = "SELECT name from tblx_center where status=1 and center_type=0";
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
		$RESULT = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		return $RESULT['name'];
	 }
    
	public function getAllDistricts($r_id){
		$sql = "SELECT count(DISTINCT td.district_id) as total from tblx_district td JOIN tblx_center tc ON td.state_id=tc.center_id  where tc.region = :region_id and tc.status=1";
		// echo $sql; exit;
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':region_id', $r_id, PDO::PARAM_INT);
		$stmt->execute();
		$RESULT = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		if(count($RESULT) > 0){
			return $RESULT; 
		} else {
			return false;
		}
    }
	
	public function getAllDistrictsByCenter($c_id){
		$sql = "SELECT count(DISTINCT td.district_id) as total from tblx_district td JOIN tblx_center tc ON td.state_id=tc.center_id  where tc.center_id = :center_id and tc.status=1";
		// echo $sql; exit;
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':center_id', $c_id, PDO::PARAM_INT);
		$stmt->execute();
		$RESULT = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		if(count($RESULT) > 0){
			return $RESULT; 
		} else {
			return false;
		}
    }
	
	public function getAllModuleCompleted($role,$r_id){
		
		$courseArr = array();
		$stmt = $this->dbConn->prepare("SELECT gmt.edge_id FROM generic_mpre_tree gmt
								JOIN course c ON c.tree_node_id = gmt.tree_node_id  
								WHERE  c.client_id=2");
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		while($row = array_shift( $RESULT )) {
			array_push($courseArr,$row['edge_id']);
		}
		$stmt->closeCursor();
		
		
		
		$courseArr = array_filter($courseArr);
		$courseArr = implode(',',$courseArr);
			
			
		if($courseArr!=""){
			$topicArr = array();
			$stmt = $this->dbConn->prepare("SELECT  gmt.edge_id
									FROM generic_mpre_tree gmt
									JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
									JOIN cap_module cm ON cm.tree_node_id = gmt.tree_node_id
									WHERE gmt.is_active = 1 AND tree_node_super_root IN($courseArr)  AND tnd.tree_node_category_id IN(3,5) AND (cm.assessment_type = 'mid' OR cm.assessment_type IS NULL) ORDER BY sequence_id");
			$stmt->execute();
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			while($row = array_shift( $RESULT )) {
				array_push($topicArr,$row['edge_id']);
				
			}
		
		$topicArr = array_filter($topicArr);
		$topicArr = implode(',',$topicArr);
		if($topicArr!=""){
			
			 $sql = "SELECT count(DISTINCT tblx_component_completion.component_edge_id) as total from user_role_map 
					join user_credential on user_credential.user_id = user_role_map.user_id 
					join user_center_map on user_center_map.user_id = user_credential.user_id 
					join tblx_component_completion on user_center_map.user_id = tblx_component_completion.user_id 
					join tblx_center on user_center_map.center_id = tblx_center.center_id 
					WHERE user_credential.is_active = '1' 
					and user_role_map.role_definition_id = '$role' 
					and tblx_center.region = :region_id and tblx_component_completion.component_edge_id IN($topicArr)";
					
					
			// echo $sql; exit;
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':region_id', $r_id, PDO::PARAM_STR);
			$stmt->execute();
			$RESULT = $stmt->fetch(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			if(count($RESULT) > 0){
				return $RESULT; 
			} 
		}
		
		}
		return false;

	}
}

?>