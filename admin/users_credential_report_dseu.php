<?php 
include_once('../header/adminHeader.php');

ini_set('max_execution_time', 0);

if(!function_exists('get_data')){
	function get_data($qry,$whr = null, $one = false){
		$pdo = DBConnection::createConn();
		$stmt = $pdo->prepare($qry);
		if($whr != null){
			foreach ($whr as $key => $w) {
				$stmt->bindValue(":$key", $w);
			}
		}

	    $stmt->execute();
	    if($one)
	    	$data = $stmt->fetch(PDO::FETCH_OBJ);
	    else 
	    	$data = $stmt->fetchAll(PDO::FETCH_OBJ);
	    
	    return $data;
	}

}


function set_str_value($str = ""){
	return trim(addslashes(filter_string($str)));
}


$region_id = $_SESSION['region_id'];

$students = get_data("SELECT u.user_id, CONCAT(IFNULL(u.first_name,' '),IFNULL(u.middle_name,' '),IFNULL(u.last_name,' ')) as student_name, u.email_id as email_id, uc.loginid as registration_id, uc.password as password, u.roll_no, tbum.batch_id, tc.center_id, tc.region,tc.dseu_center_code, tc.name as center_name FROM user u 
LEFT JOIN user_credential uc on u.user_id = uc.user_id 
LEFT JOIN user_role_map urm on u.user_id = urm.user_id 
LEFT JOIN  tblx_batch_user_map tbum on tbum.user_id = u.user_id
LEFT JOIN  user_center_map ucm on ucm.user_id = u.user_id
LEFT JOIN  tblx_center tc on tc.center_id = ucm.center_id
WHERE urm.role_definition_id = 2 AND tc.region = 5 AND tc.center_id IN(39,40,41) order by center_id ASC");

//print_r($students);exit;


$csv_header = array('sno'=>'S. No','registration_id'=>'User ID','roll_no'=>'Roll Number','email_id'=>'Email ID','password'=>'Password');
$head = 0;


$start_date = date('Y-m-01');
$end_date = date('Y-m-t');


ob_clean();

/* echo '<pre style="color:red">';
print_r($csv_header);
print_r(($students));
exit; */
   
        $file = 'user_credential_report_'.time().'.csv';
     
        $export_data = '<table>';					 
             		     $export_data .= '<tr>';
             			foreach ($csv_header as $k => $col) {
             				if(is_array($col)){
								foreach ($col as $k1 => $c) {
									$export_data .= "<th>$c</th>";
								}
							}
							else
             				  $export_data .= "<th>$col</th>";
             			}

             			$export_data .= '</tr>';

        if (count($students) > 0) {
            $i = 0;
          
             foreach($students  as $key3 => $value){
                $i++;
               
			   $export_data .= '<tr>';
               foreach ($csv_header as $field_name => $val) {
               				if('sno' == $field_name)
               					$export_data .= "<td>".trim($i)."</td>";
               				else{
								if($field_name=='pre' || $field_name=='post'){
									foreach($value->{$field_name} as $fname => $v){
										$export_data .= "<td>".trim($v)."</td>";
									}
								}
								else
									$export_data .= "<td>".trim($value->{$field_name})."</td>";
							}
             			}

                $export_data .= '</tr>';
			
			 }
        }

        $export_data .= '</table>';
	    $html = str_get_html($export_data);

// echo $html; exit;

		header('Content-type: application/ms-excel');
		header('Content-Disposition: attachment; filename='.$file);

		$fp = fopen("php://output", "w");

		if(!empty($html)){
			foreach($html->find('tr') as $element)
			{
				if(!empty($element)){
					$th = array();
					foreach( $element->find('th') as $row)  
					{
						$th [] = $row->plaintext;
					}

					$td = array();
					foreach( $element->find('td') as $row)  
					{
						$td [] = $row->plaintext;
					}
					!empty($th) ? fputcsv($fp, $th) : fputcsv($fp,  $td);
			
				}
			
			}
		}
		fclose($fp);
exit;

