<form>
    <div class="col-md-12">
        <div class="row">            
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id) ? $dataRow->id : "")?>" />
            <input type="hidden" name="type" id="type" value="<?=(!empty($dataRow->type) ? $dataRow->type : $type)?>" />
            <div class="col-md-12 form-group">
                <label for="label">Option</label>
                <input type="text" name="label" id="label" class="form-control req" value="<?=(!empty($dataRow->label) ? $dataRow->label : "")?>" />
            </div>
			<div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" id="remark" class="form-control"><?=(!empty($dataRow->remark) ? $dataRow->remark : "")?></textarea>
            </div>
            <?php
            $type = (!empty($dataRow->type) ? $dataRow->type : $type);
            if($type == 3){?>
            <div class="col-md-6 form-group">
                <label for="image_required">Image Required ? </label>
                <select name="image_required" id="image_required" class="form-control">
                    <option value="0">No</option>
                    <option value="1" <?=!empty($dataRow->image_required)?'selected':''?>>Yes</option>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="is_travel">Is Traveling Expense ? </label>
                <select name="is_travel" id="is_travel" class="form-control">
                    <option value="0">No</option>
                    <option value="1" <?=!empty($dataRow->is_travel)?'selected':''?>>Yes</option>
                </select>
            </div>
            <div class="col-md-6 form-group expenseDiv" >
                <label for="bike_expense">Bike Expense</label>
                <input type="text" name="bike_expense" id="bike_expense" class="form-control" value="<?=(!empty($dataRow->bike_expense) ? $dataRow->bike_expense : "")?>" />
            </div>
            <div class="col-md-6 form-group expenseDiv">
                <label for="car_expense">Car Expense</label>
                <input type="text" name="car_expense" id="car_expense" class="form-control" value="<?=(!empty($dataRow->car_expense) ? $dataRow->car_expense : "")?>" />
            </div>
                <?php
            }
            ?>
        </div>
    </div>
</form>
<script>
    $(document).ready(function(){
        $(".expenseDiv").hide();
        $('#is_travel').trigger("change");
        $(document).on('change','#is_travel',function(){
            var is_travel = $(this).val();
            if(is_travel == 1){ $(".expenseDiv").show(); }
            else{  $(".expenseDiv").hide(); }
        });
    });
</script>