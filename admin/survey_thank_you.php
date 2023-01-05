<?php
 include '../header/userHeader.php'; 

require '../poll_survey/controllers/Controller.php';

$page = new SurveyThankYouController;
$page->display();


 include '../footer/userFooter.php'; 
