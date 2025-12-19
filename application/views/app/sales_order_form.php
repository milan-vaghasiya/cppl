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
						<h5 class="title mb-0 text-nowrap">Create Order</h5>
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
            <form id="orderForm">
                <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id)?$dataRow->id:'')?>"/>
                <div class="row mb-3">
                    <div class="col-12">
						<label for="trans_date">Invoice Date <span class="text-danger">*</span></label>
						<input type="date" name="trans_date" id="trans_date" class="form-control req" value="<?= (!empty($dataRow->trans_date) ? $dataRow->trans_date : date("Y-m-d"))?>">
						<div class="text-danger trans_date"></div>
					</div>
                    <div class="col-12">
						<label for="inv_no">Invoice No <span class="text-danger">*</span></label>
						<input type="text" name="inv_no" id="inv_no" class="form-control req" value="<?= (!empty($dataRow->inv_no) ? $dataRow->inv_no : "")?>">
						<div class="text-danger inv_no"></div>
					</div>
                    <div class="col-12">
						<label for="party_type">Stage <span class="text-danger">*</span></label>
						<select name="party_type" id="party_type" class="form-control select2">
							<option value="">Select Stage</option>
							<?php
								if(!empty($stageList)){
									foreach($stageList as $row) {
										$selected = ((!empty($dataRow) && $dataRow->party_type == $row->id) ? "selected" : "");
										echo '<option value="'.$row->id.'" '.$selected.'>'.$row->stage_type.'</option>';
									}
								}
							?>
						</select>
					</div>
                    <div class="col-12 mt-2">
						<label for="party_id">Party <span class="text-danger">*</span></label>
						<select name="party_id" id="party_id" class="form-control req select2">
							<?= $leadOptions;?>
						</select>
						<div class="text-danger party_id"></div>
                    </div>
					<div class="col-12 mt-2">
						<label for="distributor_id">Distributer <span class="text-danger">*</span></label>
						<select name="distributor_id" id="distributor_id" class="form-control req select2">
							<option value="">Select Distributor</option>
							<?php
								if(!empty($getDealerList)){
									foreach($getDealerList as $row){
										$selected = ((!empty($dataRow->distributor_id) && $dataRow->distributor_id == $row->id) ? "selected" : "");
										echo '<option value="'.$row->id.'" '.$selected.'>'.(!empty($row->emp_code) ? '['.$row->emp_code.'] '.$row->emp_name : $row->emp_name).'</option>';
									}
								}
							?>
						</select>
						<div class="text-danger distributor_id"></div>
					</div>
					<div class="col-12 mt-2">
						<label for="net_amount">Amount <span class="text-danger">*</span></label>
						<input type="text" name="net_amount" id="net_amount" class="form-control floatOnly req" value="<?=(!empty($dataRow->net_amount)?$dataRow->net_amount:'')?>">
						<div class="text-danger net_amount"></div>
                    </div>
                </div>
                
            </form> 
        </div>
        
		<div class="footer fixed ">
			<div class="container">
				<div class="footer-btn d-flex align-items-center">
                    <?php
                        $param = "{'formId':'orderForm','fnsave':'saveOrder','controller':'app/salesOrder/'}";
                    ?>
                    <a href="javascript:void(0)" class="btn btn-primary btn-block flex-1" onclick="store(<?=$param?>)">Save</a>
                </div>
			</div>
		</div>	
    </div>
</div>  

<?php $this->load->view('app/includes/footer'); ?>

<script>
    $(document).ready(function(){
		$(".select2").select2();
		
		$(document).on("change","#party_type",function(){
			var party_type = $("#party_type").val();
			$.ajax({
				url: base_url  + 'app/salesOrder/getLeadOptions',
				data:{party_type:party_type},
				type: "POST",
				dataType:"json",
			}).done(function(response){
				$("#party_id").html(response.htmlData);
			});
		});
    });
	
	function store(postData){
		var formId = postData.formId;
		var fnsave = postData.fnsave || "save";
		var controllerName = postData.controller || controller;

		var form = $('#'+formId)[0];
		var fd = new FormData(form);
		$(".btn-save").attr("disabled", true);
		$.ajax({
			url: base_url + controllerName +fnsave,
			data:fd,
			type: "POST",
			processData:false,
			contentType:false,
			dataType:"json",
		}).done(function(data){
			$(".btn-save").removeAttr("disabled");
			if(data.status==1){
				$('#'+formId)[0].reset(); 
				Swal.fire({
					title: "Success",
					text: data.message,
					icon: "success",
					showCancelButton: false,
					confirmButtonColor: "#3085d6",
					cancelButtonColor: "#d33",
					confirmButtonText: "Ok!"
					}).then((result) => {
						window.location = base_url + 'app/salesOrder/order';
					});
			}else{
				if(typeof data.message === "object"){
					$(".error").html("");
					$.each( data.message, function( key, value ) {$("."+key).html(value);});
				}else{
					Swal.fire( 'Sorry...!', data.message, 'error' );

					
				}			
			}				
		});
	}
</script>
