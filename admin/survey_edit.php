<?php 
include '../header/adminHeader.php'; 
 
require '../poll_survey/controllers/Controller.php';

$page = new SurveyEditController;
$page->display();

 include '../footer/adminFooter.php'; ?>