<?php
namespace blakepro\Template;
use PDO;

class Sql extends Utilities{

  public function __construct($attr = []){
    $this->database_host = $this->key('host', $attr);
    $this->database_name = $this->key('name', $attr);
    $this->database_user = $this->key('user', $attr);
    $this->database_password = $this->key('password', $attr);
    $this->encryption_key = $this->key('encryption_key', $attr);
  }

  public function criteria(){
    return ['=', ' !=', 'LIKE', 'LIKE %...%', 'NOT LIKE', 'REGEXP', 'NOT REGEXP', 'IN (...)', 'NOT IN (...)', 'IS NULL', 'IS NOT NULL' , 'BETWEEN', 'NOT BETWEEN'];
  }

  public function query($args){
    $type = $this->key('type', $args);
    if($type == '')$type = 'select';
    switch ($type){
      case 'select': return $this->select($args); break;
      case 'insert': return $this->insert($args); break;
      case 'update': return $this->update($args); break;
      case 'delete': return $this->delete($args); break;
      default: return [];
    }
  }

  public function fetch($array){
    return $this->key('fetch', $array);
  }

  public function message($array){
    return $this->key('message', $array);
  }

  public function get_query($array){
    return $this->key('sql', $array);
  }


  //---------------------  FUNCTIONS  ---------------------//
  public function db(){
    $connect = '';
    try{
      $connect .= "mysql:host={$this->database_host};";
      if($this->database_name != '')$connect .= "mysql:dbname={$this->database_name};";
      $connect .= 'charset=UTF8;';
      $db = new PDO($connect, $this->database_user, $this->database_password);
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
      return $db;
    }catch(PDOException $e) {
      echo $e;
      exit;
    }
  }

  public function where($string, $delimiter = ','){
    $array = $array_where = [];
    $where = $where_str = '';
    if($string != '' && $delimiter != ''){

      if(is_array($string)){
        $array = $string;
      }else{
        $string = str_replace("'",'',$string);
        $array = explode($delimiter, $string);
      }
      if(!empty($array)){

        foreach($array as $k => $v){
          //echo "$k => $v<br>";
          if(is_array($v)){
            $array_where[] = $k;
            $where .= '?,';
            $where_str .= "'$k',";
          }else{
            $array_where[] = $v;
            $where .= '?,';
            $where_str .= "'$v',";
          }
        }
        $where = $this->remove_string($where, 1);
        $where_str = $this->remove_string($where_str, 1);
      }
    }
    return ['array' => $array_where, 'where_str' => $where_str, 'where' => $where];
  }

  public function sql($sql, $params = [], $fetch = TRUE, $return_id = FALSE, $exec_simple = FALSE){
  	$data_fetch = [];
  	$message = '';
  	$state = FALSE;
  	$sql_send = $sql;
  	if($sql != ''){
    	try{
		$db = $this->db();
		if($exec_simple){
		  $db->exec($sql);
		}else{
		  $sql = $db->prepare($sql);
		  try{
		    @$sql->execute($params);
		    $message = $sql->errorInfo();
		    if($this->key(2, $message) == ''){
		      $state = TRUE;
		      if($return_id)$data_fetch['insert'] = $db->lastInsertId(); //@$db->lastInsertId();
		      elseif($fetch)$data_fetch = $sql->fetchALL(PDO::FETCH_ASSOC); //@$sql->fetchALL(PDO::FETCH_ASSOC);
		    }
		  }catch(PDOException $exception){
		    $message = $exception->getMessage();
		  }
		}
		$db = null;
		}catch(PDOException $exception) {
			$message = $exception->getMessage();
		}
	}
  	return ['state' => $state,  'message' => $message, 'fetch' => $data_fetch, 'sql' => $this->query_print($sql_send, $params)];
  }

