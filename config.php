<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  include 'vendor/autoload.php';

  //CONFIG TEMPLATE
  define('TEMPLATE_JSON', 'insertpath/template.json'); //path or url
  define('ENCRYPTION_KEY', 'insertkeyhere');

  //DATABASE PARAMETERS MYSQL
  define('DB_HOST', '');
  define('DB_USER', '');
  define('DB_PASSWORD', '');
  define('DB_NAME', '');
  define('DB_PREFIX', '');
  define('DB', DB_NAME.'.'.DB_PREFIX);

  //DEFINE CLASS TEMPLATE
  $html = new blakepro\Template\Html();
  $pdo = new blakepro\Template\Sql(['host' => DB_HOST, 'name' => '', 'user' => DB_USER, 'password' => DB_PASSWORD]);
  $util = new blakepro\Template\Utilities(['encryption_key' => ENCRYPTION_KEY]);
