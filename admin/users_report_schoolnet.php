<?php 
include_once('../header/adminHeader.php');
//$stdRowsData = getTestReport(2,'1B');	
ini_set('max_execution_time', 0);
if(isset($_SESSION['client_id'])){
   $client_id=$_SESSION['client_id'];
 }

$reportObj = new reportController();
$centerObj = new centerController();
$country_list_arr=$reportObj->getCountryList();
$options = array();
$options['client_id'] = $client_id;



$dir = "";
$order = (isset($_GET['sort']) && $_GET['sort'] !="") ? filter_query($_GET['sort']) : 'u.first_name';
$dir = (isset($_GET['dir']) && $_GET['dir'] !="") ? filter_query($_GET['dir']) : 'ASC';




function getLastLogin($datetime, $full = false) {
	if($datetime == "")
		return '-';
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}




switch(strtoupper($dir)){
	case 'DESC': 
		$dir = 'ASC'; 
		break;
	case 'ASC': 
		$dir = 'DESC'; 
		break;
	default: 
		$dir = 'DESC'; 
		break;
}


$page_param='';

$page_param .= "sort=".filter_query($_GET['sort'])."&dir=".filter_query($_GET['dir'])."&";

$rid = isset($_SESSION['region_id'])?$_SESSION['region_id']:'';
$region_id = '';
$center_id='';
$country='';
$district_id='';
$tehsil_id='';
$role_id = '';
if (!empty($_SESSION['region_id'])) { 
    $region_id = trim($_SESSION['region_id']);
	$options['region_id'] = $region_id;
	$page_param .= "region_id=$region_id&";
}elseif (!empty($_REQUEST['region_id'])) {
    $region_id = trim(filter_query($_REQUEST['region_id']));
	$options['region_id'] = $region_id;
	$country_list_arr=$reportObj->getCountryList($region_id);
	$page_param .= "region_id=$region_id&";
}else{
	$options['region_id'] = $rid;
}
if (!empty($_REQUEST['center_id'])) {
    $center_id = trim(filter_query($_REQUEST['center_id']));
	$options['center_id'] = $center_id;
	$page_param .= "center_id=$center_id&";
}
if (!empty($_REQUEST['district_id'])) {
    $district_id = trim(filter_query($_REQUEST['district_id']));
	$options['district_id'] = $district_id;
	$page_param .= "district_id=$district_id&";
}

if (!empty($_REQUEST['tehsil_id'])) {
    $tehsil_id  = trim(filter_query($_REQUEST['tehsil_id']));
	$options['tehsil_id'] = $tehsil_id ;
	$page_param .= "tehsil_id=$tehsil_id&";
}

if (!empty($_REQUEST['role_id'])) {
    $role_id = trim(filter_query($_REQUEST['role_id']));
	$options['role_id'] = $role_id ;
	$page_param .= "role_id=$role_id&";
}else{
	$options['role_id'] = 2;
}
if (!empty($_REQUEST['student'])) {
    $student1 = trim(filter_query($_REQUEST['student']));
	$options['user_id'] = $student1 ;
	$page_param .= "student=$student1&";
}
if (!empty($_REQUEST['student_txt'])) {
    $student_txt = trim(filter_query($_REQUEST['student_txt']));
	$options['student_txt'] = $student_txt ;
	$page_param .= "student_txt=$student_txt&";
}


$_page = empty($_GET['page']) || !is_numeric($_GET['page'])? 1 : filter_query($_GET['page']); 

if(isset($_REQUEST['limit']))
$_limit = intval($_REQUEST['limit']);
else
 $_limit = PAGINATION_LIMIT;

$objPage = new Pagination($_page, $_limit);


	$response_result= $reportObj->getUsersByCenterAndCountry($options,$objPage->_db_start, '',$order,$dir);


//$dir = $dir == 'ASC' ? 'DESC' : 'ASC';

//$centres = $reportObj->getCentresList($rid);

