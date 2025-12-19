var itemCount = 0;
$(document).ready(function(){
	calculateAmount();

	$(document).on('click','.addItem',function(){
        var formData = {};

		var order_unit = parseFloat($("#order_unit").val()) || 1;
        var qty = parseFloat($("#qty").val()) || 0;
        qty = qty * order_unit;
        var price = parseFloat($("#price").val()) || 0;
		var gst_per = parseFloat($("#item_id :selected").data('gst_per')) || 0;
		var wt_pcs = parseFloat($("#item_id :selected").data('wt_pcs')) || 0;
        var disc_per = parseFloat($("#regular_disc").val()) || 0;
        var kg_price = parseFloat($("#kg_price").val()) || 0;

        var disc_price = 0; var net_price = 0; var net_amt = 0; var okp = 0;var aqp = 0;

        if(disc_per != "" || disc_per != "0"){
            disc_price = ((price * disc_per) / 100).toFixed(2);
        }
        if(kg_price != "" || kg_price != "0"){
            if(wt_pcs>0)
            {
                aqp = kg_price * wt_pcs;
                disc_price = price - aqp;
            }
        }
        net_price = price - disc_price;
        net_amt = qty * net_price;
        var amount = qty*price;
        var tax_amount = ((gst_per * net_amt) / 100).toFixed(2);

		formData.id = $("#id").val();
        formData.row_index = $("#row_index").val();
        formData.item_id = $("#item_id").val();
        formData.item_name = $("#item_id :selected").text();
        formData.qty = qty;
        formData.packing_qty = $("#qty").val();
        formData.price = price;
        formData.order_unit = order_unit;
        formData.item_remark = $("#item_remark").val();
        formData.from_entry_type = $("#from_entry_type").val();
        formData.ref_id = $("#ref_id").val();
        formData.wt_pcs = wt_pcs;
        formData.amount = amount;
        formData.gst_per = gst_per;
        formData.gst_amount = tax_amount;
        formData.disc_per = disc_per;
        formData.disc_amount = disc_price;
        formData.taxable_amount = net_amt;
        formData.net_amount = net_amt+tax_amount;
        formData.kg_price = kg_price; //|| price;
	
        $(".error").html("");
        if(formData.item_id == ""){ 
            $('.item_id').html("Item Name is required.");
        }
        if(formData.qty == "" || parseFloat(formData.qty) == 0){ 
            $('.qty').html("Qty is required.");
        }
		if($("#module_type").val() != 1){
			if(formData.price == "" || parseFloat(formData.price) == 0){ 
				$('.price').html("Price is required.");
			}
		}		
        
        var errorCount = $('.error:not(:empty)').length;
		if(errorCount == 0){           
            AddRow(formData);
			$("#itemForm input").each(function(){
				if($(this).data('resetval')){$(this).val($(this).data('resetval'));}else{$(this).val('');}
			});
			$("#itemForm").find('select').val('');
			$("#itemForm").find('textarea').val('');
			initSelect2("right_modal_lg");
        }
    });
	
	$(document).on('change','#item_id',function(){
        var item_id = $(this).val();
		var price = ($("#item_id :selected").data('price')) || 0;
		$("#price").val(price);

		$.ajax({
            url : base_url + 'finishGoods/getOrderUnitOptions',
            type : 'post',
            data : {item_id:item_id},
            dataType: 'json'
        }).done(function(res){
            $("#order_unit").html("");
            $("#order_unit").html(res.options);
        });
    });

	$(document).on('change','#business_type',function(){
        var business_type = $(this).val();
        getPartyList({"business_type":business_type});
        initSelect2("right_modal_lg");
        
    });

	$(document).on('change','.getDisc',function(){
        var disc_structure = $("#party_id :selected").data('discount_structure');
        var category_id = $("#item_id :selected").data('category_id');

        if(category_id){
            $.ajax({
                url : base_url + 'lead/getDefaultDiscount',
                type : 'post',
                data : {disc_structure:disc_structure, category_id:category_id},
                dataType: 'json'
            }).done(function(res){
                $("#regular_disc").val("");
                $("#regular_disc").val(res.regular_disc);
            });
        }
        
    });
    
    $(document).on('keyup change','#disc_per, #transport_chr, #pack_forw_chr, #other_chr',function(){
		var disc_per = $('#disc_per').val();
		var transport_chr = $('#transport_chr').val();
		var pack_forw_chr = $('#pack_forw_chr').val();
		var other_chr = $('#other_chr').val();
		var total_amt = 0;
		$('.taxable_amt').each(function(index, value){
			total_amt += parseFloat(this.value);
		});
		var discAmt = 0;var transAmt = 0;var pfAmt = 0;var otherAmt = 0;
		if(disc_per != 0 && disc_per != '') { discAmt = parseFloat(total_amt * disc_per/100).toFixed(2); total_amt -= parseFloat(discAmt); }
		if(transport_chr != 0 && transport_chr != '') { transAmt = parseFloat(total_amt * transport_chr/100).toFixed(2); total_amt -= parseFloat(transAmt); }
		if(pack_forw_chr != 0 && pack_forw_chr != '') { pfAmt = parseFloat(total_amt * pack_forw_chr/100).toFixed(2); total_amt += parseFloat(pfAmt); }
		if(other_chr != 0 && other_chr != '') { otherAmt = parseFloat(total_amt * other_chr/100).toFixed(2); total_amt += parseFloat(otherAmt); }
		
		$('#disc_per_amt').val(discAmt);
		$('#transport_amt').val(transAmt);
		$('#pack_forw_amt').val(pfAmt);
		$('#other_amt').val(otherAmt);
		
	});
});

