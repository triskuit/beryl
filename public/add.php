<?php
require_once "shared/header.php";
global $session;
    $block = new block_receipts();
    if (is_post_request()){
        // redirected from current page
        $block = new block_receipts($_POST['block']);
        $block->save();
        if(empty($block->errors)){
            $session->message("Block {$block->block_name} succesfully checked in.", "success");
            redirect_to("index");
        } else {
           var_dump($block->errors);
        }
    }
?>
<body>
	<div class="container vertical-center">
		<?php if(!empty($block->errors)){ ?>
		    <ul class="my-3 text-danger">
		    <?php foreach($block->errors as $error){ echo "<li>$error</li>"; }?>
		    </ul>
		<?php }?>
		<form class="form w-100 h-100" action="" method="POST">
			<input type="hidden" name="block[created_by]" value="Greenhouse" />
			<div class="row my-3">
				<div class="col-md-9 col-8 mb-3">
					<label for="project_name">Project Name</label>
					<input type="text" class="form-control" name="block[project_name]" value="<?php echo $block->project_name?>" required/>
				</div>
				<div class="col-md-3 col-4">
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