$objPage->_total = $response_result['total'];
$users_arr = $response_result['result'];
/*ob_clean();*/
//echo "<pre>";print_r($users_arr);//exit
/*

 if (count($users_arr) > 0) {
            $i = 0;
             foreach($users_arr  as $key => $value){
				   
				   $user_id=$value['user_id'];
				  $center_id=$value['center_id'];
				  $userBatchInfo=$reportObj->getBatchByUserCenter($user_id,$center_id);
				  $batch_id=$userBatchInfo[0]['batch_id'];
				  $batchData = $centerObj->getBatchDataByID($batch_id,$center_id);
				  $userPreTest=$reportObj->getScorePlacementByUserRegion($user_id,$batch_id,$region_id);
				  $preTest_time_spent=$userPreTest[0]['time_spent'];
				 
				$first_name=$value['first_name'];
				$last_name=$value['last_name'];
				$fullname=$first_name." ".$last_name;
				
				$email_id=$value['email_id'];
				$mother_tongue=$value['mother_tongue'];
				$status=$value['is_active'];
				if($status=='1')
				{
				$status='Active';
				}
				$created_date=$value['created_date'];
				$created_date = date('d-m-Y',strtotime($created_date));
				
				$centerName=$value['centerName'];
				
				if($value['last_visit']!= ""){
					//echo $value['last_visit'];echo "<br>";
				  $lastLoginDate = date('d-m-Y',strtotime($value['last_visit']));
				  $lastLoginTime = date('h:i:s',strtotime($value['last_visit']));
				  //echo "<br>";
				}else{
					$lastLoginDate =  '-';
					$lastLoginTime =  '-';
				}
				
				
				if($centerName==''){
				  $centerName='-';
				}
				$middle_name='';
				$district="Dehradun";
				$block="";
				$UDISE_code=5050420907;
			 
				$className=$batchData[0]['batch_name'];
				$section='';
				$levelDeterminent=100;
				$preTest=$userPreTest[0]['score'];
				
				$quizTest1=$reportObj->getScoreQuizByUserTestId($user_id,85578);
				//echo "<pre>";print_r($quizTest1);//avg_time_sp//ttl_time_sp
				$levelA1QuizTest1=$quizTest1['score_per'];//85578 //quizEdgeId
				//echo "<br>";
				$quizTest2=$reportObj->getScoreQuizByUserTestId($user_id,85580);
				$levelA1QuizTest2=$quizTest2['score_per'];//85580 //quizEdgeId
				//echo "<br>";
				$quizTest3=$reportObj->getScoreQuizByUserTestId($user_id,85582);
				$levelA1Test=$quizTest3['score_per'];//85582 //quizEdgeId
				
				$assTestA21=$reportObj->getScoreQuizByUserTestId($user_id,85563);
				$levelA2QuizTest1=$assTestA21['score_per'];//85563 //topicEdgeId
				
				$assTestA22=$reportObj->getScoreQuizByUserTestId($user_id,85583);
				$levelA2QuizTest2=$assTestA22['score_per'];//85583 //topicEdgeId
				
				$assTestA23=$reportObj->getScoreQuizByUserTestId($user_id,85564);
				$levelA2Test=$assTestA23['score_per'];///85564 //topicEdgeId
				
				$assTestB11=$reportObj->getScoreQuizByUserTestId($user_id,85565);
				$levelB1QuizTest1=$assTestB11['score_per'];///85565 //topicEdgeId
				
				$assTestB12=$reportObj->getScoreQuizByUserTestId($user_id,85584);
				$levelB1QuizTest2=$assTestB12['score_per'];///85584 //topicEdgeId
				
				$assTestB13=$reportObj->getScoreQuizByUserTestId($user_id,85566);
				$levelB1Test=$assTestB13['score_per'];///85566  //topicEdgeId
				
				$assTestB21=$reportObj->getScoreQuizByUserTestId($user_id,85569);
				$levelB2QuizTest1=$assTestB21['score_per'];///85569  //topicEdgeId
				
				$assTestB22=$reportObj->getScoreQuizByUserTestId($user_id,85585);
				$levelB2QuizTest2=$assTestB22['score_per'];//85585 //topicEdgeId
				
				$assTestB23=$reportObj->getScoreQuizByUserTestId($user_id,85570);
				$levelB2Test=$assTestB23['score_per'];//85570 //topicEdgeId //Post Assessment
				
				$totalTimeSpent=$userPreTest[0]['time_spent']+$assTestA11['avg_time_sp']+$assTestA12['avg_time_sp']+$assTestA13['avg_time_sp']+$assTestA21['avg_time_sp']+$assTestA22['avg_time_sp']+$assTestA23['avg_time_sp']+$assTestB11['avg_time_sp']+$assTestB12['avg_time_sp']+$assTestB13['avg_time_sp']+$assTestB21['avg_time_sp']+$assTestB22['avg_time_sp']+$assTestB23['avg_time_sp'];
				$totalTimeSpent= ceil($totalTimeSpent/60);
				
                $i++;
			 }
 }
 
*/

