<?php
class ThirdPartyModel extends MasterModel{
    private $partyMaster = "party_master";
	private $otherParty = "other_parties";
	private $party_udf = "party_udf";
    private $custComplaint = "customer_complaint";
    private $leadMaster = "lead_master";
	private $lead_detail = "lead_detail";
	private $sales_logs = "sales_logs";
	
	public function syncIMLeads($param=[]){
	    try {
                $this->db->trans_begin();
                $errorMessage = array();$lastSyncUpdate = Array();$TOTAL_RECORDS = 0;$insertedRecords = 0;
        	    $APIData = $this->getTPKey(['tp_code'=>'IM']);
        	    
        	    if(!empty($APIData->api_key))
        	    {
        	        $imRespoonse= new stdClass();
        	        $API_KEY = $APIData->api_key;
        	        $fromDate = date('d-M-Y00:00:00');
        	        $toDate = date('d-M-Y23:59:59');
        	        if(!empty($APIData->updated_at))
        	        {
        	            $fromDate = date('d-M-YH:i:s',strtotime($APIData->updated_at));
        	            $toDate = date('d-M-YH:i:s');
        	        }
        	        
            	    $curl = curl_init();
                    
                    curl_setopt_array($curl, array(
                      CURLOPT_URL => 'https://mapi.indiamart.com/wservce/crm/crmListing/v2/?glusr_crm_key='.$API_KEY.'&start_time='.$fromDate.'&end_time='.$toDate,
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => '',
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 0,
                      CURLOPT_FOLLOWLOCATION => true,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => 'GET',
                    ));
                    
                    $response = curl_exec($curl);
            		$err = curl_error($curl);
            		curl_close($curl);
            		
            		if ($err) {$errorMessage['gen_error'] = "cURL Error #:" . $err;echo "cURL Error #:" . $err;$this->printJson(['status' => 0, 'message' => $errorMessage]);}
            		else 
            		{
            			$resultapi = json_decode($response);
            			$imRespoonse = $resultapi->RESPONSE;
            			$TOTAL_RECORDS = $resultapi->TOTAL_RECORDS;
            		}
                    $data = Array();
                    if(!empty($imRespoonse))
                    {
                        foreach($imRespoonse as $row)
                        {
                            $wa_number = (!empty($row->SENDER_MOBILE) ? str_replace('-','',$row->SENDER_MOBILE) : '');
                            $alt_number = (!empty($row->SENDER_MOBILE_ALT) ? str_replace('-','',$row->SENDER_MOBILE_ALT) : '');
                            $prefix = "L";
                            $code = $this->party->getLeadCode(2);
                            $data = Array();
                            $data['id'] = "";
                            $data['party_type'] = 2;
                            $data['source'] = 'Indiamart';
                            $data['party_code'] = $prefix.sprintf("%03d",$code);
                            $data['party_name'] = (!empty($row->SENDER_COMPANY) ? ucwords(addslashes($row->SENDER_COMPANY)) : addslashes($row->SENDER_NAME));
                            $data['contact_person'] = $row->SENDER_NAME;
                            $data['whatsapp_no'] = $wa_number;
                            $data['contact_phone'] = (!empty($alt_number) ? $alt_number : $wa_number);
                            $data['party_email'] = $row->SENDER_EMAIL;
                            $data['party_address'] = $row->SENDER_ADDRESS;
                            $data['party_pincode'] = $row->SENDER_PINCODE;
                            
                            $statutoryData = $this->configuration->getStatutoryDetail(['single_row'=>1,'country_id'=>'101','state'=>$row->SENDER_STATE,'district'=>$row->SENDER_CITY,'taluka'=>$row->SENDER_CITY]);
                            if(!empty($statutoryData->id))
                            {
                                $data['statutory_id'] = $statutoryData->id;
                            }
                            else
                            {
                                $stData = Array();
                                $stData['id'] = '';
                                $stData['type'] = 'Taluka';
                                $stData['country_id'] = '101';
                                $stData['state'] = $row->SENDER_STATE;
                                $stData['district'] = $row->SENDER_CITY;
                                $stData['taluka'] = $row->SENDER_CITY;
                                
                                $stResult = $this->configuration->saveStatutory($stData);
                                if(!empty($stResult)){ $data['statutory_id'] = $stResult['id']; }
                            }
                            $data['query_subject'] = $row->SUBJECT;
                            $data['query_product'] = $row->QUERY_PRODUCT_NAME;
                            $data['query_msg'] = $row->QUERY_MESSAGE;
                            $data['query_statutory'] = $row->SENDER_STATE.', '.$row->SENDER_CITY;
                            $data['created_at'] = date('Y-m-d H:i:s',strtotime($row->QUERY_TIME));
                            
                            //echo '<pre>';
                            //print_r($data);
                            $result = $this->party->saveLead($data);
                            $insertedRecords++;
                        }
                    }
                    $lastSyncUpdate['id'] = $APIData->id;
                    if(isset($this->loginId)){$lastSyncUpdate['updated_by'] = $this->loginId;}
                    $lastSyncUpdate['updated_at'] = date('Y-m-d H:i:s');
        	    }
        	    else
        	    {
        	        $errorMessage['gen_error'] = "API KEY Not Found";
        	        $this->printJson(['status' => 0, 'message' => $errorMessage]);
        	    }
    	    //exit;
            
            if ($this->db->trans_status() !== FALSE) :
                $updateLastDate = $this->store('tp_credentials', $lastSyncUpdate, 'API Sync');
                $this->db->trans_commit();
                $result['insertedRecords'] = $insertedRecords;
                $result['TOTAL_RECORDS'] = $TOTAL_RECORDS;
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
	
}
?>