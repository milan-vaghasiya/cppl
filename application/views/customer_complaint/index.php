<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end">
						<button type="button" class="btn btn-info btn-sm float-right addNew press-add-btn permission-write" data-button="both" data-modal_id="right_modal" data-function="addCustComplaint" data-form_title="Add Customer Complaint"><i class="fa fa-plus"></i> Add Complaint</button>
					</div>
                    <ul class="nav nav-pills">
						<li class="nav-item"> 
							<button onclick="statusTab('custComplaintTable','0');"  class="nav-tab btn waves-effect waves-light btn-outline-danger active" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Pending</button> 
						</li>
						<li class="nav-item"> 
							<button onclick="statusTab('custComplaintTable','1');" class="nav-tab btn waves-effect waves-light btn-outline-success" style="outline:0px" data-bs-toggle="tab" aria-expanded="false">Completed</button> 
						</li>
					</ul>
				</div>
			</div>
		</div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='custComplaintTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    $(document).on('change','#party_id',function(){
        var party_id = $(this).val();

        $.ajax({
            url : base_url + controller + '/getReturnOrderList',
            type : 'post',
            data : {party_id:party_id},
            dataType: 'json'
        }).done(function(res){
            $("#order_id").html("");
            $("#order_id").html(res.options);
        });
    });
    $(document).on('change','#business_type',function(){
        var business_type = $(this).val();
        getPartyList({"business_type":business_type});
        initSelect2("right_modal");
        
    });
});
</script>