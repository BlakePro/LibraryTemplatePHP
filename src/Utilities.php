<?php

namespace blakepro\Template;

class Utilities{

   public function __construct($attr = []){
    $this->encryption_key = $this->key('encryption_key', $attr);
  }

   public function encrypt($string){
    return openssl_encrypt($string, "AES-128-ECB", $this->encryption_key);
  }

  public function decrypt($string){
    return openssl_decrypt($string, "AES-128-ECB", $this->encryption_key);
  }
   
  //FUNCTION TO ADD CURRENCY
  public function currency($number, $currency = '$', $decimal = 2){
  	 if(!is_numeric($number))$number = 0;
    if(!is_numeric($decimal))$decimal = 2;
    if($number < 0){
      $number = $number * (-1);
      return '-'.$currency.number_format($number, $decimal);
    }else return $currency.number_format($number, $decimal);
  }

  public function in_string($search, $string){
    $bool = strpos($string, $search);
    if($bool === FALSE)return FALSE;
    else return TRUE;
  }

  public function remove_string($string, $int = 1, $start = 0){
    if(is_string($string) && $string != '')return trim(substr($string, $start, strlen($string)-$int));
    else return $string;
  }
  
  //REMOVE SPECIAL CHARACTER STRING
  public function remove_special_character($string, $n_char = TRUE){
      $character = [
       'Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 
       'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 
       'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'B','à' => 'A', 'á' => 'A', 'â' => 'A', 'ã' => 'A', 'ä' => 'A', 'å' => 'A',
       'æ' => 'A', 'ç' => 'C', 'è' => 'E', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'I', 'í' => 'I', 'î' => 'I', 'ï' => 'I', 'ð' => 'O', 'ò' => 'O', 'ó' => 'O',
       'ô' => 'O', 'õ' => 'O', 'ö' => 'O', 'ø' => 'O', 'ù' => 'U', 'ú' => 'U', 'û' => 'U', 'u' => 'Y', 'þ' => 'B', 'ÿ' => 'Y'
     ];
     if($n_char){
       $character['Ñ'] = 'N';
       $character['ñ'] = 'n';
     }
     $string = strtr($string, $character);
     return $string;
  }

  public function key($key, $array, $default = ''){
    return is_array($array) ? array_key_exists($key, $array) ? $array[$key] : $default : null;
  }

  public function post($key){
    return $this->key($key, $_POST);
  }

  public function get($key){
    return $this->key($key, $_GET);
  }

  public function file_check($file){
    if(FALSE !== stream_resolve_include_path($file))return TRUE;
    else return FALSE;
  }
   
  function is_email($email) {
	   return filter_var($email, FILTER_VALIDATE_EMAIL) ? TRUE : FALSE;
  }

  public function curl($args = []){
    $response = '';
    $url = $this->key('url', $args);
    $data = $this->key('data', $args);

    //BASIC AUTH
    $credentials = $this->key('credentials', $args);
    $timeout = $this->key('timeout', $args, 60);
  	 if($url != ''){
  		$ch = curl_init($url);
  		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  		curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
  		curl_setopt($ch, CURLOPT_POST, TRUE);
  		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

      //DATA
  		if(!empty($data)){
  			curl_setopt($ch,CURLOPT_FOLLOWLOCATION, TRUE);
  			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
  			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
      }

      //TIMEOUT
  		if($timeout != ''){
  			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
  		}

      //BASIC AUTH
  		if(isset($credentials['user']) && isset($credentials['pass'])){
  			curl_setopt($ch, CURLOPT_USERPWD, "{$credentials['user']}:{$credentials['pass']}");
  		}

      //RESPONSE
     $response = curl_exec($ch);
  	  if(curl_errno($ch)){
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $response = "CURL ({$code}):".curl_error($ch);
      }
  	  curl_close($ch);
  	}
  	return $response;
  }
}
