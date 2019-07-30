<?php

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
  
  public function in_string($search, $string){
    $bool = strpos($string, $search);
    if($bool === FALSE)return FALSE;
    else return TRUE;
  }

  public function remove_str($string, $int = 1, $start = 0){
    if(is_string($string) && $string != '')return trim(substr($string, $start, strlen($string)-$int));
    else return $string;
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
