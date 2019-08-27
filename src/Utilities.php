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

  public function just_number($string){
    return preg_replace('~\D~', '', $string);
  }
	
  public function just_letter($string){
    return preg_replace('/[^a-zA-Z]/', '', $string);
  }
	
  public function remove_string($string, $int = 1, $start = 0){
    if(is_string($string) && $string != '')return trim(substr($string, $start, strlen($string)-$int));
    else return $string;
  }
  
  //REMOVE SPECIAL CHARACTER STRING
  public function remove_special_character($string, $n_char = TRUE){
      $character = [
       	'Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 
	'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 
       	'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 
	'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'o', 
       	'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'b', 'ß' => 'B',
	'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
       	'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'I', 
	'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ò' => 'o', 'ó' => 'o',
       	'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 
	'Ú' => 'U', 'þ' => 'b', 'ÿ' => 'y'
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
	
  public function count_array($key, $array){  
    $data = $this->key($key, $array);
    if(!is_array($data))$data = [];
    return count($data);
  }
	
  public function sum_array($key, $array){  
    $data = $this->key($key, $array);
    if(!is_array($data))$data = [];
    return array_sum($data);
  }
	
  public function file_check($file){
    if(FALSE !== stream_resolve_include_path($file))return TRUE;
    else return FALSE;
  }
   
  function is_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? TRUE : FALSE;
  }

   //FUNCTION TO GET USER AGENT IP
   function get_user_agent(){
     return $this->key('HTTP_USER_AGENT', $_SERVER);
   }

   function get_user_language(){
   	return $this->key('HTTP_ACCEPT_LANGUAGE', $_SERVER);
   }

//FUNCTION TO GET IP USER
function get_user_ip(){
	foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
        if (array_key_exists($key, $_SERVER) === TRUE){
            foreach (explode(',', $_SERVER[$key]) as $ip){
                $ip = trim($ip); 
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                    return $ip;
                }
            }
        }
    }
}
	
  public function parse($array, $key_select, $val_select, $name_table, $option_select = '', $return_option = TRUE, $empty_option = TRUE, $encrypt = FALSE){
    $return = [];
    if(is_array($array) && !empty($array)){
      foreach($array as $norow => $arr){
        if(is_array($arr) && !empty($arr)){
          
          foreach($arr as $field => $val){
            $name_key = "{$name_table}__{$key_select}";
            
            if(isset($arr[$name_key])){
              $arr_named_key = $arr[$name_key];
              
              if(is_array($val_select)){
                foreach($val_select as $k_val => $v_val){
	 	  //if(array_key_exists($v_val, $arr)){
		    $name_val = "{$name_table}__{$v_val}";
		    $n_val = $this->key($name_val, $arr);
		    $return[$arr_named_key][$v_val] = $n_val;
		  //}
                }
              }else{
                if($val_select == ''){
                  foreach($arr as $ka => $va){
                    $return[$arr_named_key][str_replace("{$name_table}__", '', $ka)] = $va;
                  }
                }else{
                  $name_val = "{$name_table}__{$val_select}";
                  $n_key = $this->key($name_key, $arr);
                  $n_val = $this->key($name_val, $arr);
                  $return[$n_key] = $n_val;
                }
              }
            }
          }
        }
      }
    }
    $option = [];
    if($return_option){
      if(!empty($return)){
        if($empty_option)$option[] = ['option' => '', 'value' => ''];
        foreach($return as $val => $opt){
	  if($encrypt)$val = $this->encrypt($val);
          else $val = $val;
          if($option_select != '' && $option_select == $val)$option[] = ['value' => $val, 'option' => $opt, 'selected' => 'selected'];
          else $option[] = ['option' => $opt, 'value' => $val];
        }
      }
    }else $option = $return;

    return $option;
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
