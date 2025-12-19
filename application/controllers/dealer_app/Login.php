<?php

class Login extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
		$this->load->model('LoginModel','login_model');
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters( '<div class="error">', '</div>' );
	}
	
	public function index(){	
		if($this->session->userdata('LoginOk')):
			redirect( base_url('dealer_app/dashboard') );
		else:
			$this->load->view('dealer_app/login');
		endif;
	}
	
	public function auth(){
		if($this->session->userdata('LoginOk')):
			redirect( base_url('dealer_app/dashboard') );
		else:
			$data = $this->input->post();
		
			$this->form_validation->set_rules('user_name','Username','required|trim');
			$this->form_validation->set_rules('password','Password','required|trim');
			
			if($this->form_validation->run() == true):
				$getUserData = $this->login_model->checkUserRole($data);
				if(!empty($getUserData->emp_role) && $getUserData->emp_role == 5){
					$result = $this->login_model->checkAuth($data);
				
					if($result['status'] == 1 && $result['emp_role'] == 5):
						return redirect( base_url('dealer_app/dashboard') );
					else:
						$this->session->set_flashdata('loginError',$result['message']);
						redirect( base_url('dealer_app/login') , 'refresh');
					endif;
				}else{
					redirect( base_url('dealer_app/login') , 'refresh');
				}
			
			else:
				$this->load->view('dealer_app/login');
			endif;
		endif;
	}
	
	function logout(){
		$this->session->sess_destroy();
		return redirect(base_url('dealer_app/login'));
	}

	public function setFinancialYear(){
		$year = $this->input->post('year');
		$this->login_model->setFinancialYear($year);
		echo json_encode(['status'=>1,'message'=>'Financial Year changed successfully.']);
	}
}
?>