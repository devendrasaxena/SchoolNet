<?php


class pollMainController {

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
	


 }  


?>
