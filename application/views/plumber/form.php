<form>
    <div class="col-md-12">
        <div class="row">            
                
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id) ? $dataRow->id : "")?>" />

            <div class="col-md-6 form-group">
                <label for="party_id">Customer Name</label>
                <select name="party_id" id="party_id" class="form-control modal-select2 req">
                    <option value="">Select Party</option>
                    <?=getPartyListOption($partyList,((!empty($dataRow->party_id))?$dataRow->party_id:0));?>
                </select>
            </div>
            
            <div class="col-md-6 form-group">
                <label for="name">Plumber Name</label>
                <input type="text" name="name" id="name" class="form-control text-capitalize req" value="<?=(!empty($dataRow->name))?$dataRow->name:""; ?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="phone_no">Phone No.</label>
                <input type="text" name="phone_no" id="phone_no" class="form-control numericOnly" value="<?=(!empty($dataRow->phone_no))?$dataRow->phone_no:""; ?>" />
            </div>

          
            <div class="col-md-6 form-group">
                <label for="adhar_no">Aadhar No</label>
                <input type="text" name="adhar_no" id="adhar_no" class="form-control numericOnly" value="<?=(!empty($dataRow->adhar_no))?$dataRow->adhar_no:""; ?>" />
            </div>

            <div class="col-md-12 form-group">
                <label for="address">Address</label>
                <textarea name="address" id="address" class="form-control " rows="2"><?=(!empty($dataRow->address))?$dataRow->address:""?></textarea>
            </div>

        </div>
    </div>
</form>