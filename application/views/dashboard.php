    <?php $this->load->view('includes/header'); ?>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/freeps2/Ticker@main/ent-admin.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/freeps2/Ticker@main/ent-style.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/freeps2/Ticker@main/ent-style.css">
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/plugins/newsticker/breaking-news-ticker.css">
	<style>
		.d-none{display: none;}
		.d-block {display: block;}
		#from_date,#to_date{padding:0px 0.5rem;background:#c3e1e4;border-color:#90bdc2;}
		.LAN .select2-container--default .select2-selection--single .select2-selection__rendered{line-height:32px!important;}.LAN .select2-container .select2-selection--single{height:32px!important;border-color:#90bdc2;}
		.LAN .select2-container--default .select2-selection--single {border-color:#90bdc2;background:transparent;}
	</style>
	<div class="page-content-tab">
		<div class="container-fluid" style="padding:0px 10px;">
			<div class="row">
				<div class="col-md-6 col-lg-3 TDO d-none">
					<div class="bg-white">
						<h4 class="jp-list-title m-0 d-flex justify-content-between align-items-center">
							To Do List <span class="badge bg-soft-primary badge-pill" id="rmd_count"></span>
						</h4> 
						<div class="jp-list-body" data-simplebar style="height: 545px;">
						    <div id="reminderList"></div>
						</div>
					</div>
				</div>
				<div class="col-lg-9">
					<div class="row justify-content-left"> 
						<div class="col-lg-3 NLD d-none">
							<div class="card overflow-hidden">
								<div class="card-body">
									<div class="row d-flex">
										<div class="col-3">
											<i class="fas fa-user-plus font-30 align-self-center text-dark"></i>
										</div>
										<div class="col-12 ms-auto align-self-center">
											<div id="dash_spark_1" class="mb-3"></div>
										</div>
										<div class="col-12 ms-auto align-self-center">
											<h3 class="text-dark my-0 font-22 fw-bold"><?=count($newLeadCount)?></h3>
											<p class="text-muted mb-0 fw-semibold">New Leads</p>
										</div>
									</div>
								</div> 
							</div>                        
						</div>  
						<div class="col-lg-3 WLD d-none">
							<div class="card overflow-hidden">
								<div class="card-body">
									<div class="row d-flex">
										<div class="col-3">
											<i class="fas fa-user-check font-30 align-self-center text-dark"></i>
										</div>
									
										<div class="col-12 ms-auto align-self-center">
											<div id="dash_spark_2" class="mb-3"></div>
										</div>
										<div class="col-12 ms-auto align-self-center">
											<h3 class="text-dark my-0 font-22 fw-bold"><?=count($wonLeadCount)?></h3>
											<p class="text-muted mb-0 fw-semibold">Leads Won</p>
										</div>
									</div>
								</div>
							</div>                               
						</div>  
						<div class="col-lg-3 LLD d-none">
							<div class="card overflow-hidden">
								<div class="card-body">
									<div class="row d-flex">
										<div class="col-3">
											<i class="fas fa-user-times font-30 align-self-center text-dark"></i>
										</div>
										<div class="col-12 ms-auto align-self-center">
											<div id="dash_spark_3" class="mb-3"></div>
										</div>
										<div class="col-12 ms-auto align-self-center">
											<h3 class="text-dark my-0 font-22 fw-bold"><?=count($lostLeadCount)?></h3>
											<p class="text-muted mb-0 fw-semibold">Leads Lost</p>
										</div>
									</div>
								</div>
							</div>                               
						</div>  
						
						<div class="col-lg-3 ORD d-none">
							<div class="card overflow-hidden">
								<div class="card-body">
									<div class="row d-flex">
										<div class="col-3">
											<i class="fas fa-cart-plus font-30 align-self-center text-dark"></i>
										</div>
										
										<div class="col-12 ms-auto align-self-center">
											<div id="dash_spark_4" class="mb-3"></div>
										</div>
										<div class="col-12 ms-auto align-self-center">
											<h3 class="text-dark my-0 font-22 fw-bold"><?=$orderCount?></h3>
											<p class="text-muted mb-0 fw-semibold">Orders</p>
										</div>
									</div>
								</div>
							</div>                               
						</div>                                                                    
					</div>
					<div class="row LDO d-none">
						<div class="col-12">       
							<div class="card">
								<div class="card-header">
									<div class="row align-items-center">
										<div class="col"><h4 class="card-title">Overview</h4>  </div>
									</div>
								</div>
								<div class="card-body">
									<div class="text-center">
										<div class="chart-container">
											<div id="enqChart" class="apex-charts"></div>
										</div>
									</div>                                     
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-4 TPM d-none">
					<div class="bg-white">
						<h4 class="jp-list-title m-0 d-flex justify-content-between align-items-center">Top Performer of Month</h4> 
						<div class="jp-list-body" data-simplebar style="height: 325px;">
							<?php
								if(!empty($performerData))
								{
									foreach($performerData as $row)
									{
									
										echo '<div class="jp-list-item py-3">
												<small class="float-end text-muted ps-2">
													<i class="fas fa-rupee-sign"></i> '.moneyFormatIndia(intval($row->net_amount)).'
												</small>
												<div class="media">
													<div class="media-body align-self-center ms-2 text-truncate">
														<h6 class="my-0 fw-normal text-dark">'.$row->emp_name.'</h6>
													</div>
												</div>
											</div>';
									}
								}
							?>
							
						</div>
					</div>
				</div>
				<div class="col-lg-8  LAN d-none">
					<div class="card">
						<div class="card-header" style="background:#c3e1e4;">
            					<div class="float-end" style="width:70%;">
            					    <div class="input-group ">
                                        <div class="input-group-append" style="width:15%;">
                                            <select name="group_by" id="group_by" class="form-control select2">
                                                <option value="source" selected>Source</option>
                                                <option value="business_type">Business Type</option>
                                            </select>
                                        </div>
                                        <div class="input-group-append" style="width:30%;">
                                            <select name="executive_id" id="executive_id" class="form-control select2">
                                                <option value="ALL">Select Executive</option>
                                                <?php
                                                    if(!empty($salesExecutives)){
                                                        foreach($salesExecutives as $row){
                                                            $selected = (!empty($dataRow->executive_id) and $dataRow->executive_id == $row->id)?"selected":"";
                                                            echo '<option value="'.$row->id.'" '.$selected .'>'.$row->emp_name.'</option>';
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        <input type="date" name="from_date" id="from_date" class="form-control" value="<?=date('Y-m-01')?>" />                                    
                                        <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-t')?>" />
                                        <div class="input-group-append">
                                            <button type="button" class="btn waves-effect waves-light btn-success float-right refreshLAN" title="Refresh"><i class="fas fa-sync-alt"></i></button>
                                        </div>
                                    </div>
                                    <div class="error fromDate"></div>
                                    <div class="error toDate"></div>
                                </div> 
							<h4 class="card-title">Lead Analysis</h4>
						</div>
						<div class="card-body">
							<div class="">
								<div class="table-responsive laDetail"></div>
							</div>                                        
						</div>
					</div>
				</div>
				
				<!--
				<div class="col-lg-4 CAN d-none">
					<div class="card">
						<div class="card-header">
							<h4 class="card-title">Conversion Analysis</h4>
						</div>
						<div class="card-body">
							<div class="">
								<div id="conversionChart" class="apex-charts"></div>
							</div>                                        
						</div>
					</div>
				</div>
				
				<div class="col-lg-4 RLD d-none">
					<div class="bg-white">
						<h4 class="jp-list-title m-0 d-flex justify-content-between align-items-center">Recent Leads</h4> 
						<div class="jp-list-body" data-simplebar >
							<?php
								/*if(!empty($recentLeadList))
								{
									foreach($recentLeadList as $row)
									{
										echo '<div class="jp-list-item py-3">
												<small class="float-end text-muted ps-2">
													'.formatDate($row->created_at,'d M Y').'
												</small>
												<div class="media">
													<div class="media-body align-self-center ms-2 text-truncate">
														<h6 class="my-0 fw-normal text-dark">'.$row->party_name.'</h6>
														<small class="text-muted mb-0">'.$row->executive.'</small><br>
														<small class="text-muted mb-0">'.$row->source.'</small>
													</div>
												</div>
											</div>';
									}
								}*/
							?>
							
						</div>
					</div>
				</div>
				<div class="col-lg-4 RAC d-none">
					<div class="bg-white">
						<h4 class="jp-list-title m-0 d-flex justify-content-between align-items-center">Recent Activities </h4> 
						<div class="jp-list-body" data-simplebar >
							<div class=" card-body" id="cd_body">
								<div class="activity ">
									<?php
										/*if(!empty($activityList))
										{
											foreach($activityList as $row)
											{
												echo  '<div class="activity-info">
															<div class="icon-info-activity"><i class="'.$iconClass[$row->log_type].'"></i></div>
															<div class="activity-info-text">
																<div class="d-flex justify-content-between align-items-center">
																	<h6 class="m-0 fs-13">'.$logTitle[$row->log_type].'</h6>
																	<span class="text-muted w-30 d-block font-12">
																	'.date("d F",strtotime($row->created_at)).'</span>
																</div>
																<p class="text-muted mt-1 font-13">'.$row->notes.$row->ref_no.'</p>
															</div>
														</div>'; 	
											}
										}*/
									?>	
								</div>
							</div>						
						</div>
					</div>
				</div>
			</div>
			-->
		</div>
	</div>

<?php $this->load->view('includes/footer'); ?>

<!-- Javascript  -->   
<script src="<?=base_url()?>assets/plugins/apexcharts/apexcharts.min.js"></script>
<script src="<?=base_url()?>assets/plugins/newsticker/breaking-news-ticker.min.js"></script>
<script>
	$(document).ready(function() {
		reminderList();
		setTimeout(function(){ $(".refreshLAN").trigger("click"); }, 50);
		var wp = '<?=(!empty($widgetPermission) ? implode(",",$widgetPermission) : '')?>';
		var wpArr = wp.split(',');
	
		$.each(wpArr , function(index, val) {
		    $('.'+val).removeClass('d-none');
		    $('.'+val).addClass('d-block');
        });
		$('#rtdNews').breakingNews();
		
		/***************** Enquiry Bar Chart ***********************/
		var chartOptions = {
			chart: {
				height: 396,
				type: 'bar',
				toolbar: {
					show: false
				},
			},
			plotOptions: {
				bar: {
					horizontal: false,
					endingShape: 'rounded',
					columnWidth: '55%',
				},
			},
			dataLabels: {
				enabled: false
			},
			stroke: {
				show: true,
				width: 2,
				colors: ['transparent']
			},
			colors: ["rgba(42, 118, 244, .18)", '#2a76f4', "rgba(251, 182, 36, .6)"],
			series: [{
				name: 'New Lead',
				data: [
				<?=!empty($chartData->jan_new)?$chartData->jan_new:0?>,
				<?=!empty($chartData->feb_new)?$chartData->feb_new:0?>,
				<?=!empty($chartData->mar_new)?$chartData->mar_new:0?>,
				<?=!empty($chartData->apr_new)?$chartData->apr_new:0?>,
				<?=!empty($chartData->may_new)?$chartData->may_new:0?>,
				<?=!empty($chartData->jun_new)?$chartData->jun_new:0?>,
				<?=!empty($chartData->jul_new)?$chartData->jul_new:0?>,
				<?=!empty($chartData->aug_new)?$chartData->aug_new:0?>,
				<?=!empty($chartData->sep_new)?$chartData->sep_new:0?>,
				<?=!empty($chartData->oct_new)?$chartData->oct_new:0?>,
				<?=!empty($chartData->nov_new)?$chartData->nov_new:0?>,
				<?=!empty($chartData->dec_new)?$chartData->dec_new:0?>
				]
			}, {
				name: 'Won',
				data: [
				<?=!empty($chartData->jan_won)?$chartData->jan_won:0?>,
				<?=!empty($chartData->feb_won)?$chartData->feb_won:0?>,
				<?=!empty($chartData->mar_won)?$chartData->mar_won:0?>,
				<?=!empty($chartData->apr_won)?$chartData->apr_won:0?>,
				<?=!empty($chartData->may_won)?$chartData->may_won:0?>,
				<?=!empty($chartData->jun_won)?$chartData->jun_won:0?>,
				<?=!empty($chartData->jul_won)?$chartData->jul_won:0?>,
				<?=!empty($chartData->aug_won)?$chartData->aug_won:0?>,
				<?=!empty($chartData->sep_won)?$chartData->sep_won:0?>,
				<?=!empty($chartData->oct_won)?$chartData->oct_won:0?>,
				<?=!empty($chartData->nov_won)?$chartData->nov_won:0?>,
				<?=!empty($chartData->dec_won)?$chartData->dec_won:0?>
				]
			},{
				name: 'Lost',
				data: [
				<?=!empty($chartData->jan_lost)?$chartData->jan_lost:0?>,
				<?=!empty($chartData->feb_lost)?$chartData->feb_lost:0?>,
				<?=!empty($chartData->mar_lost)?$chartData->mar_lost:0?>,
				<?=!empty($chartData->apr_lost)?$chartData->apr_lost:0?>,
				<?=!empty($chartData->may_lost)?$chartData->may_lost:0?>,
				<?=!empty($chartData->jun_lost)?$chartData->jun_lost:0?>,
				<?=!empty($chartData->jul_lost)?$chartData->jul_lost:0?>,
				<?=!empty($chartData->aug_lost)?$chartData->aug_lost:0?>,
				<?=!empty($chartData->sep_lost)?$chartData->sep_lost:0?>,
				<?=!empty($chartData->oct_lost)?$chartData->oct_lost:0?>,
				<?=!empty($chartData->nov_lost)?$chartData->nov_lost:0?>,
				<?=!empty($chartData->dec_lost)?$chartData->dec_lost:0?>
				]
			}],
			xaxis: {
				categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
				axisBorder: {
					show: true,
					color: '#bec7e0',
				},  
				axisTicks: {
					show: true,
					color: '#bec7e0',
				},    
			},
			legend: {
				offsetY: 6,
			},
			yaxis: {
				title: {
					text: 'No Of Leads'
				}
			},
			fill: {
				opacity: 1

			},
			// legend: {
			//     floating: true
			// },
			grid: {
				row: {
					colors: ['transparent', 'transparent'], // takes an array which will be repeated on columns
					opacity: 0.2
				},
				borderColor: '#f1f3fa'
			},
			tooltip: {
				y: {
					formatter: function (val) {
						return val + " Leads"
					}
				}
			}
		}
		var chart = new ApexCharts(
			document.querySelector("#enqChart"),
			chartOptions
		);
		chart.render();

		/***************** Conversion Analysis Pie Chart ***********************/
		/*
		var options = {
			chart: {
				height: 320,
				type: 'pie',
			}, 
			stroke: {
				show: true,
				width: 2,
				colors: ['transparent']
			},
			series: [ 
				<?=(!empty($newLead) ? count($newLead) : 0)?>, 
				<?=(!empty($wonLead) ? count($wonLead) : 0)?> 
			],
			labels: ["New Lead", "Won"],
			colors: ["#4a8af6","#fbc659"],
			legend: {
				show: true,
				position: 'bottom',
				horizontalAlign: 'center',
				verticalAlign: 'middle',
				floating: false,
				fontSize: '14px',
				offsetX: 0,
				offsetY: 6
			},
			responsive: [{
				breakpoint: 600,
				options: {
					chart: {
						height: 240
					},
					legend: {
						show: false
					},
				}
			}]
		}						
		var chart = new ApexCharts(
			document.querySelector("#conversionChart"),
			options
		);		
		chart.render();
		*/
		$(document).on('click','.refreshLAN',function(){
            var group_by = $("#group_by").val();
            var executive_id = $("#executive_id").val();
            var from_date = $("#from_date").val();
            var to_date = $("#to_date").val();
    
			$.ajax({
				url: base_url + controller + '/getLeadAnalysis',
				data: {group_by:group_by, executive_id:executive_id, from_date:from_date, to_date:to_date},
				type: "POST",
				global:false,
				dataType:"json",
			}).done(function(response){
				$(".laDetail").html(response.laDetail);
			});
            
    	});
	});

	function reminderReponse(data,formId){
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
				colseModal(formId);
				reminderList();
			});
		}else{
			if(typeof data.message === "object"){
				$(".error").html("");
				$.each( data.message, function( key, value ) {$("."+key).html(value);});
			}else{
				Swal.fire({ icon: 'error', title: data.message });
			}			
		}	
	}
	function reminderList(){
		$.ajax({
            url: base_url  + '/dashboard/getReminderData',
            data:{'visit_status':status},
            type: "POST",
            dataType:"json",
        }).done(function(response){
			$("#reminderList").html(response.html);			
			$("#rmd_count").html(response.rmd_count);			
		});
	}
	
</script>