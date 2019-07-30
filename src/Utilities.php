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

  public function key($key, $array){
    if(!is_array($key) && is_array($array) && array_key_exists($key, $array))return $array[$key];
    else return null;
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
}
