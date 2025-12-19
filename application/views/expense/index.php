<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
			<!-- <div class="col-sm-12"> -->
				<div class="page-title-box">
					<div class="row form-group">
							<div class="col-md-4">
								<select id="party_id" name="party_id" class="form-control select2">
									<option value="ALL">All Customers</option>
									<?php foreach($customerData as $row){echo '<option value="'.$row->id.'">'.$row->party_name.'</option>';} ?>
								</select>
							</div>
							<div class="col-md-4 float-end">
								<select id="emp_id" name="emp_id" class="form-control select2" >
									<option value="ALL">All Employees</option>
									<?php foreach($empData as $row){echo '<option value="'.$row->id.'">'.$row->emp_name.'</option>';} ?>
								</select>
							</div>
							<div class="col-md-4 float-end">
								<div class="input-group">
									<input type="date" name="from_date" id="from_date" class="form-control" value="<?=getFyDate("Y-m-d",date('Y-m-01'))?>" />

									<input type="date" name="to_date" id="to_date" class="form-control" value="<?=getFyDate()?>" />

									<div class="input-group-append ml-2">
										<button type="button" class="btn btn-success loaddata btn-block" style="height:2.1rem" title="Load Data"><i class="fas fa-sync-alt"></i></button>
									</div>
								</div>
								<div class="error fromDate"></div>
								<div class="error toDate"></div>
							</div>
					</div>
					
					<div class="float-end">
						<button type="button" class="btn btn-info btn-sm float-right addNew press-add-btn permission-write" data-button="both" data-modal_id="right_modal" data-function="addExpense" data-form_title="Add Expense"><i class="fa fa-plus"></i> Add Expense</button>
					</div>
                    <ul class="nav nav-pills">
						<li class="nav-item activeTab"> 
							<button data-status="0" class="loaddata tabBtns nav-tab btn waves-effect waves-light btn-outline-warning active" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Pending</button> 
						</li>
						<li class="nav-item activeTab"> 
							<button data-status="1" class="loaddata tabBtns nav-tab btn waves-effect waves-light btn-outline-success" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Approved</button> 
						</li>
                        <li class="nav-item activeTab" > 
							<button data-status="2" class="loaddata tabBtns nav-tab btn waves-effect waves-light btn-outline-danger" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Rejected</button> 
						</li>
					</ul>
				</div>
			<!-- </div> -->
		</div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='expenseTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>

<script>
$(document).ready(function() {
	
	initBulkApproveButton();	
    $(document).on('click','.loaddata',function(){ 
		loadDataTable(); 
	}); 

	$(document).on('click', '.BulkApproveRequest', function() {
		if ($(this).attr('id') == "masterApproveSelect") {
			if ($(this).prop('checked') == true) {
				$(".bulkApprove").show();
				$("input[name='ref_id[]']").prop('checked', true);
			} else {
				$(".bulkApprove").hide();
				$("input[name='ref_id[]']").prop('checked', false);
			}
		} else {
			if ($("input[name='ref_id[]']").not(':checked').length != $("input[name='ref_id[]']").length) {
				$(".bulkApprove").show();
				$("#masterApproveSelect").prop('checked', false);
			} else {
				$(".bulkApprove").hide();
			}

			if ($("input[name='ref_id[]']:checked").length == $("input[name='ref_id[]']").length) {
				$("#masterApproveSelect").prop('checked', true);
				$(".bulkApprove").show();
			}
			else{$("#masterApproveSelect").prop('checked', false);}
		}
	});

	$(document).on('click', '.bulkApprove', function() {
		var ref_id = [];
		$("input[name='ref_id[]']:checked").each(function() {
			ref_id.push(this.value);
		});
		var ids = ref_id.join("~");
		console.log(ids);
		var ajaxParam = {
			url: base_url + controller + '/approveBulkRequest',
			data: { ids : ids },
			type: "POST",
			dataType:"json"
		};

		Swal.fire({
			title: 'Are you sure?',
			text: 'Are you sure want to Approve Expense?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, Do it!',
		}).then(function(result) {
			if (result.isConfirmed == true)
			{
				$.ajax(ajaxParam).done(function(response){
					if(response.status==1){
						loadDataTable();
						Swal.fire( 'Success', response.message, 'success' );
					}else{
						if(typeof response.message === "object"){
							$(".error").html("");
							$.each( response.message, function( key, value ) {$("."+key).html(value);});
						}else{
							loadDataTable();
							Swal.fire( 'Sorry...!', response.message, 'error' );
						}			
					}			
				});
			}
		});
	});

});

function loadDataTable(){
	var status = $('.activeTab .active').data('status');
    var party_id = $('#party_id').val()|| 0;
    var emp_id = $('#emp_id').val();
    var from_date = $('#from_date').val();
    var to_date = $('#to_date').val();
    $("#expenseTable").attr("data-url",$("#expenseTable").data('url')+'/'+status+'/'+party_id+'/'+emp_id+'/'+from_date+'/'+to_date);
    ssTable.state.clear();initTable();
	initBulkApproveButton();
}

function initBulkApproveButton() {
	var bulkApproveBtn = '<button class="btn btn-outline-primary bulkApprove" tabindex="0" aria-controls="expenseTable" type="button"><span>Bulk Approve</span></button>';
	$("#expenseTable_wrapper .dt-buttons").append(bulkApproveBtn);
	$(".bulkApprove").hide();
}
</script>