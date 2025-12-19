<?php
class Order extends MY_Controller{
	
    private $orderIndex = "dealer_app/order_index";

    public function __construct(){
        parent::__construct();
		$this->data['headData']->pageTitle = "Order List";
		$this->data['headData']->controller = "dealer_app/Order";
    }

    public function index(){
		$this->data['headData']->appMenu = "dealer_app/order";
		$this->data['logClass'] = $this->logClass;
	    $this->data['logTitle'] = $this->logTitle;
        $this->load->view($this->orderIndex, $this->data);
    }
	
	public function saveApproveOrder(){
        $data = $this->input->post();
		
		if(empty($data['amount']))
            $errorMessage['amount'] = "Amount is required.";
		
        if(!empty($errorMessage)):
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $this->printJson($this->sales->saveApproveOrder($data));
        endif;
    }
	
	public function delete(){
        $data = $this->input->post();
        if (empty($data['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->printJson($this->sales->deleteOrder($data));
        endif;
    }
	
	public function getOrderData(){
		$data = $this->input->post();
		$getEmpData = $this->usersModel->getEmployee(['id'=>$this->loginId]);
		// $data['executive_ids'] = (!empty($getEmpData->executive_ids) ? $getEmpData->executive_ids : NULL);
		$data['start_date'] = $this->startYearDate;
		$data['end_date'] = $this->endYearDate;
		$data['distributor_id'] = $this->loginId;
		$orderList = $this->sales->getOrderLists($data);
		$html = "";
		if(!empty($orderList)){
			foreach($orderList as $row){
				$html .= '<li class="listItem transition '.$row->party_name.'"  data-category="transition">
					<div class="card order-box">
						<div class="card-body">
							<a href="javascript:void(0)">
								<div class="order-content justify-content-between mb-0">
									<div class="right-content">
										<div class="title mb-0">'.$row->party_name.'</div>
										<ul>
											<li>
												<p class="order-name"><i class="far fa-clock"></i> '.(!empty($row->inv_no) ? '<b style="font-weight:bold;">Invoice No: </b>'.$row->inv_no." & " : "").'<b style="font-weight:bold;">Date: </b>.'.date('d, M Y', strtotime($row->trans_date)).'</p>
											</li>
										</ul>
									</div>
									<div class="left-content w-auto">
										<a class="dropdown-toggle lead-action" data-bs-toggle="dropdown" href="#" role="button"><i class="mdi mdi-chevron-down fs-3" style="margin: 5px;"></i></a>
										<div class="dropdown-menu dropdown-menu-end text-left">';
											if(empty($row->approve_by)){
												$html .= '<a class="dropdown-item btn btn-success btn-edit approveOrderBtn" data-form_title="Approve Order" data-bs-toggle="offcanvas" data-bs-target="#approveOrder" data-id="'.$row->id.'" data-inv_no="'.$row->inv_no.'" data-net_amount="'.$row->net_amount.'" aria-controls="offcanvasBottom" style="justify-content: flex-start;">Approve</a>';
												$deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Expense'}";
												$html .= '<a class="dropdown-item btn btn-success btn-edit approveOrderBtn" onclick="trash('.$deleteParam.');" flow="down" style="justify-content: flex-start;">Remove</a>';
											}
										$html .= '</div><br/>
										<span class="order-quantity">'.floatval($row->net_amount).'</span>
									</div>
								</div>
							</a>
						</div>
					</div>
				</li>';
			}
		}
		
		$this->printJson(['status'=>1, 'htmlData' => $html]);
	}
}
?>
