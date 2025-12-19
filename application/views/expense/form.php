<form>
    <div class="col-md-12">
        <div class="row">            
                
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id) ? $dataRow->id : "")?>" />

            <div class="col-md-6 form-group">
                <label for="exp_number">Expense No.</label>
                <input type="text" name="exp_number" id="exp_number" class="form-control req" value="<?=(!empty($dataRow->exp_number) ? $dataRow->exp_number : $exp_prefix.sprintf("%03d",$exp_no))?>" readOnly />
                <input type="hidden" name="exp_prefix" id="exp_prefix" value="<?=(!empty($dataRow->exp_prefix)) ? $dataRow->exp_prefix : $exp_prefix?>" />
                <input type="hidden" name="exp_no" id="exp_no" value="<?=(!empty($dataRow->exp_no)) ? $dataRow->exp_no : $exp_no?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="exp_date">Expense Date</label>
                <input type="date" name="exp_date" id="exp_date" class="form-control req" value="<?=(!empty($dataRow->exp_date) ? $dataRow->exp_date : date('Y-m-d'))?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="exp_source">Expense By</label>
                <select name="exp_source" id="exp_source" class="form-control modal-select2">
                    <option value="1" <?=(!empty($dataRow->exp_source) && $dataRow->exp_source == "1") ? "selected" : ""?>>Employee</option>
                    <option value="2" <?=(!empty($dataRow->exp_source) && $dataRow->exp_source == "2") ? "selected" : ""?>>Customer</option>
                </select>
            </div>

            <div class="col-md-8 form-group">
                <label for="exp_by_id">Employee / Customer</label>
                <select name="exp_by_id" id="exp_by_id" class="form-control modal-select2 req">
                    <option value="">Select Employee</option>
                    <?php
                        if(!empty($options)){
                            echo $options;
                        }else{
                            foreach($empList as $row){
                                echo '<option value="'.$row->id.'" '.$selected.'>'.$row->emp_name.'</option>';
                            }
                        }
                    ?>
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label for="exp_type">Expense Type</label>
                <select name="exp_type" id="exp_type" class="form-control modal-select2 req">
                    <option value="">Select Type</option>
                    <?php
                        if(!empty($expTypeList)){
                            foreach($expTypeList as $row){
                                $selected = ((!empty($dataRow->exp_type) && $dataRow->exp_type == $row->id) ? "selected" : "");
                                echo '<option value="'.$row->id.'" '.$selected.' data-is_travel="'.$row->is_travel.'" data-bike_expense="'.$row->bike_expense.'"  data-car_expense="'.$row->car_expense.'">'.$row->label.'</option>';
                            }
                        }
                    ?>
                </select>
            </div>
            
            <div class="col-md-6 form-group">
                <label for="location">Location</label>
                <input type="text" name="location" id="location" class="form-control" value="<?=(!empty($dataRow->location) ? $dataRow->location : "")?>" />
            </div>
            
            <div class="col-md-6 form-group travelExpense">
                <label for="vehicle_type">Vehicle</label>
                <select class="form-control calculateKM" name="vehicle_type"  id="vehicle_type">
                    <option value="1" <?=(!empty($dataRow->vehicle_type) && $dataRow->vehicle_type == 1)?'selected':''?>>Bike</option>
                    <option value="2" <?=(!empty($dataRow->vehicle_type) && $dataRow->vehicle_type == 2)?'selected':''?>>Car</option>
                </select>
                    
            </div>
            <div class="col-md-6 form-group travelExpense">
                <label for="start_km">Start KM</label>
                <input type="text" class="form-control floatOnly calculateKM" name="start_km"  id="start_km" value="<?=(!empty($dataRow->start_km) ? $dataRow->start_km : "")?>">
            </div>
            <div class="col-md-6 form-group travelExpense">
                <label for="end_km">End KM</label>
                <input type="text" class="form-control floatOnly calculateKM" name="end_km"  id="end_km"  value="<?=(!empty($dataRow->end_km) ? $dataRow->end_km : "")?>">
            </div>
            <div class="col-md-6 form-group">
                <label for="demand_amount">Amount</label>
                <input type="text" name="demand_amount" id="demand_amount" class="form-control floatOnly req" value="<?=(!empty($dataRow->demand_amount) ? $dataRow->demand_amount : "")?>" />
            </div>

            <div class="col-md-12 form-group">
                <label for="proof_file">File Upload</label>
                <div class="input-group">
                    <input type="file" name="proof_file" class="form-control" style="width:<?=(!empty($dataRow->proof_file)) ? "75%" : "" ?>"  />
                    <?php
                    if(!empty($dataRow->proof_file)){
                    ?>
                        <div class="input-group-append">
                            <a href="<?=base_url('assets/uploads/expense/'.$dataRow->proof_file)?>" class="btn btn-outline-primary" download><i class="fa fa-download"></i></a>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
            
            <div class="col-md-12 form-group">
                <label for="notes">Notes</label>
                <textarea name="notes" id="notes" class="form-control" rows="2"><?=(!empty($dataRow->notes) ? $dataRow->notes : "")?></textarea>
            </div>
			
        </div>
    </div>
</form>
<div class="">

</div>
<script>
$(document).ready(function(){
    $(".travelExpense").hide();
    $("#exp_type").trigger('change');
    $(document).on('change','#exp_source',function(){
        var exp_source = $(this).val();
        if(exp_source){
            $.ajax({
				url: base_url + controller + '/getExpenseByOptions',
				type:'post',
				data:{exp_source:exp_source},
				dataType:'json',
				success:function(data)
                {
					$("#exp_by_id").html("");
					$("#exp_by_id").html(data.options);
				}
			});
        }
    });

    $(document).on('change','#exp_type',function(){
        var is_travel = ($("#exp_type :selected").data('is_travel'));
        if(is_travel == 1){  $(".travelExpense").show();  $('#demand_amount').attr("readonly","readonly");}
        else{ $(".travelExpense").hide(); $('#demand_amount').removeAttr("readonly");}
    });

    $(document).on('keyup change','.calculateKM',function(){
        var bike_expense = parseFloat($("#exp_type :selected").data('bike_expense')) || 0;
        var car_expense = parseFloat($("#exp_type :selected").data('car_expense')) || 0;
        var start_km = parseFloat($("#start_km").val()) || 0;
        var end_km = parseFloat($("#end_km").val())|| 0;
        var vehicle_type = $("#vehicle_type").val();
        var totalKm = 0; 
        if(end_km >= start_km){ totalKm = end_km-start_km; }
        var amount = 0;
        if(vehicle_type == 1){  amount = totalKm*bike_expense; }
        else{ amount = totalKm*car_expense; }

        $('#demand_amount').val(amount);
        
    });

});
</script>