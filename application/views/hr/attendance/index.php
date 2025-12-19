<?php $this->load->view('includes/header'); ?>
<link href="<?=base_url()?>assets/plugins/tobii/tobii.min.css" rel="stylesheet" type="text/css" />
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="page-title">Attendance</h4>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
									<input type="date" id="from_date" name="from_date" class="form-control" value="<?=date("Y-m-d")?>" max=<?=date('Y-m-d')?> >
									<input type="date" id="to_date" name="to_date" class="form-control" value="<?=date("Y-m-d")?>" max=<?=date('Y-m-d')?> >
									<div class="input-group-append">
										<button class="btn btn-info loaddata" type="button">Go!</button>
										<button type="button" class="btn btn-success btn-sm float-right addNew press-add-btn permission-write" data-button="both" data-modal_id="right_modal" data-function="addManualAttendence" data-form_title="Add Manual Attendence"><i class="fa fa-plus"></i> Add Manual Attendence</button>
									</div>
								</div>
								<div class="error reportDate"></div>
                            </div>                       
                        </div>                                         
                    </div>
					 <div class="row">
						<div class="col-12">
							<div class="card">
								<div class="card-body">
									<div class="table-responsive">
										<table id='attendanceTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
									</div>
								</div>
							</div>
						</div>
					</div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?=base_url()?>assets/plugins/shuffle/shuffle.min.js?v=<?=time()?>"></script>
<script src="<?=base_url()?>assets/plugins/tobii/tobii.min.js?v=<?=time()?>"></script>
<script>

$(document).ready(function() {
	 $(document).on('click',".loaddata",function(){
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val(); 
		$("#attendanceTable").attr("data-url", '/getDTRows/' + from_date + '/' + to_date);
        initTable();
    }); 
});
</script>