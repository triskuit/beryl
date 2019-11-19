<?php

function db_connect() {

  try {
    $conn = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";", DB_USER, DB_PASS,
    array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
  } catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
  }

  $conn->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
  return $conn;
}

function confirm_db_connect($connection) {
  if($connection->connect_errno) {
    $msg = "Database connection failed: ";
    $msg .- $connection->connect_error;
    $msg .= " (" . $connection->connect_errno . ")";
    exit($msg);
  }
}

function db_disconnect($connection) {
  if(isset($connection)) {
    $connection->close();
  }
}
