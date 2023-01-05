<?php 
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-type:application/json");
if(!empty($_FILES['recfile'])) 
{
    $filename = $_FILES['recfile']['name'];
    $file_locationtmp = $_FILES['recfile']['tmp_name'];
    $filetype = $_FILES['recfile']['type'];
    $filesize_bytes = $_FILES['recfile']['size'];
    

   $_FILES['recfile']['name'] = 'tFeedback'.'-'.time().'-'.base64_decode($_REQUEST['aid']).'.mp3';
   $target_Path = "../assignment_audio_feedback/";
   $target_Path = $target_Path.basename( $_FILES['recfile']['name'] );
   $st = move_uploaded_file( $_FILES['recfile']['tmp_name'], $target_Path );
   $data = array('status'=>$st, 'filename'=>$_FILES['recfile']['name']);
   echo json_encode($data);

}else{

  $data = array('status'=>false, 'filename'=>null);
  echo json_encode($data);

 }