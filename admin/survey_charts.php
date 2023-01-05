<?php 
include '../header/adminHeader.php'; 

require '../poll_survey/controllers/Controller.php';

$page = new SurveyChartsController;
$page->display();

 include '../footer/adminFooter.php'; ?> 