function AddRow(data) {
    var tblName = "salesOrderItems";

    //Remove blank line.
	$('table#'+tblName+' tr#noData').remove();

	//Get the reference of the Table's TBODY element.
	var tBody = $("#" + tblName + " > TBODY")[0];

	//Add Row.
	if (data.row_index != "") {
		var trRow = data.row_index;
		//$("tr").eq(trRow).remove();
		$("#" + tblName + " tbody tr:eq(" + trRow + ")").remove();
	}
	var ind = (data.row_index == "") ? -1 : data.row_index;
	row = tBody.insertRow(ind);
	$(row).attr('id',itemCount);

    //Add index cell
	var countRow = (data.row_index == "") ? ($('#' + tblName + ' tbody tr:last').index() + 1) : (parseInt(data.row_index) + 1);
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style", "width:5%;");

    var itemIdInput = $("<input/>",{type:"hidden",name:"itemData["+itemCount+"][item_id]", class:"item_id",value:data.item_id});
	var transIdInput = $("<input/>",{type:"hidden",name:"itemData["+itemCount+"][id]",value:data.id});
	var refIdInput = $("<input/>",{type:"hidden",name:"itemData["+itemCount+"][ref_id]",value:data.ref_id});
	var formEntryTypeInput = $("<input/>",{type:"hidden",name:"itemData["+itemCount+"][from_entry_type]",value:data.from_entry_type});
	cell = $(row.insertCell(-1));
	cell.html(data.item_name);
	cell.append(itemIdInput);
	cell.append(transIdInput);
	cell.append(formEntryTypeInput);
	cell.append(refIdInput);
	
	var qtyInput = $("<input/>",{type:"hidden",name:"itemData["+itemCount+"][qty]",value:data.qty});
	var packQtyInput = $("<input/>",{type:"hidden",name:"itemData["+itemCount+"][packing_qty]",value:data.packing_qty});
	cell = $(row.insertCell(-1));
	cell.html(data.qty);
	cell.append(qtyInput);
	cell.append(packQtyInput);

	var priceInput = $("<input/>",{type:"hidden",name:"itemData["+itemCount+"][price]",value:data.price});
	var ordUnitInput = $("<input/>",{type:"hidden",name:"itemData["+itemCount+"][order_unit]",value:data.order_unit});
	var wtPcsInput = $("<input/>",{type:"hidden",name:"itemData["+itemCount+"][wt_pcs]",value:data.wt_pcs});
	var gstPerInput = $("<input/>",{type:"hidden",name:"itemData["+itemCount+"][gst_per]",value:data.gst_per});
	var gstAmountInput = $("<input/>",{type:"hidden",name:"itemData["+itemCount+"][gst_amount]",value:data.gst_amount});
	var amountInput = $("<input/>",{type:"hidden",name:"itemData["+itemCount+"][amount]",value:data.amount});
	var taxableAmtInput = $("<input/>",{type:"hidden",name:"itemData["+itemCount+"][taxable_amount]",class:'taxable_amt',value:data.taxable_amount});
	var netAmtInput = $("<input/>",{type:"hidden",name:"itemData["+itemCount+"][net_amount]",value:data.net_amount});
	var discPerInput = $("<input/>",{type:"hidden",name:"itemData["+itemCount+"][disc_per]",value:data.disc_per});
	var discAmtInput = $("<input/>",{type:"hidden",name:"itemData["+itemCount+"][disc_amount]",value:data.disc_amount});
	var kgPriceInput = $("<input/>",{type:"hidden",name:"itemData["+itemCount+"][kg_price]",value:data.kg_price});
	cell = $(row.insertCell(-1));
	cell.html(data.price);
	cell.append(priceInput);
	cell.append(ordUnitInput);
	cell.append(gstPerInput);
	cell.append(amountInput);
	cell.append(taxableAmtInput);
	cell.append(netAmtInput);
	cell.append(wtPcsInput);
	cell.append(gstAmountInput);
	cell.append(discPerInput);
	cell.append(discAmtInput);
	cell.append(kgPriceInput);
	cell.append('<div class="error price'+itemCount+'"></div>');
	if($("#module_type").val() == 1){cell.attr("hidden", true);}

	var itemRemarkInput = $("<input/>",{type:"hidden",name:"itemData["+itemCount+"][item_remark]",value:data.item_remark});
	cell = $(row.insertCell(-1));
	cell.html(data.item_remark);
	cell.append(itemRemarkInput);

    //Add Button cell.
	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="mdi mdi-trash-can-outline"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "Remove(this);");
	btnRemove.attr("style", "margin-left:4px;");
	btnRemove.attr("class", "btn btn-sm btn-outline-danger waves-effect waves-light");
	
	var btnEdit = $('<button><i class="mdi mdi-square-edit-outline"></i></button>');
	btnEdit.attr("type", "button");
	btnEdit.attr("onclick", "EditRow(" + JSON.stringify(data) + ",this);");
	btnEdit.attr("class", "btn btn-sm btn-outline-warning waves-effect waves-light");

	cell.append(btnEdit);
	cell.append(btnRemove);
	cell.attr("class", "text-center");
	cell.attr("style", "width:10%;");
	
	$('#disc_per').trigger('change');
	itemCount++;
}

