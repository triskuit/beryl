<?php
require_once ('initialize.php');
global $session;
    $block = new receipt_log();
    if (is_post_request()){
        // redirected from current page
        $block = new receipt_log($_POST['block']);
        $block->save();
        if(empty($block->errors)){
            $session->message("Block {$block->block_name} succesfully checked in.", "success");
            redirect_to("index");
        } //else go an print the errors
    }
?>

<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="">
<meta name="author" content="">
<link rel="icon" href="../resources/images/favicon.ico">

<!-- hook this into a variable -->
<title>Block Checkin</title>

<!-- Bootstrap core CSS -->
<link href="../CSS/bootstrap.css" rel="stylesheet">

<!-- Bootstrap core JavaScript -->
<script src="../JS/jquery-3.2.1.min.js" type="text/javascript"></script>
<script src="../JS/popper.min.js" type="text/javascript"></script>
<script src="../JS/holder.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.1/dist/jquery.validate.js" type="text/javascript"></script>
<script src="../JS/bootstrap.min.js" type="text/javascript"></script>

<!-- Font awesome v4 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">

</head>

<body>

	<div class="container">
		<?php if(!empty($block->errors)){ ?>
		    <ul class="my-3 text-danger">
		    <?php foreach($block->errors as $error){ echo "<li>$error</li>"; }?>
		    </ul>
		<?php }?>
		<form class="form" action="" method="POST">
			<input type="hidden" name="block[created_by]" value="Greenhouse" />
			<div class="row my-3">
				<div class="col-md-10 col-8 mb-3">
					<label for="project_name">Project Name</label>
					<input type="text" class="form-control" name="block[project_name]" value="<?php echo $block->project_name?>" required/>
				</div>
				<div class="col-md-2 col-4">
					<label for="block_number">Block Number</label>
					<select class="form-control" name="block[block_number]" required>
						<option></option>
						<?php for($i=1; $i<20; $i++){echo "<option>$i</option>";}?>
					</select>
				</div>
				<div class="col">
					<label for="delivered_by">Delivered by</label>
					<select name="block[delivered_by]" class="form-control" id="delivered_by" required>
						<option></option>
						<?php 
						global $user_list;
						foreach ($user_list as $user){ echo "<option>$user</option>"; }
						?>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 mb-3">
					<a href="index" class="btn btn-block btn-secondary">Cancel</a>
				</div>
				<div class="col-md-6">
					<button class="btn btn-block btn-primary">Submit</button>
				</div>
			</div>
		</form>
	</div>

</body>