<form id="uploadImage">
    <input type="hidden" name="id" id="id" value="<?=$id?>" />
    <div class="mb-3">
        <label for="ref_date" class="form-label">Image File</label>
        <input type="file" class="form-control" name="party_image[]" id="party_image" accept="image/*"  multiple="multiple" >
        <div class="error party_image"></div>
    </div>
    <div class="">
        <?php
            $param = "{'formId':'uploadImage','fnsave':'savePartyImage','controller':'app/lead/'}";
        ?>
        
        <button type="button" class="btn btn-success btn-round btn-outline-dashed btn-block" onclick="uploadImage(<?=$param?>)" >Upload</button>
    </div>
</form>