function EditRow(data, button) {
	var row_index = $(button).closest("tr").index();
	$("#right_modal_lg").modal('show');
	$(".btn-close").hide();
	$.each(data, function (key, value) {
		//$("#right_modal_lg #" + key).val(value);
		if(key != "disc_per"){			
			$("#right_modal_lg #" + key).val(value);
		}
	});
	$("#id").val(data.id);
	$("#qty").val(data.packing_qty);
	$("#regular_disc").val(data.disc_per);
	
	initSelect2('right_modal_lg');
	
	//$("#item_id").trigger('change');
	
	$.ajax({
        url : base_url + 'finishGoods/getOrderUnitOptions',
        type : 'post',
        data : {item_id:data.item_id,order_unit:data.order_unit},
        dataType: 'json'
    }).done(function(res){
        $("#order_unit").html("");
        $("#order_unit").html(res.options);
    });
    
	setTimeout(function(){ $("#order_unit").val(data.order_unit); initSelect2('right_modal_lg'); }, 100);
	$("#right_modal_lg #row_index").val(row_index);
}

function Remove(button) {
    var tableId = "salesOrderItems";
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#"+tableId)[0];
	table.deleteRow(row[0].rowIndex);
	$('#'+tableId+' tbody tr td:nth-child(1)').each(function (idx, ele) {
		ele.textContent = idx + 1;
	});
	var countTR = $('#'+tableId+' tbody tr:last').index() + 1;
	if (countTR == 0) {
		$("#tempItem").html('<tr id="noData"><td colspan="14" align="center">No data available in table</td></tr>');
	}
	$('#disc_per').trigger('change');
}

function resSaveOrder(data,formId){
    if(data.status==1){
        $('#'+formId)[0].reset();
		Swal.fire({ icon: 'success', title: data.message});
        window.location = base_url + controller;
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
			Swal.fire({ icon: 'error', title: data.message });
        }			
    }	
}

function calculateAmount(){
	var amountArray = $(".amount").map(function(){return $(this).val();}).get();
	var amountSum = 0;
	$.each(amountArray,function(){amountSum += parseFloat(this) || 0;});

	var taxAmountArray = $(".taxAmount").map(function(){return $(this).val();}).get();
	var taxAmountSum = 0;
	$.each(taxAmountArray,function(){taxAmountSum += parseFloat(this) || 0;});

	var totalAmt = amountSum+taxAmountSum;
	$("#amount").html(amountSum);
	$("#taxable_amount").html(taxAmountSum);
	$("#net_amount").html(totalAmt);
}
