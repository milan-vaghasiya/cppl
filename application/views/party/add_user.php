<form autocomplete="off">
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="id" value="<?= (!empty($getEmpList->id) ? $getEmpList->id : "");?>" />
            <input type="hidden" name="party_id" value="<?=(!empty($partyData->id))?$partyData->id:""; ?>" />
            <input type="hidden" name="emp_role" value="5" />
			
            <div class="col-md-12 form-group">
				<label for="emp_code">User/Login ID</label>
				<div class="input-group">
					<input type="text" name="emp_code" class="form-control numericOnly req" value="<?= (!empty($getEmpList->emp_code) ? $getEmpList->emp_code : (!empty($partyData->contact_phone) ? $partyData->contact_phone : ""));?>" />
				</div>
            </div>

            <div class="col-md-12 form-group">
                <label for="emp_name">User Name</label>
                <input type="text" name="emp_name" class="form-control text-capitalize req" value="<?= (!empty($getEmpList->emp_name) ? $getEmpList->emp_name : (!empty($partyData->party_name) ? $partyData->party_name : ""));?>" />
            </div>
           
            <div class="col-md-12 form-group">
                <label for="emp_email">Email ID</label>
                <input type="text" name="emp_email" class="form-control" value="<?= (!empty($getEmpList->emp_email) ? $getEmpList->emp_email : "");?>" />
            </div>
			
            <div class="col-md-12 form-group">
                <label for="emp_contact">Phone No.</label>
                <input type="text" name="emp_contact" class="form-control numericOnly req" value="<?= (!empty($getEmpList->emp_contact) ? $getEmpList->emp_contact : (!empty($partyData->contact_phone) ? $partyData->contact_phone : ""));?>" />
            </div>
			
            <div class="col-md-12 form-group">
                <label for="emp_psc">Password</label>
                <input type="text" name="emp_psc" class="form-control req" value="<?= (!empty($getEmpList->emp_psc) ? $getEmpList->emp_psc : "123456");?>" />
            </div>
			
			<div class="col-md-12 form-group">
                <label for="executive_id">Executive</label>
                <select name="executive_id[]" id="executive_id" class="form-control modal-select2" multiple>
					<option value="">Select Executive</option>
					<?php
						$getExecutice = (!empty($getEmpList->executive_ids) ? explode(",",$getEmpList->executive_ids) : array());
						if(!empty($empList)){
							foreach($empList as $row){
								$selected = (!empty($getExecutice) && in_array($row->id,$getExecutice) ? "selected" : "");
								echo '<option value="'.$row->id.'" '.$selected.'>'.(!empty($row->emp_name) ? '['.$row->emp_code.'] '.$row->emp_name : $row->emp_name).'</option>';
							}
						}
					?>
				</select>
            </div>
        </div>
    </div>
</form>