<?php $this->load->view('app/includes/header'); ?>
	<!-- Header -->
	<header class="header">
		<div class="main-bar bg-primary-2">
			<div class="container">
				<div class="header-content">
					<div class="left-content">
						<a href="javascript:void(0);" class="back-btn">
							<svg height="512" viewBox="0 0 486.65 486.65" width="512"><path d="m202.114 444.648c-8.01-.114-15.65-3.388-21.257-9.11l-171.875-171.572c-11.907-11.81-11.986-31.037-.176-42.945.058-.059.117-.118.176-.176l171.876-171.571c12.738-10.909 31.908-9.426 42.817 3.313 9.736 11.369 9.736 28.136 0 39.504l-150.315 150.315 151.833 150.315c11.774 11.844 11.774 30.973 0 42.817-6.045 6.184-14.439 9.498-23.079 9.11z"></path><path d="m456.283 272.773h-425.133c-16.771 0-30.367-13.596-30.367-30.367s13.596-30.367 30.367-30.367h425.133c16.771 0 30.367 13.596 30.367 30.367s-13.596 30.367-30.367 30.367z"></path>
							</svg>
						</a>
						<h5 class="title mb-0 text-nowrap">Add Other Party</h5>
					</div>
					<div class="mid-content"> </div>
					<div class="right-content headerSearch">
					    <div class="jpsearch" id="qs1">
							<!--<input type="text" class="input quicksearch qs1" placeholder="Search Here ..." />
							<button class="search-btn"><i class="fas fa-search"></i></button>-->
						</div>
					</div>
				</div>
			</div>
		</div>
	</header>
	<!-- Header -->
	
    <!-- Page Content -->
    <div class="page-content">
        <div class="container bottom-content shop-cart-wrapper">
            <form id="otherPartyForm">
                <input type="hidden" name="id" id="id" value=""/>
                <input type="hidden" name="ref_id" id="ref_id" value="<?= (!empty($ref_id) ? $ref_id : 0);?>"/>
                <div class="row mb-3">
                    <div class="col-12 mb-2">
						<label for="party_name">Party Name <span class="text-danger">*</span></label>
						<input type="text" name="party_name" id="party_name" class="form-control req" value="">
						<div class="text-danger party_name"></div>
					</div>
                    <div class="col-12 mb-2">
						<label for="party_contact">Contact No. <span class="text-danger">*</span></label>
						<input type="text" name="party_contact" id="party_contact" class="form-control numericOnly req" value="">
						<div class="text-danger party_contact"></div>
					</div>
					<div class="col-12">
						<label for="party_type">Stage <span class="text-danger">*</span></label>
						<select name="party_type" id="party_type" class="form-control select2">
							<option value="1">Customer</option>
							<option value="2">Plumber</option>
						</select>
					</div>
                </div>
				<div class="col-md-12 form-group text-right float-end">
					<?php
						$param = "{'formId':'otherPartyForm','fnsave':'saveOtherParty','controller':'parties','res_function':'getOtherPartyHtml'}";
					?>
					<button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form float-right" onclick="customStore(<?=$param?>)" style="height:36px"><i class="fa fa-check"></i>&nbsp;Save</button>
				</div>
            </form>
        <br/><br/><hr>
		<div class="row">
			<div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
				<table id="otherPartyTbl" class="table table-bordered align-items-center">
					<thead class="thead-info">
						<tr>
							<th style="width:5%;">#</th>
							<th>Party Name</th>
							<th>Contact No.</th>
							<th>Type</th>
							<th class="text-center" style="width:10%;">Action</th>
						</tr>
					</thead>
					<tbody id="otherParties">
					</tbody>
				</table>
			</div>        
		</div>
        </div>
		<div class="footer fixed ">
			<div class="container">
				<div class="footer-btn d-flex align-items-center">
                    <a href="<?= base_url("app/lead/customerDesk");?>" class="btn btn-dark btn-block flex-1">Close</a>
                </div>
			</div>
		</div>	
    </div>
</div>  

<?php $this->load->view('app/includes/footer'); ?>

