<?php
require_once '../private/initialize.php';

if(is_get_request()){
    
    //delete for production server
    $project_name = $_GET['project_name'] ?? null;
    $block_number= $_GET['block_number']  ?? 0;
    $created_by= $_GET['created_by']  ?? null;
    $wrike_id= $_GET['wrike_id']  ?? null;
    
    $project_exists = block_receipts::project_exists($project_name);
    
    if ($project_exists > 0){
        if($project_exists !== $block_number){
            // Blocks already exist but with a different number of total blocks.
            // Delete and recreate.
            block_receipts::delete_by_project($project_name);
            $results = block_receipts::create_blocks($project_name, $block_number, $created_by, $wrike_id);
        } else {
            // Blocks already exist but no changes need to be made
        }
    } else {
        // Blocks do not already exist. Make new.
        $results = block_receipts::create_blocks($project_name, $block_number, $created_by, $wrike_id);
    }
    
    //print_r($results);
    echo "success";
    exit;
}

if(is_post_request()){

    //delete for production server
    $project_name = $_POST['project_name'] ?? null;
    $block_number= $_POST['block_number']  ?? 0;
    $created_by= $_POST['created_by']  ?? null;
    $wrike_id= $_POST['wrike_id']  ?? null;
    
    $project_exists = block_receipts::project_exists($project_name);
    
    if ($project_exists > 0){
        if($project_exists !== $block_number){
            // Blocks already exist but with a different number of total blocks.
            // Delete and recreate.
            block_receipts::delete_by_project($project_name);
            $results = block_receipts::create_blocks($project_name, $block_number, $created_by, $wrike_id);
        } else {
            // Blocks already exist but no changes need to be made
        }
    } else {
        // Blocks do not already exist. Make new.
        $results = block_receipts::create_blocks($project_name, $block_number, $created_by, $wrike_id);
    }
    
    //print_r($results);
    echo "success";
    exit;
    
} else {
    redirect_to("index.php");
}