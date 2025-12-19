<?php $this->load->view('includes/header'); ?>
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
				<div class="page-title-box">
					<div class="col-md-12" style="width:100%;">
					    <div class="input-group">
                            <div class="input-group-append" style="width:15%;">
                                <label for="business_type">Business Type</label>
                                <select id="business_type" class="form-control select2">
                                    <option value="ALL">All Business Type</option>
                                    <?php
                                        foreach($bTypeList as $row):
                                            echo '<option value="'.$row->type_name.'" >'.$row->type_name.'</option>';
                                        endforeach;
                                    ?>
                                </select>
                            </div>
                            <div class="input-group-append" style="width:20%;">
                                <label for="zone_id">Zone</label>
                                <select  id="zone_id" class="form-control select2">
                                    <option value="ALL">All Zone</option>
                                    <?php   
                                        foreach($zoneList as $row): 
                                            echo '<option value="'.$row->id.'">'.$row->zone_name.'</option>';
                                        endforeach; 
                                    ?>
                                </select>
                            </div>
                            <div class="input-group-append" style="width:15%;">
                                <label for="from_date">From Date</label>
                                <input type="date" id="from_date" class="form-control" value="<?=$startDate?>" /> 
                            </div>   
                            <div class="input-group-append" style="width:15%;">   
                                <label for="to_date">To Date</label>                             
                                <input type="date" id="to_date" class="form-control" value="<?=$endDate?>" />
                            </div>  
                            <div class="input-group-append mt-4" style="width:10%;">   
                                <button type="button" class="btn waves-effect waves-light btn-success float-right loadData" style="width:100%;" title="Load Data">
                                    <i class="fas fa-sync-alt"></i> Load
                                </button>
                            </div>  
                            <div class="error fromDate"></div>
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
                                    <thead class="thead-info">
                                        <tr>
                                            <th>Executive</th>
                                            <th>Visit</th>
                                            <th>New Lead</th>
                                            <th>Sales Enquiry</th>
                                            <th>Sales Order</th>
                                            <th>Order Value</th>
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
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){

	reportTable();
    setTimeout(function(){ $(".loadData").trigger('click'); },500);
    
    $(document).on('click','.loadData',function(e){
		$(".error").html("");
		var valid = 1;
		var business_type = $('#business_type').val();
		var zone_id = $('#zone_id').val();
        var from_date = $('#from_date').val();
	    var to_date = $('#to_date').val();
        if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	    if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	    if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}

		if(valid){
            $.ajax({
                url: base_url + controller + '/getExecutiveAnalysisData',
                data: {business_type:business_type, zone_id:zone_id, from_date:from_date, to_date:to_date},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").DataTable().clear().destroy();
					$("#tbodyData").html(data.tbody);
					reportTable();
                }
            });
        }
    });   
});
</script>