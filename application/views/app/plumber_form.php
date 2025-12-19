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
            <form id="plumberForm">
                <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id)?$dataRow->id:'')?>"/>
                <div class="row">
					<div class="col-12 mb-2">
						<label for="party_id">Customer Name <span class="text-danger">*</span></label>
						<select name="party_id" id="party_id" class="form-control select2">
							<option value="">Select Customer</option>
							<?php
								if(!empty($partyList)){
									foreach($partyList as $row) {
										$selected = ((!empty($dataRow) && $dataRow->party_id == $row->id) ? "selected" : "");
										echo '<option value="'.$row->id.'" '.$selected.'>'.$row->party_name.'</option>';
									}
								}
							?>
						</select>
						<div class="text-danger party_id"></div>
					</div>
                    <div class="col-12 mb-2">
						<label for="name">Plumber Name <span class="text-danger">*</span></label>
						<input type="text" name="name" id="name" class="form-control req" value="<?= (!empty($dataRow->name) ? $dataRow->name : "");?>">
						<div class="text-danger name"></div>
					</div>
                    <div class="col-12 mb-2">
						<label for="phone_no">Phone No.</label>
						<input type="text" name="phone_no" id="phone_no" class="form-control floatOnly req" value="<?= (!empty($dataRow->phone_no) ? $dataRow->phone_no : "")?>">
					</div>
					<div class="col-12 mb-2">
						<label for="adhar_no">Aadhar No</label>
						<input type="text" name="adhar_no" id="adhar_no" class="form-control req" value="<?= (!empty($dataRow->adhar_no) ? $dataRow->adhar_no : "")?>">
					</div>
					<div class="col-12">
						<label for="address">Address</label>
						<textarea name="address" id="address" class="form-control " rows="2" placeholder="Address" autocomplete="off"><?= (!empty($dataRow->address) ? $dataRow->address : "")?></textarea>
					</div>
                </div>
                
            </form> 
        </div>
        
		<div class="footer fixed ">
			<div class="container">
				<div class="footer-btn d-flex align-items-center">
                    <?php
                        $param = "{'formId':'plumberForm','controller':'plumber/'}";
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
						window.location = base_url + 'app/plumber';
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
