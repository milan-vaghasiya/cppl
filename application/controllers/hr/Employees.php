<?php
class Employees extends MY_Controller{
    private $indexPage = "hr/employee/index";
    private $employeeForm = "hr/employee/form";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Users";
		$this->data['headData']->controller = "hr/employees";   
        $this->data['headData']->pageUrl = "hr/employees";
	}

    public function index(){        
        $this->data['tableHeader'] = getHrDtHeader('employees');
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0){
        $data = $this->input->post(); $data['status']=$status;
        $result = $this->usersModel->getEmployeeDTRows($data);
        $sendData = array();$i=($data['start']+1);
		foreach($result['data'] as $row):
			$row->sr_no = $i++; 
			$row->emp_role = $this->empRole[$row->emp_role];
			$sendData[] = getEmployeeData($row);
		endforeach;
		
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addEmployee(){
        $this->data['roleList'] = $this->empRole;
        $this->data['genderList'] = $this->gender;
        $this->data['designationList'] = $this->usersModel->getDesignations();
        $this->data['zoneList'] = $this->configuration->getSalesZoneList();
        $this->data['authList'] = $this->usersModel->getEmployeeList();
        $this->load->view($this->employeeForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['emp_name']))
            $errorMessage['emp_name'] = "Employee name is required.";
        if(empty($data['emp_code']))
            $errorMessage['emp_code'] = "Emp. Code is required.";
        if(empty($data['emp_role']))
            $errorMessage['emp_role'] = "Role is required.";
        if(empty($data['emp_contact']))
            $errorMessage['emp_contact'] = "Contact No. is required.";
        if(empty($data['emp_password']))
            $errorMessage['emp_password'] = "Password is required.";
        
        // if(empty($data['id'])):
        //     $data['emp_password'] = "123456";
        // endif;
		unset($data[0]);
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['zone_id'] = (!empty($data['zone_id']) ? implode(',',$data['zone_id']) : "");
            $data['auth_id'] = (!empty($data['auth_id']) ? implode(',',$data['auth_id']) : "");
            $data['emp_name'] = ucwords($data['emp_name']);      
            $this->printJson($this->usersModel->saveEmployee($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['roleList'] = $this->empRole;
        $this->data['genderList'] = $this->gender;
        $this->data['designationList'] = $this->usersModel->getDesignations();
        $this->data['dataRow'] = $dataRow = $this->usersModel->getEmployee($data);
        $this->data['zoneList'] = $this->configuration->getSalesZoneList();
        $this->data['authList'] = $this->usersModel->getEmployeeList();
        $this->data['travel_by'] = (!empty($dataRow->travel_by) ? explode(',', $dataRow->travel_by) : []);
        $this->load->view($this->employeeForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $checkData['columnName'] = ['created_by','updated_by'];
            $checkData['value'] = $id;
            $checkUsed = $this->usersModel->checkUsage($checkData);

            if($checkUsed == true):
                return ['status'=>0,'message'=>'The Shift is currently in use. you cannot delete it.'];
            endif;
            $this->printJson($this->usersModel->trash('employee_master',['id'=>$id]));
        endif;
    }

    public function activeInactive(){
        $postData = $this->input->post();
        if(empty($postData['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->usersModel->activeInactive($postData));
        endif;
    }
    
    public function changePassword(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['old_password']))
            $errorMessage['old_password'] = "Old Password is required.";
        if(empty($data['new_password']))
            $errorMessage['new_password'] = "New Password is required.";
        if(empty($data['cpassword']))
            $errorMessage['cpassword'] = "Confirm Password is required.";
        if(!empty($data['new_password']) && !empty($data['cpassword'])):
            if($data['new_password'] != $data['cpassword'])
                $errorMessage['cpassword'] = "Confirm Password and New Password is Not match!.";
        endif;

        if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $data['id'] = $this->loginId;
			$result =  $this->usersModel->changePassword($data);
			$this->printJson($result);
		endif;
    }

    public function resetPassword(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->usersModel->resetPassword($data['id']));
        endif;
    }
}
?>