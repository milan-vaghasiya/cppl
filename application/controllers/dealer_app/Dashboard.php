<?php
class Dashboard extends MY_Controller{

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Dashboard";
		$this->data['headData']->controller = "dealer_app/dashboard";
	}
	
	public function index(){
		$this->data['headData']->appMenu = "dealer_app/dashboard";
	    $this->data['logClass'] = $this->logClass;
	    $this->data['logTitle'] = $this->logTitle;
        $this->load->view('dealer_app/dashboard',$this->data);
    }
}
?>