  private function update($data){
    $return = [];
    $set = $this->key('set', $data);
    $array_criteria_where = $this->criteria();

    if(!empty($set) && is_array($set)){
      $where_criteria = $this->key('where', $data);

      $table = [];
      foreach($set as $name_table => $arr_set)$table[] = $name_table;

      $arr_sql_desc = $this->description($table);
      $arr_not_null = $this->key('not_null', $arr_sql_desc);

      foreach($set as $name_table => $arr_set){
        $str_empty = $set_table = $where_table = '';
        $params = [];

        /*-------- SET --------*/
        if(isset($arr_set) && !empty($arr_set) && is_array($arr_set)){
          foreach($arr_set as $field_name => $value){
            if(isset($arr_not_null[$name_table][$field_name]) && $value == ''){
  						$str_empty .= "<li>{$arr_not_null[$name_table][$field_name]}</li>";
  					}
            if(is_string($value)){
  						$set_table .= " $field_name = ?,";
  						$params[] = ($value); //utf8_encode
  					}
          }
        }

        /*-------- WHERE --------*/
        if(isset($where_criteria[$name_table]) && !empty($where_criteria[$name_table]) && is_array($where_criteria[$name_table])){

          foreach($where_criteria[$name_table] as $field_name => $array_criteria_data){

            $value_criteria = $this->key('value', $array_criteria_data);
            $option_sel_criteria = $this->key('option', $array_criteria_data);

            if(is_numeric($option_sel_criteria)){
              $option_criteria = $this->key($option_sel_criteria, $array_criteria_where);

              if($option_sel_criteria == 3){
                $option_criteria = $this->key(2, $array_criteria_where);
                if(is_string($value_criteria))$value_criteria = "%{$value_criteria}%";
              }

              if(in_array($option_sel_criteria, [9, 10])){
                $where_table .= " AND $field_name $option_criteria";

              }elseif(in_array($option_sel_criteria, [7, 8])){

                if($option_sel_criteria == 7)$option_criteria = 'IN';
                else $option_criteria = 'NOT IN';
              }

              if(is_array($value_criteria)){
                $array_pdo = $this->where($value_criteria);
                $where_table .= " AND $field_name $option_criteria ({$array_pdo['where']})";
                foreach($array_pdo['array'] as $kpdo => $vpdo)$params[] = $vpdo;

              }else{
                $where_table .= " AND $field_name $option_criteria ?";
                $params[] = $value_criteria;
              }
            }
          }
        }

        $set_table = trim($this->remove_string($set_table, 1));

  			$where_table = trim(substr(trim($where_table), 3));
  			if($where_table != '')$where_table = "WHERE {$where_table}";

        $sql = "UPDATE {$name_table} SET $set_table $where_table";
        $return[$name_table]['sql'] = $this->query_print($sql, $params);
        $return[$name_table]['state'] = FALSE;

        if($str_empty != ''){
  				$return[$name_table]['message'] = "Fill: <ul>{$str_empty}</ul>";
        }else{
          if(!empty($params) && $set_table != '' && $where_table != ''){
            $return[$name_table] = $this->sql($sql, $params, FALSE);
          }else{
  					$return[$name_table]['state'] = FALSE;
  					$return[$name_table]['message'] = 'Not empty where allowed';
  				}
        }
      }
    }
    return $return;
  }

