<?php $this->load->view('includes/header'); ?>
<link href="<?=base_url()?>assets/plugins/tobii/tobii.min.css" rel="stylesheet" type="text/css" />
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
				
                    <div class="card-header">
                        <div class="row">
                            <!-- <div class="col-md-12">   -->
								<div class="col-md-2 form-group">
									<select id="party_type" name="party_type" class="form-control select2">
										<option value="1">Customer</option>
										<option value="2">Lead</option>
									</select>
								</div>
								<div class="col-md-3 form-group">
									<select id="sales_executive" name="sales_executive" class="form-control select2">
										<option value="">All Executives</option>
										<?php foreach($salesExecutives as $row){echo '<option value="'.$row->id.'">'.$row->emp_name.'</option>';} ?>
									</select>
								</div>
								<div class="col-md-3 form-group">
									<select id="party_id" name="party_id" class="form-control select2" >
										<option value="">All Party</option>
									</select>
								</div>
                                <div class="col-md-4 form-group">
									<div class="input-group">
										<input type="date" name="from_date" id="from_date" class="form-control" style="width:40%" value="<?=getFyDate("Y-m-d",date('Y-m-01'))?>" />
										<div class="error fromDate" ></div>
										<input type="date" name="to_date" id="to_date" class="form-control" style="width:40%" value="<?=getFyDate()?>" />
										<div class="input-group-append ml-2">
											<button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data" >
												<i class="fas fa-sync-alt"></i> Load
											</button>
										</div>
									</div>
                                </div>
                                <div class="error toDate"></div>
                            <!-- </div>                   -->
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
					
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered jpDataTable colSearch" data-srowposition="2" data-ninput="[0]">
								<thead class="thead-info " id="theadData">
									<tr class="text-center">
										<th colspan="10">Visit History</th>
									</tr>
									<tr id="tr2">
										<th style="min-width:25px;">#</th>
										<th style="min-width:25px;">Date</th>
										<th style="min-width:25px;">Party Name</th>
										<th style="min-width:100px;">Contact Person</th>
										<th style="min-width:100px;">Contact Number</th>
										<th style="min-width:100px;">Purpose</th>
										<th style="min-width:100px;">Discussion Points</th>
										<th style="min-width:100px;">Address</th>
										<th style="min-width:50px;">Attachment</th>
										<th style="min-width:100px;">Duration<br><small>(Minutes)</small></th>
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


<?php $this->load->view('includes/footer'); ?>
<script src="<?=base_url()?>assets/plugins/shuffle/shuffle.min.js?v=<?=time()?>"></script>
<script src="<?=base_url()?>assets/plugins/tobii/tobii.min.js?v=<?=time()?>"></script>
<script>
$(document).ready(function() {
	$(".select2").select2()
	reportTable();
	loadData();
    $(document).on('click','.loaddata',function(){
		var file_type=$(this).data('file_type');
		loadData(file_type);
	}); 
	
	$(document).on('change','#party_type',function(){
		var party_type = $("#party_type").val();
		if(party_type){
			$.ajax({
				url: base_url + controller + '/getPartyList',
				data: {party_type:party_type},
				type: "POST",
				dataType:'json',
				success:function(data){
					$("#party_id").html("");
					$("#party_id").html(data.options);
					$("#party_id").select2()
				}
			});
		}
	});  
});

function loadData(file_type=''){
	$(".error").html("");
	var valid = 1;
	var from_date = $('#from_date').val();
	var to_date = $('#to_date').val();
	var sales_executive = $('#sales_executive').val();
	var party_id = $('#party_id').val();
	var party_type = $('#party_type').val();

	if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
	var postData= {from_date:from_date, to_date:to_date,sales_executive:sales_executive,party_id:party_id,file_type:file_type,party_type:party_type};
	if(valid){
		if(file_type == "")
		{
			$.ajax({
			url: base_url + controller + '/getVisitHistory',
			data: postData,
			type: "POST",
			dataType:'json',
				success:function(data){
					$("#reportTable").DataTable().clear().destroy();
					$("#tbodyData").html("");
					$("#tbodyData").html(data.tbody);
					reportTable();
					const tobii = new Tobii({
									captions: false,
									zoom: false
								});
				}
			});
		}
		else
		{
			var u = window.btoa(JSON.stringify(postData)).replace(/=+$/, "");
			var url = base_url + controller + '/getVisitHistory/' + encodeURIComponent(u);
			window.open(url);
		}
	}
}
function reportTable()
{
	var reportTable = $('#reportTable').DataTable( 
	{
		responsive: true,
		scrollY: '55vh',
        scrollCollapse: true,
		"scrollX": true,
		"scrollCollapse":true,
		//'stateSave':true,
		"autoWidth" : false,
		order:[],
		"columnDefs": 	[
							{ type: 'natural', targets: 0 },
							{ orderable: false, targets: "_all" }, 
							{ className: "text-left", targets: [0,1] }, 
							{ className: "text-center", "targets": "_all" } 
						],
		pageLength:25,
		language: { search: "" },
		lengthMenu: [
            [ 10, 25, 50, 100, -1 ],[ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        ],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {$(".loaddata").trigger('click');}}]
	});
	reportTable.buttons().container().appendTo( '#reportTable_wrapper toolbar' );
	$('.dataTables_filter .form-control-sm').css("width","97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('.dataTables_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
	return reportTable;
}

</script>