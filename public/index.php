<?php
require_once "shared/header.php";

$search_term = $_GET['q'] ?? "";
global $session;

if (is_post_request() && isset($_POST['block'])) {
    // update a block to be received

    $block = new block_receipts($_POST['block']);
    // $block = new block_receipts($_POST['block']);
    // set date_received when reciept is registered
    $block->date_delivered = date("Y-m-d H:i:s");
    $block->save();

    if ($block->errors) {
        $session->message("Block could not be updated", "danger");
    } else {
        if ($block->is_project_complete()) {
            // TODO write wrike update call here
        }
        $session->message("Block received.");
    }
    redirect_to("index");
} elseif (is_get_request()) {
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
				<form action="" class="m-0 p-0">
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
			<div class="col-auto">
				<a href="add" class="btn btn-primary">Checkin unlisted block</a>
			</div>
			<div class="col">
				<div class="col d-flex justify-content-center">
	    			<?php echo $pagination->page_links("")?>
	    		</div>			
			</div>
		</div>

	</div>

	<!-- MODAL BEGIN -->
	<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<form class="modal-content" method="POST" action="">
				<div class="modal-header mb-2">
					<h5 class="modal-title text-center" id="exampleModalLongTitle"></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
    				<div class="form-row mb-4">
    					<div class="col">
        					<select name="block[delivered_by]" class="form-control" required>
        						<option value="" disabled selected>Choose a user</option>
        						<?php global $user_list; foreach ($user_list as $user) { echo "<option>$user</option>";} ?>
        					</select>
    					</div>
        				<div class="col-auto">   				
    						<button role="submit" class="btn btn-primary">
    							Check-in
							</button>
    					</div>
					</div>
					<a class="" data-toggle="collapse" href="#options_collapse" role="button" >Other options <i class="fa fa-chevron-circle-right" aria-hidden="true"></i></a>
					<div class="form-row mt-2 collapse" id="options_collapse">
						<div class="col-6">
							<a href="" class="btn btn-outline-secondary btn-block btn-sm">Uncheck</a>
						</div>
						<div class="col-6">
							<a href="" class="btn btn-outline-danger btn-block btn-sm">Delete</a>
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

// $('#exampleModalCenter').on('show.bs.modal', function (event) {
// 	  var button = $(event.relatedTarget)
// 	  var id = button.data('id')
// 	  var modal = $(this)
// 	  modal.find('#block_id').val(id)
// 	  //console.log(id)
// 	})
	
$('.block').click( function(){
	var id = $(this).data('id')
	var modal = $('#exampleModalCenter')
	modal.modal()
	modal.find('#block_id').val(id)
	$('#exampleModalLongTitle').text($(this).data('name'))
});
	
$(document).ready(function() {
    $(':checkbox').change(function() {
        $('#search_button').click()
    });
});

</script>