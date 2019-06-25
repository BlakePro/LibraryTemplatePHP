<?php

namespace blakepro\Template;

class Html{

  public function __construct($attr = []){
    //$this->html = key('html', $attr);
  }

  //FUNCTION TO CREATE ATTRIBUTES FROM TAG
  public function attr($attr){
    $html = '';
    if(is_array($attr) && !empty($attr)){
      foreach($attr as $k => $v){
        if(is_string($k) && $k != ''){
          if($v == '')$html .= " {$k}";
          else $html .= " {$k}='{$v}'";
        }
      }
    }
    return $html;
  }

  //FUNCTION TO CREATE HTML TAG
  public function tag($name, $html, $attr = [], $close_tag = TRUE){
    if(is_string($name) && is_string($html)){
      $html_attr = $this->attr($attr);
      if($close_tag)return "<{$name}{$html_attr}>{$html}</{$name}>";
      else return "<{$name}{$html_attr}/>";
    }
  }

  //FUNCTION TO GET ROW / GRID BOOSTRAP
  public function row($array){
    $data = '';
    $delete = ['html', 'col', 'sm', 'md', 'lg', 'xl'];
  	if(!empty($array)){
  		foreach($array as $k => $row){
        $style = '';
        $html = $this->key('html', $row);
        $col = $this->key('col', $row, 12);
        $col_sm = $this->key('sm', $row, $col);
        $col_md = $this->key('md', $row, $col);
        $col_lg = $this->key('lg', $row, $col);
        $col_xl = $this->key('xl', $row, $col);
        foreach($row as $key => $val)if(in_array($key, $delete))unset($row[$key]);
        $row['class'] = "col-sm-{$col_sm} col-md-{$col_md} col-lg-{$col_lg} col-xl-{$col_xl} col-{$col}";
        $data .= $this->div($html, $row);
  		}
  	}
    return $this->div($data, ['class' => 'row']);
  }

  //FUNCTION REDIRECT HTML
  public function meta($url, $time = 0){
  	return "<script>setTimeout(function(){window.location = '{$url}';}, {$time}*1000);</script>";
  }

  //FUNCTION TO GET ICON (FONTAWESOME PREFIX)
  public function icon($name, $prefix = 'fas fa-'){
    return "<i class='{$prefix}{$name}'></i>";
  }

  //FUNCTION TO GET HTML BUTTON
  public function button($title, $attr = []){
    $icon = $this->key('icon', $attr);
    if($icon != ''){
      $icon = $this->icon($icon);
      unset($attr['icon']);
    }
    return $this->tag('button', "$title {$icon}", $attr);
  }

  //FUNCTION TO GET INPUT
  public function input($attr = [], $label = ''){
    $html_attr = $this->attr($attr);
    $input = "<input{$html_attr}/>";

    if($label == '')return $input;
    else{
      return "<div class='form-group form-group-default'>
                <label>{$label}</label>
                {$input}
              </div>";
    }
  }

  //FUNCTION TO GET TEXTAREA
  public function textarea($html, $attr = []){
    return $this->tag('textarea', $html, $attr);
  }

  //FUNCTION TO GET PARAGRAPH
  public function p($html, $attr = []){
    return $this->tag('p', $html, $attr);
  }

  //FUNCTION TO GET FORM
  public function form($html, $attr = []){
    return $this->tag('form', $html, $attr);
  }

  //FUNCTION TO GET FORM
  public function div($html, $attr = []){
    return $this->tag('div', $html, $attr);
  }

  //FUNCTION TO GET FORM
  public function img($attr = []){
    return $this->tag('img', '', $attr, FALSE);
  }

  //FUNCTION TO GET SCRIPT
  public function script($attr = []){
    return $this->tag('script', '', $attr);
  }

  //FUNCTION TO GET BR
  public function br($no = ''){
    $html = '';
    if(is_numeric($no) && $no > 0)for($i = 0; $i <= $no; ++$i)$html .= '<br>';
    else $html .= '<br>';
    return $html;
  }

  //FUNCTION TO GET HR
  public function hr($no = ''){
    $html = '';
    if(is_numeric($no) && $no > 0)for($i = 0; $i <= $no; ++$i)$html .= '<hr>';
    else $html .= '<hr>';
    return $html;
  }

  //FUNCTION TO GET HEADINGS
  public function h($number, $html, $attr = []){
    return $this->tag("h{$number}", $html, $attr);
  }

  //FUNCTION TO GET BOLD
  public function b($html, $attr = []){
    return $this->tag('b', $html, $attr);
  }

  //FUNCTION TO GET SELECTL
  public function select($array, $attr = []){
    $html = '';
    if(!empty($array) && is_array($array)){
      foreach($array as $no => $arg_option){
        $option = $this->key('option', $arg_option);
        if($option != '')unset($arg_option['option']);
        $html .= $this->tag('option', $option, $arg_option);
      }
    }
    
    $attr_label = $this->key('attr_label', $attr);
    if(array_key_exists('attr_label', $attr))unset($attr['attr_label']);
    
    $select =  $this->tag('select', $html, $attr);
    
    if(is_array($attr_label)){
      $label = $this->key('label', $attr_label);
      $label = $this->label($label);
      if(array_key_exists('label', $attr_label))unset($attr_label['label']);
      
      $html_attr = $this->attr($attr_label);
      return $html->div($label.$select, $html_attr);
    }else{
      return $select;
    }  
  }
  
  //FUNCTION TO GET LABEL
  public function label($html, $attr = []){
    return $this->tag('label', $html, $attr);
  }

