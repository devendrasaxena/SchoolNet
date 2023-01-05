<?php 

class serviceController{
    
    private $enc_key = WEB_SERVICE_ENCRYPTION_KEY;
    private $url = WEB_SERVICE_URL;

  /*  public function mb_chr($char) {
        return mb_convert_encoding('&#'.intval($char).';', 'UTF-8', 'HTML-ENTITIES');
    }

    public function mb_ord($char) {
            $result = unpack('N', mb_convert_encoding($char, 'UCS-4BE', 'UTF-8'));

            if (is_array($result) === true) {
                    return $result[1];
            }
            return ord($char);
    }*/
    
    public function rc4($key, $str) {
        
        if (extension_loaded('mbstring') === true) {
            mb_language('Neutral');
            mb_internal_encoding('UTF-8');
            mb_detect_order(array('UTF-8', 'ISO-8859-15', 'ISO-8859-1', 'ASCII'));
        }

        $s = array();
        for ($i = 0; $i < 256; $i++) {
            $s[$i] = $i;
        }
        $j = 0;
        for ($i = 0; $i < 256; $i++) {
            $j = ($j + $s[$i] + $this->mb_ord(mb_substr($key, $i % mb_strlen($key), 1))) % 256;
            $x = $s[$i];
            $s[$i] = $s[$j];
            $s[$j] = $x;
        }
        $i = 0;
        $j = 0;
        $res = '';
        for ($y = 0; $y < mb_strlen($str); $y++) {
        $i = ($i + 1) % 256;
            $j = ($j + $s[$i]) % 256;
            $x = $s[$i];
            $s[$i] = $s[$j];
            $s[$j] = $x;

            $res .= $this->mb_chr($this->mb_ord(mb_substr($str, $y, 1)) ^ $s[($s[$i] + $s[$j]) % 256]);
        }
        return $res;
        
    }
    
    function getCurl($url, $dataArray) {
        $fields_string = [];
        foreach($dataArray as $key=>$value){
            $fields_string[]=$key.'='.urlencode($value);
        }
        $fields_string = implode('&',$fields_string);
        //$urlStringData = $url.'?'.implode('&',$fields_string);
  /*      $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,120); # timeout after 10 seconds, you can increase it
        curl_setopt($ch, CURLOPT_URL, $url ); #set the url and get string together
        curl_setopt($ch,CURLOPT_POST, count($dataArray));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $return = curl_exec($ch);
        curl_close($ch);*/
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,120); # timeout after 10 seconds, you can increase it
        curl_setopt($ch, CURLOPT_URL, $url ); #set the url and get string together
        curl_setopt($ch,CURLOPT_POST, count($dataArray));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $return = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
		//echo "-->".$url;
		//echo "<br>";
		//echo "-->".$return;
		//echo "<br>";
		//echo $httpcode;exit;
        return $return;
    }
    
    public function processRequest($token, $decree, $params, $extra = array() ){
        
        $decree = trim($decree);
       
        $obj = new stdClass();
        $obj->token = $token;
        $obj->decree = $decree;
		$obj->client = CLIENT_NAME;// $client name;
		$obj->class_name = CLIENT_NAME;// $client name;
		$obj->platform = WEB_SERVICE_PLATFORM;
		$obj->deviceId = WEB_SERVICE_DEVICE_ID;
		$obj->appVersion = WEB_SERVICE_APP_VERSION;
        $obj->param = $params;
		
       // echo "<pre>";print_r($obj);exit;	
        foreach( $extra as $key => $val){
             $obj->$key = $val;
        }
        
//        if( isset($extra['unique_code'] ) ){
//            $obj->unique_code = $extra['unique_code'];
//        }
//        
//        if( isset($extra['platform'] ) ){
//            $obj->platform = $extra['platform'];
//        }
//        
        
        $str = json_encode($obj);
        
        //$estr = $this->rc4($this->enc_key, $str);
        $estr = openssl_encrypt($str, 'AES-256-CBC', $this->enc_key);
    
        $response_enc = $this->getCurl($this->url, array("obj"=>$estr) );
        
        //$response = $this->rc4($this->enc_key, $response_enc);
        $response = openssl_decrypt($response_enc, 'AES-256-CBC', $this->enc_key);
        //print_r($response); 
        $obj = json_decode($response);
        return $obj;
        
    }
    
}
