<?php
function is_get_request() {
  return $_SERVER['REQUEST_METHOD'] ==  'GET';
}

function is_post_request() {
  return $_SERVER['REQUEST_METHOD'] ==  'POST';
}

function redirect_to($location) {
  header("Location: " . $location);
  exit;
}

function h($string=""){
  return htmlspecialchars($string);
}

function raw_u($string=""){
  return rawurlencode($string);
}

function u($string="") {
  return urlencode($string);
}

function url_for($script_path) {
  if($script_path[0] != '/') {
    $script_path = "/" . $script_path;
  }
  return WWW_ROOT . $script_path;
}

function display_errors($errors=array()) {
  $output = '';
  if(!empty($errors)) {
    $output .= "<div class=\"errors\">";
    $output .= "Please fix the following errors:";
    $output .= "<ul>";
    foreach($errors as $error) {
      $output .= "<li>" . h($error) . "</li>";
    }
    $output .= "</ul>";
    $output .= "</div>";
  }
  return $output;
}

function require_login() {
  global $session;
  if(!$session->is_logged_in()) {
    $session->message("You must be logged in to do that", "warning");
    redirect_to(url_for('/index.php'));
  } else {
    // Do nothing, let the rest of the page proceed
  }
}

function print_errors($errors){
    $output = "";
    if(!empty($errors)){
        $output .= "<ul>";
        foreach($errors as $e){
            $output .= "<li>$e</li>";
        }
        $output .= "</ul>";
    }
    echo $output;
}
