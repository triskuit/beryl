<?php

ob_start();

define("PRIVATE_PATH", dirname(__FILE__));
define("PROJECT_PATH", dirname(PRIVATE_PATH));
define("PUBLIC_PATH", PROJECT_PATH . '/public');
define("SHARED_PATH", PRIVATE_PATH . '/shared');
define("WWW_ROOT", "");

require_once('functions.php');
require_once('validation_functions.php');
require_once('database/db_credentials.php');
require_once('database/db_functions.php');

foreach(glob('classes/*.class.php') as $file) {
  require_once($file);
}

function my_autoload($class) {
  if(preg_match('/\A\w+\Z/', $class)) {
    include('classes/' . $class . '.class.php');
  }
}
spl_autoload_register('my_autoload');

$database = db_connect();
DatabaseObject::set_database($database);

$session = new Session;

// VALUES TO EDIT

$user_list =[
    'Jennifer',
    'Jesse',
    'Kyndra',
    'Lisa',
    'Maya',
    'Paul',
    'Robin',
    'Sarah',
    'Vanessa',
];