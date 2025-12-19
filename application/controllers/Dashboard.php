<?php
class Dashboard extends MY_Controller{

	public function __construct()	{
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Dashboard";
		$this->data['headData']->controller = "dashboard";
	}
	
	public function index(){

		$widgetPermission = $this->permission->getEditDashPermission($this->loginId);
		$this->data['widgetPermission'] =$widget_class  =!empty($widgetPermission)?array_column($widgetPermission,'sys_class'):[];
		
	    $this->data['newLeadCount'] = (in_array('NLD',$widget_class)) ? $this->party->getLeadList(['party_type'=>2]) : [];
	    $this->data['lostLeadCount'] = (in_array('LLD',$widget_class)) ? $this->party->getLeadList(['party_type'=>3]) : [];
	    $this->data['wonLeadCount'] = (in_array('WLD',$widget_class)) ? $this->party->getPartyList(['party_type'=>1]) : [];
	    $this->data['performerData'] =(in_array('TPM',$widget_class)) ?$this->sales->getTopSoForDashboard() : [];
	    $this->data['chartData'] =(in_array('LDO',$widget_class)) ? $this->sales->getSalesEnqForChart() : [];
		$this->data['orderCount'] = (in_array('ORD',$widget_class)) ?$this->sales->getOrderCount(['entry_type'=>1])->total_so:0;
		// $this->data['salesData'] = $this->sales->getSoAmountData();
        $this->data['salesExecutives'] = $this->usersModel->getEmployeeList();
	    $this->data['appointmentIcon'] = $this->appointmentIcon;
	    
        $this->load->view('dashboard',$this->data);
    }
	
	public function mapApi(){
        $this->load->view('map_api',$this->data);
    }

	public function empProfile(){
		$this->data['empData'] = $this->usersModel->getEmployee(['id'=>$this->loginId]);
        $this->load->view('emp_profile',$this->data);
	}
	public function getReminderData(){
	    $exe_id = (in_array($this->leadRights,[1,2])?(!in_array($this->userRole,[1,-1])?$this->loginId:''):'');
		$reminderList = $this->sales->getReminders(['status'=>1,'executive_id'=>$exe_id,'ref_date'=>date("Y-m-d")]);
		$html="";
		if(!empty($reminderList)){
			foreach($reminderList as $row){
				$link = '<p class="text-muted fs-11 text-right">'.date("d M Y H:i A",strtotime($row->ref_date." ".$row->reminder_time)).'</p>';
				$responseParam = "{'postData':{'id' : ".$row->id.",'lead_id' : ".$row->lead_id."},'modal_id' : 'modal-md', 'form_id' : 'response', 'title' : 'Reminder Response', 'fnedit' : 'reminderResponse', 'fnsave' : 'saveSalesLog','controller':'lead','res_function':'reminderReponse','js_store_fn':'customStore'}";
				$btn = '<button class="btn btn-sm btn-success" datatip="Response" flow="down" >Response</button>';
				$html .= '<a href="#" class="jp-list-item py-3">
								<small class="float-end text-muted ps-2">
									<i class="far fa-fw fa-clock"></i>'.date("d-m-Y H:i:s",strtotime($row->ref_date." ".$row->reminder_time)).'
								</small>
								<div class="media">
									<div class="avatar-md bg-soft-primary" flow="down" datatip="Add Response" onclick="edit('.$responseParam.');">
										<i  class="'.(!empty($row->mode) ? $this->appointmentIcon[$row->mode] : '').'" ></i>
										
									</div>
									<div class="media-body align-self-center ms-2 text-truncate">
										<h6 class="m-0 fs-13 text-dark">'.$row->party_name.'</h6>
										<h6 class="my-0 fw-normal text-dark">'.$row->executive.'</h6>
										<small class="text-muted mb-0">'.$row->notes.'</small>
									</div>
									
								</div>
							</a>';
				
				
			}
		}
		$this->printJson(['status'=>1,'html'=>$html,'rmd_count'=>count($reminderList)]);
	}

