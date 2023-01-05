<?php
//error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
// Turn off all error reporting
//error_reporting(1);
//ini_set('display_errors',1);
include_once('header/lib.php');
$adminObj = new languageController();

$data =  $adminObj->getlanguage();

$json = json_decode($data, true);
file_put_contents('language/language.json', $data); 

echo '<pre>' ; print_r($json) ; die;

?>