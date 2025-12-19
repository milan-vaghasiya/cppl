<form autocomplete="off" id="saveSalesOrder" data-res_function="resSaveOrder">
    <div class="col-md-12">
        <div class="row">

            <div class="hiddenInput">
                <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
                <input type="hidden" name="lead_id" id="lead_id" value="<?=(!empty($dataRow->lead_id))?$dataRow->lead_id:(!empty($lead_id)?$lead_id:"")?>">
                <input type="hidden" name="sales_executive" id="sales_executive" value="<?=(isset($dataRow->sales_executive))?$dataRow->sales_executive:(!empty($executive_id)?$executive_id:"")?>">
                <input type="hidden" name="trans_prefix" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:(!empty($trans_prefix)?$trans_prefix:"")?>" />
                <input type="hidden" name="trans_no" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:(!empty($trans_no)?$trans_no:"")?>" /> 
                <input type="hidden" name="from_entry_type" id="from_entry_type" value="<?=(!empty($dataRow->from_entry_type))?$dataRow->from_entry_type:(!empty($from_entry_type)?$from_entry_type:'')?>" >
                <input type="hidden" name="from_ref_id" id="from_ref_id" value="<?=(!empty($dataRow->ref_id))?$dataRow->ref_id:(!empty($from_ref_id)?$from_ref_id:'')?>">
                <input type="hidden" name="module_type" id="module_type" value="<?=$module_type?>">
            </div>

            <div class="col-md-2 form-group">
                <label for="trans_number">No.</label>
                <input type="text" name="trans_number" id="trans_number" class="form-control" value="<?=(!empty($dataRow->trans_number))?$dataRow->trans_number:$trans_number?>" readonly>
            </div>

            <div class="col-md-2 form-group">
                <label for="trans_date">Date</label>
                <input type="date" name="trans_date" id="trans_date" class="form-control" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:getFyDate()?>">
            </div>
            <?php if((empty($party_id)) && empty($lead_id) && empty($dataRow->id)) { ?>
                <div class="col-md-2 form-group">
                    <label for="business_type">Business Type</label>
                    <select  id="business_type" class="form-control modal-select2">
                        <option value="">Select Type</option>
                        <?php
                            foreach($bTypeList as $row):
                                $selected = (!empty($dataRow->business_type) && $dataRow->business_type == $row->type_name)?"selected":"";
                                echo '<option value="'.$row->type_name.'" '.$selected.' data-parent_type="'.$row->parentType.'">'.$row->type_name.'</option>';
                            endforeach;
                        ?>
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label for="party_id">Customer</label>   
                    <select name="party_id" id="party_id" class="form-control modal-select2 req getDisc partyOptions">
                        <option value="">Select Customer</option>
                    </select>
                    <div class="error party_id"></div>          
                </div>
            <?php }else{?> 
                <input type="hidden" name="party_id" id="party_id" value="<?=(!empty($dataRow->party_id))?$dataRow->party_id:(!empty($party_id)?$party_id:"")?>">
            <?php } ?>
            <div class="col-md-4 form-group" <?=!in_array($module_type,[2,3])?'hidden':''?>>
                <label for="doc_no">Reference</label>
                <input type="text" name="doc_no" id="doc_no" class="form-control" value="<?=(!empty($dataRow->doc_no))?$dataRow->doc_no:""?>" <?=!in_array($module_type,[2,3])?'disabled':''?>>
            </div>
            <div class="col-md-4 form-group" <?=!in_array($module_type,[2])?'hidden':''?>>
                <label for="delivery_date">Valid Till</label>
                <input type="date" name="delivery_date" id="delivery_date" class="form-control" value="<?=(!empty($dataRow->delivery_date))?$dataRow->delivery_date:getFyDate()?>" <?=!in_array($module_type,[2])?'disabled':''?>>
            </div>

            <div class="col-md-4 form-group" <?=!in_array($module_type,[2,3])?'hidden':''?>>
                <label for="currency">Currency</label>
                <select name="currency" id="currency" class="form-control modal-select2" >
                    <option value="">Select Currency</option>
                    <?php $i=1; foreach($currencyData as $row):
                        $selected = (!empty($dataRow->currency) && trim($dataRow->currency) == trim($row->currency)) ? "selected" : "";
                        if(empty($dataRow->currency) && trim($row->currency) == "INR"){$selected = "selected";}
                    ?>
                    <option value="<?=trim($row->currency)?>" <?=$selected?> ><?=$row->currency?> [<?=$row->code2000?> - <?=$row->currency_name?>]</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 form-group" <?=!in_array($module_type,[2,3])?'hidden':''?>>
                <label for="order_file">Attachment</label>
                <div class="input-group">
                    <input type="file" name="order_file" id="order_file" <?=(!empty($dataRow->order_file)?'style="width:70%"':'')?> class="form-control">
                    <div class="input-group-append">
                        <?php
                        if(!empty($dataRow->order_file)){
                            ?>
                            <a href="<?=base_url('assets/uploads/sales_order/'.$dataRow->order_file)?>" class="btn btn-outline-primary" download><i class="fa fa-arrow-down"></i></a>
                            <?php
                        }
                        ?>
                    </div>                                        
                </div>                           
            </div>
        </div>

        <hr>
        <div class="row" id="itemForm">

            <input type="hidden"  id="id" value="">
            <input type="hidden" id="row_index" value="">
			<input type="hidden"  id="ref_id" value="" >

            <div class="<?=!in_array($module_type,[2,3])?'col-md-9':'col-md-6'?> form-group">
                <label for="item_id">Product Name</label>
                <select id="item_id" class="form-control modal-select2 req getDisc">
                    <option value="">Select Product Name</option>
                    <?=getItemListOption($itemList)?>
                </select>
            </div>
            <?php if($module_type == 3): ?>
            <div class="col-md-3 form-group">
                <label for="order_unit">Order Unit</label>
                <select id="order_unit" class="form-control modal-select2">
                    <option value="">Select Order Unit</option>
                </select>
            </div>
            <?php endif; ?>

            <div class="col-md-3 form-group">
                <label for="qty">Quantity</label>
                <input type="text"  id="qty" class="form-control floatOnly req" value="0">
            </div>
            
            <div class="<?=!in_array($module_type,[1,2])?'col-md-4':'col-md-3'?> form-group" <?=!in_array($module_type,[2,3])?'hidden':''?>>
                <label for="price">Price</label>
                <input type="text" id="price" class="form-control floatOnly req calculateRow" value="0"/>
            </div>

            <div class="<?=!in_array($module_type,[1,2])?'col-md-4':'col-md-2'?> form-group" <?=!in_array($module_type,[2,3])?'hidden':''?>>
                <label for="regular_disc">Disc. (%)</label>
                <input type="text" id="regular_disc" class="form-control floatOnly req calculateRow" value="0" />
            </div>

            <?php if($module_type == 3):
                    if($this->kgprice == 2 ): ?>  
                        <div class="col-md-4 form-group">
                            <label for="kg_price">KG Price</label>
                            <input type="text" id="kg_price" class="form-control floatOnly req calculateRow" value="0" />
                        </div>
            <?php endif; endif; ?>
            
            <div class="<?=in_array($module_type,[1,3])?'col-md-10':'col-md-8'?> form-group">
                <label for="item_remark">Remark</label>
                <input type="text" id="item_remark" class="form-control" value="" />
            </div>

            <div class="col-md-2">
				<button type="button" class="btn btn-info btn-block waves-effect float-right addItem mt-25"><i class="fa fa-plus"></i> Add </button>
			</div>

        </div>

        <hr>
        <div class="row">
            <div class="error itemData"></div>
            <div class="table-responsive">
                <table id="salesOrderItems" class="table table-striped table-borderless">
                    <thead class="thead-info">
                        <tr>
                            <th style="width:5%;">#</th>
                            <th style="width:25%;">Item Name</th>
                            <th style="width:15%;">Qty.</th>
                            <th style="width:15%;" <?=!in_array($module_type,[2,3])?'hidden':''?>>Price</th>
                            <th style="width:25%;">Remark</th>
                            <th class="text-center" style="width:15%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="tempItem" class="temp_item">
                        <tr id="noData">
                            <td colspan="6" class="text-center">No data available in table</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <?php  if(in_array($module_type,[2,3])){ ?>
        
            <hr>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-striped table-borderless">
                        <thead class="thead-info">
                            <tr>
                                <th style="width:30%">Description</th>
                                <th style="width:20%">Percentage</th>
                                <th style="width:30%">Amount</th>
                                <th style="width:20%">GST(%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Discount</td>
                                <td><input type="number" name="disc_per" id="disc_per" class="form-control floatOnly" min="0" max="100" value="<?=(!empty($dataRow->disc_per) ? $dataRow->disc_per : (!empty($fromRefItemList->disc_per) ? $fromRefItemList->disc_per : 0) )?>" /></td>
                                <td><input type="number" name="disc_per_amt" id="disc_per_amt" class="form-control" value="<?=(!empty($dataRow->disc_per_amt) ? $dataRow->disc_per_amt : (!empty($fromRefItemList->disc_per_amt) ? $fromRefItemList->disc_per_amt : 0) )?>" /></td>
                                <td>
                                    <select name="disc_gst" id="disc_gst" class="form-control modal-select2">
                                        <?php
                                        foreach ($this->gstPer as $key => $row) {
                                            $selected = (!empty($dataRow->disc_gst) && $key == $dataRow->disc_gst) ? "selected" : ((!empty($fromRefItemList->disc_gst) && $key == $fromRefItemList->disc_gst) ? "selected" : "") ;
                                            echo "<option value='".$key."' ".$selected.">".$row."</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Transport Charge</td>
                                <td><input type="number" name="transport_chr" id="transport_chr" class="form-control floatOnly" min="0" max="100" value="<?=(!empty($dataRow->transport_chr) ? $dataRow->transport_chr : (!empty($fromRefItemList->transport_chr) ? $fromRefItemList->transport_chr : 0) )?>" /></td>
                                <td><input type="number" name="transport_amt" id="transport_amt" class="form-control floatOnly" value="<?=(!empty($dataRow->transport_amt) ? $dataRow->transport_amt : (!empty($fromRefItemList->transport_amt) ? $fromRefItemList->transport_amt : 0) )?>" /></td>
                                <td>
                                    <select name="transport_gst" id="transport_gst" class="form-control modal-select2">
                                        <?php
                                        foreach ($this->gstPer as $key => $row) {
                                            $selected = (!empty($dataRow->transport_gst) && $key == $dataRow->transport_gst) ? "selected" : ((!empty($fromRefItemList->transport_gst) && $key == $fromRefItemList->transport_gst) ? "selected" : "") ;
                                            echo "<option value='".$key."' ".$selected.">".$row."</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Packing & forwarding Charge</td>
                                <td><input type="number" name="pack_forw_chr" id="pack_forw_chr" class="form-control floatOnly" min="0" max="100" value="<?=(!empty($dataRow->pack_forw_chr) ? $dataRow->pack_forw_chr : (!empty($fromRefItemList->pack_forw_chr) ? $fromRefItemList->pack_forw_chr : 0) )?>" /></td>
                                <td><input type="number" name="pack_forw_amt" id="pack_forw_amt" class="form-control floatOnly" value="<?=(!empty($dataRow->pack_forw_amt) ? $dataRow->pack_forw_amt : (!empty($fromRefItemList->pack_forw_amt) ? $fromRefItemList->pack_forw_amt : 0) )?>" /></td>
                                <td>
                                    <select name="pack_forw_gst" id="pack_forw_gst" class="form-control modal-select2">
                                        <?php
                                        foreach ($this->gstPer as $key => $row) {
                                            $selected = (!empty($dataRow->pack_forw_gst) && $key == $dataRow->pack_forw_gst) ? "selected" : ((!empty($fromRefItemList->pack_forw_gst) && $key == $fromRefItemList->pack_forw_gst) ? "selected" : "") ;
                                            echo "<option value='".$key."' ".$selected.">".$row."</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Other Charge</td>
                                <td><input type="number" name="other_chr" id="other_chr" class="form-control floatOnly" min="0" max="100" value="<?=(!empty($dataRow->other_chr) ? $dataRow->other_chr : (!empty($fromRefItemList->other_chr) ? $fromRefItemList->other_chr : 0))?>" /></td>
                                <td><input type="number" name="other_amt" id="other_amt" class="form-control floatOnly" value="<?=(!empty($dataRow->other_amt) ? $dataRow->other_amt : (!empty($fromRefItemList->other_amt) ? $fromRefItemList->other_amt : 0) )?>" /></td>
                                <td>
                                    <select name="other_gst" id="other_gst" class="form-control modal-select2">
                                        <?php
                                        foreach ($this->gstPer as $key => $row) {
                                            $selected = (!empty($dataRow->other_gst) && $key == $dataRow->other_gst) ? "selected" : ((!empty($fromRefItemList->other_gst) && $key == $fromRefItemList->other_gst) ? "selected" : "") ;
                                            echo "<option value='".$key."' ".$selected.">".$row."</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } ?>
    </div>
</form>

<script src="<?php echo base_url(); ?>assets/js/custom/sales-form.js?v=<?= time() ?>"></script>
<?php
if(!empty($fromRefItemList->itemList)):
    foreach($fromRefItemList->itemList as $row):
        if($from_entry_type == 1 || ($from_entry_type == 2 && $row->approve_by > 0)):
            $row->row_index = "";
            $row->ref_id = $row->id;
            $row->packing_qty = $row->qty;
            $row->from_entry_type = $from_entry_type;
            $row->item_name = (!empty($row->item_code) ? "[ ".$row->item_code." ] ".$row->item_name : $row->item_name);
            unset($row->id);
            echo '<script>AddRow('.json_encode($row).');</script>';
        endif;
    endforeach;
endif;

if(!empty($dataRow->itemList)):
    foreach($dataRow->itemList as $row):
        $row->row_index = "";
		$row->item_name = (!empty($row->item_code) ? "[ ".$row->item_code." ] ".$row->item_name : $row->item_name);
        echo '<script>AddRow('.json_encode($row).');</script>';
    endforeach;
endif;
?>