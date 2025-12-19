<?php
class SalesOrder extends MY_Controller{
	
    private $salesOrder = "app/sales_order";
    private $salesOrderForm = "app/sales_order_form";

    public function __construct(){
        parent::__construct();
		$this->data['headData']->pageTitle = "Orders";
		$this->data['headData']->controller = "app/SalesOrder";    
		$this->data['headData']->pageUrl = "app/salesOrder/order";
    }
	
	/*Created By @Raj:- 02-09-2025*/
    public function order(){
		$this->data['headData']->pageTitle = "Order List";
		$this->data['headData']->appMenu = "app/salesOrder/order";
		$this->data['headData']->pageUrl = "app/salesOrder/order";
        $this->load->view($this->salesOrder, $this->data);
    }
	
	public function addSalesOrder(){
		$this->data['stageList'] = $this->configuration->getLeadStagesList();
		$this->data['leadOptions'] = '<option value="">Select Customer</option>';
		$this->data['getDealerList'] = $this->usersModel->getDealerList(['executive_ids'=>$this->loginId]);
        $this->load->view($this->salesOrderForm,$this->data);
    }
	
	public function saveOrder(){
        $data = $this->input->post();
        $errorMessage = array();
        
        if(empty($data['trans_date']))
            $errorMessage['trans_date'] = "Invoice Date is required.";
            
        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Party is required.";

        if(empty($data['net_amount']))
            $errorMessage['net_amount'] = "Amount is required."; 
        
        if(empty($data['inv_no']))
            $errorMessage['inv_no'] = "Invoice No is required.";
            
        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Party is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else:
            $this->printJson($this->sales->saveOrder($data));
        endif;
    }
	
	public function edit($id){
        $this->data['dataRow'] = $dataRow = $this->sales->getOrderLists(['id'=>$id, 'singleRows'=>1]);
        $this->data['partyList'] = $this->party->getPartyList(['party_type'=>1]);
		$this->data['stageList'] = $this->configuration->getLeadStagesList();
		$leadData = $this->party->getLeadListForSelect(['party_type'=>$dataRow->party_type]);
		
		$leadOptions = '<option value="">Select Customer</option>';
		if(!empty($leadData)){
			foreach($leadData as $row){
				$selected = ((!empty($dataRow->party_id) && $dataRow->party_id == $row->id) ? "selected" : "");
				$leadOptions .= '<option value="'.$row->id.'" '.$selected.'>'.$row->party_name.'</option>';
			}
		}
		
		$this->data['leadOptions'] = $leadOptions;
		$this->data['getDealerList'] = $this->usersModel->getDealerList(['executive_ids'=>$this->loginId]);
		$this->load->view($this->salesOrderForm, $this->data);
    }
	
	public function delete(){
        $data = $this->input->post();
        if (empty($data['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->printJson($this->sales->deleteOrder($data));
        endif;
    }
	
	public function getLeadOptions(){
		$data = $this->input->post();
		$leadData = $this->party->getLeadListForSelect($data);
		
		$leadOptions = '<option value="">Select Customer</option>';
		if(!empty($leadData)){
			foreach($leadData as $row){
				$leadOptions .= '<option value="'.$row->id.'" >'.$row->party_name.'</option>';
			}
		}
		
		$this->printJson(['status' => 1, 'htmlData' => $leadOptions]);
	}
	
	public function getLeadOption($leadData = array(), $lead_id = 0){
		// Columns to keep
        $columnsToKeep = ['id', 'party_name'];

        // Retain only specific columns in the result
        $leadData = array_map(function($row) use ($columnsToKeep) {
            return array_intersect_key((array) $row, array_flip($columnsToKeep));
        }, $leadData);
		
		$htmlData = '<option value="">Select Customer</option>';
		if(!empty($leadData)){
			foreach($leadData as $row){
				$selected = ((!empty($lead_id) && $lead_id == $row['id']) ? "selected" : "");
				$htmlData .= '<option value="'.$row['id'].'" '.$selected.'>'.$row['party_name'].'</option>';
			}
		}
		
		return $htmlData;
	}
	
	public function getOrderData(){
		$data = $this->input->post();
		$html = '';
		$data = [
			'start_date' => $this->startYearDate,
			'end_date' => $this->endYearDate,
			'emp_id' => $this->loginId,
			'status' => $data['status']
		];
		$orderList = $this->sales->getOrderLists($data);
		if(!empty($orderList)){
			foreach($orderList as $row){
				$editButton = '<a class="dropdown-item btn btn-success btn-edit permission-modify" href="'.base_url("app/salesOrder/edit/".$row->id).'" style="justify-content: flex-start;" flow="down"><i class="mdi mdi-square-edit-outline"></i> Edit</a>';
				
				$deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Expense'}";
				$deleteButton = '<a class="dropdown-item btn btn-danger btn-delete permission-remove" href="javascript:void(0)" style="justify-content: flex-start;" onclick="trash('.$deleteParam.');" flow="down"><i class="mdi mdi-trash-can-outline"></i> Remove</a>';
				
				$html .= '<li class="listItem item transition '.$row->party_name.'"  data-category="transition">
					<div class="card order-box">
						<div class="card-body">
							<a href="javascript:void(0)">
								<div class="order-content justify-content-between mb-0">
									<div class="right-content">
										<div class="title mb-0">'.$row->party_name.'</div>
										<ul>
											<li>
												<p class="order-name"><i class="far fa-clock"></i>'.(!empty($row->inv_no) ? '<b style="font-weight:bold;">Invoice No: </b>'.$row->inv_no." & " : "").' <b style="font-weight:bold;">Date: </b>'.date('d, M Y', strtotime($row->trans_date)).'</p>
											</li>
										</ul>
									</div>
									<div class="left-content w-auto">
										<a class="dropdown-toggle lead-action" data-bs-toggle="dropdown" href="#" role="button"><i class="mdi mdi-chevron-down fs-3" style="margin: 5px;"></i></a>
										<div class="dropdown-menu dropdown-menu-end text-left">'.$editButton.$deleteButton.'</div><br/>
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
	/*Ended By @Raj:- 02-09-2025*/
}
?>