<?php
 include '../header/userHeader.php'; 

require '../poll_survey/controllers/Controller.php';

$page = new SurveyFormController;
$page->display();


 include '../footer/userFooter.php'; 