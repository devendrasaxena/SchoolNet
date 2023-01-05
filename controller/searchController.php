<?php 

class searchController {

    public $dbConn;
    public function __construct() {
		$this->dbConn = DBConnection::createConn();
		
	}

	function d($data){
		echo "<pre>";print_r($data);
	}
	
	function dd($data){
		echo "<pre>";print_r($data);exit;
	}
	
	public function search($input,$course_id){
		   // $uId = trim( base64_decode($_GET['uid']) );  
			$res = array();
			
			$arr = array(
			array(
			'table'=>'course',
			'cols'=>array('code','title','description')
			),
			
			array(
			'table'=>'cap_module', // topic
			'cols'=>array('name','description')
			),
			
			array(
			'table'=>'session_node', // chaper
			'cols'=>array('title','code','objective','description')
			),

			array(
			'table'=>'tbl_component', 
			'cols'=>array('scenario_type','scenario_subtype','scenario_name','scenario_description','instruction')
			)

			);

			
			
			foreach($arr as $i=>$value):
				$tbl = $value['table'];
				$cols = $value['cols'];
				$where = 'WHERE '; 
				foreach($cols as $j=>$val):
					if(count($cols)-1==$j)
						$where .="$val LIKE ?";
					else
						$where .="$val LIKE ? OR ";
				endforeach;
			$sql = '';	
			if(count($arr)-1 == $i)	
				 $sql .= "SELECT * FROM `$tbl` $where ";
			else
				$sql .= "SELECT * FROM `$tbl` $where ";

			 $stmt = $this->dbConn->prepare($sql);
			foreach($cols as $j=>$ar):
					$stmt->bindValue($j+1, '%'.$input.'%', PDO::PARAM_STR);
			endforeach;
			$stmt->execute();
			
			$RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			if(count($RESULT) > 0 ){
				
					$res[] = ['table'=>$tbl,'data'=>$RESULT];
					
				
			} 
		endforeach;
	
		$search_result = array();
		$tree_node_id=array();
		$edge_ids = array();
		$tbl =array();
		foreach ($res as $key => $r) {
			 if($r['table'] == 'tbl_component'){
				foreach ($r['data'] as $key => $value) {
					$chapter_edge_id = $value['parent_edge_id'];
					$qry = "SELECT gmt.tree_node_parent as topic_edge_id, gmt.edge_id, cm.tree_node_id FROM generic_mpre_tree gmt
								JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
								JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id
								WHERE gmt.is_active = 1 AND gmt.edge_id = :chapter_edge_id AND tnd.tree_node_category_id=2";
					/*$qry = "SELECT DISTINCT(cm.name) as topic,cm.*,gmt.edge_id FROM  `generic_mpre_tree` AS `gmt`
			JOIN `tree_node_def` AS `tnd` ON `tnd`.`tree_node_id`=`gmt`.`tree_node_id`
			JOIN `cap_module` AS `cm` ON `cm`.`tree_node_id`=`gmt`.`tree_node_id`  WHERE assessment_type IS NULL AND cm.tree_node_id IN (select tree_node_id from generic_mpre_tree where tree_node_super_root='$chapter_edge_id');";*/
					$stmt = $this->dbConn->prepare($qry);
					$stmt->bindValue(':chapter_edge_id', $chapter_edge_id, PDO::PARAM_INT);

						$stmt->execute();
						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$stmt->closeCursor();
						if($result){
							$edge_ids[] = $result['topic_edge_id'];
						}
						
						
							
				}
			}elseif($r['table'] == 'session_node'){
				foreach ($r['data'] as $key => $value) {
						$tree_node_id = $value['tree_node_id'];
						
						$qry = "SELECT gmt.tree_node_parent as topic_edge_id, gmt.edge_id,  cm.tree_node_id FROM generic_mpre_tree gmt
								JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
								JOIN session_node cm ON cm.tree_node_id = gmt.tree_node_id
								WHERE gmt.is_active = 1 AND cm.tree_node_id = :tree_node_id AND tnd.tree_node_category_id=2";


								$stmt = $this->dbConn->prepare($qry);
								$stmt->bindValue(':tree_node_id', $tree_node_id, PDO::PARAM_INT);
						$stmt->execute();
						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$stmt->closeCursor();
						if($result){
							$edge_ids[] = $result['edge_id'];
							
						}

				}				
			}elseif($r['table'] == 'cap_module'){

				foreach ($r['data'] as $key => $value) {
					$tree_node_id = $value['tree_node_id'];
				$qry = "SELECT  cm.name, gmt.edge_id
								FROM generic_mpre_tree gmt
								JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
								JOIN cap_module cm ON cm.tree_node_id = gmt.tree_node_id
								WHERE gmt.is_active = 1 AND gmt.tree_node_id = :tree_node_id";

								$stmt = $this->dbConn->prepare($qry);
								$stmt->bindValue(':tree_node_id', $tree_node_id, PDO::PARAM_INT);
						$stmt->execute();
						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$stmt->closeCursor();
						if($result){
							$edge_ids[] = $result['edge_id'];
							
						}
								
							}

			}else{
				
				foreach ($r['data'] as $key => $value) {
					$tree_node_id = $value['tree_node_id'];
				$qry = "SELECT  c.title,c.code, gmt.edge_id
								FROM generic_mpre_tree gmt
								JOIN tree_node_def tnd ON tnd.tree_node_id = gmt.tree_node_id
								JOIN course c ON c.tree_node_id = gmt.tree_node_id
								WHERE gmt.is_active = 1 AND gmt.tree_node_id = :tree_node_id AND c.id=:course_id" ;
								$stmt->bindValue(':tree_node_id', $tree_node_id, PDO::PARAM_INT);
								$stmt->bindValue(':course_id', $course_id, PDO::PARAM_INT);
								
								$stmt = $this->dbConn->prepare($qry);
						$stmt->execute();
						$result = $stmt->fetch(PDO::FETCH_ASSOC);
						$stmt->closeCursor();
						if($result){
							$edge_ids[] = $result['edge_id'];
							
						}

						
								
							}

				
			}
			$tbl[] = $r['table'];
		}
		
		if(count($edge_ids)){
		 $qry = "SELECT DISTINCT cm.name as topic,gmt.edge_id FROM  `generic_mpre_tree` AS `gmt`
			JOIN `tree_node_def` AS `tnd` ON `tnd`.`tree_node_id`=`gmt`.`tree_node_id`
			JOIN `cap_module` AS `cm` ON `cm`.`tree_node_id`=`gmt`.`tree_node_id` WHERE (cm.tree_node_id IN 
		(SELECT tree_node_id from generic_mpre_tree Where edge_id IN(". implode(',', $edge_ids).")  
		AND tree_node_super_root = 9455) AND assessment_type IS NULL)";
		/*$qry = "SELECT  name as topic FROM cap_module WHERE (tree_node_id IN 
		(SELECT tree_node_id from generic_mpre_tree Where edge_id IN(". implode(',', $edge_ids).")  
		) )";*/
			 $stmt = $this->dbConn->prepare($qry);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		if(count($result)){
			array_push($search_result,$result);
		}
		}	
		unset($arr);
		unset($res);
		return array('table'=>$tbl,'data'=>$search_result);		
	}
	


	
	
}





