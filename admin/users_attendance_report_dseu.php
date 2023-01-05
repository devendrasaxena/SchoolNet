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
		 $stmt->closeCursor();
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


function createAttendanceDays($date, &$students, $key, $attendance, &$csv_header){

	 $dates = array();
	$begin = new DateTime('2022-09-19');
	$end = new DateTime('2022-11-15');


	$interval = DateInterval::createFromDateString('1 day');
	$period = new DatePeriod($begin, $interval, $end);

	foreach ($period as $k=>$dt) {
	    $d = $dt->format("d-m-Y");
	    $date = $dt->format("Y-m-d");
	    $i = $k+1;
	    $csv_header["Day-$i"] = $d . " (Day-$i)";
	    $students[$key]->{"Day-$i"} = '-';
	    $dates[] = $date;
	}
	
	
	

	foreach ($attendance as $key2 => $att) {
		
		   if(in_array($att->attendance_date, $dates)){
			$col = array_search($att->attendance_date, $dates);
		    $col++;
			if($att->is_present == 'no')
				$present = 'Absent';
			else if($att->is_present == 'yes')
				$present = 'Present';
			else
				$present = 'Holiday';

			$students[$key]->{"Day-$col"} = $present;
		}
	}


    return $days;
	
}


$region_id = $_SESSION['region_id'];

