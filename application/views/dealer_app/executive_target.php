<?php $this->load->view('dealer_app/includes/header'); ?>
    <!-- Header -->
	<header class="header">
		<div class="main-bar bg-primary-2">
			<div class="container">
				<div class="header-content">
					<div class="left-content">
						<a href="javascript:void(0);" class="menu-toggler me-2">
    						<!-- <i class="fa-solid fa-bars font-16"></i> -->
    						<svg class="text-dark" xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 0 24 24" width="30px" fill="#000000"><path d="M13 14v6c0 .55.45 1 1 1h6c.55 0 1-.45 1-1v-6c0-.55-.45-1-1-1h-6c-.55 0-1 .45-1 1zm-9 7h6c.55 0 1-.45 1-1v-6c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1zM3 4v6c0 .55.45 1 1 1h6c.55 0 1-.45 1-1V4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1zm12.95-1.6L11.7 6.64c-.39.39-.39 1.02 0 1.41l4.25 4.25c.39.39 1.02.39 1.41 0l4.25-4.25c.39-.39.39-1.02 0-1.41L17.37 2.4c-.39-.39-1.03-.39-1.42 0z"></path></svg>
    					</a>
						<h5 class="title mb-0 text-nowrap">Target</h5>
					</div>
					<div class="mid-content"> </div>
					<div class="right-content headerSearch">
					    <div class="jpsearch" id="qs1">
							<input type="text" class="input quicksearch qs1" placeholder="Search Here ..." />
							<button class="search-btn"><i class="fas fa-search"></i></button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</header>
	<!-- Header -->
    <!-- Page Content -->
    <div class="page-content">
        <div class="content-inner pt-0">
			<div class="container bottom-content ">
				<form id="targetForm">
					<div class="row mb-3">
						<div class="col-5">
							<select id="zone_id" class="form-control select2">
								<option value="ALL">All Zone</option>
								<?php   
									foreach($zoneList as $row): 
										echo '<option value="'.$row->id.'">'.$row->zone_name.'</option>';
									endforeach; 
								?>
							</select>
							<div class="error zone_id"></div>
						</div>
						<div class="col-4">
							<select name="month" id="month" class="form-control select2">
								<option value="">Month</option>
								<?php   
									foreach($monthData as $row): 
										echo '<option value="'.$row['val'].'">'.$row['label'].'</option>';
									endforeach; 
								?>
							</select>
							<div class="error month"></div>
						</div>
						<div class="col-3">
							<button type="button" class="btn waves-effect waves-light btn-block btn-success loaddata" title="Load Data" style="padding:10px;"><i class="fas fa-sync-alt"></i> Load</button>
						</div>
					</div>
					<ul class="list-grid" id="salesTargetData">
					</ul>
				</form>
				<div class="review-box">
					<?php
                        $param = "{'formId':'targetForm','fnsave':'saveTargets','controller':'executiveTarget/'}";
                    ?>
					<a href="javascript:void(0)" class="add-btn" onclick="store(<?=$param?>)" style="margin-bottom:80px">
						<i class="fas fa-save"></i>
					</a>
				</div>
			</div>    
		</div>
    </div>    
    <!-- Page Content End-->
</div>  
<?php $this->load->view('dealer_app/includes/bottom_menu'); ?>
<?php $this->load->view('dealer_app/includes/footer'); ?>
<?php $this->load->view('dealer_app/includes/sidebar'); ?>

<script src="<?=base_url()?>assets/plugins/isotop/isotope.pkgd.min.js"></script>
<script>
	$(document).ready(function(){
		$(document).on("click",".loaddata",function(){
			var valid = 1;
			var month = $("#month").val();
			var zone_id = $("#zone_id").val();
			if(month == ""){$(".month").html("Month is required.");valid=0;}else{$(".month").html("");}
			
			if(valid){
				$.ajax({
					url:base_url + controller + '/getTargetRows',
					type:'post',
					data:{month:month,zone_id:zone_id},
					dataType:'json',
					success:function(data)
					{
						$("#salesTargetData").html(data.targetData);
					}
				});
			}
		});
		$(".select2").select2();
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
						window.location = base_url + 'dealer_app/executiveTarget';
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