<?php include_once('../header/trainerHeader.php');
if(isset($_GET['cid'])){
    $course_id = trim($_GET['cid']);
	$courseEdge= trim($_GET['cid']);
	$_SESSION['course_id']=trim($_GET['cid']);
}

if(isset($_GET['tEdge_id'])){
    $topic_edge_id = trim($_GET['tEdge_id']);
	$_SESSION['topic_edge_id']=$topic_edge_id;
}
if(isset($_GET['nid'])){
    $tree_node_id = trim($_GET['nid']);
	$_SESSION['nid']=$tree_node_id;
}

if( isset($_GET['cEdge_id']) ){
    $ch_edge_id = trim($_GET['cEdge_id']);
}
 if(isset($_GET['lid'])){
    $level_id = trim($_GET['lid']);
	$level=$COURSE_NAME." ".$level_id;
	$_SESSION['lid']=$$level_id;
	
}else{
	$level_id = $getRange;
	$level=$COURSE_NAME." ".$getRange;
	$_SESSION['lid']=$$level_id;
}
if( isset($_GET['chtid']) ){
    $ch_tree_node_id = trim($_GET['chtid']);
}
if( isset($_GET['tCount']) ){
    $tCount = trim($_GET['tCount']);
	if($tCount<10){
	 $topicCount="0".$tCount;
	}else{
	 $topicCount=$tCount;  
	}
}
if( isset($_GET['cCount']) ){
    $cCount = trim($_GET['cCount']);
	if($cCount<10){
	 $chCount="0".$cCount;
	}else{
	 $chCount=$cCount;  
	}
} 
//echo "<pre>"; print_r($ch_edge_id); exit;
$courseId=$assessmentObj->getCourseByEdgeId($ch_edge_id);
$course_code='CRS-'.$courseId;	
 
$modulePath= "learning_module.php";
$returnPath='&lid='.$level_id.'&tEdge_id='.$topic_edge_id.'&nid='.$tree_node_id.'&batch_id='.$batch_id.'&course_id='.$courseId.'&tid='.$tCount;

$chap_info = $assessmentObj->getScenarioByChapterId($ch_edge_id);

//echo "<pre>"; print_r($chap_info); die;
 if($_SESSION['role_id']==1){
 // if( count($chap_info[0]['scenario_type']['activity']) ){
	if( count($chap_info[0]->scenario_type) ){
	  
    if(PREPMODE==1){
		header('Location:../activity_player/prep_mode.php?chid='.$ch_edge_id.$returnPath);
		die();
	}else{
		$session_mode=ATTDENDANCE;
		header('Location:../activity_player/attendanceList.php?chid='.$ch_edge_id.$returnPath.'&session_mode='.$session_mode);
		die();
	}
 }else{
	  header('Location:../activity_player/nocontent.php?chid='.$ch_edge_id.$returnPath.'&session_mode=1');
	  die();
 }
}

?>

<?php include_once('../footer/trainerFooter.php');?>


