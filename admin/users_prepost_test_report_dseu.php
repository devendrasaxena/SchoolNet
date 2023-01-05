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

$students = get_data("SELECT u.user_id, CONCAT(IFNULL(u.first_name,' '),IFNULL(u.middle_name,' '),IFNULL(u.last_name,' ')) as student_name, u.fathers_name, u.mothers_name, u.email_id,u.gender, u.ex_phone as mobile , uc.loginid, uc.loginid as registration_id, u.roll_no, tbum.batch_id, tc.center_id,tc.dseu_center_code, tc.name as center_name FROM user u 
LEFT JOIN user_credential uc on u.user_id = uc.user_id 
LEFT JOIN user_role_map urm on u.user_id = urm.user_id 
LEFT JOIN  tblx_batch_user_map tbum on tbum.user_id = u.user_id
LEFT JOIN  user_center_map ucm on ucm.user_id = u.user_id
LEFT JOIN  tblx_center tc on tc.center_id = ucm.center_id
WHERE urm.role_definition_id = 2 AND tc.region = :region AND tc.center_id IN(39,40,41) order by center_name ASC", array('region'=>$region_id));

//print_r($students);exit;

$csv_pre_header = array('grammer'=>'Grammer','reading'=>'Reading','listening'=>'Listening','total_marks'=>'Total Marks','percentage'=>'Percentage','pre_level'=>'Pre-Assessment CEFR','assessment_date'=>'Assessment Date','assessment_time'=>'Assessment Time');

$csv_post_header = array('grammer'=>'Grammer','reading'=>'Reading','listening'=>'Listening','total_marks'=>'Total Marks','percentage'=>'Percentage','post_level'=>'Post-Assessment CEFR','assessment_date'=>'Assessment Date','assessment_time'=>'Assessment Time');

$csv_header = array('sno'=>'S. No','center_name'=>'Center Name','registration_id'=>'Registration No','roll_no'=>'Roll Number','student_name'=>'Candidate Name', 'pre'=> $csv_pre_header, 'post'=> $csv_post_header);
$head = 0;


$start_date = date('Y-m-01');
$end_date = date('Y-m-t');


foreach ($students as $key => $student) {
	$user_id = $student->user_id;
	$roll_no = $student->loginid;

	$pre_placement = get_data("SELECT score, date_attempted, total_questions, skill FROM tblx_placement_result WHERE exam_type = 'pre' AND user_id =:user_id", array('user_id'=>$user_id),1);
	
	$post_placement = get_data("SELECT score, date_attempted, total_questions, skill FROM tblx_placement_result WHERE exam_type = 'post' AND user_id =:user_id", array('user_id'=>$user_id),1);
    
	
	if(isset($pre_placement->date_attempted)){
		$pre_assessment_date = date('d/m/Y', strtotime($pre_placement->date_attempted));
		$pre_assessment_time = date('h:i:s A', strtotime($pre_placement->date_attempted));
	}
	else{
		$pre_assessment_date = '';
		$pre_assessment_time = '';
	}
	
	if(isset($post_placement->date_attempted)){
		$post_assessment_date = date('d/m/Y', strtotime($post_placement->date_attempted));
		$post_assessment_time = date('h:i:s A', strtotime($post_placement->date_attempted));
	}
	else{
		$post_assessment_date = '';
		$post_assessment_time = '';
	}
	
	if(isset($pre_placement->score)){
		$pre_total_marks =  $pre_placement->score;
		$pre_percentage =  round(($pre_placement->score/$pre_placement->total_questions) * 100).' %';
	}
	else{
		$pre_total_marks =  '0';
		$pre_percentage =  '0';
	}
	
	if(isset($post_placement->score)){
		$post_total_marks =  $post_placement->score;
		$post_percentage =  round(($post_placement->score/$post_placement->total_questions) * 100).' %';
	}
	else{
		$post_total_marks =  '0';
		$post_percentage =  '0';
	}
	
	if($pre_placement->skill==''){
		$skill = new stdClass;
		$skill->grammer = 0;
		$skill->reading = 0;
		$skill->listening = 0;
	}else{
		$skill = json_decode($pre_placement->skill);
	}
	
	$pre_level = get_data("SELECT roll_no, level_value FROM dseu_prepost WHERE exam_type = 'pre' AND roll_no = :roll_no", array('roll_no'=>$student->loginid),1);
	
	$post_level = get_data("SELECT roll_no, level_value FROM dseu_prepost WHERE exam_type = 'post' AND roll_no = :roll_no", array('roll_no'=>$student->loginid),1);
	
	$pre_level = isset($pre_level->level_value) ? $pre_level->level_value : '';
	$post_level = isset($post_level->level_value) ? $post_level->level_value : '';
	
	$pre = array('grammer'=>$skill->grammer, 'reading'=>$skill->reading, 'listening'=>$skill->listening, 'total_marks'=>$pre_total_marks, 'percentage'=>$pre_percentage, 'pre_level'=>$pre_level, 'assessment_date'=>$pre_assessment_date, 'assessment_time'=>$pre_assessment_time);

	if($post_placement->skill==''){
		$skill = new stdClass;
		$skill->grammer = 0;
		$skill->reading = 0;
		$skill->listening = 0;
	}else{
		$skill = json_decode($post_placement->skill);
	}
	$post = array('grammer'=>$skill->grammer, 'reading'=>$skill->reading, 'listening'=>$skill->listening, 'total_marks'=>$post_total_marks, 'percentage'=>$post_percentage, 'post_level'=>$post_level, 'assessment_date'=>$post_assessment_date, 'assessment_time'=>$post_assessment_time);

	$students[$key]->pre = $pre;
	$students[$key]->post = $post;

}

ob_clean();

/* echo '<pre style="color:red">';
print_r($csv_header);
print_r(($students));
exit; */
   
        $file = 'users_prepost_test_report_'.time().'.csv';
     
        $export_data = '<table>';
   
             		     $export_data .= '<tr><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
						 <th>Pre Test</th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
						 <th>Post Test</th>
						 </tr>';						 
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

