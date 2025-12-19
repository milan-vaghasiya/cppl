<?php
class LoginModel extends CI_Model{

	private $employeeMaster = "employee_master";
    private $empRole = ["-1"=>"Super Admin","1"=>"Admin","2"=>"Production Manager","3"=>"Accountant","4"=>"Sales Manager","5"=>"Purchase Manager","6"=>"Employee"];

	public function checkAuth($data){
		$result = $this->db->where('emp_code',$data['user_name'])->where('emp_password',md5($data['password']))->where('is_delete',0)->get($this->employeeMaster);
	
		if($result->num_rows() == 1):
			$resData = $result->row();
			if($resData->is_active == 0):
				return ['status'=>0,'message'=>'Your Account is Inactive. Please Contact Your Admin.'];
			else:
				// Company Data
				$cmpData = $this->db->where('id',1)->get('company_info')->row();
				$this->session->set_userdata('emp_prefix',$cmpData->emp_code_prefix);
				
				//Employe Data
				$this->session->set_userdata('LoginOk','login success');
				$this->session->set_userdata('loginId',$resData->id);
				$this->session->set_userdata('role',$resData->emp_role);
				$this->session->set_userdata('roleName',$this->empRole[$resData->emp_role]);
				$this->session->set_userdata('emp_name',$resData->emp_name);
				$this->session->set_userdata('superAuth',$resData->super_auth_id);
				$this->session->set_userdata('zoneId',$resData->zone_id);
				$this->session->set_userdata('leadRights',$resData->lead_rights);
				$this->session->set_userdata('indiamart',$resData->india_mart);
				$this->session->set_userdata('kgprice',$resData->kg_price);
				
				//FY Data
				$fyData=$this->db->where('is_active',1)->get('financial_year')->row();
				$startDate = $fyData->start_date;
				$endDate = $fyData->end_date;
				$cyear  = date("Y-m-d H:i:s",strtotime("01-04-".date("Y")." 00:00:00")).' AND '.date("Y-m-d H:i:s",strtotime("31-03-".((int)date("Y") + 1)." 23:59:59"));
				$this->session->set_userdata('currentYear',$cyear);
				$this->session->set_userdata('financialYear',$fyData->financial_year);
				$this->session->set_userdata('isActiveYear',$fyData->close_status);
				$this->session->set_userdata('shortYear',$fyData->year);
				$this->session->set_userdata('startYear',$fyData->start_year);
				$this->session->set_userdata('endYear',$fyData->end_year);
				$this->session->set_userdata('startDate',$startDate);
				$this->session->set_userdata('endDate',$endDate);
				$this->session->set_userdata('currentFormDate',date('d-m-Y'));
				
				if(!empty($data['fyear']) && $data['fyear'] != $cyear):
					$this->session->set_userdata('currentFormDate',date('d-m-Y',strtotime($endDate)));
				endif;
				
				return ['status'=>1,'message'=>'Login Success.','emp_role'=>$resData->emp_role];
			endif;
		else:
			return ['status'=>0,'message'=>"Invalid Username or Password."];
		endif;
	}

	public function setFinancialYear($year){
		$fyData=$this->db->where('financial_year',$year)->get('financial_year')->row();
		$startDate = $fyData->start_date;
		$endDate = $fyData->end_date;
		$cyear  = date("Y-m-d H:i:s",strtotime("01-04-".date("Y")." 00:00:00")).' AND '.date("Y-m-d H:i:s",strtotime("31-03-".((int)date("Y") + 1)." 23:59:59"));
		$this->session->set_userdata('currentYear',$cyear);
		$this->session->set_userdata('financialYear',$fyData->financial_year);
		$this->session->set_userdata('isActiveYear',$fyData->close_status);
		
		$this->session->set_userdata('shortYear',$fyData->year);
		$this->session->set_userdata('startYear',$fyData->start_year);
		$this->session->set_userdata('endYear',$fyData->end_year);
		$this->session->set_userdata('startDate',$startDate);
		$this->session->set_userdata('endDate',$endDate);
		$this->session->set_userdata('currentFormDate',date('d-m-Y'));
		return true;
	}
	
	public function checkUserRole($data){
		return $this->db->where('emp_code',$data['user_name'])->where('emp_password',md5($data['password']))->where('is_delete',0)->get($this->employeeMaster)->row();
	}

}
?>