  //FUNCTION TO GET HTML TABLE
  public function table($table){
    $html = '';
    if(is_array($table)){
      //FIX ADD BLANK SPACE IF NOT EXISTS
      $max_array = array();
      foreach($table as $ktype => $arrt){
        if(!empty($arrt) && is_array($arrt)){
          foreach($arrt as $kt => $vt){
            $tot = count($vt);
            if(!is_numeric($kt))$tot = $tot-1;
            $max_array[$tot] = $tot;
          }
        }
      }

      krsort($max_array); $max = key($max_array);
      foreach($table as $ktype => $arrt){
        if(!empty($arrt) && is_array($arrt)){
          foreach($arrt as $key => $vt){
            $t = count($vt);
            if(!is_numeric($key))$t = $t-1;
            if($t < $max){
              $size = $max - $t;
              for($i = $t; $i < $max; ++$i){
                $table[$ktype][$key][] = '';
              }
            }
          }
        }
      }
      //FIX ADD BLANK SPACE IF NOT EXISTS

      $html_table = $head = '';
      foreach($table as $type => $arr){
        if($type == 'th')$head = '<thead role="row" class="even">';
        if($type == 'td')$head = '<tbody>';
        if(!is_array($head))$html_table .= $head;
          if(!empty($arr) && is_array($arr)){
            $no_line = 0;
            foreach($arr as $row => $arr_row){
              if(!empty($arr_row)){

                if(is_array($arr_row) && !empty($arr_row)){
                  if($no_line % 2 == 0)$class_row = "role='row' class='even'";
                  else $class_row = "role='row' class='odd'";

                  $html_table .= "<tr $class_row>";
                  foreach($arr_row as $no => $data_row){
                    $d_value = $this->key('value', $data_row);
                    $style_td = $this->attr($this->key('attr', $data_row));
                    $html_table .= "<{$type}{$style_td}>$d_value</{$type}>";
                  }
                  $html_table .= '</tr>';
                }
              }
              ++$no_line;
            }
          }
        if($type = 'th')$head = '</thead>';
        if($type = 'td')$head = '</tbody>';
        $html_table .= $head;
      }
      $table = $html_table;
    }
    return "<table>$table</table>";
  }

  //FUNCTION TO GET HTML ALERT
  public function alert($title, $text, $type){
    $title = $this->p($this->b($title));
    $text = $this->p($text);
    $message = $this->div("{$title}{$text}", ['class' => 'pgn-message']);
    $alert = $this->div($message, ['class' => "alert alert-{$type}"]);
    return $this->div($alert, ['class' => 'push-on-sidebar-open']);
  }

  //FUNCTION TO GET HTML AS CURRENCY
  public function currency($number, $currency = '$', $decimal = 2){
  	if(!is_numeric($number))$number = 0;
    if(!is_numeric($decimal))$decimal = 2;
  	return $currency.number_format($number, $decimal);
  }

  //FUNCTION TO PRINT OR SHOW ARRAY AS CLEANEST POSSIBLE
  public function pre($array){
    if(is_array($array))$html = print_r($array, TRUE);
    else $html = $array;
    return $this->tag('pre', $html);
  }

  //FUNCTION TO COMPRESS HTML
  public function html($html, $echo = TRUE){
    if(is_string($html)){
    	$html = str_replace('> <','><',preg_replace('/\s+/', ' ', $html));
    	$html = str_replace(' >', '>', $html);
    	$html = str_replace(' />', '/>', $html);
      $html = str_replace(' </', '</', $html);
    	if($echo)echo $html;
      else return $html;
    }
  }

  public function template($url_json, $attr = []){

    $file_json = $this->curl(['url' => $url_json]);

    //JSON FILE CONFIG
    $configuration = json_decode($file_json, TRUE);
    $lang = $this->key('lang', $configuration);
    $title = $this->key('title', $configuration);
    $logo = $this->key('logo', $configuration);
    $configuration_css = $this->key('css', $configuration);
    $configuration_js = $this->key('js', $configuration);

    //REMOVE CONFIGURATION
    $array_no_params = ['lang', 'title', 'logo', 'css', 'js'];
    if(!empty($array_no_params)){
      foreach($array_no_params as $k => $param){
        if(isset($configuration[$param]))unset($configuration[$param]);
      }
    }

    //INIT HTML TEMPLATE
    $template = "<!DOCTYPE html>
            <html lang='{$lang}'>
              <head>";

    //TITLE TEMPLATE
    $template .= $this->tag('title', $title);

    //META CONFIG
    if(!empty($configuration)){
      foreach($configuration as $tag => $array_tag){
        foreach($array_tag as $no => $attr_config){
          $template .= $this->tag($tag, '', $attr_config, FALSE);
        }
      }
    }

    //CSS FILES
    if(!empty($configuration_css)){
      foreach($configuration_css as $no => $url){
        $template .= $this->tag('link', '', ['href' => $url, 'rel' => 'stylesheet', 'type' => 'text/css'], FALSE);
      }
    }
    //BODY ARGS
    $body = $this->key('html', $attr);
    if(array_key_exists('html', $attr))unset($attr['html']);

    $body_attr = $this->attr($attr);
    $template .= "</head><body{$body_attr}>";

    //BODY HTML REPLACE LOGO AND TEMPLATE NAME TAG
    $template .= str_replace(['{template_logo}', '{template_name}'], [$logo, $title], $body);

    //JS FILES
    if(!empty($configuration_js)){
      foreach($configuration_js as $no => $url){
        $template .= $this->script(['src' => $url]);
      }
    }

    //HTML
    $template .= '</body></html>';

    //RETURN HTML
    return $this->html($template);
  }

  //HELPER FUNCTIONS
  public function key($key, $array, $default = ''){
    return is_array($array) && is_string($key) ? array_key_exists($key, $array) ? $array[$key] : $default : '';
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
