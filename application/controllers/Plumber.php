<?php
class Plumber extends MY_Controller{
    private $index = "plumber/index";
    private $form = "plumber/form";

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Plumber";
		$this->data['headData']->controller = "plumber";
        $this->data['headData']->pageUrl = "plumber";
	}
	
	public function index(){
        $this->data['tableHeader'] = getMasterDtHeader($this->data['headData']->controller);
        $this->load->view($this->index,$this->data);
    }
	
    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->plumber->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getPlumberData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPlumber(){
        $this->data['partyList'] = $this->party->getPartyList(['party_type'=>1]);
        $this->load->view($this->form, $this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();

        if(empty($data['party_id'])){
			$errorMessage['party_id'] = "Customer is required.";
        }
        if(empty($data['name'])){
			$errorMessage['name'] = "Plumber Name is required.";
        }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            
            $this->printJson($this->plumber->save($data));
        endif;
    }

    public function edit(){     
        $data = $this->input->post();
        $this->data['dataRow'] = $this->plumber->getPlumber($data);
        $this->data['partyList'] = $this->party->getPartyList(['party_type'=>1]);
        $this->load->view($this->form, $this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->plumber->trash('plumber_master',['id'=>$id]));
        endif;
    }
}
?>