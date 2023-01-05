<?php
//REPORTING ERROR ON///OFF
//error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
// Turn off all error reporting
//error_reporting(1);
//ini_set('display_errors',1);

function utf8ize($d) {
        if (is_array($d)) {
            foreach ($d as $k => $v) {
                $d[$k] = utf8ize($v);
            }
        } else if (is_string ($d)) {
            return utf8_encode($d);
        }
        return $d;
    }
class languageController {

  public $dbConn;
  public function __construct() {
    $this->dbConn = DBConnection::createConn();

  }



  
  public function getlanguage(){
    $langadata = array();
    $sql = "SELECT * FROM language_master ";
    $stmt = $this->dbConn->prepare($sql);
    $stmt->execute();
    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);


    foreach ($RESULT as $key => $value) {
  // $value['language_id'];
       $sql2="SELECT product_word_master.word_txt,product_word_translation.translated_word FROM product_word_translation INNER JOIN product_word_master ON product_word_master.word_id=product_word_translation.word_id where  product_word_translation.language_id = :language_id";


    $stmt = $this->dbConn->prepare($sql2);
	  $stmt->bindValue(':language_id', $value['language_id'], PDO::PARAM_INT);
      $stmt->execute();
      $ladata = $stmt->fetchAll(PDO::FETCH_ASSOC);

      
      foreach ($ladata as  $valuadata) {
       $langadata[$value['language_code']][$valuadata['word_txt']]  = $valuadata['translated_word'];
     }
   }


   $data = json_encode(utf8ize($langadata));
    return $data;
 }
 public function getLanguageList(){
    $sql = "SELECT * FROM language_master order by language_id";
    $stmt = $this->dbConn->prepare($sql);
    $stmt->execute();
    $RESULT = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    return $RESULT;
	
 }
 
 public function test(){
  echo "string";

}
}
?>