$students = get_data("SELECT u.user_id, CONCAT(IFNULL(u.first_name,' '),IFNULL(u.middle_name,' '),IFNULL(u.last_name,' ')) as student_name, u.fathers_name, u.mothers_name, u.email_id,u.gender, u.ex_phone as mobile , uc.loginid, uc.loginid as registration_id, u.roll_no, tbum.batch_id, tc.center_id,tc.dseu_center_code, tc.name as center_name FROM user u 
LEFT JOIN user_credential uc on u.user_id = uc.user_id 
LEFT JOIN user_role_map urm on u.user_id = urm.user_id 
LEFT JOIN  tblx_batch_user_map tbum on tbum.user_id = u.user_id
LEFT JOIN  user_center_map ucm on ucm.user_id = u.user_id
LEFT JOIN  tblx_center tc on tc.center_id = ucm.center_id
WHERE urm.role_definition_id = 2 AND tc.region = :region AND tc.center_id IN(39,40,41)", array('region'=>$region_id));

$csv_header = array('sno'=>'S. No','registration_id'=>'Registration No','roll_no'=>'Roll No','student_name'=>'Name of Student','gender'=>'Gender','fathers_name'=>'Father\'s Name','mothers_name'=>'Mother\'s Name','email_id'=>'Email ID','mobile'=>'Mobile Number','dseu_center_code'=>'Center ID','center_name'=>'Center Name','batch_name'=>'Slot Name','slot'=>'Slot','section'=>'Section','loginid'=>'User ID','teacher_name'=>'Name of Teacher','senior_teacher_name'=>'Name of Senior Teacher','pre_assessment_time'=>'Pre-Assessment Time (in mins)','post_assessment_time'=>'Post-Assessment Time (in mins)','time_spend'=>'Time spend in mins (App only)','total_time'=>'Total Time (in mins)','pre_score'=>'Pre assessment ( App based numbers)','pre_level'=>'Pre assessment ( CEFR level)','post_score'=>'Post assessment ( App based numbers)','post_level'=>'Post assessment ( CEFR level)');
$head = 0;


$start_date = date('Y-m-01');
$end_date = date('Y-m-t');


foreach ($students as $key => $student) {
	$user_id = $student->user_id;
	$roll_no = $student->loginid;

	$batch = get_data("SELECT  batch_name FROM tblx_batch WHERE batch_id = :batch_id AND center_id = :center_id", array('batch_id'=>$student->batch_id,'center_id'=>$student->center_id),1);

	$gender = get_data("SELECT  name FROM tblx_gender WHERE id = :gender", array('gender'=>$student->gender),1);
	$students[$key]->gender = $gender->name;

	$students[$key]->batch_name  = $batch->batch_name;

	$attendance = get_data("SELECT da.is_present, da.attendance_date FROM dseu_attendance da where da.roll_no = :roll_no GROUP BY da.attendance_date", array('roll_no'=>$roll_no));

	

	$time = get_data("select sum(actual_seconds)/60 as time_spend from user_session_tracking where (session_type = 'CM' OR session_type = 'AS') AND user_id = :user_id", array('user_id'=>$user_id),1);

	$students[$key]->time_spend = ceil($time->time_spend);


	$teacher = get_data("SELECT CONCAT(IFNULL(u.first_name,' '),IFNULL(u.middle_name,' '),IFNULL(u.last_name,' ')) as teacher_name, u.user_id  FROM user u JOIN user_center_map ucm on u.user_id = ucm.user_id join user_role_map urm on u.user_id = urm.user_id  join tblx_batch_user_map tbum on u.user_id = tbum.user_id
 WHERE ucm.center_id = :center_id AND urm.role_definition_id = 1 AND tbum.batch_id=:batch_id", array('center_id'=>$student->center_id,'batch_id'=>$student->batch_id),1);


	$senior_teacher = get_data("SELECT CONCAT(IFNULL(u.first_name,' '),IFNULL(u.middle_name,' '),IFNULL(u.last_name,' ')) as senior_teacher_name, u.user_id  FROM user u JOIN user_center_map ucm on u.user_id = ucm.user_id join user_role_map urm on u.user_id = urm.user_id WHERE ucm.center_id = :center_id AND urm.role_definition_id = 4", array('center_id'=>$student->center_id),1);


	$pre_placement = get_data("SELECT score, time_spent FROM tblx_placement_result WHERE exam_type = 'pre' AND user_id =:user_id", array('user_id'=>$user_id),1);
	
	$post_placement = get_data("SELECT score, time_spent FROM tblx_placement_result WHERE exam_type = 'post' AND user_id =:user_id", array('user_id'=>$user_id),1);
    

	$students[$key]->teacher_name = $teacher->teacher_name;
	$students[$key]->senior_teacher_name = $senior_teacher->senior_teacher_name;
	$secEx = explode('-',$student->batch_name);
	
	$totalTimeapp = $students[$key]->time_spend = ceil($time->time_spend);
	$preTimeSpent = ceil($pre_placement->time_spent/60);
	$postTimeSpent = ceil($post_placement->time_spent/60);
	$totalTimeSpent = $preTimeSpent + $postTimeSpent + $totalTimeapp;
	

	$students[$key]->section = end($secEx);
	$students[$key]->pre_score = isset($pre_placement->score) ? $pre_placement->score : '';
	$students[$key]->pre_assessment_time = isset($pre_placement->time_spent) ? $preTimeSpent : '';
	$students[$key]->post_score = isset($post_placement->score) ? $post_placement->score : '';
	$students[$key]->post_assessment_time = isset($post_placement->time_spent) ? $postTimeSpent : '';
	$students[$key]->total_time = $totalTimeSpent;
	$slot_id = str_replace("Slot-","",$student->batch_name);

	$slot = get_data("SELECT slot_name FROM tblx_slot_master WHERE slot_id = :slot_id", array('slot_id'=>$slot_id),1);
	$students[$key]->slot = $slot->slot_name;

	$pre_level = get_data("SELECT roll_no, level_value FROM dseu_prepost WHERE exam_type = 'pre' AND roll_no = :roll_no", array('roll_no'=>$student->loginid),1);
	
	$post_level = get_data("SELECT roll_no, level_value FROM dseu_prepost WHERE exam_type = 'post' AND roll_no = :roll_no", array('roll_no'=>$student->loginid),1);
	

	$students[$key]->pre_level = isset($pre_level->level_value) ? $pre_level->level_value : '';
	$students[$key]->post_level = isset($post_level->level_value) ? $post_level->level_value : '';

	createAttendanceDays(date('Y-m-d'),$students,$key, $attendance, $csv_header);
    	
}


ob_clean();

/* echo '<pre style="color:red">';


print_r($csv_header);
print_r(($students));
exit; */

   
        $file = 'users_progress_report_'.time().'.csv';
     
        $export_data = '<table>';
   
     	
             		     $export_data .= '<tr>';
             			foreach ($csv_header as $k => $col) {
             				
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
               				else	
             				  $export_data .= "<td>".trim($value->{$field_name})."</td>";
             			}

                $export_data .= '</tr>';
			
			 }
        }



        $export_data .= '</table>';
	    $html = str_get_html($export_data);





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

