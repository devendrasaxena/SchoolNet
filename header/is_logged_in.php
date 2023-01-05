<?php 

require_once dirname(__FILE__) .'/lib.php'; 
$objUser = new User();
$objUser->setUserSession();
$objUser->refreshUserDetail();
$objUser->setUserSession();
    
if( ! empty($_SESSION['user']) ){
    
    header('Location:dashboard.php');
    die;
    
}
