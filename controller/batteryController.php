<?php
class batteryController {

    public $dbConn;
    public function __construct() {
		$this->dbConn = DBConnection::createConn();
		
    }

	 public function getBatteryAllType(){
   
	 $sql = "Select * from tblx_battery_type";
	 $stmt = $this->dbConn->prepare($sql);	
	 $stmt->execute();
	 $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
	 $stmt->closeCursor();
	//echo "<pre>";print_r($RESULT);exit;
	 return $RESULT;
  }
	
  public function getBatteryType($type){
   
	 $sql = "Select * from tblx_battery_type where id='$type'";
	 $stmt = $this->dbConn->prepare($sql);	
	 $stmt->execute();
	 $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
	 $stmt->closeCursor();
	//echo "<pre>";print_r($RESULT);exit;
	 return $RESULT;
  }
	
  public function getBattery($client_id){
   
	 $sql = "Select * from tblx_battery where client_id='$client_id'";
	 $stmt = $this->dbConn->prepare($sql);	
	 $stmt->execute();
	 $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
	 $stmt->closeCursor();
	//echo "<pre>";print_r($RESULT);exit;
	 return $RESULT;
  }
  
  public function getBatteryById($batteryId,$client_id){
   
	 $sql = "Select * from tblx_battery where id='$batteryId' AND client_id='$client_id'";
	 $stmt = $this->dbConn->prepare($sql);	
	 $stmt->execute();
	 $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
	 $stmt->closeCursor();
	//echo "<pre>";print_r($RESULT);exit;
	 return $RESULT[0];
  } 
public function getBatterySeqById($batteryId,$client_id){
   
	 $sql = "Select * from client_battery_map where id='$batteryId' AND client_id='$client_id'";
	 $stmt = $this->dbConn->prepare($sql);	
	 $stmt->execute();
	 $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
	 $stmt->closeCursor();
	//echo "<pre>";print_r($RESULT);exit;
	 return $RESULT[0];
  }    
 
