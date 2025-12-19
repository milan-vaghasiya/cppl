<?php
class ExecutiveTarget extends MY_Controller{

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Executive Target";
		$this->data['headData']->controller = "dealer_app/executiveTarget";
		$this->data['monthData'] = $this->getMonthListFY();
	}
	
	public function index(){
		$this->data['headData']->appMenu = "dealer_app/executiveTarget";
	    $this->data['logClass'] = $this->logClass;
	    $this->data['logTitle'] = $this->logTitle;
		$this->data['zoneList'] = $this->configuration->getSalesZoneList();
        $this->load->view('dealer_app/executive_target',$this->data);
    }
	
	public function getTargetRows(){
		$postData = $this->input->post();
        $errorMessage = array();
		
        if(empty($postData['month']))
            $errorMessage['month'] = "Month is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$postData['zone_id'] = (!empty($postData['zone_id']) && $postData['zone_id'] == 'ALL')?'':$postData['zone_id'];
			$postData['is_se'] = 'Yes';
			$postData['executive_target'] = 1;
			$getEmpData = $this->usersModel->getEmployee(['id'=>$this->loginId]);
			$postData['executive_ids'] = (!empty($getEmpData->executive_ids) ? $getEmpData->executive_ids : NULL);
			$empData = $this->usersModel->getEmployeeList($postData);
			$targetData = '';  $i=1;
			if(!empty($empData)):
				foreach($empData as $row):
					$targetData .= '<li class="listItem item transition '.$row->emp_name.'"  data-category="transition">
						<div class="card order-box">
							<div class="card-body">
								<a href="javascript:void(0)">
									<div class="order-content justify-content-between mb-0">
										<div class="right-content">
											<div class="title mb-0">'.$row->emp_code.'</div>
											<ul>
												<li>
													<p class="order-name"><i class="fa fa-user"></i> '.$row->emp_name.'</p>
												</li>
											</ul>
										</div>
										<div class="left-content w-auto">
											<input type="hidden" name="id[]" value="'.$row->target_id.'">
											<input type="hidden" name="emp_id[]" value="'.$row->id.'">
											<input type="hidden" name="zone_id[]" value="'.$row->zone_id.'">
											<input type="number" name="sales_amount[]" value="'.$row->sales_amount.'" class="form-control floatOnly">
										</div>
									</div>
								</a>
							</div>
						</div>
					</li>';
				endforeach;
			endif;
			$this->printJson(['status'=>1,'targetData'=>$targetData]);
		endif;
    }
}
?>