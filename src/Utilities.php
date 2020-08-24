<?php namespace blakepro\Template;

class Utilities{

  public function __construct($attr = []){
    $this->encryption_key = $this->key('encryption_key', $attr);
  }

  public function encrypt($string){
    return openssl_encrypt($string, 'AES-128-ECB', $this->encryption_key);
  }

  public function decrypt($string){
    return openssl_decrypt($string, 'AES-128-ECB', $this->encryption_key);
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

  public function just_number($string){
    if(is_string($string) && $string != '')return preg_replace('~\D~', '', $string);
  }
  //ALIAS JUST NUMBER
  public function number($string){
    return $this->just_number($string);
  }

  public function just_letter($string){
    if(is_string($string) && $string != '')return preg_replace('/[^a-zA-Z]/', '', $string);
  }
  //ALIAS JUST LETTER
  public function letter($string){
    return $this->just_letter($string);
  }

  public function just_word($string, $special = TRUE){
    if(is_string($string) && $string != ''){
      if($special)$string = trim(preg_replace('/[^0-9a-zA-ZÁÉÍÓÚáéíóúÑñ@\/,;.\s]/', '', utf8_encode($string)));
      else $string = trim(preg_replace('/[^0-9a-zA-ZÁÉÍÓÚáéíóúÑñ@\s]/', '', utf8_encode($string)));
      return $string;
    }
  }
  //ALIAS JUST WORD
  public function word($string, $special = TRUE){
    return $this->just_word($string, $special);
  }

  public function remove_string($string, $int = 1, $start = 0){
    if(is_string($string) && $string != '' && is_numeric($int) && is_numeric($start))return trim(substr($string, $start, strlen($string)-$int));
    else return $string;
  }

  //FUNCTION TO CREATE CLEAN URL
  public function slug($string, $separator = '-',  $special_cases = ['&' => 'and', "'" => '']){
    if(is_string($string) && $string != ''){
      $accents_regex = '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i';
      $string = mb_strtolower(trim($string), 'UTF-8');
      $string = str_replace(array_keys($special_cases), array_values($special_cases), $string);
      $string = preg_replace($accents_regex, '$1', htmlentities($string, ENT_QUOTES, 'UTF-8'));
      $string = preg_replace("/[^a-z0-9]/u", $separator, $string);
      $string = preg_replace("/[{$separator}]+/u", $separator, $string);
      return $string;
    }
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

  public function ttrim($value){
    if(is_string($value))return trim($value);
    return $value;
  }

  public function key($key, $array, $default = ''){
    return is_array($array) ? array_key_exists($key, $array) ? $this->ttrim($array[$key]) : $this->ttrim($default) : null;
  }

  function key_array($keys, $array){
    if($this->is_content($keys) && $this->is_content($array)){
      $total_keys = count($keys);
      foreach($keys as $no => $key){
        if(array_key_exists($key, $array)){
          unset($keys[$no]);
          $new = $array[$key];
          if($total_keys == 1)return $new;
          else return $this->key_array($keys, $new);
        }
      }
    }
    return;
  }

  public function session($key, $decrypt = FALSE){
    $data = $this->key($key, $_SESSION);
    if($decrypt)$data = $this->decrypt($data);
    return $data;
  }

  public function post($key, $decrypt = FALSE){
    $data = $this->key($key, $_POST);
    if($decrypt)$data = $this->decrypt($data);
    return $data;
  }

  public function get($key, $decrypt = FALSE){
    $data = $this->key($key, $_GET);
    if($decrypt)$data = $this->decrypt($data);
    return $data;
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

  public function in_string($search, $string){
    if($this->is_content($string)){
      $in_string = FALSE;
      foreach($string as $n => $word){
        if($this->in_string($word, $search)){
          $in_string = TRUE;
          break;
        }
      }
      return $in_string;
    }else{
      $bool = @strpos($string, $search);
      if($bool === FALSE)return FALSE;
      else return TRUE;
    }
  }

  //CHEK HEADERS 200
  public function is_remote($url){
    if($this->is_url($url)){
      $headers = get_headers($url);
      if($this->in_string('OK', $this->key(0, $headers)))return TRUE;
    }
    return FALSE;
  }

  //ALIAS FILE CHECK
  public function is_file($file){
    return $this->file_check($file);
  }

  //CHECK STRUCTURE EMAIL
  public function is_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? TRUE : FALSE;
  }

  //CHECK STRUCTURE URL
  public function is_url($url) {
    return filter_var($url, FILTER_VALIDATE_URL) ? TRUE : FALSE;
  }

  //CHECK CONTENT ARRAY
  public function is_content($array){
    if(is_array($array) && !empty($array))return TRUE;
    else return FALSE;
  }

  public function file_check($file){
    if(FALSE !== stream_resolve_include_path($file))return TRUE;
    else return FALSE;
  }

  //FUNCTION TO GET USER AGENT IP
  public function get_user_agent(){
    return $this->key('HTTP_USER_AGENT', $_SERVER);
  }

  public function get_user_language(){
     return $this->key('HTTP_ACCEPT_LANGUAGE', $_SERVER);
  }

  //FUNCTION TO GET IP USER
  function get_user_ip(){
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
      if(array_key_exists($key, $_SERVER) === TRUE){
    	  foreach(explode(',', $_SERVER[$key]) as $ip){
          $ip = trim($ip);
    	    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false)return $ip;
    	  }
      }
    }
  }

