<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12">
				<div class="page-title-box">
					<div class="float-end" style="width:100%;">
						<div class="input-group justify-content-end">
							<div class="col-md-4">
								<select name="emp_id" id="emp_id" class="form-control select2">
									<option value="">Select Executive</option>
									<?php
										foreach($empList as $row){												
											echo '<option value="'.$row->id.'">'.(!empty($row->emp_code) ? "[".$row->emp_code."] ".$row->emp_name : $row->emp_name).'</option>';
										}
									?>
								</select>
							</div>
							<div class="col-md-8 form-group">
								<div class="input-group">
									<input type="date" name="from_date" id="from_date" class="form-control" style="width:40%" value="<?=getFyDate("Y-m-d",date('Y-m-01'))?>" />
									<div class="error fromDate" ></div>
									<input type="date" name="to_date" id="to_date" class="form-control" style="width:40%" value="<?=getFyDate()?>" />
									<div class="input-group-append ml-2">
										<button type="button" class="btn waves-effect waves-light btn-success float-end  loadData" title="Load Data" >
											<i class="fas fa-sync-alt"></i> Load
										</button>
									</div>
								</div>
								<div class="error toDate"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<div class="col-12">
						<div class="card">
							<div class="card-body reportDiv" style="min-height:75vh">
								<div class="table-responsive">
									<table id='reportTable' class="table table-bordered">
										<thead id="theadData" class="thead-info">
											<tr>
												<th>#</th>
												<th>Invoice Date</th>
												<th>Invoice No</th>
												<th>Party Name</th>
												<th>Executive</th>
												<th>Distributer</th>
												<th>Amount</th>
												<th>Approved Amount</th>
											</tr>
										</thead>
										<tbody id="tbodyData"></tbody>
										
									</table>
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
<script>
$(document).ready(function(){
          
	reportTable();
    setTimeout(function(){
        $(".loadData").trigger('click');
        $("#statutory_id").select2({ width: '50%' });
    },500);
    
    $(document).on('click','.loadData',function(e){
		$(".error").html("");
		var valid = 1;
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        var emp_id = $('#emp_id').val();
		if(valid){
            $.ajax({
                url: base_url + controller + '/getCustOrdMonitoring',
                data: {from_date:from_date,to_date:to_date,emp_id:emp_id},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").DataTable().clear().destroy();
					$("#theadData").html(data.thead);
					$("#tbodyData").html(data.tbody);
					reportTable();
                }
            });
        }
    });   
});
</script>