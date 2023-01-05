<?php
class productController
{
	
	public $dbConn;
    public function __construct() {
		$this->dbConn = DBConnection::createConn();
		
    }
	
	 public function savemasterproduct($masterdata)
    {
        $values = implode(',', $masterdata);
		
		$sql="INSERT INTO tbl_master_product_list (`name`, `publish`, `is_show_lti`, `is_show_dashboard`) VALUES $values";
      
		$stmt = $this->dbConn->prepare($sql);
		$stmt->execute();
        $inserted_id = $this->dbConn->lastInsertId();
        return $inserted_id;

    }
	public function mapmasterproduct($data)
    {
        $values = implode(',', $data);
        $stmt = $this->dbConn->prepare("INSERT INTO tbl_product (`client_id`,`product_name`, `price`, `thumbnail`, `code`,`package_code`,`currency_code`, `discount`, `discount_type`, `master_products_ids`,`expire_on`,`status`,`product_type`) VALUES $values");
        $stmt->execute();
        $inserted_id = $this->dbConn->lastInsertId();
        $stmt->closeCursor();
        return $inserted_id;
    }
	
	 public function getAllMaterProductList()
    {
        try {
            $sql = "Select tp.* from tbl_master_product_list as tmpl JOIN tbl_product as tp ON  tp.master_products_ids=tmpl.id AND product_type='master'";
            $stmt = $this->dbConn->prepare($sql);
            $stmt->execute();
            $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            if (count($RESULT) > 0) {
                return $RESULT;
            } else {
                return false;
            }
        } //catch exception
        catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
            exit;
        }
    }
	
   public function getMasterProductList()
    {
        try {
            $sql = "Select * from tbl_master_product_list where publish='yes'";
            $stmt = $this->dbConn->prepare($sql);
            $stmt->execute();
            $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            if (count($RESULT) > 0) {
                return $RESULT;
            } else {
                return false;
            }
        } //catch exception
        catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
            exit;
        }
    }
	   public function getAllMaterProductListById($master_products_ids)
    {
        try {
            $sql = "Select tp.* from tbl_master_product_list as tmpl JOIN tbl_product as tp ON  tp.master_products_ids=tmpl.id AND product_type='master' AND master_products_ids='$master_products_ids'";
           $stmt = $this->dbConn->prepare($sql);
            $stmt->execute();
            $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            if (count($RESULT) > 0) {
                return $RESULT;
            } else {
                return false;
            }
        } //catch exception
        catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
            exit;
        }
    }
	
      public function getProductList()
    {
        try {
            $sql = "Select * from tbl_product where product_type='custom'";
            $stmt = $this->dbConn->prepare($sql);
            $stmt->execute();
            $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            if (count($RESULT) > 0) {
                return $RESULT;
            } else {
                return false;
            }
        } //catch exception
        catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
            exit;
        }
    }
	
	public function getProductListShop()
    {
        try {
            $sql = "Select * from tbl_product where product_type='custom' AND is_active='1'";
            $stmt = $this->dbConn->prepare($sql);
            $stmt->execute();
            $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            if (count($RESULT) > 0) {
                return $RESULT;
            } else {
                return false;
            }
        } //catch exception
        catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
            exit;
        }
    }
    public function ProductExitsByName($name){
        try {
            $sql = "Select * from tbl_product WHERE product_name = :name";
            $stmt = $this->dbConn->prepare($sql);
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->execute();
            $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            if (count($RESULT) > 0) {
                return false;
            } else {
                return true;
            }
        } //catch exception
        catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
            exit;
        }
    }
    public function ProductExitsByCode($productcode){
        try {
            $sql = "Select * from tbl_product WHERE code = :code and product_type='custom'";
            $stmt = $this->dbConn->prepare($sql);
            $stmt->bindValue(':code', $productcode, PDO::PARAM_STR);
            $stmt->execute();
            $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            if (count($RESULT) > 0) {
                return false;
            } else {
                return true;
            }
        } //catch exception
        catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
            exit;
        }
    }
    public function getcurrencylist(){
       /* try {
            $sql = "Select * from currency_master";
            $stmt = $this->dbConn->prepare($sql);
            $stmt->execute();
            $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            if (count($RESULT) > 0) {
                return $RESULT;
            } else {
                return false;
            }
        } //catch exception
        catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
            exit;
        }*/
    }
    public function getCourseProductid($id){
        try {
            $sql = "Select * from course WHERE course_id = :id";
            $stmt = $this->dbConn->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            $stmt->execute();
            $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            if (count($RESULT) > 0) {
                return $RESULT;
            } else {
                return false;
            }
        } //catch exception
        catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
            exit;
        }
    }
    public function getCourseForProduct()
    {
        $productids = array();
        $prodid_sql = "SELECT * FROM tbl_master_product_list";
        $stmt = $this->dbConn->prepare($prodid_sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        foreach($results AS $res){
            array_push($productids,$res['id']);
        }
        //print_r($productids);exit;
/*         $numItems = count($productids);
        $i = 0;
        $where_pid = '(';
        foreach($productids AS $pid){
            $where_pid .= 'c.product_id='.$pid;
            if(++$i === $numItems) {
                $where_pid .='';
            }else{
                $where_pid .=' OR ';
            }
        }
        $where_pid .= ')'; */
		 return $productids;
        //echo $where_pid; exit;
        /* $sql = "SELECT c.course_id, c.tree_node_id, c.code, c.title, c.description, c.course_type, c.duration, c.course_status, gmt.is_active, c.updated_by, c.created_date, c.modified_date, c.delivery_type_id, c.product_id FROM  course c JOIN generic_mpre_tree gmt ON gmt.tree_node_id = c.tree_node_id  WHERE $where_pid AND gmt.is_active = 1";
        $stmt = $this->dbConn->prepare($sql);
        //print_r($stmt);exit;
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        $cList = array();
        while ($row = array_shift($RESULT)) {
            $bcm = new stdClass();
            $bcm->title = $row['title'];
            $bcm->course_code = $row['code'];
            $bcm->course_id = str_replace("CRS-", "", $row['code']);
            $bcm->description = $row['description'];
            $bcm->edge_id = $row['edge_id'];
            $bcm->thumnailImg = $row['thumnailImg'];
            $bcm->product_id_master = $row['product_id'];
            array_push($cList, $bcm);
        }
        //echo "<pre>";print_r($cList);exit;
        $courseArr = array();
        foreach ($cList as $key => $value) {
            $stmt = $this->dbConn->prepare("select st.standard, slm.level_text,slm.level_description,slm.level_cefr_map from tblx_standards st, tblx_standards_levels slm, course c where c.standard_id=st.id and c.level_id=slm.id and c.code='" . $value->course_code . "'");
            //print_r($stmt);
            $stmt->execute();
            $RESULTFINAL = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($value->thumnailImg != "") {
                $crsImagetemp = $this->thumnail_Img_url . $value->thumnailImg;
            } else {
                $crsImagetemp = $this->img_url . $value->course_code . ".png";
            }
            while ($row = array_shift($RESULTFINAL)) {
                $bcm = new stdClass();
                $bcm->percentage = 0;
                $bcm->edge_id = $value->edge_id;
                $bcm->name = $value->title;
                $bcm->desc = $value->description;
                $bcm->course_code = $value->course_code;
                $bcm->course_id = $value->course_id;
                $bcm->product_id_master = $value->product_id_master;
                $bcm->imgPath = $crsImagetemp;
                $bcm->standard = $row['standard'];
                $bcm->level_text = $row['level_text'];
                $bcm->level_description = $row['level_description'];
                $bcm->level_cefr_map = $row['level_cefr_map'];

                array_push($courseArr, $bcm);
            }
            $stmt->closeCursor();
            //echo "<pre>";print_r($bcm);exit;
        }
        //echo "<pre>";print_r($courseArr);exit;
        return $courseArr; */
    }
    public function GetProductById($id)
    {
        try {
            $sql = "Select * from tbl_product WHERE id = :id and product_type='custom'";
            $stmt = $this->dbConn->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            $stmt->execute();
            $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            if (count($RESULT) > 0) {
                return $RESULT;
            } else {
                return false;
            }
        } //catch exception
        catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
            exit;
        }
    }
    public function GetProductConfigById($id)
    {
        try {
            $sql = "Select * from tbl_custom_product_configuration WHERE product_id = :product_id";
            $stmt = $this->dbConn->prepare($sql);
            $stmt->bindValue(':product_id', $id, PDO::PARAM_INT);

            $stmt->execute();
            $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            if (count($RESULT) > 0) {
                return $RESULT;
            } else {
                return false;
            }
        } //catch exception
        catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
            exit;
        }
    }
    public function saveproduct($data)
    {
        $values = implode(',', $data);
        $stmt = $this->dbConn->prepare("INSERT INTO tbl_product (`client_id`,`product_name`, `price`, `thumbnail`, `code`,`package_code`,`currency_code`, `discount`, `discount_type`, `master_products_ids`,`expire_on`,`status`) VALUES $values");
        $stmt->execute();
        $inserted_id = $this->dbConn->lastInsertId();
        $stmt->closeCursor();
        return $inserted_id;
    }
    public function saveproductconfig($data)
    {
        $values = implode(',', $data);
        $stmt = $this->dbConn->prepare("INSERT INTO tbl_custom_product_configuration(`product_id`, `type`, `is_enabled`) VALUES $values");
        $stmt->execute();
        $inserted_id = $this->dbConn->lastInsertId();
        $stmt->closeCursor();
        return $inserted_id;
    }
    public function updateproduct($pid, $data)
    { 
        $stmt = $this->dbConn->prepare("UPDATE `tbl_product` SET $data WHERE `id`='$pid'");
        $stmt->execute();
        $stmt->closeCursor();
        if($stmt->rowCount()>0){
            return $stmt->rowCount();
        }else{
            return 0;
        }
    }
    public function UpdateProductConfigByTypeID($pid, $type, $data)
    {
        $sql = "Select * from tbl_custom_product_configuration WHERE product_id = :product_id AND type=:ptype";
        $stmt = $this->dbConn->prepare($sql);
        $stmt->bindValue(':product_id', $pid, PDO::PARAM_INT);
        $stmt->bindValue(':ptype', $type, PDO::PARAM_STR);
        $stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        if (count($RESULT) > 0) {
            $stmt = $this->dbConn->prepare("UPDATE `tbl_custom_product_configuration` SET `is_enabled`=:is_enabled WHERE `product_id`=:pid AND `type`=:ptype");
            $stmt->bindValue(':is_enabled', $data, PDO::PARAM_STR);
            $stmt->bindValue(':pid', $pid, PDO::PARAM_INT);
            $stmt->bindValue(':ptype', $type, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();
            if($stmt->rowCount()>0){
                $restult = $stmt->rowCount();
            }else{
                $restult = 0;
            }
        } else {
            $stmt = $this->dbConn->prepare("INSERT INTO tbl_custom_product_configuration(`product_id`, `type`, `is_enabled`) VALUES ('$pid','$type','$data')");
            $stmt->execute();
            $inserted_id = $this->dbConn->lastInsertId();
            $stmt->closeCursor();
            if($inserted_id >0){
                $restult = $inserted_id;
            }else{
                $restult = 0;
            }
        }
        return $restult;
    }
    public function SavePurchaseProduct($data){
        $values = implode(',', $data);
        $stmt = $this->dbConn->prepare("INSERT INTO tbl_product_purchase(`product_id`, `user_id`, `transection_id`) VALUES $values");
        $stmt->execute();
        $inserted_id = $this->dbConn->lastInsertId();
        $stmt->closeCursor();
        return $inserted_id;
    }
    public function GetCurrency(){
        try {
            $sql = "Select * from currency_master";
            $stmt = $this->dbConn->prepare($sql);
            //$stmt->bindValue(':id', $id, PDO::PARAM_INT);

            $stmt->execute();
            $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            if (count($RESULT) > 0) {
                //return $RESULT;
                /*$id = $RESULT[0]['id'];
                $name = $RESULT[0]['name'];
                $symbol = $RESULT[0]['symbol'];
                $obj = new stdclass();
                $obj->id = $id;
                $obj->name = $name;
                $obj->symbo = $symbol;
                return $obj;*/
                $listcurency = array();
                while($row = array_shift( $RESULT ) ) {
                    array_push($listcurency,$row);
                }
                return $listcurency;
            } else {
                return false;
            }
        } //catch exception
        catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
            exit;
        }
    }
    public function getproductmastername(){
        try {
            $sql = "Select * from tbl_master_product_list where publish='yes'";
            $stmt = $this->dbConn->prepare($sql);
            $stmt->execute();
            $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            if (count($RESULT) > 0) {
                $masterproduct = array();
                while($row = array_shift( $RESULT ) ) {
                    array_push($masterproduct,$row);
                }
                return $masterproduct;
            } else {
                return false;
            }
        } //catch exception
        catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
            exit;
        }
    }
	public function getProductMasterNameById($pid){
        try {
            $sql = "Select * from tbl_master_product_list where id IN ($pid) and publish='yes'";
            $stmt = $this->dbConn->prepare($sql);
            $stmt->execute();
            $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            if (count($RESULT) > 0) {
               
                return $RESULT;
            } else {
                return false;
            }
        } //catch exception
        catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
            exit;
        }
    }
	
	 public function getProductByClientId($client_id){
		$sql = "SELECT * from  tbl_product WHERE status=1 AND client_id=:client_id";
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
   public function getProductInfoByClientId($client_id){
		$sql = "SELECT tp.*,tpcm.product_id,tpcm.status from  tbl_product_client_map AS tpcm JOIN tbl_product AS tp ON tp.master_products_ids=tpcm.product_id  WHERE tpcm.status=1 AND tpcm.client_id=:client_id";
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
    public function getProduct(){
		$sql = "SELECT * from  tbl_product WHERE status=1";
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
    public function getPaymentStatusByUserId($user_id){
		$prodArr = array();
		$sql = "SELECT tup.product_id, tp.client_id,tp.standard_id, tp.product_name ,tp.product_desc ,tup.payment_status,tup.payment_date from  tblx_user_payment as tup JOIN tbl_product AS tp ON tp.id=tup.product_id WHERE user_id =:user_id";// order by payment_date desc
		$stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

		//echo "<pre>"; print_r($RESULT); die;
		$prodArr = array();
		if(count($RESULT) > 0 ){
			while($row = array_shift( $RESULT ) ) {
			  array_push($prodArr,$row);
		    }
		}
		
		return $prodArr; 
		
    }
	 public function setUserCurrentProductVisit($user_id,$product_id){
		 $old_product='';
		$sql = "insert into tblx_user_product_visit(user_id,old_product_id,current_product_id,visited_date) values('$user_id','$old_product','$product_id',NOW())";
		$stmt = $this->dbConn->prepare( $sql );
		$stmt->execute();
		$stmt->closeCursor(); 
         return true;
	 }
	 public function updateUserCurrentProductVisit($user_id,$product_id,$old_product){
		$sql = "update tblx_user_product_visit set  old_product_id='$old_product',current_product_id='$product_id',visited_date=Now() where user_id =:user_id";
		$stmt = $this->dbConn->prepare( $sql );
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->execute();
		$stmt->closeCursor(); 
         return true;
	 }
   function getVisitingProduct($user_id){

	   $sql="select id,old_product_id,current_product_id from tblx_user_product_visit where user_id =:user_id";
	    $stmt = $this->dbConn->prepare($sql);
		$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		//echo "<pre>"; print_r($RESULT); die;
		if(count($RESULT) > 0 ){
           return $RESULT[0]; 
		}else{
			return false;
		}

   }
   function getProdcutIdByMasterId($client_id,$master_product_id){
      $whr='';
		if($master_product_id!=''){
		  $whr.= 'AND tp.master_products_ids IN('.$master_product_id.')';			  
		}
	    $sql="select tp.id,tmpl.name from tbl_product as tp JOIN tbl_master_product_list as tmpl on tmpl.id=tp.master_products_ids  where tp.client_id=:client_id ".$whr." and tp.status=1";
	    $stmt = $this->dbConn->prepare($sql);
		//$stmt->bindValue(':master_product_id', $master_product_id, PDO::PARAM_INT);
		$stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		//echo "<pre>"; print_r($RESULT); die;
		if(count($RESULT) > 0 ){
           return $RESULT[0]; 
		}else{
			return false;
		}

   }
   function getProdcutDetailByIdArr($productArr){
      
	    $sql="select * from tbl_product where id IN($productArr) and status=1";
	    $stmt = $this->dbConn->prepare($sql);
		//$stmt->bindValue(':productArr', $productArr, PDO::PARAM_INT);
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		//echo "<pre>"; print_r($RESULT); die;
		if(count($RESULT) > 0 ){
           return $RESULT; 
		}else{
			return false;
		}

   }
   function getProdcutDetailById($product_id){
      
	    $sql="select * from tbl_product where id ='$product_id' and status=1";
	    $stmt = $this->dbConn->prepare($sql);
		//$stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
		$stmt->execute();
        $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
		//echo "<pre>"; print_r($RESULT); //die;
		if(count($RESULT) > 0 ){
           return $RESULT[0]; 
		}else{
			return false;
		}

   }
   
    public function checkProductAndCourseType($courseType,$courseArr){
	    // $course_id = implode(',', $course_array);
		   $courseStr= str_replace("CRS-","",$courseArr);

			$sql = "SELECT course_id,product_id,course_type from  course where course_type ='$courseType' AND course_id IN($courseStr) group by product_id";
			
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
  	 public function getProductIdFromCourse($courseType,$course_id){
	    // $course_id = implode(',', $course_array);
		$sql = "SELECT product_id from  course where course_type ='$courseType' AND course_id IN($course_id) group by product_id";
		
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
	
	   public function getCourseSuperClientIdByUserGroupId($user_group_id){

			$sql = "select user_id from user_role_map where user_group_id=:user_group_id and role_definition_id=3";
			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindValue(':user_group_id',$user_group_id, PDO::PARAM_INT);
			$stmt->execute();
			$RESULT1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();
			if(count($RESULT1) > 0 ){
				$course_client_id=$RESULT1[0]['user_id'];
					return $course_client_id;
				}else{
					return false;
				}  
		
    }
	
	 public function updateProductInCourse($cCode, $master_product_id,$sequence_id){
	    // $course_id = implode(',', $course_array);
		$sql = "update course set product_id='$master_product_id',sequence_id='$sequence_id' where code IN('$cCode')";
		$stmt = $this->dbConn->prepare( $sql );
		//$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt->execute();
		$stmt->closeCursor(); 
         return true;
		
    }
}

?>