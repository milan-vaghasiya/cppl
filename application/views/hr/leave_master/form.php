<form>
	<div class="col-md-12">
        <div class="row">
			<input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />

            <div class="col-md-12 form-group">
                <label for="emp_id">Employee Name</label>
                <select name="emp_id" id="emp_id" class="form-control modal-select2 req"> 
                    <option value="">Select Employee</option>
                    <?php
                        foreach($employeeList as $row):
                            $selected = (!empty($dataRow->emp_id) && $row->id == $dataRow->emp_id)?"selected":"";
                            $emp_name = ($this->loginId == $row->id) ? "My Self" : $row->emp_name;
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$emp_name.'</option>';
                        endforeach;
                    ?>
                </select>
				<div class="error emp_id"></div>
            </div>

			<div class="col-md-6 form-group">
                <label for="start_date">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control req" value="<?=(!empty($dataRow->start_date))?formatDate($dataRow->start_date,'Y-m-d'):date("Y-m-d")?>" min="<?=(!empty($dataRow->start_date))?formatDate($dataRow->start_date,'Y-m-d'):date("Y-m-d")?>" />
            </div>
			
			<div class="col-md-6 form-group">
                <label for="start_section">Start Section</label>
                <select name="start_section" id="start_section" class="form-control req modal-select2">
                    <option value="F" <?=(!empty($dataRow->start_section) && $dataRow->start_section == 'F')?"selected":""?>>Full day</option>
                    <option value="H" <?=(!empty($dataRow->start_section) && $dataRow->start_section == 'H')?"selected":""?>>Half Day</option>
                </select>
            </div>
            
            <div class="col-md-6 form-group">
                <label for="end_date">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control req" value="<?=(!empty($dataRow->end_date))?formatDate($dataRow->end_date,'Y-m-d'):date("Y-m-d")?>"  />
            </div>
			
			<div class="col-md-6 form-group">
                <label for="end_section">End Section</label>
                <select name="end_section" id="end_section" class="form-control req modal-select2">
                    <option value="F" <?=(!empty($dataRow->end_section) && $dataRow->end_section == 'F')?"selected":""?>>Full day</option>
                    <option value="H" <?=(!empty($dataRow->end_section) && $dataRow->end_section == 'H')?"selected":""?>>Half Day</option>
                </select>
            </div>

            <div class="col-md-12 form-group">
                <label for="leave_type_id">Leave Type</label>
                <select name="leave_type_id" id="leave_type_id" class="form-control modal-select2 req">
                    <option value="">Select Employee</option>
                    <?php
                        foreach($leaveList as $row):
                            $selected = (!empty($dataRow->leave_type_id) && $row->id == $dataRow->leave_type_id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->label.'</option>';
                        endforeach;
                    ?>
                </select>
				<div class="error leave_type_id"></div>
            </div>

            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
            </div>

		</div>
	</div>	
</form>