<script>
	var tbodyData = false;
    $(document).ready(function(){
		$(".select2").select2();
		
		$(document).on("keypress",".numericOnly",function (e) {
			if (String.fromCharCode(e.keyCode).match(/[^0-9]/g)) return false;
		});
		
		if(!tbodyData){
			var postData = {'postData':{'ref_id':$("#ref_id").val()},'table_id':"otherPartyTbl",'tbody_id':'otherParties','tfoot_id':'','fnget':'otherPartiesHtml','controller':'parties'};
			getTransHtml(postData);
			tbodyData = true;
		}
    });
	
	function customStore(postData){
		setPlaceHolder();

		var formId = postData.formId;
		var fnsave = postData.fnsave || "save";
		var tableId = postData.table_id || "";
		var controllerName = postData.controller || controller;

		var form = $('#'+formId)[0];
		var fd = new FormData(form);
		var resFunctionName = $("#"+formId).data('res_function') || "";
		$(".btn-save").attr("disabled", true);
		$.ajax({
			url: base_url + controllerName + '/' + fnsave,
			data:fd,
			type: "POST",
			processData:false,
			contentType:false,
			dataType:"json",
		}).done(function(data){
			$(".btn-save").removeAttr("disabled");
			if(resFunctionName != ""){
				window[resFunctionName](data,formId);
			}else{
				if(data.status==1){
					$('#'+formId)[0].reset();
					Swal.fire({ icon: 'success', title: data.message}).then((result) => {if(tableId != ""){window.location.reload();}else{
						var postData = {'postData':{'ref_id':$("#ref_id").val()},'table_id':"otherPartyTbl",'tbody_id':'otherParties','tfoot_id':'','fnget':'otherPartiesHtml','controller':'parties'};
						getTransHtml(postData);
					}});
					$(".modal-select2").select2();
				}else{
					if(typeof data.message === "object"){
						$(".error").html("");
						$.each( data.message, function( key, value ) {$("."+key).html(value);});
					}else{
						Swal.fire({ icon: 'error', title: data.message });
					}			
				}		
			}			
		});
	}
	
	function getOtherPartyHtml(data,formId="otherPartyForm"){
		if(data.status==1){
			$('#'+formId)[0].reset();
			var postData = {'postData':{'ref_id':$("#ref_id").val()},'table_id':"otherPartyTbl",'tbody_id':'otherParties','tfoot_id':'','fnget':'otherPartiesHtml','controller':'parties'};
			getTransHtml(postData);
		}else{
			if(typeof data.message === "object"){
				$(".error").html("");
				$.each( data.message, function( key, value ) {$("."+key).html(value);});
			}else{
				Swal.fire({ icon: 'error', title: data.message });
			}			
		}	
	}
	
	function getTransHtml(data){
		var postData = data.postData || {};
		var fnget = data.fnget || "";
		var controllerName = data.controller || controller;
		var resFunctionName = data.res_function || "";

		var table_id = data.table_id || "";
		var thead_id = data.thead_id || "";
		var tbody_id = data.tbody_id || "";
		var tfoot_id = data.tfoot_id || "";	

		if(thead_id != ""){
			$("#"+table_id+" #"+thead_id).html(data.thead);
		}
		
		$.ajax({
			url: base_url + controllerName + '/' + fnget,
			data:postData,
			type: "POST",
			dataType:"json",
			beforeSend: function() {
				if(table_id != ""){
					var columnCount = $('#'+table_id+' thead tr').first().children().length;
					$("#"+table_id+" #"+tbody_id).html('<tr><td colspan="'+columnCount+'" class="text-center">Loading...</td></tr>');
				}
			},
		}).done(function(res){
			if(resFunctionName != ""){
				window[resFunctionName](response);
			}else{
				$("#"+table_id+" #"+tbody_id).html('');
				$("#"+table_id+" #"+tbody_id).html(res.tbodyData);

				if(tfoot_id != ""){
					$("#"+table_id+" #"+tfoot_id).html('');
					$("#"+table_id+" #"+tfoot_id).html(res.tfootData);
				}
			}
		});
	}
	
	function trash(data){
		var controllerName = data.controller || controller;
		var fnName = data.fndelete || "delete";
		var msg = data.message || "Record";
		var send_data = data.postData;
		var tableId = data.table_id || "";
		var resFunctionName = data.res_function || "";
		
		Swal.fire({
			title: 'Are you sure?',
			text: "You won't be able to revert this!",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, delete it!',
		}).then(function(result) {
			if (result.isConfirmed)
			{
				$.ajax({
					url: base_url + controllerName + '/' + fnName,
					data: send_data,
					type: "POST",
					dataType:"json",
				}).done(function(response){
					if(resFunctionName != ""){
						window[resFunctionName](response);
					}else{
						if(response.status==0){
							Swal.fire( 'Sorry...!', response.message, 'error' );
						}else{
							Swal.fire( 'Deleted!', response.message, 'success' ).then((result) => {if(tableId != ""){window.location.reload();}else{
								var postData = {'postData':{'ref_id':$("#ref_id").val()},'table_id':"otherPartyTbl",'tbody_id':'otherParties','tfoot_id':'','fnget':'otherPartiesHtml','controller':'parties'};
								getTransHtml(postData);
							}});
						}	
					}
				});
				Swal.fire( 'Deleted!', 'Your file has been deleted.', 'success' );
			}
		});
		
	}
</script>