  private function select($data){
  	$arr_sql_data = $arr_sql_desc = [];
    $db = $this->get_database_name($this->key('db', $data));
    $table = $this->key('table', $data);
  	$options = $this->key('options', $data);
    $array_criteria_where = $this->criteria();
    if(!empty($table) && is_array($table)){

  		$arr_sql_field_key = [];
      $debug = $this->key('debug', $data, FALSE);
      $where_criteria = $this->key('where', $data);
      $limit = $this->key('limit', $data);
      $on = $this->key('on', $data);
  		$group = $this->key('group', $data);
      $order = $this->key('order', $data);

      if(is_numeric($limit) && $limit > 0)$limit = "LIMIT $limit"; else $limit = '';
      $arr_sql_desc = $this->description($table, $db);

      $arr_sql_fields = $this->key('fields', $arr_sql_desc);
  		$arr_sql_key = $this->key('key', $arr_sql_desc);
      $arr_data_rows = $all_params = [];
      $sql_join = $rows = $keys = $val_join = $group_by = $order_by = '';

  		//KEYS
  		if(is_array($on) && !empty($on)){
  			foreach($on as $key_name => $arr_on){
					$str_key = '';
					foreach($arr_on as $notable => $key_table){
						if(in_array($key_table, $table) && $key_name != '' && $key_table != ''){
							$str_key .= "{$key_table}.{$key_name} = ";
						}
					}
					if($str_key != ''){
						$str_key = $this->remove_string($str_key, 2);
						$keys .= "{$str_key} AND ";
					}
  			}
        //KEY
        $keys = $this->remove_string($keys, 4);
        if(!$this->in_string('=', $keys))$keys = '';
        $keys = "ON ($keys)";
  		}

  		//GROUP
  		if(is_array($group) && !empty($group)){
  			foreach($group as $no_on => $arr_on){
  				foreach($arr_on as $key_name => $arrtable){
  					$str_key = '';
  					foreach($arrtable as $notable => $key_table){
  						if(in_array($key_table, $table) && $key_name != '' && $key_table != ''){
  							$str_key .= "{$key_table}.{$key_name} = ";
  						}
  					}
  					if($str_key != ''){
  						$str_key = $this->remove_string($str_key, 2);
  						$group_by .= "{$str_key} AND ";
  					}
  				}
  			}
  			$group_by = $this->remove_string($group_by, 5);
  			if($group_by != '')$group_by = "GROUP BY $group_by";
  		}

      //ORDER
      if(is_array($order) && !empty($order)){
				foreach($order as $key_table => $arrtable){
					$str_order = '';
					foreach($arrtable as $key_name => $order_type){
  					if(in_array($key_table, $table) && $key_name != '' && $key_table != '' && $order_type != ''){
  						$str_order .= "{$key_table}__{$key_name} {$order_type}, ";
  					}
					}
				}
  			$order_by = $this->remove_string($str_order, 2);
  			if($order_by != '')$order_by = "ORDER BY $order_by";
  		}

  		//ROWS
      foreach($table as $no_table => $name_table){
        $name_table_col = $name_table;
        $name_table = $this->get_table_name($name_table, $db);
        $where_table = '';
        $params = [];
  			$arr_rows = $this->key($name_table, $arr_sql_fields);
  			$val_join = mb_strtoupper(trim($this->key($no_table, $options)));
  			if(!in_array($val_join, array('LEFT', 'RIGHT', 'INNER')))$val_join = '';

        if(is_array($arr_rows) && !empty($arr_rows)){
          foreach($arr_rows as $nrtab => $field_name){

  					//ROWS
  					$rows .= "{$name_table}.{$field_name} AS {$name_table}__{$field_name}, ";

            //WHERE
            if(isset($where_criteria[$name_table][$field_name]) && is_array($where_criteria[$name_table][$field_name])){

              $array_criteria_data = $where_criteria[$name_table][$field_name];
              $value_criteria = $this->key('value', $array_criteria_data);
              $option_sel_criteria = $this->key('option', $array_criteria_data);

              if(is_numeric($option_sel_criteria)){

                if($option_sel_criteria == 3){
                  $option_criteria = $this->key(2, $array_criteria_where);
                  if(is_string($value_criteria))$value_criteria = "%{$value_criteria}%";

  							}elseif(in_array($option_sel_criteria, [9, 10])){
  								if($option_sel_criteria == 9)$where_table = "AND $field_name IS NULL";
                  else $where_table = "AND $field_name IS NOT NULL";

                }elseif(in_array($option_sel_criteria, [7, 8])){
                  if($option_sel_criteria == 7)$option_criteria = 'IN';
                  else $option_criteria = 'NOT IN';

                }else{

  								$option_criteria = $this->key($option_sel_criteria, $array_criteria_where);
  	              if(is_array($value_criteria)){
  	                $array_pdo = $this->where($value_criteria);
  	                $where_table .= " AND $field_name {$option_criteria} ({$array_pdo['where']})";
  	                foreach($array_pdo['array'] as $kpdo => $vpdo){
  	                  $params[] = $vpdo;
  	                  $all_params[] = $vpdo;
  	                }
  	              }else{
  	                $where_table .= " AND $field_name {$option_criteria} (?)";
  	                $params[] = $value_criteria;
  	                $all_params[] = $value_criteria;
  	              }
                }
              }
            }
          }
          if($where_table != '')$where_table = "WHERE 1 {$where_table}";
          $sql = trim("SELECT * FROM {$db}{$name_table_col} {$where_table}"); //$limit
          $sql_join .= "($sql) $name_table $val_join JOIN ";
        }
      }


      //DATA
      $sql_join = $this->remove_string($sql_join, 6);
      $rows = $this->remove_string($rows, 2);

      $sql_join = "SELECT $rows FROM ({$sql_join} {$keys}) $group_by $order_by $limit";
      $return = $this->sql($sql_join, $all_params);
  		$return['desc'] = $arr_sql_desc;
  		return $return;
    }
  }

