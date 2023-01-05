<?php


class graphController {

    public $dbConn;
    public function __construct() {
		$this->dbConn = DBConnection::createConn();
    }


    public function fetch_rows($qry,$arr=0,$whrValue=array()) {
		$pre = $this->dbConn->prepare($qry);
		foreach ($whrValue  as $key => $val) {
			$val = ltrim($val," ");
			$pre->bindValue(":$key", $val);
		}
        $pre->execute();
		if($arr)
			$res = $pre->fetchAll(PDO::FETCH_ASSOC);
		else
			$res = $pre->fetchAll(PDO::FETCH_OBJ);

		$pre->closeCursor();
		return $res;
    }

    public function fetch($qry,$arr=0,$whrValue=array()) {
		$pre = $this->dbConn->prepare($qry);
		foreach ($whrValue  as $key => $val) {
			$val = ltrim($val," ");
			$pre->bindValue(":$key", $val);
		}
		$pre->execute();
		
		if($arr)
			$res = $pre->fetch(PDO::FETCH_ASSOC);
		else
			$res = $pre->fetch(PDO::FETCH_OBJ);

			$pre->closeCursor();
			return $res;
    }

    public function dd($data,$clean = 0){

		if($clean){
			ob_clean();
		}
    	echo "<pre>";
		print_r($data);
		
    	exit;
    }
	

	public function getStateUsers($cond_arr = array()){ 
	
		$whr = "where 1=1";
		$whrValue = array();
		if(!empty($cond_arr['region_id'])){

			$whr.= " AND tc.region = :region_id and tc.status=1";
			$whrValue['region_id']=$cond_arr['region_id'];
		}else{
			$whr.= " AND tc.region = :region_id and tc.status=1";
			$whrValue['region_id']=1;
		}
	
		if($cond_arr['role_id']!="" && $cond_arr['role_id']!='All' && $cond_arr['role_id']!='0'){
			$whr.= " AND urm.role_definition_id = :role_definition_id";
			$whrValue['role_definition_id']=$cond_arr['role_id'];
		}

		
	 $sql = "SELECT
		COUNT(u.user_id) AS users,tc.name
		FROM user u
        join user_role_map as urm ON urm.user_id = u.user_id
        join user_center_map as ucm ON ucm.user_id = u.user_id
        join tblx_center as tc ON tc.center_id = ucm.center_id
		$whr
		GROUP BY tc.name ORDER BY tc.name";
		$stmt = $this->dbConn->prepare($sql);
		
		foreach ($whrValue  as $key => $val) {
			$val = ltrim($val," ");
            $stmt->bindValue(":$key", $val);
		}
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		return array('total' =>count($RESULT) , 'result' => $RESULT);
   
   }

public function getStateCentralAdmins($cond_arr = array()){ 
	
		$whr = "where 1=1";
		$whrValue = array();
		if(!empty($cond_arr['region_id'])){

			$whr.= " AND trum.region_id = :region_id";
			$whrValue['region_id']=$cond_arr['region_id'];
		}else{
			$whr.= " AND trum.region_id = :region_id";
			$whrValue['region_id']=1;
		}
	
		if($cond_arr['role_id']!="" && $cond_arr['role_id']!='All' && $cond_arr['role_id']!='0'){
			$whr.= " AND urm.role_definition_id = :role_definition_id";
			$whrValue['role_definition_id']=$cond_arr['role_id'];
		}

		
	 $sql = "SELECT
		COUNT(distinct u.user_id) AS users ,tc.state_name as 'name' 
		FROM state tc  Left JOIN address_master a on (tc.state_name COLLATE utf8_unicode_ci =  a.state) left join user u on a.address_id = u.address_id 
        left join user_role_map as urm ON urm.user_id = u.user_id
        left join tblx_region_user_map as trum ON trum.user_id = u.user_id
		$whr
		GROUP BY tc.state_name ORDER BY tc.state_name";
		$stmt = $this->dbConn->prepare($sql);
		
		foreach ($whrValue  as $key => $val) {
			$val = ltrim($val," ");
            $stmt->bindValue(":$key", $val);
		}
		$stmt->execute();
		$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		return array('total' =>count($RESULT) , 'result' => $RESULT);
   
   }

   public function getStateRanking($options){

	   if(isset($options['region_id'])){
		
		   $whr = "WHERE tc.region = :region_id";
		   $whrData = array('region_id'=>$options['region_id']);
	   
		$sql = "SELECT rsr.* FROM  rpt_state_ranking AS rsr
		JOIN tblx_center AS tc ON rsr.center_id = tc.center_id
		$whr
		order by total DESC";
		$res = $this->fetch_rows($sql,1,$whrData);
		 return $res;
		}else{

			return array();
		}

   }

