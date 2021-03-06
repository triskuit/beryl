<?php
require_once "shared/header.php";

// page variables
$search_term = $_GET['q'] ?? "";
global $session;

// Stuff to uncheck a block
if(isset($_POST['uncheck'])){

    $block = block_receipts::find_by_id($_POST['id']);
    $block->uncheck();
    $session->message("Block unchecked", "warning");
    redirect_to("/");
}

// Update a block as received
if (is_post_request() && isset($_POST['block'])) {
    
    // Set received date to today and save
    $block = new block_receipts($_POST['block']);
    $block->date_delivered = date("Y-m-d H:i:s");
    $block->save();

    if ($block->errors) {
        // Catch errors
        $session->message("Block could not be updated", "danger");
    } else {
        //Assuming no errors, update project start date in wrike to today
        $block = block_receipts::find_by_id($block->id);
        if ($block->is_project_complete()) {
            // Update project with new start date
            wrike_update_project_start_date($block->wrike_id, date('Y-m-d'));
        }
        $session->message("Your block has been received - Thanks!");
    }
    redirect_to($_SERVER['HTTP_REFERER']);
} 

// Stuff for pagination
if (is_get_request()) {
    $page = $_GET['page'] ?? 1;
    $block_count = block_receipts::count_all($_GET);
    $pagination = new Pagination($page, 10, $block_count);
    $blocks = block_receipts::find($_GET, $pagination);
}
?>

<body class="vertical-center m-0 p-0">
	<div class="container">
		<div class="row">
			<div class="col"><?php $session->print_message();?></div>
		</div>

		<div class="row">
			<!--  SEARCH BAR BEGIN -->
			<div class="col-12 mb-5">
				<form action="" class="m-0 p-0" method="GET">
					<div class="input-group mb-2">
						<input class="form-control form-control-lg" type="text" placeholder="Search" name="q" value="<?php echo $search_term?>">
						<div class="input-group-append">
							<button class="input-group-text" id="search_button">
								<i class="fa fa-search text-black" aria-hidden="true"></i>
							</button>
						</div>
					</div>
					<div class="d-flex justify-content-around">
						<div class="custom-control custom-switch">
							<input type="checkbox" class="custom-control-input" id="switch_received" name="received" 
							<?php echo isset($_GET['received']) ? " checked" : "";?>>
							<label class="custom-control-label" for="switch_received">Received</label>
						</div>
						<div class="custom-control custom-switch">
							<input type="checkbox" class="custom-control-input" id="switch_manual" name="manually_entered" 
							<?php echo isset($_GET['manually_entered']) ? " checked" : "";?>>
							<label class="custom-control-label" for="switch_manual">Manually entered</label>
						</div>
						<a href="index" class="text-danger">&times; Clear</a>
					</div>
				</form>
			</div>
			<!--  SEARCH BAR END -->

			<!--  TABLE BEGIN -->
			<?php if($blocks){?>
			<div class="col-12 mb-3">
				<ul class="list-group mt-3">
					<li class="list-group-item list-group-item-action d-flex justify-content-between font-weight-bold">
						<span class="w-25">Block Name</span> 
						<span class="w-20">Created</span> 
						<span class="w-20">Delivered By</span> 
						<span class="w-20">Delivered On</span>
					</li>
                	<?php foreach($blocks as $block){?>
                		<li class="list-group-item list-group-item-action d-flex justify-content-between block" data-id="<?php echo $block->id?>" data-name="<?php echo $block->block_name?>">
                    		<span class="w-25 overflow-ellipsis "><?php echo $block->block_name?></span>
    						<span class="w-20"><?php echo $block->format_date($block->date_created)?></span> 
    						<span class="w-20"><?php echo $block->delivered_by?></span>
    						<span class="w-20"><?php echo $block->format_date($block->date_delivered)?></span>
						</li>
                	<?php }?>
                </ul>
			</div>
			<?php } else {?>
				<h2 class="mx-auto">No results</h2>
			<?php }?>
			<!--  TABLE END -->
		</div>

		<div class="row  mb-3">
			<div class="col">
				<div class="col d-flex justify-content-center">
	    			<?php echo $pagination->page_links($_SERVER['REQUEST_URI']) ?>
	    		</div>			
			</div>
		</div>
		<div class="row">
			<div class="col">
				<a href="add" class="btn btn-primary">Checkin unlisted block</a>
			</div>
		</div>
	</div>
	

	<!-- MODAL BEGIN -->
	<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<form class="modal-content" method="POST" action="">
				<input type="hidden" name="id" value="" id="id" />
				<div class="modal-header mb-2">
					<h5 class="modal-title text-center" id="exampleModalLongTitle"></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
    				<div class="form-row mb-4">
    					<div class="col">
        					<select name="block[delivered_by]" class="form-control" id="user_select" required>
        						<option disabled selected></option>
        						<?php global $user_list; foreach ($user_list as $user) { echo "<option>$user</option>";} ?>
        					</select>
    					</div>
        				<div class="col-auto">   				
    						<button role="submit" class="btn btn-primary">
    							Check-in
							</button>
    					</div>
					</div>
					<a class="" data-toggle="collapse" href="#options_collapse" role="button">Other options <i class="fa fa-chevron-circle-right" aria-hidden="true"></i></a>
					<div class="form-row mt-2 collapse" id="options_collapse">
						<div class="col-6">
							<?php if(isset($_GET['received'])){?><button class="btn btn-outline-secondary btn-block btn-sm" name="uncheck">Uncheck</button><?php }?>
						</div>
						<div class="col-6">
							<a href="" class="btn btn-outline-danger btn-block btn-sm" id="link_delete">Delete</a>
						</div>
					</div>
				</div>
				<input type="hidden" id="block_id" name="block[id]" value="" />
			</form>
		</div>
	</div>
	<!-- MODAL END -->

</body>

<script>
	
$('.block').click( function(){
	var id = $(this).data('id')
	var modal = $('#exampleModalCenter')
	modal.modal()
	modal.find('#block_id').val(id)
	$('#exampleModalLongTitle').text($(this).data('name'))
	$('#options_collapse').removeClass("show")
	$('#user_select').val($('#user_select option:first').val())
	$('#link_delete').attr("href", "/delete?id=" + id)
	$('#id').val(id)
});
	
$(document).ready(function() {
    $(':checkbox').change(function() {
        $('#search_button').click()
    });
});

</script>