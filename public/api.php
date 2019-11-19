<?php
require_once ('initialize.php');

if(is_post_request()){
    $block = new receipt_log($_POST['block']);
    $block->save();
    if($block->errors){
        echo "error";
    } else {
        // do nothing if there is no error
    }
} else {
    redirect_to("index.php");
}