 public function addBattery(array $res){
	   // echo "<pre>";print_r($res);exit;
        $name=$res['batteryName'];
        $battery_type=$res['battery_type'];
	    $client_id=$res['client_id'];
		$testEdgeListId =array();
		//$sequenceListId=array();
		foreach($res['testEdge']  as $key => $value){
			//echo "<pre>";print_r($key+1);
			array_push($testEdgeListId,$value);
			//array_push($sequenceListId,$key+1);
		} 
       $testEdgeId = implode(',', $testEdgeListId);
	  // $sequenceId = implode(',', $sequenceListId);
		
      //echo "<pre>";print_r($testEdgeListId);exit;
	    //// Adding battery 
		$stmt = $this->dbConn->prepare("insert into tblx_battery(battery_type,battery_name,client_id,edge_id,create_date,status) values('$battery_type','$name','$client_id','$testEdgeId',NOW(),'1')");
		//echo "<pre>";print_r($stmt);exit;
		$stmt->execute();
		$battery_id =$this->dbConn->lastInsertId();
		$stmt->closeCursor();
		
		foreach($res['testEdge']  as $key => $value){
			//echo "<pre>";print_r($key+1);
			$edgeId=$value;
			$sequenceId=$key+1;
		
		  //// Adding batter and client sequence map 
			$stmt = $this->dbConn->prepare("insert into client_battery_map(client_id,battery_id,edge_id,sequence_id) values('$client_id','$battery_id','$edgeId','$sequenceId')");
	       //echo "<pre>";print_r($stmt);exit;
			$stmt->execute();
		} 
		$stmt->closeCursor();
		
		//echo "<pre>";print_r($stmt);exit;
          return array('battery_id' => $battery_id);
    }
   //Delete Batch Battery map data
	public function deleteClientBatteryMapDetails($battery_id,$client_id){

		$sql = "DELETE  FROM client_battery_map WHERE client_id=:client_id and battery_id=:battery_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
        $stmt->bindValue(':battery_id', $battery_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();	
	
	}
	
   public function updateBattery(array $res){
	   //echo "<pre>";print_r($res);exit;
	    $battery_id=$res['batteryIdValue'];
        $name=$res['batteryName'];
        $battery_type=$res['battery_type'];
	    $client_id=$res['client_id'];
		$testEdgeListId =array();
		foreach($res['testEdge']  as $key => $value){
			array_push($testEdgeListId,$value);
		} 
       $testEdgeId = implode(',', $testEdgeListId);
	    //// update battery 
		$stmt = $this->dbConn->prepare("update tblx_battery set battery_name='$name',battery_type='$battery_type', edge_id='$testEdgeId' where id=:battery_id AND client_id=:client_id");
		//echo "<pre>";print_r($stmt);exit;
		$stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
        $stmt->bindValue(':battery_id', $battery_id, PDO::PARAM_INT);
		$stmt->execute();
		$stmt->closeCursor();
		
		 //// Adding battery and client sequence map 
		
		foreach($res['testEdge']  as $key => $value){
			$edgeId=$value;
			$sequenceId=$key+1;
		
			$stmt = $this->dbConn->prepare("insert into client_battery_map(client_id,battery_id,edge_id,sequence_id) values('$client_id', '$battery_id', '$edgeId', '$sequenceId')");
			//$stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
            //$stmt->bindValue(':battery_id', $battery_id, PDO::PARAM_INT);
			$stmt->execute();
		} 
		$stmt->closeCursor();
         return array('battery_id' => $battery_id);
    }
	//Get Batch Battery map data
	public function getBatchBatteryMapList($batch_id,$center_id){

		$sql = "SELECT battery_id FROM tblx_batch_battery_map WHERE center_id=:center_id and batch_id=:batch_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
        $stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		$batteryArr = array();
		while($row = array_shift( $RESULT )) {
			array_push($batteryArr,$row['battery_id']);
		}
        return $batteryArr;
	
	}
	//Get Batch Battery map
	public function getBatchBatteryMapDetails($batch_id,$batteryid,$center_id){

		$sql = "SELECT COUNT(*) as 'cnt' FROM tblx_batch_battery_map WHERE center_id=:center_id and batch_id=:batch_id and battery_id=:battery_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
        $stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
        $stmt->bindValue(':battery_id', $batteryid, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
			//echo "<pre>";print_r($RESULT);exit;
		return $RESULT;
	
	}
	//=============  Update Batch Battery Map
	
	public function updateBatchBatteryMap($batch_id,$batteryid,$center_id){
		//echo "<pre>";print_r($center_id);exit;
		$batchBatteryDetails = $this->getBatchBatteryMapDetails($batch_id,$batteryid,$center_id);
		if($batchBatteryDetails[0]['cnt']>0)
		{
			$sql = "UPDATE tblx_batch_battery_map SET date_created=NOW() WHERE center_id=:center_id AND batch_id = :batch_id AND battery_id=:battery_id";

			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
			$stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
			$stmt->bindValue(':battery_id', $batteryid, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();  
		}
		else{
			$sql = "INSERT INTO tblx_batch_battery_map (center_id,batch_id,battery_id,date_created) values (:center_id, :batch_id, :battery_id,NOW())";
			$stmt = $this->dbConn->prepare($sql);
			//echo "<pre>";print_r($stmt);exit;
			$stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
			$stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
			$stmt->bindValue(':battery_id', $batteryid, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor(); 
		}
		return true;	 
	}
	
	//Delete Batch Battery map data
	public function deleteBatchBatteryMapDetails($batch_id,$center_id){

		$sql = "DELETE  FROM tblx_batch_battery_map WHERE center_id=:center_id and batch_id=:batch_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':center_id', $center_id, PDO::PARAM_INT);
        $stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();	
	
	}
	
	//Get All Battery Test
	public function getBatteryTest($battId){

		$sql = "DELETE  FROM tblx_batch_battery_map WHERE center_id=:center_id and batch_id=:batch_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':center_id', $_SESSION['center_id'], PDO::PARAM_INT);
        $stmt->bindValue(':batch_id', $batch_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();	
	
	}	
	//Get All Battery Test
	public function getBatteryNameById($bid,$client_id){

		$sql = "Select *  FROM tblx_battery WHERE id=:battery_id  AND client_id=:client_id";
		//echo "<pre>";print_r($stmt);exit;
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
        $stmt->bindValue(':battery_id', $bid, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		//echo "<pre>";print_r($RESULT);exit;
		return $RESULT;
	
	}	
	
	//Map product with battery
	 
 public function assignProduct(array $res){
	  
        $product=trim($res['product']);
        $battery=$res['battery'];
		$this->deleteProductMapping($product);
		foreach($battery  as $key => $value){
			
			//// Adding battery 
			$stmt = $this->dbConn->prepare("insert into tbl_product_battery_map(battery_id,product_id) values(:battery_id,:product_id)");
			$stmt->bindValue(':battery_id', $value, PDO::PARAM_INT);
			$stmt->bindValue(':product_id', $product, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();
		
		}
			return true;
    }

	//Delete mapping of product with battery
	public function deleteProductMapping($productId){

		$sql = "DELETE FROM tbl_product_battery_map WHERE product_id=:product_id";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();	
	
	}
     //Get All Product and Battery Test map list
	public function getProductBatteryMap($prodId){

		$sql = "Select *  FROM tbl_product_battery_map WHERE product_id=:product_id";
		//echo "<pre>";print_r($stmt);exit;
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':product_id',$prodId, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		//echo "<pre>";print_r($RESULT);exit;
		return $RESULT;
	
	}	
	 //check All Product and Battery Test map list
	public function checkProductBatteryMap($prodId){

		$sql = "Select id  FROM tbl_product_battery_map WHERE product_id=:product_id AND battery_id=:battery_id";
		//echo "<pre>";print_r($stmt);exit;
        $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':product_id',$prodId, PDO::PARAM_INT);
		$stmt->bindValue(':battery_id',$bid, PDO::PARAM_INT);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		//echo "<pre>";print_r($RESULT);exit;
		return $RESULT;
	
	}	



}

?>