   public function getModuleScore($options)
   {

   		  if(isset($options['region_id'])){
		
		   $whr = " AND  tc.region = :region_id";
		   $whrData = array('region_id'=>$options['region_id']);


			$topic_edge_ids = array(9457, 9596, 9601, 9606, 9611, 9616, 9621, 9626, 9631, 9636);
			$res = array();
			foreach($topic_edge_ids as $key => $id):
			$sql = "SELECT AVG(tus.score) score,gmt.edge_id,cm.name  FROM `tblx_user_score` as tus
				join generic_mpre_tree as gmt ON  gmt.edge_id = tus.topic_id
				join cap_module as cm ON gmt.tree_node_id = cm.tree_node_id
				JOIN tblx_center as tc 
				WHERE tus.topic_id = $id $whr";
				$res[] = $this->fetch($sql,1,$whrData);
		   endforeach;
		   return $res;
		}else{
			return array();
		}

   }
   public function getModuleTime($options)
   {

   	if(isset($options['region_id'])){
		
		   $whr = " AND  c.region = :region_id";
		   $whrData = array('region_id'=>$options['region_id']);


		$topic_edge_ids = array(9457, 9596, 9601, 9606, 9611, 9616, 9621, 9626, 9631, 9636);
		$res = array();
		foreach($topic_edge_ids as $key => $id):



		$sql = "SELECT SUM(ust.`actual_seconds`) as total, gmt.tree_node_parent
		 FROM `user_session_tracking` as ust
		JOIN tbl_component AS tc ON ust.session_id = tc.component_edge_id
		JOIN generic_mpre_tree as gmt ON gmt.edge_id = tc.parent_edge_id
		JOIN tblx_center as c 
		WHERE ust.`session_type` = 'CM' AND gmt.tree_node_parent = $id   $whr
		GROUP BY gmt.tree_node_parent";

		

			$res[] = $this->fetch($sql,1,$whrData);
	   endforeach;

	   return $res;
	}else{
		return array();
	}

   }


   public function getActiveUsers($options)
   {
		$whereData = array();
		$whr = "WHERE 1 = 1 ";
		$res = array();
	   	if(isset($options['date'])){
			   $whr = " AND  vu.date = :date ";
			   $whereData['date']=$options['date'];
		}
		if(isset($options['region_id'])){
			
			$sqlState = "SELECT name,center_id FROM tblx_center WHERE region = :region ORDER BY name ASC"; 
			$stateArr = $this->fetch_rows($sqlState,1,array(
				'region'=>$options['region_id']
			));
	 	}

		foreach($stateArr as $key => $state):
			$sql = " SELECT count(vu.user_id) as users,tc.name FROM `visiting_user` AS vu
			JOIN user_center_map as ucm ON ucm.user_id = vu.user_id
			JOIN tblx_center as tc ON tc.center_id = ucm.center_id
			JOIN user_role_map AS urm ON vu.user_id = urm.user_id
			$whr AND tc.center_id = :center_id AND urm.role_definition_id = 2";
			$whereData['center_id'] = $state['center_id'];
			$res[] = $this->fetch($sql,1,$whereData);
			
		endforeach;	

		return $res;
   }


   public function getLoginsUsers($options,$report_by='daily')
   {
	$users = array();
	
	$start_date_qry = $options['start_date_qry'];
	$end_date_qry = $options['end_date_qry'];

	 $start_date_qry = date('Y-m-d', strtotime($start_date_qry));
	 $end_date_qry = date('Y-m-d', strtotime($end_date_qry.'+1 day'));

		if($report_by == 'daily'){
		$whr = "where 1=1";
	
		 $sql = "SELECT tr.date_with_time as date,
		COUNT(tr.user_id) total,MONTH(tr.date_with_time)
		 AS month,WEEK(tr.date_with_time) as week,
		 DATE_FORMAT( tr.date_with_time, '%b' ) as month_name,
		  DATE_FORMAT( tr.date_with_time, '%Y' ) as year_number , 
		  DATE_FORMAT( tr.date_with_time, '%d' ) as day "
		. " FROM visiting_user tr "
		." JOIN user_role_map AS urm ON tr.user_id = urm.user_id"
		. " $whr  and tr.date_with_time between DATE(:start_date) 
		and DATE(:end_date) AND urm.role_definition_id = 2
		GROUP BY DAYOFMONTH(tr.date_with_time),
		month order by month,
		DAYOFMONTH(tr.date_with_time)";

		}else if($report_by == 'weekly'){
		
			$whr = "where 1=1";
	
			 $sql = "SELECT tr.date_with_time as date,COUNT(tr.user_id) total,
			WEEK(tr.date_with_time) as week,DATE_FORMAT( tr.date_with_time, '%b' ) 
			as month_name, DATE_FORMAT( tr.date_with_time, '%Y' ) as year_number , 
			DATE_FORMAT( tr.date_with_time, '%d' ) as day "
				. " FROM visiting_user tr "
				." JOIN user_role_map AS urm ON tr.user_id = urm.user_id"
				. " $whr  and tr.date_with_time between DATE(:start_date) 
				and DATE(:end_date) AND urm.role_definition_id = 2 
				GROUP BY WEEK(tr.date_with_time)";
	
		}
		else if($report_by == 'monthly'){
			$whr = "where 1=1";
			
			$sql = "SELECT COUNT(tr.user_id) total,MONTH(tr.date_with_time) 
			AS month,WEEK(tr.date_with_time) as week,
			DATE_FORMAT( tr.date_with_time, '%b' ) as 
			month_name, DATE_FORMAT( tr.date_with_time, '%Y' ) as 
			year_number , DATE_FORMAT( tr.date_with_time, '%d' ) as day "
			  . " FROM visiting_user tr "
			  ." JOIN user_role_map AS urm ON tr.user_id = urm.user_id"
			  . " $whr  and tr.date_with_time between DATE(:start_date)
			   and DATE(:end_date) AND urm.role_definition_id = 2
			   GROUP BY MONTH(tr.date_with_time),
			   YEAR(tr.date_with_time) order by MONTH(tr.date_with_time),
			   YEAR(tr.date_with_time)";				  
			  
		}
		
		$users = $this->fetch_rows($sql,0,array(
			'start_date'=>$start_date_qry,
			'end_date'=>$end_date_qry
		));
		return $users;
   
   }

 }  


?>
