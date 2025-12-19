<?php
class AutoCron extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->model('masterModel');
		$this->load->model('SalesModel','sales');
		$this->load->model('ConfigurationModel','configuration');
		$this->load->model('PartyModel','party');
		$this->load->model('ThirdPartyModel','TPModel');
    }

	/*** Sync Indiamart Lead  ****/
    public function syncTPLeads(){
        if( (date('Y-m-d H:i:s') < date('Y-m-d H:i:s',strtotime(date('Y-m-d 20:00:00')))) AND (date('Y-m-d H:i:s') > date('Y-m-d H:i:s',strtotime(date('Y-m-d 08:25:00')))) )
        {
            $response = $this->TPModel->syncIMLeads();
        }
        print_r($response);
        return true;
    }
	
}
?>