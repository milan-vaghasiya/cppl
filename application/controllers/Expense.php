<?php
class Expense extends MY_Controller{
    private $indexPage = "expense/index";
    private $form = "expense/form";
    private $approve_form = "expense/approve_form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Expense";
		$this->data['headData']->controller = "expense";
		$this->data['headData']->pageUrl = "expense";
	}
	
	public function index(){
        $this->data['tableHeader'] = getMasterDtHeader($this->data['headData']->controller);
        $this->data['customerData'] = $this->party->getPartyList(['party_type'=>1]);
        $this->data['empData'] = $this->usersModel->getEmployeeList(['is_se'=>'Yes']);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0,$party_id=0,$emp_id=0,$from_date="",$to_date=""){
        $data = $this->input->post(); $data['status'] = $status; $data['party_id'] = $party_id;
        $data['emp_id'] = $emp_id; $data['from_date'] = $from_date; $data['to_date'] = $to_date;
        $result = $this->expense->getExpenseDTRows($data);
        $sendData = array();$i=($data['start'] + 1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getExpenseData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addExpense(){	
        $this->data['exp_prefix'] = "EXP".n2y(date('Y')).n2m(date('m'));  
        $this->data['exp_no'] = $this->expense->getNextExpNo();
        $this->data['expTypeList'] = $this->configuration->getSelectOptionList(['type'=>3]);
        $this->data['empList'] = $this->usersModel->getEmployeeList();	
        $this->data['custList'] = $this->party->getPartyList(['party_type'=>1]);	
		$this->load->view($this->form, $this->data);
    }

	public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['exp_number'])){
            $errorMessage['exp_number'] = "Expense Number is required.";
        }  
        if(empty($data['exp_date'])){
            $errorMessage['exp_date'] = "Expense date is required.";
        }      
        if(empty($data['exp_by_id'])){
            $errorMessage['exp_by_id'] = "Employee is required.";
        }     
        if(empty($data['exp_type'])){
            $errorMessage['exp_type'] = "Expense type is required.";
        }      
        if(empty($data['demand_amount'])){
            $errorMessage['demand_amount'] = "Amount is required.";
        }  
      
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			if(!empty($_FILES['proof_file'])):
                if($_FILES['proof_file']['name'] != null || !empty($_FILES['proof_file']['name'])):
                    $this->load->library('upload');
    				$_FILES['userfile']['name']     = $_FILES['proof_file']['name'];
    				$_FILES['userfile']['type']     = $_FILES['proof_file']['type'];
    				$_FILES['userfile']['tmp_name'] = $_FILES['proof_file']['tmp_name'];
    				$_FILES['userfile']['error']    = $_FILES['proof_file']['error'];
    				$_FILES['userfile']['size']     = $_FILES['proof_file']['size'];
    				
    				$imagePath = realpath(APPPATH . '../assets/uploads/expense/');
    				$fileName = preg_replace('/[^A-Za-z0-9]+/', '_', strtolower($_FILES['proof_file']['name']));
    				$config = ['file_name' => $fileName,'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];
    
    				$this->upload->initialize($config);
    				if (!$this->upload->do_upload()):
    					$errorMessage['proof_file'] = $this->upload->display_errors();
    					$this->printJson(["status"=>0,"message"=>$errorMessage]);
    				else:
    					$uploadData = $this->upload->data();
    					$data['proof_file'] = $uploadData['file_name'];
    				endif;
    			endif;
            endif;

            $this->printJson($this->expense->saveExpense($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post(); 
        $this->data['dataRow'] = $dataRow = $this->expense->getExpense($data);        
        $this->data['expTypeList'] = $this->configuration->getSelectOptionList(['type'=>3]);
        $this->data['empList'] = $this->usersModel->getEmployeeList();	

        if($dataRow->exp_source == 2){
            $custData = $this->party->getPartyList(['party_type'=>1]);
            $options = "";
            if(!empty($custData)){
                foreach($custData as $row){
                    $selected = (!empty($dataRow->exp_by_id) && $dataRow->exp_by_id == $row->id) ? "selected" : "";
                    $options .= '<option value="'.$row->id.'" '.$selected.'>'.$row->party_name.'</option>';
                }
            }
        }
        else{
            $empData = $this->usersModel->getEmployeeList();
            $options = "";
            if(!empty($empData)){
                foreach($empData as $row){
                    $selected = (!empty($dataRow->exp_by_id) && $dataRow->exp_by_id == $row->id) ? "selected" : "";
                    $options .= '<option value="'.$row->id.'" '.$selected.'>'.$row->emp_name.'</option>';
                }
            }
        }
        $this->data['options'] = $options;
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->expense->trash('expense_manager',['id'=>$id]));
        endif;
    }
    
    public function getApprovedData(){
        $data = $this->input->post(); 
        $this->data['id'] = $data['id'];
        $this->data['dataRow'] = $this->expense->getExpense($data);
        $this->load->view($this->approve_form,$this->data);
    }

    public function saveApprovedData(){
        $data = $this->input->post();
        $errorMessage = array();
       
        if($data['status'] == 1){
            if(empty($data['amount'])){
                $errorMessage['amount'] = "Amount is required.";
            } 
        }else{
            if(empty($data['rej_reason'])){
                $errorMessage['rej_reason'] = "Reason is required.";
            } 
        }
      
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if($data['status'] == 1){
                $data['approved_by'] = $this->loginId;
                $data['approved_at'] = date('Y-m-d H:i:s');
            }
            $this->printJson($this->expense->saveExpense($data));
        endif;
    }

    public function getExpenseByOptions(){
        $data = $this->input->post();
        
        if($data['exp_source'] == 2){
            $custData = $this->party->getPartyList(['party_type'=>1]);
            $options = "";
            if(!empty($custData)){
                foreach($custData as $row){
                    $options .= '<option value="'.$row->id.'">'.$row->party_name.'</option>';
                }
            }
        }
        else{
            $empData = $this->usersModel->getEmployeeList();
            $options = "";
            if(!empty($empData)){
                foreach($empData as $row){
                    $options .= '<option value="'.$row->id.'">'.$row->emp_name.'</option>';
                }
            }
        }
        $this->printJson(['status'=>1, 'options'=>$options]);
    }

    public function approveBulkRequest(){
        $data = $this->input->post();
        if(empty($data['ids'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->expense->saveBulkExpenseApproval($data));
        endif;
    }
}
?>