  public function description($table = [], $db = ''){
    $arr_not_null = $arr_rows_table = $arr_rows = $arr_sql_desc_key = $arr_key_pairs = $arr_sql_field_key = $arr_key_pairs_table = [];
    if(!empty($table)){
      //echo $db, print_r($table, TRUE).'<hr>';
      foreach($table as $no_table => $name_table){
        $name_table_col = $name_table;

        $sql = "SHOW FULL COLUMNS FROM {$db}{$name_table_col}";
        $arr_sql_desc = $this->sql($sql);
        if(!empty($arr_sql_desc)){
          $name_table = $this->get_table_name($name_table, $db);
          $fetch = $this->key('fetch', $arr_sql_desc);
          foreach($fetch as $kdesc => $vdesc){
            $key_desc = $this->key('Key', $vdesc);
            $field_desc = $this->key('Field', $vdesc);
            $field_null = $this->key('Null', $vdesc);
            $field_comment = $this->key('Comment', $vdesc);

            if($field_null == 'NO')$arr_not_null[$name_table][$field_desc] = $field_comment;

            $arr_sql_field_key[$name_table][$field_desc] = $field_desc;
            if($key_desc == 'PRI')$arr_sql_desc_key[$field_desc] = $field_desc;

            $arr_rows["$name_table.$field_desc"] = $vdesc;
            $arr_rows_table[$name_table][$field_desc] = $field_desc;
          }

          if(!empty($arr_sql_field_key)){
            foreach($arr_sql_field_key as $namettable => $arr_ttable){
              foreach($arr_ttable as $kfield => $nfield){
                if(isset($arr_sql_desc_key[$nfield])){
                  $arr_key_pairs[$nfield][$namettable] = "{$namettable}.{$nfield}";
                  $arr_key_pairs_table[$namettable][$nfield] = $nfield;
                }
              }
            }
          }
        }
      }
    }
    $return = ['all_rows' => $arr_rows_table, 'key' => $arr_key_pairs, 'not_null' => $arr_not_null, 'rows' => $arr_rows, 'fields' => $arr_sql_field_key];
    return $return;
  }

  private function insert($data){
    $return = [];
    $arr_insert = $this->key('values', $data);
    if(!empty($arr_insert) && is_array($arr_insert)){

      $table = [];
      foreach($arr_insert as $name_table => $insert)$table[] = $name_table;

      $arr_sql_desc = $this->description($table);
      $arr_not_null = $this->key('not_null', $arr_sql_desc);

      foreach($arr_insert as $name_table => $insert){
        $str_empty = $set_table = $set_values = '';
       	$params = [];

        /*-------- SET --------*/
        if(isset($insert) && !empty($insert) && is_array($insert)){
          foreach($insert as $field_name => $value){
            if(isset($arr_not_null[$name_table][$field_name]) && $value == ''){
  						$str_empty .= "<li>{$arr_not_null[$name_table][$field_name]}</li>";
  					}
            if(is_string($value)){
              $set_table .= "{$field_name} = ?, ";
              $params[] = ($value); //utf8_encode
            }
          }
        }
        $set_table = $this->remove_string($set_table, 2);
        /*-------- SET --------*/

        $sql = "INSERT INTO {$name_table} SET $set_table";
        $return[$name_table]['state'] = FALSE;
        $return[$name_table]['sql'] = $this->query_print($sql, $params);

        if($str_empty != '')$return[$name_table]['message'] = "Fill:<ul>{$str_empty}</ul>";
        else{
          if(!empty($params) && $set_table != '')$return[$name_table] = $this->sql($sql, $params, FALSE, TRUE);
          else $return[$name_table]['message'] = 'Empty params';
        }
      }
    }
    return $return;
  }

