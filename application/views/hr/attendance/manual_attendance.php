<form>
	<div class="col-md-12">
        <div class="row">
			
			<input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
			<input type="hidden" name="punch_type" id="punch_type" value="2" />
			
			<div class="col-md-12 form-group">
                <label for="emp_id">Employee</label>   
                <select name="emp_id" id="emp_id" class="form-control modal-select2 req">
                    <option value="">Select Employee</option>
                    <?php
                    if(!empty($empList)):
                        foreach($empList as $row):
                            $selected = (!empty($dataRow->emp_id) && $dataRow->emp_id == $row->id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>['.$row->emp_code.']'.$row->emp_name.'</option>';
                        endforeach;
                    endif;
                    ?>
                </select>
                <div class="error emp_id"></div>          
            </div>
            
			<div class="col-md-4 form-group">
				<label for='type'>Type</label>
                <select name="type" id="type" class="form-control modal-select2 req">
                    <option value="IN" <?=(!empty($dataRow) && $dataRow->type == "IN")?"selected":""?>>IN</option>
                    <option value="OUT" <?=(!empty($dataRow) && $dataRow->type == "OUT")?"selected":""?>>OUT</option>
                </select>
			</div>

            <div class="col-md-4 form-group">
                <label for='attendance_date'>Attendance Date</label>
				<input type="date" id="attendance_date" name="attendance_date" class="form-control req" value="<?=(!empty($dataRow->attendance_date))?$dataRow->attendance_date:date('Y-m-d')?>">
			</div>

            <div class="col-md-4 form-group">
                <label for='punch_date'>Punch Time</label>
				<input type="time" id="punch_date" name="punch_date" class="form-control req" value="<?=(!empty($dataRow->punch_date))?date('H:i',strtotime($dataRow->punch_date)):date('H:i')?>">
			</div>

		</div>
	</div>	
</form>