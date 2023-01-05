<?php
$_html_relative_path='../';//dirname(dirname(__FILE__)).'/';
$_html_relative_path = isset($_html_relative_path) ? $_html_relative_path : '';

if(isset($_SESSION['client_id'])){
   $client_id=$_SESSION['client_id'];
 }

 //echo "<pre>";print_r($_SESSION);exit;
 if(isset($_SESSION['user_id'])){
   $user_id=$_SESSION['user_id'];
 }
//echo "<pre>";print_r($client_id);exit;
$customer_id=$lic_customer_id;
$commonObj = new commonController();
$clientObj = new clientController();
$centerObj = new centerController(); 
$adminObj = new centerAdminController();
$userObj = new userController();
$serObj = new serviceController();
$regObj = new registrationController();
$assessmentObj = new assessmentController();
$batteryObj = new batteryController();
$objCon = new componentController();
$objTR = new trackController();
$langObj = new languageController();

 $systemLogo = ($companyInfo['logo'] != "") ? $_html_relative_path."profile_pic/".$companyInfo['logo'] :$_html_relative_path."images/logo.png";	
 $appTitle = ($companyInfo['app_name'] != "") ? $companyInfo['app_name'] : $softwareTitle;
$center=($companyInfo['lbl_center']) ? $companyInfo['lbl_center'] : 'State'; 
$centers=($companyInfo['lbl_center']) ? $companyInfo['lbl_center'].'s' : 'States';
$batch=($companyInfo['lbl_batch']) ? $companyInfo['lbl_batch'] : 'Class';
$batches=($companyInfo['lbl_batch']) ? $companyInfo['lbl_batch'].'es' : 'Classes';
$teacher=($companyInfo['lbl_teacher']) ? $companyInfo['lbl_teacher'] : 'Teacher';
$teachers=($companyInfo['lbl_teacher']) ? $companyInfo['lbl_teacher'].'s' : 'Teachers';
$student=($companyInfo['lbl_student']) ? $companyInfo['lbl_student'] : 'Learner';
$students=($companyInfo['lbl_student']) ? $companyInfo['lbl_student'].'s' : 'Learners';
$test=($companyInfo['lbl_test']) ? $companyInfo['lbl_test'] : 'Level';
$tests=($companyInfo['lbl_test']) ? $companyInfo['lbl_test'].'s' : 'Level';
$sectionConfig=($companyInfo['section_type'])? $companyInfo['section_type']:'';
$section=($companyInfo['lbl_section']) ? $companyInfo['lbl_section'] : 'Section';
$is_teacher=($companyInfo['is_teacher'])? $companyInfo['is_teacher']:''; 
$is_color=$companyInfo['is_color'];
$topBanner =$companyInfo['top_banner'];
$district =  'District';
$districts =  'Districts'; 
$tehsil =  'Tehsil';
$tehsils =  'Tehsils';

$start_module =  'START MODULE';  

$arrTB= json_decode($topBanner);
//echo "<pre>";print_r($arrTB);exit;
$tbColor=(!empty($arrTB))? $arrTB->bg:'#25313e';
$tbFontColor=(!empty($arrTB))? $arrTB->fc:'#ffffff';
$leftBanner =$companyInfo['left_banner'];
$arrLB= json_decode($leftBanner);
$lbColor=(!empty($arrLB))? $arrLB->bg:'#41586e';
$lbFontColor=(!empty($arrLB))? $arrLB->fc:'#adbece';


		