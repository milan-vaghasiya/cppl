<form>
    <div class="col-md-12">
        <div class="row">            
                
            <div class="col-md-5 form-group">
                <label for="structure_name">Name</label>
                <input type="text" name="structure_name" id="structure_name" class="form-control req" value="<?=(!empty($dataRow->structure_name) ? $dataRow->structure_name : "")?>" />
            </div>
            
            <div class="col-md-2 form-group">
                <label for="type">Type</label>
                <select name="type" id="type" class="form-control modal-select2">
                    <option value="">Select Type</option>
                    <option value="1" <?=(!empty($dataRow->type) && $dataRow->type == "1") ? "selected" : ""?>>Regular</option>
                    <option value="2" <?=(!empty($dataRow->type) && $dataRow->type == "2") ? "selected" : ""?>>Master</option>
                </select>
            </div>
            
            <div class="col-md-3 form-group masterSelect">
                <label for="master_id">Master</label>
                <select name="master_id" id="master_id" class="form-control modal-select2 req">
                    <option value="">Select Master</option>
                    <?php
                        if(!empty($masterList)){
                            foreach($masterList as $row){
                                $selected = ((!empty($dataRow->master_id) && $dataRow->master_id == $row->id) ? "selected" : "");
                                echo '<option value="'.$row->id.'" '.$selected.'>'.$row->structure_name.'</option>';
                            }
                        }
                    ?>
                </select>
            </div>
            
            <div class="col-md-2 form-group">
                <label for="is_default">Is Default ? </label>
                <select name="is_default" id="is_default" class="form-control modal-select2">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
            </div>
            
            <div class="row">
               <?=$catHtml?>
            </div>

        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    setTimeout(function(){ $("#type").trigger('change') },500);

    $(document).on('change','#type',function(){
		var type = $(this).val();
		if(type == 1){ $('.masterSelect').show(); }
		else{ $('.masterSelect').hide(); }
	});
});
</script>