  public function parse($array, $key_select, $val_select, $name_table, $option_select = '', $return_option = TRUE, $empty_option = TRUE, $encrypt = FALSE, $clean = []){
    $return = [];
    if($this->is_content($array) && is_string($key_select)){
      foreach($array as $norow => $arr){
        if(is_array($arr) && !empty($arr)){

          foreach($arr as $field => $val){
            if($name_table == '')$name_key = $key_select;
            else $name_key = "{$name_table}__{$key_select}";

            if(array_key_exists($name_key, $arr)){
              $arr_named_key = $arr[$name_key];

              if($this->is_content($val_select)){
                foreach($val_select as $k_val => $v_val){
                  if($name_table == '')$name_val = $v_val;
                  else $name_val = "{$name_table}__{$v_val}";

                  $n_val = $this->key($name_val, $arr);
                  $return[$arr_named_key][$v_val] = $n_val;
                }
              }else{
                if($val_select == '' || empty($val_select)){
                  foreach($arr as $ka => $va){
                    if($name_table == '')$return[$arr_named_key][$ka] = $va;
                    else $return[$arr_named_key][str_replace("{$name_table}__", '', $ka)] = $va;
                  }
                }else{
                  if($name_table == '')$name_val = $val_select;
                  else $name_val = "{$name_table}__{$val_select}";

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
    if($return_option){
      $option = [];
      if($this->is_content($return)){
        if($empty_option)$option[] = ['option' => '', 'value' => ''];
        foreach($return as $val => $opt){
          if($encrypt)$val = $this->encrypt($val); else $val = $val;
          if($option_select != '' && $option_select == $val)$option[] = ['option' => "{$opt}", 'value' => "{$val}", 'selected' => 'selected'];
          else $option[] = ['option' => "{$opt}", 'value' => "{$val}"];
        }
      }
      return $option;
    }else{
      if($this->is_content($clean)){
        $array = [];
        if($this->is_content($return)){
          foreach($return as $kid => $arr){
            foreach($arr as $key => $val){
              if($this->in_string($key, $clean))$key = str_replace('__', '', strstr($key, '__'));
              if($key != '')$array[$kid][$key] = $val;
            }
          }
        }
        $return = $array;
      }
    }
    return $return;
  }

  //CURL
  public function curl($args = []){
    $response = '';
    $url = $this->key('url', $args);

    if($url != ''){
      $data = $this->key('data', $args);
      $credentials = $this->key('credentials', $args);
      $timeout = $this->key('timeout', $args, 60);
      $port = $this->key('port', $args);
      $agent = $this->key('agent', $args);

    	$ch = curl_init($url);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
   	  curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

      //PORT
      if(is_numeric($port))curl_setopt($ch, CURLOPT_PORT, $port);

      //DATA
      if($this->is_content($data)){
    		$build_query = http_build_query($data, '', '&');
    		curl_setopt($ch, CURLOPT_POST, TRUE);
      	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
     		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
     		curl_setopt($ch, CURLOPT_POSTFIELDS, $build_query);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
    		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-length:'.strlen($build_query)]);
      }

      //AGENT
      if($agent != ''){
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
      }

     	//TIMEOUT
    	if(is_numeric($timeout)){
    	  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    	}

      //BASIC AUTH
      if($this->is_content($credentials)){
        if(array_key_exists('user', $credentials) && array_key_exists('pass', $credentials)){
    	    curl_setopt($ch, CURLOPT_USERPWD, "{$credentials['user']}:{$credentials['pass']}");
        }
      }

      //RESPONSE
      $response = curl_exec($ch);
      if(curl_errno($ch)){
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $response = "Error ({$code}): ".curl_error($ch);
      }

      curl_close($ch);
    }
    return $response;
  }
}
