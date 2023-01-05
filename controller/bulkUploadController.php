<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

class BulkUploadController {

    public $dbConn;
    public function __construct() {
		$this->dbConn = DBConnection::createConn();
		
    }


    public function insertBulkAttendance($data) {
    	$table='dseu_attendance';
        $whr = 'WHERE roll_no= :roll_no AND attendance_date = :attendance_date';
        $sql = "Select count(*) as cnt FROM `$table` $whr";
        $stmt = $this->dbConn->prepare($sql);
            $stmt->bindValue(":roll_no", $data['roll_no']);
            $stmt->bindValue(":attendance_date", $data['attendance_date']);

            $stmt->execute();
            $RESULT_CNT = $stmt->fetch(PDO::FETCH_OBJ);

            
            if($RESULT_CNT->cnt==1)
                return true;
            $stmt->closeCursor();

           
        ksort($data);
        $fieldNames = implode('`, `', array_keys($data));
        $fieldValues = ':' . implode(', :', array_keys($data));
        $sth = $this->dbConn->prepare("INSERT INTO `$table` (`$fieldNames`) VALUES ($fieldValues)");

        foreach ($data as $key => $value) {
            $sth->bindValue(":$key", $value);
        }

	        $s = $sth->execute();
	        return $s;

    }
	
	
	public function insertBulkAssessment($data) {
    	$table='dseu_prepost';
		$whr = 'WHERE roll_no= :roll_no AND exam_type = :exam_type';
		$sql = "Select count(*) as cnt FROM `$table` $whr";
        $stmt = $this->dbConn->prepare($sql);
            $stmt->bindValue(":roll_no", $data['roll_no']);
            $stmt->bindValue(":exam_type", $data['exam_type']);

            $stmt->execute();
            $RESULT_CNT = $stmt->fetch(PDO::FETCH_OBJ);

            
			if($RESULT_CNT->cnt==1)
				return true;

            $stmt->closeCursor();
			
        ksort($data);
        $fieldNames = implode('`, `', array_keys($data));
        $fieldValues = ':' . implode(', :', array_keys($data));
        $sth = $this->dbConn->prepare("INSERT INTO `$table` (`$fieldNames`) VALUES ($fieldValues)");

        foreach ($data as $key => $value) {
            $sth->bindValue(":$key", $value);
        }

	        $s = $sth->execute();
	        return $s;

    }



    public function getAttendance($cond_arr = array(), $start = 0, $limit = 10,$order,$dir){
        
            $whr = 'WHERE 1=1 ';
            if(isset($cond_arr['roll_no'])  && $cond_arr['roll_no'] !=""){
                $whr .="AND roll_no=:roll_no";
            }

            $limit_sql = '';

            if( !empty($limit) ){
                $limit_sql .= " LIMIT $start, $limit";
            }

            $sql = "Select count(*) as 'cnt' FROM dseu_attendance $whr "; 
            $stmt = $this->dbConn->prepare($sql);  
            if(isset($cond_arr['roll_no'])  && $cond_arr['roll_no'] !="")
                $stmt->bindValue(":roll_no", $cond_arr['roll_no']);

            $stmt->execute();
            $RESULT_CNT = $stmt->fetchAll(PDO::FETCH_ASSOC); 
            $stmt->closeCursor();
            $row_cnt = array_shift( $RESULT_CNT );

            $sql = "Select * FROM dseu_attendance $whr  ORDER BY ".$order." ".$dir." $limit_sql";
         
            $stmt = $this->dbConn->prepare($sql);
            if(isset($cond_arr['roll_no'])  && $cond_arr['roll_no'] !="")
                $stmt->bindValue(":roll_no", $cond_arr['roll_no']);
            $stmt->execute();
            $RESULT = $stmt->fetchAll(PDO::FETCH_OBJ);
            $stmt->closeCursor();
            $userList = array();
           
  
        return array('total' =>$row_cnt['cnt'] , 'result' => $RESULT);

    }


    public function deleteAttendance($id) {
        $table='dseu_attendance';
        $sth = $this->dbConn->prepare("DELETE FROM `$table` WHERE id = :id");
        $sth->bindValue(":id", $id);
        $s = $sth->execute();
        return $s;

    }
    
    

 }