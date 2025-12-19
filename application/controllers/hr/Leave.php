<?php
class Leave extends MY_Controller{
    private $indexPage = "hr/leave_master/index";
    private $leaveForm = "hr/leave_master/form";
    
	public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Leave";
		$this->data['headData']->controller = "hr/leave";
	}
	
	public function index(){
        $this->data['tableHeader'] = getHrDtHeader('leave');
        $this->load->view($this->indexPage,$this->data);
    }

    public function getLeaveDTRows($status=0){
        $data = $this->input->post(); $data['status'] = $status;
        $data['login_emp_id']=$this->session->userdata('loginId');
        $result = $this->usersModel->getLeaveDTRows($data);
        $sendData = array();$i=($data['start']+1);
		foreach($result['data'] as $row):
			$row->sr_no = $i++;       
			$sendData[] = getLeaveData($row);
		endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addLeave(){
        $this->data['employeeList'] = $this->usersModel->getEmployeeList($this->session->userdata('loginId'));
        $this->data['leaveList'] = $this->configuration->getSelectOptionList(['type'=>4]);
        $this->load->view($this->leaveForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if (empty($data['emp_id'])) {
            $errorMessage['emp_id'] = "Employee name is required.";
        }
        if (empty($data['leave_type_id'])) {
            $errorMessage['leave_type_id'] = "Leave Type is required.";
        }
        if (empty($data['start_section'])) {
            $errorMessage['start_section'] = "Start Section is required.";
        }
        if (empty($data['end_section'])) {
            $errorMessage['end_section'] = "End Section is required.";
        }

        if (empty(strtotime($data['start_date']))) {
            $errorMessage['start_date'] = "Start Date is required.";
        }
        elseif (strtotime($data['start_date']) < strtotime(date('Y-m-d'))) {
            $errorMessage['start_date'] = "Invalid Start Date";
        }
        else {
            $data['start_date'] = date("Y-m-d",strtotime($data['start_date']));
        }  

        if (empty(strtotime($data['end_date']))) {
            $errorMessage['end_date'] = "End Date is required.";
        } else {
            $data['end_date'] = date("Y-m-d",strtotime($data['end_date']));
        }
                
        if (strtotime($data['start_date']) > strtotime($data['end_date']) || date("m-Y",strtotime($data['start_date'])) != date("m-Y",strtotime($data['end_date']))) {
            $errorMessage['end_date'] = "End Date must be greater than or equal to Start Date and within the same month as the Start Date.";
        }
            
        $data['total_days'] = 0;
        if (!empty(strtotime($data['start_date'])) AND !empty(strtotime($data['end_date']))) {
            if(strtotime($data['start_date']) == strtotime($data['end_date'])) {
                if($data['start_section'] == "F"){$data['total_days'] = 1;}
                if($data['start_section'] == "H"){$data['total_days'] = 0.5;}
                $data['end_section'] = $data['start_section'];
            } else {
                if($data['start_section'] == "F"){$data['total_days'] += 1;}else{$data['total_days'] += 0.5;}
                if($data['end_section'] == "F"){$data['total_days'] += 1;}else{$data['total_days'] += 0.5;}
                
                $sd = new DateTime($data['start_date']);
                $ed = new DateTime($data['end_date']);
                $diff = $sd->diff($ed);
                
                $data['total_days'] += $diff->days - 1;				
            }
        }
        
        if (empty($data['total_days'])){
            $errorMessage['end_date'] = "You have to apply atleast 1 Day Leave.";
        }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->usersModel->saveLeave($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->usersModel->getLeave($data);
        $this->data['employeeList'] = $this->usersModel->getEmployeeList($this->session->userdata('loginId'));
        $this->data['leaveList'] = $this->configuration->getSelectOptionList(['type'=>4]);
        $this->load->view($this->leaveForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->usersModel->trash('leave_master',['id'=>$id]));
        endif;
    }

    public function approveLeave(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $data['approve_by'] = $this->loginId;
            $data['approve_at'] = date('Y-m-d');
            $this->printJson($this->usersModel->approveLeave($data));
        endif;
    }
}
?>