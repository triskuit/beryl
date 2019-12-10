<?php
require_once "shared/header.php";
if(is_post_request()){
    $id = $_POST['id'] ?? 0;
    if (!$id){redirect_to("/");}
    $block = block_receipts::find_by_id($id);
    $block->delete();
    $session->message("Block deleted.", "warning");
    redirect_to("/");
}
    $id = $_GET['id'] ?? 0;
    if (!$id){redirect_to("/");}
    $block = block_receipts::find_by_id($id);
?>
<body class="vertical-center m-0 p-0">
    <form class="container" method="POST">
    	<input type="hidden" name="id" value="<?php echo $id?>"/>
        <div class="row">
            <div class="col">
            	<h2>Are you sure you want to delete <?php echo $block->block_name;?>?</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-6 mb-2">
            	<a href="/" class="btn btn-outline-secondary btn-block">Cancel</a>
            </div>
            <div class="col-12 col-md-6">
            	<button class="btn btn-outline-danger btn-block">Delete</button>
            </div>	
        </div>
    </form>
</body>
