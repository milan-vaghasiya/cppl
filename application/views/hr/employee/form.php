<form autocomplete="off">
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="emp_role" value="6" />
			
            <div class="col-md-4 form-group">
				<label for="emp_code">User/Login ID</label>
				<div class="input-group">
					<input type="text" name="emp_code" class="form-control numericOnly req" value="<?=(!empty($dataRow->emp_code))?$dataRow->emp_code:''?>" />
				</div>
            </div>

            <div class="col-md-8 form-group">
                <label for="emp_name">User Name</label>
                <input type="text" name="emp_name" class="form-control text-capitalize req" value="<?=(!empty($dataRow->emp_name))?$dataRow->emp_name:""; ?>" />
            </div>
           
            <div class="col-md-4 form-group">
                <label for="emp_email">Email ID</label>
                <input type="text" name="emp_email" class="form-control" value="<?=(!empty($dataRow->emp_email))?$dataRow->emp_email:""?>" />
            </div>
			
            <div class="col-md-4 form-group">
                <label for="emp_contact">Phone No.</label>
                <input type="text" name="emp_contact" class="form-control numericOnly req" value="<?=(!empty($dataRow->emp_contact))?$dataRow->emp_contact:""?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="emp_gender">Gender</label>
                <select name="emp_gender" id="emp_gender" class="form-control modal-select2">
                    <option value="">Select Gender</option>
                    <?php
                        foreach($genderList as $value):
                            $selected = (!empty($dataRow->emp_gender) && $value == $dataRow->emp_gender)?"selected":"";
                            echo '<option value="'.$value.'" '.$selected.'>'.$value.'</option>';
                        endforeach;
                    ?>
                </select>
				<div class="error emp_gender"></div>
            </div>

            <div class="col-md-4 form-group">
                <label for="emp_birthdate">Date of Birth</label>
                <input type="date" name="emp_birthdate" id="emp_birthdate" class="form-control" value="<?=(!empty($dataRow->emp_birthdate))?$dataRow->emp_birthdate:date("Y-m-d")?>" max="<?=(!empty($dataRow->emp_birthdate))?$dataRow->emp_birthdate:date("Y-m-d")?>" />
            </div>

            <!--<div class="col-md-3 form-group">-->
            <!--    <label for="emp_joining_date">Date of Joining</label>-->
            <!--    <input type="date" name="emp_joining_date" id="emp_joining_date" class="form-control" value="<?=(!empty($dataRow->emp_joining_date))?$dataRow->emp_joining_date:date("Y-m-d")?>" max="<?=(!empty($dataRow->emp_joining_date))?$dataRow->emp_joining_date:date("Y-m-d")?>" />-->
            <!--</div>-->
            
            <div class="col-md-4 form-group">
                <label for="emp_designation">Designation</label>
                <select name="emp_designation" id="emp_designation" class="form-control modal-select2 req">
                    <option value="">Select Designation</option>
                    <?php
                        foreach($designationList as $row):
                            $selected = (!empty($dataRow->emp_designation) && $row->id == $dataRow->emp_designation)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->title.'</option>';
                        endforeach;
                    ?>
                </select>
				<div class="error emp_designation"></div>
            </div>

            <div class="col-md-4 form-group">
                <label for="is_se">Is Executive?</label>
                <select name="is_se" id="is_se" class="form-control modal-select2">
                    <option value="Yes" <?=(!empty($dataRow->is_se) && $dataRow->is_se == "Yes") ? "selected" : ""?>>Yes</option>
                    <option value="No" <?=(!empty($dataRow->is_se) && $dataRow->is_se == "No") ? "selected" : ""?>>No</option>
                </select>
            </div>
            
            <div class="col-md-4 form-group">
                <label for="zone_id">Sales Zone</label>
                <select name="zone_id[]" id="zone_id" class="form-control modal-select2" multiple>
                    <option value="">Select Sales Zone</option>
                    <?php
                        foreach($zoneList as $row):
                            $selected = (!empty($dataRow->zone_id) && in_array($row->id,explode(',',$dataRow->zone_id)))?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->zone_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <label for="auth_id">Higher Authority</label>
                <select name="auth_id[]" id="auth_id" class="form-control modal-select2" >
                    <option value="">Select Sales Zone</option>
                    <?php
                        foreach($authList as $row):
                            $selected = (!empty($dataRow->auth_id) && in_array($row->id,explode(',',$dataRow->auth_id)))?"selected":"";
                            if(empty($dataRow->id) || (!empty($dataRow->id) && $dataRow->id != $row->id)):
                                echo '<option value="'.$row->id.'" '.$selected.'>'.$row->emp_name.'</option>';
                            endif;    
                        endforeach;
                    ?>
                </select>
            </div>
            
            <div class="col-md-4 form-group">
                <label for="lead_rights">Leads Rights</label>
                <select name="lead_rights" id="lead_rights" class="form-control modal-select2">
                    <option value="">Select Leads Rights</option>
                    <?php
                        $leadsRights = ['','Individual','Sales Zone Wise','All Rights'];
                        foreach($leadsRights as $key=>$value):
                            if(!empty($value)):
                                $selected = (!empty($dataRow->lead_rights) && $key == $dataRow->lead_rights)?"selected":"";
                                echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                            endif;
                        endforeach;
                    ?>
                </select>
				<div class="error emp_gender"></div>
            </div>
            
             <div class="col-md-4 form-group">
                <label for="india_mart">India Mart</label>
                <select name="india_mart" id="india_mart" class="form-control modal-select2">
                    <option value="1" <?=(!empty($dataRow->india_mart) && $dataRow->india_mart == "1") ? "selected" : ""?>>No</option>
                    <option value="2" <?=(!empty($dataRow->india_mart) && $dataRow->india_mart == "2") ? "selected" : ""?>>Yes</option>
                </select>
            </div>
            
            <div class="col-md-4 form-group">
                <label for="kg_price">Kg Price</label>
                <select name="kg_price" id="kg_price" class="form-control modal-select2">
                    <option value="1" <?=(!empty($dataRow->kg_price) && $dataRow->kg_price == "1") ? "selected" : ""?>>No</option>
                    <option value="2" <?=(!empty($dataRow->kg_price) && $dataRow->kg_price == "2") ? "selected" : ""?>>Yes</option>
                </select>
            </div>
            
            <div class="col-md-4 form-group">
                <label for="is_attendance">Attendance</label>
                <select name="is_attendance" id="is_attendance" class="form-control modal-select2">
					<option value="1" <?=(!empty($dataRow->is_attendance) && $dataRow->is_attendance == 1) ? "selected" : ""?>>Yes</option>
					<option value="2" <?=(!empty($dataRow->is_attendance) && $dataRow->is_attendance == 2) ? "selected" : ""?>>No</option>
				</select>
            </div>
			
			<div class="col-md-4 form-group">
                <label for="travel_by">Travel By</label>
                <select name="travel_by[]" id="travel_by" class="form-control modal-select2" multiple>
					<option value="">Select Travel By</option>
					<option value="Bike" <?=(!empty($travel_by) && in_array("Bike",$travel_by)) ? "selected" : ""?>>Bike</option>
					<option value="Car" <?=(!empty($travel_by) && in_array("Car",$travel_by)) ? "selected" : ""?>>Car</option>
					<option value="Other" <?=(!empty($travel_by) && in_array("Other",$travel_by)) ? "selected" : ""?>>Other</option>
				</select>
            </div>
            
            <div class="col-md-4 form-group">
                <label for="emp_password">Password</label>
                <input type="text" name="emp_password" class="form-control req" value="<?=(!empty($dataRow->emp_psc))?$dataRow->emp_psc:"123456"?>" />
            </div>
           
            <div class="col-md-12 form-group">
                <label for="emp_address">Address</label>
                <textarea name="emp_address" class="form-control" style="resize:none;" rows="2"><?=(!empty($dataRow->emp_address))?$dataRow->emp_address:""?></textarea>
            </div>
            
        </div>
    </div>
</form>