ini_set('display_errors', 1);
        ob_clean();
      // $file = 'learners_report_'.time().'.xls';
        $file = 'users_report_schoolnet_'.time().'.csv';
       
        $export_data = '<table>';
        $export_data .= '<tr>';
        $export_data .= '<th>Sr.No.</th>';
        $export_data .= '<th>First Name</th>';
		$export_data .= '<th>Middle Name</th>';
		$export_data .= '<th>Last Name</th>';
        $export_data .= '<th>District</th>';
        $export_data .= '<th>Block</th>';
        $export_data .= '<th>School Name</th>';
        $export_data .= '<th>UDISE Code</th>';
        $export_data .= '<th>Class</th>';
		$export_data .= '<th>Section</th>';
		$export_data .= '<th>Last Login Date</th>';
		$export_data .= '<th>Last Login Time</th>';
		$export_data .= '<th>Baselining Test/ Level Determinent (LDT)</th>';
		$export_data .= '<th>Baselining Test Score (LDT)</th>';
		$export_data .= '<th>Student Baseline Level</th>';
		$export_data .= '<th>Remedial Test 1 (A1)</th>';
		$export_data .= '<th>Remedial Test 2 (A1)</th>';
		$export_data .= '<th>A1 Level Completion</th>';
		$export_data .= '<th>Remedial Test 1 (A2)</th>';
		$export_data .= '<th>Remedial Test 2 (A2)</th>';
		$export_data .= '<th>A2 Level Completion</th>';
		$export_data .= '<th>Remedial Test 1 (B1)</th>';
		$export_data .= '<th>Remedial Test 2 (B1)</th>';
		$export_data .= '<th>B1 Level Completion</th>';
		$export_data .= '<th>Remedial Test 1 (B2)</th>';
		$export_data .= '<th>Remedial Test 2 (B2)</th>';
		$export_data .= '<th>Post Assessment</th>';
		$export_data .= '<th>Time Spent (Overall, In Minutes)</th>';
		$export_data .= '</tr>';
		
		
       if (count($users_arr) > 0) {
            $i = 0;
             foreach($users_arr  as $key => $value){
				   
				  $user_id=$value['user_id'];
				  $center_id=$value['center_id'];
				  $userBatchInfo=$reportObj->getBatchByUserCenter($user_id,$center_id);
				  $batch_id=$userBatchInfo[0]['batch_id'];
				  $batchData = $centerObj->getBatchDataByID($batch_id,$center_id);
				  $userPreTest=$reportObj->getScorePlacementByUserRegion($user_id,$batch_id,$region_id);
				  $preTest_time_spent=$userPreTest[0]['time_spent'];
				 
				$first_name=$value['first_name'];
				$last_name=$value['last_name'];
				$fullname=$first_name." ".$last_name;
				
				$email_id=$value['email_id'];
				$mother_tongue=$value['mother_tongue'];
				$status=$value['is_active'];
				if($status=='1')
				{
				$status='Active';
				}
				$created_date=$value['created_date'];
				$created_date = date('d-m-Y',strtotime($created_date));
				
				$centerName=$value['centerName'];
				
				if($value['last_visit']!= ""){
					//echo $value['last_visit'];echo "<br>";
				  $lastLoginDate = date('d-m-Y',strtotime($value['last_visit']));
				  $lastLoginTime = date('h:i:s',strtotime($value['last_visit']));
				  //echo "<br>";
				}else{
					$lastLoginDate =  '-';
					$lastLoginTime =  '-';
				}
				
				
				if($centerName==''){
				  $centerName='-';
				}
				$middle_name='';
				$district="Dehradun";
				$block="";
				$UDISE_code=5050420907;
			 
				$className=$batchData[0]['batch_name'];
				$section='';
				
				$preTest=$userPreTest[0]['score'];
				$levelDeterminent=($preTest!='')?100:'';
				if($preTest>0 && $preTest<=50){
					$studentBaseLavel='A1';
				}else if($preTest>=51 && $preTest<=60){
					$studentBaseLavel='A2';
				}else if($preTest>=61 && $preTest<=70){
					$studentBaseLavel='B1';
				}else if($preTest>=71){
					$studentBaseLavel='B2';
				}else{
					$studentBaseLavel='';
				}
			
				
				$quizTest1=$reportObj->getScoreQuizByUserTestId($user_id,85578);
				//echo "<pre>";print_r($quizTest1);//avg_time_sp//ttl_time_sp
				$levelA1QuizTest1=$quizTest1['score_per'];//85578 //quizEdgeId
				//echo "<br>";
				$quizTest2=$reportObj->getScoreQuizByUserTestId($user_id,85580);
				$levelA1QuizTest2=$quizTest2['score_per'];//85580 //quizEdgeId
				//echo "<br>";
				$quizTest3=$reportObj->getScoreQuizByUserTestId($user_id,85582);
				$levelA1Test=$quizTest3['score_per'];//85582 //quizEdgeId
				
				$assTestA21=$reportObj->getScoreQuizByUserTestId($user_id,85563);
				$levelA2QuizTest1=$assTestA21['score_per'];//85563 //topicEdgeId
				
				$assTestA22=$reportObj->getScoreQuizByUserTestId($user_id,85583);
				$levelA2QuizTest2=$assTestA22['score_per'];//85583 //topicEdgeId
				
				$assTestA23=$reportObj->getScoreQuizByUserTestId($user_id,85564);
				$levelA2Test=$assTestA23['score_per'];///85564 //topicEdgeId
				
				$assTestB11=$reportObj->getScoreQuizByUserTestId($user_id,85565);
				$levelB1QuizTest1=$assTestB11['score_per'];///85565 //topicEdgeId
				
				$assTestB12=$reportObj->getScoreQuizByUserTestId($user_id,85584);
				$levelB1QuizTest2=$assTestB12['score_per'];///85584 //topicEdgeId
				
				$assTestB13=$reportObj->getScoreQuizByUserTestId($user_id,85566);
				$levelB1Test=$assTestB13['score_per'];///85566  //topicEdgeId
				
				$assTestB21=$reportObj->getScoreQuizByUserTestId($user_id,85569);
				$levelB2QuizTest1=$assTestB21['score_per'];///85569  //topicEdgeId
				
				$assTestB22=$reportObj->getScoreQuizByUserTestId($user_id,85585);
				$levelB2QuizTest2=$assTestB22['score_per'];//85585 //topicEdgeId
				
				$assTestB23=$reportObj->getScoreQuizByUserTestId($user_id,85570);
				$levelB2Test=$assTestB23['score_per'];//85570 //topicEdgeId //Post Assessment
				
				$totalTimeSpent=$userPreTest[0]['time_spent']+$assTestA11['avg_time_sp']+$assTestA12['avg_time_sp']+$assTestA13['avg_time_sp']+$assTestA21['avg_time_sp']+$assTestA22['avg_time_sp']+$assTestA23['avg_time_sp']+$assTestB11['avg_time_sp']+$assTestB12['avg_time_sp']+$assTestB13['avg_time_sp']+$assTestB21['avg_time_sp']+$assTestB22['avg_time_sp']+$assTestB23['avg_time_sp'];
				$totalTimeSpent= ceil($totalTimeSpent/60);
				
                $i++;
				
				$export_data .= '<tr>';
                $export_data .= '<th>' . $i . '</th>';
                $export_data .= '<th>' . ltrim($first_name,"@-+="). '</th>';
				$export_data .= '<th>' . ltrim($middle_name,"@-+="). '</th>';
				$export_data .= '<th>' . ltrim($last_name,"@-+="). '</th>';
				
				$export_data .= '<th>' . ltrim($district,"@-+="). '</th>';
				$export_data .= '<th>' . ltrim($block,"@-+="). '</th>';
				
				$export_data .= '<th>' . ltrim($centerName,"@-+="). '</th>';
                $export_data .= '<th>' . ltrim($UDISE_code,"@-+="). '</th>';
				
                $export_data .= '<th>' . ltrim($className,"@-+=") . '</th>';
                $export_data .= '<th>' . ltrim($section,"@-+="). '</th>';
				
                $export_data .= '<th>' . ltrim($lastLoginDate,"@-+=") . '</th>';
				$export_data .= '<th>' . ltrim($lastLoginTime,"@-+="). '</th>';
				
				$export_data .= '<th>' . ltrim($levelDeterminent,"@-+="). '</th>';
				$export_data .= '<th>' . ltrim($preTest,"@-+="). '</th>';
				$export_data .= '<th>' . ltrim($studentBaseLavel,"@-+="). '</th>';

				$export_data .= '<th>' . ltrim($levelA1QuizTest1,"@-+="). '</th>';
				$export_data .= '<th>' . ltrim($levelA1QuizTest2,"@-+="). '</th>';
				$export_data .= '<th>' . ltrim($levelA1Test,"@-+="). '</th>';
				
				$export_data .= '<th>' . ltrim($levelA2QuizTest1,"@-+="). '</th>';
				$export_data .= '<th>' . ltrim($levelA2QuizTest2,"@-+="). '</th>';
				$export_data .= '<th>' . ltrim($levelA2Test,"@-+="). '</th>';
				
				$export_data .= '<th>' . ltrim($levelB1QuizTest1,"@-+="). '</th>';
				$export_data .= '<th>' . ltrim($levelB1QuizTest2,"@-+="). '</th>';
				$export_data .= '<th>' . ltrim($levelB1Test,"@-+="). '</th>';

				$export_data .= '<th>' . ltrim($levelB2QuizTest1,"@-+="). '</th>';
				$export_data .= '<th>' . ltrim($levelB2QuizTest2,"@-+="). '</th>';
				$export_data .= '<th>' . ltrim($levelB2Test,"@-+="). '</th>';
				
                $export_data .= '<th>' . ltrim($totalTimeSpent,"@-+="). '</th>';
				
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
					!empty($th) ? fputcsv($fp, $th) : fputcsv($fp, $td);
			
				}
			
			}
		}
	
		fclose($fp);
		exit;
		
?>

<?php include_once('../footer/adminFooter.php'); ?>
 



 
 

