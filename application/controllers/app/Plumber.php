<?php
class Plumber extends MY_Controller{
	
    private $plumberIndex = "app/plumber_index";
    private $salesOrderForm = "app/sales_order_form";
    private $plumberForm = "app/plumber_form";

    public function __construct(){
        parent::__construct();
		$this->data['headData']->pageTitle = "Plumber";
		$this->data['headData']->controller = "app/plumber";    
		$this->data['headData']->pageUrl = "app/plumber";
    }
	
	/*Created By @Raj:- 24-09-2025*/
    public function index(){
		$this->data['headData']->pageTitle = "Order List";
		$this->data['headData']->appMenu = "app/plumber";
		$this->data['headData']->pageUrl = "app/plumber";
        $this->data['plumberList'] = $this->plumber->getPlumberData(['loginId'=>$this->loginId]);
        $this->load->view($this->plumberIndex, $this->data);
    }
	
	public function addPlumber(){
		$this->data['partyList'] = $this->party->getPartyList(['party_type'=>1]);
        $this->load->view($this->plumberForm,$this->data);
    }
	
	public function edit($id){
        $this->data['dataRow'] = $this->plumber->getPlumberData(['id'=>$id, 'single_rows'=>1]);
        $this->data['partyList'] = $this->party->getPartyList(['party_type'=>1]);
		$this->load->view($this->plumberForm, $this->data);
    }
	/*Ended By @Raj:- 24-09-2025*/
}
?>