	public function getLeadAnalysis(){
	    $postData = $this->input->post();
	    $heading = [];$sourceList = [];$sourceLabel = 'label';$totalCount = [];
	    if($postData['group_by'] == "business_type")
	    {
	        $sourceList = $this->configuration->getBusinessTypeList();
	        $sourceLabel = 'type_name';
	    }
	    else
	    {
	        $sourceList = $this->configuration->getSelectOptionList(['type'=>1]);
	        $sourceLabel = 'label';
	    }
	    $postData['executive_id'] = ($postData['executive_id']=='ALL') ? '' : $postData['executive_id'] ;
	    
	    $headRow = '<table class="table border-dashed mb-0 table-striped"><tr><th class="fw-bold" style="background:#a8dde2!important;--bs-table-accent-bg:#a8dde2;">STAGES</th>';
	    $xAxise = [];
		if(!empty($sourceList)):
			foreach($sourceList as $row):
				$heading[] = $row->{$sourceLabel};$totalCount[] = 0;
				$headRow .= '<th class="text-center fw-bold" style="background:#a8dde2!important;--bs-table-accent-bg:#a8dde2;padding:0.50rem 0.20rem;">'.$row->{$sourceLabel}.'</th>';
				$xAxise[] = $row->{$sourceLabel};
			endforeach;
		endif;
	    $headRow .= '<th class="fw-bold" style="background:#a8dde2!important;--bs-table-accent-bg:#a8dde2;">TOTAL</th>';
	    $headRow .= '</tr>';
        $leadStages = $this->configuration->getLeadStagesList();
        $laData =$this->party->getLeadAnalysisCount($postData);
        $laDetail = $headRow;
        $leadCounts = [];
		if(!empty($laData)):
			foreach($laData as $row):
				$leadCounts[$row->party_type][$row->{$postData['group_by']}] = $row->lead_count;
			endforeach;
		endif;

		$lCount = [];
        if(!empty($leadStages))
        {
            foreach($leadStages as $row)
            {
				$stageArray = [];
				$stageArray[]= $row->stage_type;
                $rowTotal = 0;
                $laDetail .= '<tr>';
				$laDetail .= '<th class=" fw-bold" style="">'.$row->stage_type.'</th>';
				for($i=0; $i<count($heading); $i++):
				    $leadCount = (!empty($leadCounts[$row->id][$heading[$i]]) ? $leadCounts[$row->id][$heading[$i]] : 0);
					$laDetail .= '<td class="text-center" style="padding:0.50rem 0.20rem;">'.$leadCount.'</td>';
					$rowTotal += $leadCount;
					$totalCount[$i] += $leadCount;
					$stageArray[] = $leadCount;
				endfor;
				$laDetail .= '<th class="text-center">'.$rowTotal.'</th>';
				$laDetail .= '</tr>';
				$lCount[] =$stageArray;
            }
        }
        $rowTotal = 0;
        $laDetail .= '<tr><th class="fw-bold" style="background:#a8dde2!important;--bs-table-accent-bg:#a8dde2;">TOTAL</th>';
        foreach($totalCount as $lc):
			$laDetail .= '<th class="text-center fw-bold" style="background:#a8dde2!important;--bs-table-accent-bg:#a8dde2;padding:0.50rem 0.20rem;">'.$lc.'</th>';
			$rowTotal += $lc;
		endforeach;
		$laDetail .= '<th class="text-center fw-bold" style="background:#a8dde2!important;--bs-table-accent-bg:#a8dde2;padding:0.50rem 0.20rem;">'.$rowTotal.'</th>';
		$laDetail .= '</tr>';
		$laDetail .= '</table>';
		
        $this->printJson(['laDetail'=>$laDetail,'result'=>$lCount,'xAxise'=>$xAxise]);
	}

    
}
?>