  private function delete($data){
    $return = [];
    $db = $this->get_database_name($this->key('db', $data));
    $table = $this->key('table', $data);
    $where_criteria = $this->key('where', $data);
    $array_criteria_where = $this->criteria();
    if(!empty($table)){

      foreach($table as $no_table => $name_table){
        $name_table = $this->get_table_name($name_table, $db);
        $where_table = '';
        $params = [];

        /*-------- WHERE --------*/
        if(isset($where_criteria[$name_table]) && !empty($where_criteria[$name_table]) && is_array($where_criteria[$name_table])){

          foreach($where_criteria[$name_table] as $field_name => $array_criteria_data){

            $value_criteria = $this->key('value', $array_criteria_data);
            $option_sel_criteria = $this->key('option', $array_criteria_data);

            if(is_numeric($option_sel_criteria)){
              $option_criteria = $this->key($option_sel_criteria, $array_criteria_where);

              if($option_sel_criteria == 3){
                $option_criteria = $this->key(2, $array_criteria_where);
                if(is_string($value_criteria))$value_criteria = "%{$value_criteria}%";
              }

              if(in_array($option_sel_criteria, [9, 10])){
                $where_table .= " AND $field_name $option_criteria";

              }elseif(in_array($option_sel_criteria, [7, 8])){

                if($option_sel_criteria == 7)$option_criteria = 'IN';
                else $option_criteria = 'NOT IN';
              }

              if(is_array($value_criteria)){
                $array_pdo = $this->where($value_criteria);
                $where_table .= " AND $field_name $option_criteria ({$array_pdo['where']})";
                foreach($array_pdo['array'] as $kpdo => $vpdo)$params[] = $vpdo;

              }else{
                $where_table .= " AND $field_name $option_criteria ?";
                $params[] = $value_criteria;
              }
            }
          }
        }

  			$where_table = trim(substr(trim($where_table), 3));
  			if($where_table != '')$where_table = "WHERE {$where_table}";
        $sql = "DELETE FROM {$db}{$name_table} $where_table";
        $return['sql'][$name_table] = $this->query_print($sql, $params);

        if(!empty($params) && $where_table != ''){
          $arr_data_rows = $this->sql($sql, $params, FALSE);
          $return[$name_table] = $arr_data_rows;
        }else{
  				$return['state'] = TRUE;
  				$return['message'] = 'Not allowed empty where';
  			}
      }
    }
    return $return;
  }

  public function parse($array, $key_select, $val_select, $name_table, $option_select = '', $return_option = TRUE, $empty_option = TRUE){
     $return = [];
     if(is_array($array) && !empty($array)){
  	foreach($array as $norow => $arr){

        if(is_array($arr) && !empty($arr)){
          foreach($arr as $field => $val){
            $name_key = "{$name_table}__{$key_select}";
     
            if(isset($arr[$name_key])){
              if(is_array($val_select)){
                foreach($val_select as $k_val => $v_val){
                  if(array_key_exists($v_val, $arr)){
                    $return[$arr[$name_key]][str_replace("{$name_table}__", '', $v_val)] = $arr[$v_val];
                  }
                }
              }else{
                 if($val_select == ''){
                  foreach($arr as $ka => $va){
                    $return[$arr[$name_key]][str_replace("{$name_table}__", '', $ka)] = $va;
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
          if($option_select != '' && $option_select == $val)$option[] = ['value' => $val, 'option' => $opt, 'selected' => 'selected'];
          else $option[] = ['option' => $opt, 'value' => $val];
        }
      }
    }else $option = $return;

    return $option;
  }

  private function get_table_name($name_table, $db){
    if($db == '')$name_table = substr(strstr($name_table, '.', FALSE), 1);
    return $name_table;
  }

  private function get_database_name($db){
    if($db != '' && strpos($db, '.') === false)$db = "{$db}.";
    return $db;
  }

  private function query_print($sql, $params){
    $arr_join = explode('?', $sql);
    $str_sql = '';
    foreach ($arr_join as $kj => $vj) {
      $str_sql .= $vj;
      if(array_key_exists($kj, $params))$str_sql .= "'{$params[$kj]}'";
    }
    return $str_sql;
  }
}
