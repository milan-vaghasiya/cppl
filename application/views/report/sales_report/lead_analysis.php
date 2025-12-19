<?php $this->load->view('includes/header'); ?>
<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/extra-libs/c3/c3.min.css">
<div class="page-content-tab">
	<div class="container-fluid">
        <div class="row mb-3">
            <div class="col-sm-12">
				<div class="page-title-box">
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
                    <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
				</div>
            </div>
		</div>
        <!-- Start Stacked Column Chart -->
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body">
                            <h4 class="card-title"></h4>
                            <div id="stacked-column"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Stacked Column Chart -->
        <div class="row">
            <div class="col-12">
				<div class="col-12">
					<div class="card">
                        <div class="card-body reportDiv" style="min-height:75vh">
                            <div class="table-responsive">
                                <table id='reportTable' class="table table-bordered laDetail">
                                    
                                </table>
                            </div>
                        </div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>


<?php $this->load->view('includes/footer'); ?>
<script src="<?=base_url()?>assets/extra-libs/c3/d3.min.js"></script>
<script src="<?=base_url()?>assets/extra-libs/c3/c3.min.js"></script>
<script>
$(document).ready(function(){
	reportTable();
    setTimeout(function(){ $(".refreshLAN").trigger("click"); }, 50);
    
    $(document).on('click','.refreshLAN',function(e){
        var group_by = $("#group_by").val();
        var executive_id = $("#executive_id").val();
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();

        $.ajax({
            url: base_url  + '/dashboard/getLeadAnalysis',
            data: {group_by:group_by, executive_id:executive_id, from_date:from_date, to_date:to_date},
            type: "POST",
            global:false,
            dataType:"json",
        }).done(function(response){
            $(".laDetail").html(response.laDetail);
            loadChart(response.result,response.xAxise);
        });
    });   
});


function loadChart(lData,xAxise){
   console.log(lData);
    arr = [];stgArr=[];
    stgArr.push(lData.map(function(value){
        return value[0];
    }));
    console.log(stgArr);

    // Callback that creates and populates a data table, instantiates the stacked column chart, passes in the data and draws it.
    var stackedColumnChart = c3.generate({
        bindto: '#stacked-column',
        size: { height: 400 },
        color: {
            pattern: ['#2962FF', '#ced4da', '#4fc3f7', '#f62d51','#2E8B57','#e5acb6']
        },

        // Create the data table.
        data: {
            columns: lData
             
           ,
            type: 'bar',
           groups: [
                stgArr[0]
            ]
        },
        grid: {
            y: {
                show: true
            }
        },
        axis: {
            x: {
                type: 'categorized',
                categories: xAxise
            }
        },
        bar: {
            width: {
                ratio: 0.3
            }
        }
    });

    // Instantiate and draw our chart, passing in some options.
    setTimeout(function() {
        stackedColumnChart.groups([
            stgArr[0]
        ]);
    }, 1000);



    // Resize chart on sidebar width change
    $(".sidebartoggler").on('click', function() {
        stackedColumnChart.resize();
    });
}
</script>