<?php
class PartyModel extends MasterModel{
    private $partyMaster = "party_master";
	private $otherParty = "other_parties";
	private $party_udf = "party_udf";
    private $custComplaint = "customer_complaint";
    private $leadMaster = "lead_master";
	private $lead_detail = "lead_detail";
	private $sales_logs = "sales_logs";

    /********** Customer **********/
        public function getPartyCode($type=1){
            $queryData['tableName'] = $this->partyMaster;
            $queryData['select'] = "ifnull((MAX(CAST(REGEXP_SUBSTR(party_code,'[0-9]+') AS UNSIGNED)) + 1),1) as code";
            $queryData['where']['party_type'] = $type;
            $result = $this->row($queryData)->code;
            return $result;
        }

        public function getPartyDTRows($data){
            $data['tableName'] = $this->partyMaster;
            $data['select'] = "party_master.*,employee_master.emp_name,statutory_detail.state,statutory_detail.district,statutory_detail.taluka,party_udf.contact_person,party_udf.party_image";
            $data['leftJoin']['employee_master'] = "employee_master.id = party_master.executive_id";
            $data['leftJoin']['party_udf'] = "party_udf.party_id = party_master.id";
            $data['leftJoin']['statutory_detail'] = "statutory_detail.id = party_master.statutory_id";
            $data['where']['party_type'] = 1; 

            if(!in_array($this->userRole,[1,-1])){
                $data['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) >0 OR employee_master.id = '.$this->loginId.')';
            }
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "party_master.party_code";
            $data['searchCol'][] = "party_master.party_name";
            $data['searchCol'][] = "party_master.business_type";
            $data['searchCol'][] = "party_udf.contact_person";
            $data['searchCol'][] = "party_master.contact_phone";
            $data['searchCol'][] = "employee_master.emp_name";
            $data['searchCol'][] = "statutory_detail.district";
            $data['searchCol'][] = "statutory_detail.taluka";

            $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
            if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
            
            return $this->pagingRows($data);
        }

        public function getPartyList($data=array()){
            $queryData = array();
            $queryData['tableName']  = $this->partyMaster;
            $queryData['select'] = "party_master.*,emp.emp_name as executive,party_udf.contact_person";
            $queryData['leftJoin']['party_udf'] = "party_udf.party_id = party_master.id";
            $queryData['leftJoin']['employee_master emp'] = "emp.id = party_master.executive_id";
            
            if(!empty($data['group_code'])):
                $queryData['where_in']['group_code'] = $data['group_code'];
            endif;

            if(!empty($data['party_type'])):
                $queryData['where']['party_type'] = $data['party_type'];
            endif;

            if(!empty($data['executive_id'])):
                $queryData['where']['executive_id'] = $data['executive_id'];
            endif;

            if(!empty($data['sales_zone_id'])):
                $queryData['where']['sales_zone_id'] = $data['sales_zone_id'];
            endif;

            if(!empty($data['business_type'])):
                $queryData['where']['business_type'] = $data['business_type'];
            endif;
            
            if(!empty($data['system_code'])):
                $queryData['where_in']['system_code'] = $data['system_code'];
                $queryData['order_by_field']['system_code'] = $data['system_code'];
            else:
                $queryData['order_by']['party_name'] = "ASC";
            endif;
            if(isset($data['is_active'])):
                $queryData['where']['party_master.is_active'] = $data['is_active'];
        
            endif;

            if(isset($data['executive_required']) && $data['executive_required'] == 1){
                $queryData['where']['executive_id >'] = 0;
            }

            if(!empty($data['limit'])) { 
                $queryData['limit'] = $data['limit']; 
                $queryData['order_by']['party_master.created_at'] = "DESC"; 
            }
            	// Search
            if(!empty($param['skey'])):
				$queryData['like']['party_master.party_name'] = str_replace(" ", "%", $param['skey']);
				$queryData['like']['emp.emp_name'] = str_replace(" ", "%", $param['skey']);
				$queryData['like']['party_udf.contact_person'] = str_replace(" ", "%", $param['skey']);
				$queryData['like']['party_master.source'] = str_replace(" ", "%", $param['skey']);
			endif;
    
           
			if(isset($param['start'])):
				$queryData['start'] = $param['start'];
			endif;
			if(!empty($param['length'])):
				$queryData['length'] = $param['length'];
			endif;
			
            
            if(!in_array($this->userRole,[1,-1])):
                // $queryData['where']['executive_id'] = $this->loginId;
                $queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", emp.super_auth_id) >0 OR emp.id = '.$this->loginId.')';
            endif;
           
            return $this->rows($queryData);
        }
        
        public function getPartyListForSelect($data=array()){
            $queryData = array();
            $queryData['tableName']  = $this->partyMaster;
            $queryData['select'] = "party_master.id,party_master.party_name";
            $queryData['leftJoin']['employee_master emp'] = "emp.id = party_master.executive_id";
            
            if(!empty($data['party_type'])):
                $queryData['where']['party_type'] = $data['party_type'];
            endif;

            if(!empty($data['executive_id'])):
                $queryData['where']['executive_id'] = $data['executive_id'];
            endif;

            if(!empty($data['sales_zone_id'])):
                $queryData['where']['sales_zone_id'] = $data['sales_zone_id'];
            endif;

            if(!empty($data['business_type'])):
                $queryData['where']['business_type'] = $data['business_type'];
            endif;
            
            if(isset($data['is_active'])):
                $queryData['where']['party_master.is_active'] = $data['is_active'];
        
            endif;

            if(isset($data['executive_required']) && $data['executive_required'] == 1){
                $queryData['where']['executive_id >'] = 0;
            }

            if(!in_array($this->userRole,[1,-1])):
                $queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", emp.super_auth_id) > 0 OR emp.id = '.$this->loginId.')';
            endif;
           
            return $this->rows($queryData);
        }

        public function getParty($data){
            $queryData = array();
            $queryData['tableName']  = $this->partyMaster;
            $queryData['select'] = "party_master.*,b_countries.name as country_name,statutory_detail.state,statutory_detail.state_code,statutory_detail.district,sales_logs.ref_date,sales_logs.notes,party_udf.contact_person,party_udf.party_email,party_udf.party_address,party_udf.party_pincode,party_udf.registration_type,party_udf.gstin,party_udf.party_image,party_udf.currency,party_udf.cp_1,party_udf.cp_2,party_udf.cp_3,party_udf.cn_1,party_udf.cn_2,party_udf.cn_3";
            $queryData['leftJoin']['party_udf'] = "party_udf.party_id = party_master.id";
            $queryData['leftJoin']['statutory_detail'] = "party_master.statutory_id = statutory_detail.id";
            $queryData['leftJoin']['countries as b_countries'] = "statutory_detail.country_id = b_countries.id";
            $queryData['leftJoin']['sales_logs'] = "sales_logs.party_id = party_master.id";


            if(!empty($data['id'])):
                $queryData['where']['party_master.id'] = $data['id'];
            endif;

            if(!empty($data['system_code'])):
                $queryData['where']['party_master.system_code'] = $data['system_code'];
            endif;
            $result = $this->row($queryData);
            return $result;
        }

        public function getPartyUdfData($param = []){
            $queryData['tableName'] = $this->party_udf;
            if(!empty($param['party_id'])):
                $queryData['where']['party_udf.party_id'] = $param['party_id'];
            endif;
            return $this->row($queryData);
        }

        public function saveParty($data){
            try {
                $this->db->trans_begin();

                $app_date = !empty( $data['appointment_date'])? $data['appointment_date']:null;
                $notes = !empty($data['notes'])?$data['notes']:'';

                unset($data['appointment_date'],$data['notes']);

                $masterData['checkDuplicate']['first_key'] = 'party_name';
                $masterData['checkDuplicate']['customWhere'] = "((party_name = '".$data['party_name']."') or (contact_phone = '".$data['contact_phone']."'))";
                $customField = (!empty($data['customField']) ? $data['customField'] : []); unset($data['customField']);
                $masterData = [
                    'id'=>$data['id'],
                    'party_name'=>$data['party_name'],
                    'party_type '=>$data['party_type'],
                    'party_name '=>$data['party_name'],
                    'contact_phone '=>$data['contact_phone'],
                    'whatsapp_no '=>$data['whatsapp_no'],
                    'business_type  '=>$data['business_type'],
                    'executive_id'=>!empty($data['executive_id'])?$data['executive_id']:0,
                    'sales_zone_id'=>!empty($data['sales_zone_id'])?$data['sales_zone_id']:0,
                    'statutory_id'=>$data['statutory_id'],
                    'source'=>$data['source'],
                    'discount_structure'=>!empty($data['discount_structure'])?$data['discount_structure']:'',
                ];
                if(empty($data['id']))
                {
                    $code = $this->party->getPartyCode();
                    $masterData['party_code'] = 'C'.sprintf("%03d",$code);
                }
                $result = $this->store($this->partyMaster, $masterData, 'Party');
            
                if($result['status'] == 1){
                    $partyId = (!empty($data['id']) ? $data['id'] : $result['insert_id']);

                    $udfData = $this->getPartyUdfData(['party_id'=>$partyId]); 
                    
                    $customField = [
                        'contact_person'=>$data['contact_person'],
                        'party_email'=>$data['party_email'],
                        'party_address'=>$data['party_address'],
                        'party_pincode'=>!empty($data['party_pincode'])?$data['party_pincode']:'',
                        'registration_type'=>!empty($data['registration_type'])?$data['registration_type']:'',
                        'gstin'=>$data['gstin'],
                        'cp_1'=>$data['cp_1'],
                        'cp_2'=>$data['cp_2'],
                        'cp_3'=>$data['cp_3'],
                        'cn_1'=>$data['cn_1'],
                        'cn_2'=>$data['cn_2'],
                        'cn_3'=>$data['cn_3'],
                    ];
                    $customField['party_id'] =$partyId;       
                    $customField['id'] = !empty($udfData->id)?$udfData->id :'';
                    if(!empty($data['party_image'])){  $customField['party_image'] = $data['party_image']; }
                    $this->store($this->party_udf,$customField);

                    if(empty($data['id'])){
                        $approchData = [
                            'id' => '',
                            'log_type' => 1,
                            'party_id' => $partyId,
                            'ref_id' => $partyId,
                            'executive_id' => (!empty($data['executive_id'])?$data['executive_id']:''),
                            'ref_date' => $app_date,
                            'mode' => $data['source'],
                            'notes' => $notes
                        ];
                        $this->sales->saveSalesLogs($approchData);
                    }  				
                }
                
                if ($this->db->trans_status() !== FALSE) :
                    $this->db->trans_commit();
                    return $result;
                endif;
            } catch (\Exception $e) {
                $this->db->trans_rollback();
                return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
            }
        }

        public function deleteParty($id){
            try {
                $this->db->trans_begin();

                $checkData['columnName'] = ['party_id','acc_id','opp_acc_id','vou_acc_id'];
                $checkData['value'] = $id;
                $checkUsed = $this->checkUsage($checkData);

                if($checkUsed == true):
                    return ['status'=>0,'message'=>'The Party is currently in use. you cannot delete it.'];
                endif;
                
                $result = $this->trash($this->partyMaster, ['id' => $id], 'Party');
                $this->trash($this->party_udf, ['party_id' => $id]);

                if ($this->db->trans_status() !== FALSE) :
                    $this->db->trans_commit();
                    return $result;
                endif;
            } catch (\Exception $e) {
                $this->db->trans_rollback();
                return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
            }
        }
    
        /********** Create User **********/
            public function createUser($data){
				try {
                    $this->db->trans_begin();
					$data['emp_psc'] = $data['emp_psc'];
                    $data['emp_password'] = md5($data['emp_psc']);
					$emp_result = $this->store('employee_master',$data);
					
					$this->edit('party_master', ['id' => $data['party_id']], ['user_status' => $emp_result['id']]);
					$result = ['status'=>1,'message'=>"Create User successfully."];
					
					if ($this->db->trans_status() !== FALSE) :
                        $this->db->trans_commit();
                        return $result;
                    endif;
                } catch (\Exception $e) {
                    $this->db->trans_rollback();
                    return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
                }
            }
        /********** End Create User **********/

        /********** Other Party **********/
            public function saveOtherParty($data){
                try {
                    $this->db->trans_begin();

                    $result = $this->store($this->otherParty, $data, 'Other Party');
                    
                    if ($this->db->trans_status() !== FALSE) :
                        $this->db->trans_commit();
                        return $result;
                    endif;
                } catch (\Exception $e) {
                    $this->db->trans_rollback();
                    return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
                }
            }

            public function getOtherParties($postData){
                $data['tableName'] = $this->otherParty;
                $data['where']['ref_id'] = $postData['ref_id'];
                return $this->rows($data);
            }

            public function deleteOtherParty($id){
                try {
                    $this->db->trans_begin();

                    $result = $this->trash($this->otherParty, ['id'=>$id], 'Other Party');
                    
                    if ($this->db->trans_status() !== FALSE) :
                        $this->db->trans_commit();
                        return $result;
                    endif;
                } catch (\Exception $e) {
                    $this->db->trans_rollback();
                    return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
                }
            }
        /********** End Other Party **********/

    /********** End Customer **********/

    /********** Customer Complaint **********/
        public function getNextComplaintNo(){
            $queryData['tableName'] = $this->custComplaint;
            $queryData['select'] = "ifnull(MAX(trans_no + 1),1) as next_no";
            $queryData['where']['trans_date >='] = $this->startYearDate;
            $queryData['where']['trans_date <='] = $this->endYearDate;
            return $this->row($queryData)->next_no;
        }

        public function getComplaintDTRows($data){
            $data['tableName'] = $this->custComplaint;
            $data['select'] = "customer_complaint.*,party_master.party_name,item_master.item_Code,item_master.item_name,so_master.trans_number as so_number,so_master.trans_date as so_date";
            $data['leftJoin']['party_master'] = "party_master.id = customer_complaint.party_id";
            $data['leftJoin']['employee_master'] = "employee_master.id = party_master.executive_id";
            $data['leftJoin']['item_master'] = "item_master.id = customer_complaint.item_id";
            $data['leftJoin']['so_master'] = "so_master.id = customer_complaint.order_id";

            if(!empty($data['status'])) { $data['where']['customer_complaint.status'] = $data['status']; }
            else { $data['where']['customer_complaint.status'] = 0; }
            if(!in_array($this->userRole,[1,-1])){
                 $data['customWhere'][] = 'find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) >0 OR employee_master.id = '.$this->loginId;
            }
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "customer_complaint.trans_number";
            $data['searchCol'][] = "DATE_FORMAT(customer_complaint.trans_date,'%d-%m-%Y')";
            $data['searchCol'][] = "so_master.trans_number";
            $data['searchCol'][] = "DATE_FORMAT(so_master.trans_date,'%d-%m-%Y')";
            $data['searchCol'][] = "party_master.party_name";
            $data['searchCol'][] = "CONCAT(''[ ',item_master.item_Code',' ] ','item_master.item_name')";
            $data['searchCol'][] = "customer_complaint.notes";

        	$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

        	if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        	return $this->pagingRows($data);
        }

        public function getCustComplaint($data){
            $queryData['tableName'] = $this->custComplaint;
            $queryData['select'] = "customer_complaint.*,party_master.party_name,party_master.business_type";
            $queryData['leftJoin']['party_master'] = "party_master.id = customer_complaint.party_id";
            $queryData['where']['customer_complaint.id'] = $data['id'];
            return $this->row($queryData);
        }

        public function saveComplaint($data){
            try{
                $this->db->trans_begin();

                $result = $this->store($this->custComplaint, $data, 'Customer Complaint');

                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Exception $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }

        public function completeComplaint($data) {
            try{
                $this->db->trans_begin();

                $this->store($this->custComplaint, ['id'=> $data['id'], 'status' => $data['status']]);

                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return ['status' => 1, 'message' => 'Customer Complaint '. $data['msg'] . ' Successfully.'];
                endif;
            }catch(\Exception $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            } 
        }
    /********** End Customer Complaint **********/

    /********** Lead **********/
        public function getLeadCode(){
            $queryData['tableName'] = $this->leadMaster;
            $queryData['select'] = "ifnull((MAX(CAST(REGEXP_SUBSTR(party_code,'[0-9]+') AS UNSIGNED)) + 1),1) as code";
            $result = $this->row($queryData)->code;
            return $result;
        }

        public function getLeadList($param=array()){
            $queryData = array();        
            $queryData['tableName'] = $this->leadMaster;
            if(!empty($param['analysisCount'])){
                $queryData['select'] = "COUNT(*) as lead_count,lead_master.party_type,lead_master.business_type";
            }else{
                $queryData['select'] = "lead_master.*,emp.emp_name as executive,statutory_detail.state,statutory_detail.district,statutory_detail.taluka,lead_detail.contact_person";
            }
           
            $queryData['leftJoin']['employee_master emp'] = "emp.id = lead_master.executive_id";
            $queryData['leftJoin']['statutory_detail'] = "statutory_detail.id = lead_master.statutory_id";
            $queryData['leftJoin']['lead_detail'] = "lead_detail.lead_id = lead_master.id";
            
            if(!empty($param['reminder'])){
                $queryData['select'] .= ",IFNULL(sl.reminder_date,'') as reminder_date";
                $queryData['leftJoin']['(SELECT MIN(ref_date) as reminder_date,lead_id FROM sales_logs WHERE is_delete = 0 AND log_type = 3 and remark IS NULL GROUP BY lead_id) sl'] = "sl.lead_id = lead_master.id";
                $queryData['order_by']['sl.reminder_date'] = "ASC";
                $queryData['where']['party_type !='] = 1;
            }
            else
            {
                
                if(!empty($param['party_type'])):
                    if($param['party_type'] != 'ALL'):
                        $queryData['where_in']['lead_master.party_type'] = $param['party_type'];
                    endif;
                else:
                    $queryData['where']['party_type !='] = 1;
                endif;
                
            }
            
            if(!empty($param['from_date']) && !empty($param['to_date'])):
                $queryData['customWhere'][] = "lead_master.created_at BETWEEN '".$param['from_date']."' AND '".$param['to_date']."'"; 
            endif;
                    
            if(!empty($param['group_code'])):
                $queryData['where_in']['group_code'] = $param['group_code'];
            endif;
    
            if(!empty($param['executive_id'])):
                $queryData['where']['executive_id'] = $param['executive_id'];
            endif;
    
            if(!empty($param['sales_zone_id'])):
                $queryData['where']['lead_master.sales_zone_id'] = $param['sales_zone_id'];
            endif;
    
            if(!empty($param['lead_source']) AND $param['lead_source']!='All'):
                $queryData['where']['lead_master.source'] = $param['lead_source'];
            endif;
    
            if(!empty($param['business_type'])):
                $queryData['where']['business_type'] = $param['business_type'];
            endif;
            
            if(!in_array($this->userRole,[1,-1])):
                $customWhere = "";
                if($this->indiamart == 2):
                    $customWhere = ' OR (lead_master.source = "Indiamart" AND lead_master.executive_id = 0)';
                endif;

                if($this->leadRights == 2): // Zone Wise Leads Rights
                    $queryData['where']['lead_master.sales_zone_id'] = $this->zoneId;
                    $queryData['customWhere'][] = '((find_in_set("'.$this->loginId.'", emp.super_auth_id ) > 0 OR emp.id = '.$this->loginId.')'.$customWhere.' )';
                elseif($this->leadRights == 1):
                    $queryData['customWhere'][] = '((find_in_set("'.$this->loginId.'", emp.super_auth_id ) > 0 OR emp.id = '.$this->loginId.')'.$customWhere.' )';
                endif;
            endif;
    
            if(isset($param['is_active'])):
                $queryData['where']['lead_master.is_active'] = $param['is_active'];       
            endif;
    
            if(isset($param['executive_required']) && $param['executive_required'] == 1):
                $queryData['where']['lead_master.executive_id >'] = 0;
            endif;
    
            if(!empty($param['bulk_executive'])):
				$queryData['where']['lead_master.executive_id'] = 0;
			endif;
			
			if(!empty($param['business_type'])):
                $queryData['where']['lead_master.business_type'] = $param['business_type'];
            endif;
            
            if(!empty($param['state'])):
                $queryData['where']['statutory_detail.state'] = $param['state'];
            endif;
            
            if(!empty($param['district'])):
                $queryData['where']['statutory_detail.district'] = $param['district'];
            endif;
            
            if(!empty($param['statutory_id'])):
                $queryData['where']['lead_master.statutory_id'] = $param['statutory_id'];
            endif;

            if(!empty($param['created_at'])):
				$queryData['where']['MONTH(lead_master.created_at)'] = $param['created_at'];
			endif;
            
			// Search
            if(!empty($param['skey'])):
				$queryData['like']['lead_master.party_name'] = str_replace(" ", "%", $param['skey']);
				$queryData['like']['emp.emp_name'] = str_replace(" ", "%", $param['skey']);
				$queryData['like']['lead_detail.contact_person'] = str_replace(" ", "%", $param['skey']);
				$queryData['like']['lead_master.source'] = str_replace(" ", "%", $param['skey']);
				$queryData['like']['lead_master.contact_phone'] = str_replace(" ", "%", $param['skey']);
			endif;
    
            if(!empty($param['limit'])):
                $queryData['limit'] = $param['limit']; 
            endif;
			if(isset($param['start'])):
				$queryData['start'] = $param['start'];
			endif;
			if(!empty($param['length'])):
				$queryData['length'] = $param['length'];
			endif;
			
            //$queryData['order_by']['lead_master.party_name'] = "ASC";
			$queryData['order_by']['lead_master.created_at'] = "DESC";
			
            if(!empty($param['group_by'])){
                $queryData['group_by'][] = $param['group_by'];
            }
			if(isset($param['count'])):
				$result = $this->numRows($queryData);
			else:
				$result = $this->rows($queryData);
			endif;
			
			//$this->printQuery();
			return  $result;
        }


        public function getLeadListForSelect($param=array()){
            $queryData = array();        
            $queryData['tableName'] = $this->leadMaster;
            $queryData['select'] = "lead_master.id,lead_master.party_name";
            $queryData['leftJoin']['employee_master emp'] = "emp.id = lead_master.executive_id";
            
            if(!empty($param['party_type'])):
                $queryData['where_in']['lead_master.party_type'] = $param['party_type'];
            endif;
    
            if(!empty($param['executive_id'])):
                $queryData['where']['executive_id'] = $param['executive_id'];
            endif;
    
            if(!empty($param['sales_zone_id'])):
                $queryData['where']['lead_master.sales_zone_id'] = $param['sales_zone_id'];
            endif;
    
            if(!empty($param['bulk_executive'])):
				$queryData['where']['lead_master.executive_id'] = 0;
			endif;
            
            if(!in_array($this->userRole,[1,-1])):
                $customWhere = "";
                if($this->indiamart == 2):
                    $customWhere = ' OR (lead_master.source = "Indiamart" AND lead_master.executive_id = 0)';
                endif;

                if($this->leadRights == 2): // Zone Wise Leads Rights
                    $queryData['where']['lead_master.sales_zone_id'] = $this->zoneId;
                    $queryData['customWhere'][] = '((find_in_set("'.$this->loginId.'", emp.super_auth_id ) > 0 OR emp.id = '.$this->loginId.')'.$customWhere.' )';
                elseif($this->leadRights == 1):
                    $queryData['customWhere'][] = '((find_in_set("'.$this->loginId.'", emp.super_auth_id ) > 0 OR emp.id = '.$this->loginId.')'.$customWhere.' )';
                endif;
            endif;
			
            //$queryData['order_by']['lead_master.party_name'] = "ASC";
			$queryData['order_by']['lead_master.created_at'] = "DESC";
			
			$result = $this->rows($queryData);
			
			//$this->printQuery();
			return  $result;
        }

        public function getLead($data){
            $queryData = array();
            $queryData['tableName']  = $this->leadMaster;
            $queryData['select'] = "lead_master.*,b_countries.name as country_name,statutory_detail.state,statutory_detail.state_code,statutory_detail.district,statutory_detail.taluka,sales_logs.ref_date,sales_logs.notes,lead_detail.contact_person,lead_detail.party_email,lead_detail.party_address,lead_detail.registration_type,lead_detail.gstin,lead_detail.party_image,lead_detail.cp_1,lead_detail.cp_2,lead_detail.cp_3,lead_detail.cn_1,lead_detail.cn_2,lead_detail.cn_3";
            $queryData['select'] .= ",lead_detail.query_subject,lead_detail.query_product,lead_detail.query_msg";
            $queryData['leftJoin']['lead_detail'] = "lead_detail.lead_id = lead_master.id";
            $queryData['leftJoin']['statutory_detail'] = "lead_master.statutory_id = statutory_detail.id";
            $queryData['leftJoin']['countries as b_countries'] = "statutory_detail.country_id = b_countries.id";
            $queryData['leftJoin']['sales_logs'] = "sales_logs.lead_id = lead_master.id";
    
            if(!empty($data['id'])):
                $queryData['where']['lead_master.id'] = $data['id'];
            endif;
            if(!empty($data['customWhere'])):
                $queryData['customWhere'][] = $data['customWhere'];
            endif;
            if(!empty($data['system_code'])):
                $queryData['where']['lead_master.system_code'] = $data['system_code'];
            endif;
            return $this->row($queryData);
        }

        public function saveLead($data){
            try {
                $this->db->trans_begin();
    
                $app_date = (!empty( $data['appointment_date']) ? $data['appointment_date'] : null);
                $notes = !empty($data['notes'])?$data['notes']:'';
    
                unset($data['appointment_date'],$data['notes']);
                $masterData = [];
                
               
                $customField = (!empty($data['customField']) ? $data['customField'] : []); unset($data['customField']);
                $masterData = [
                    'id'=>$data['id'],
                    'party_type'=>$data['party_type'],
                    'party_name'=>$data['party_name'],
                    'contact_phone'=>$data['contact_phone'],
                    'whatsapp_no'=>$data['whatsapp_no'],
                    'executive_id'=>(!empty($data['executive_id'])?$data['executive_id']:0),
                    'sales_zone_id'=>(!empty($data['sales_zone_id'])?$data['sales_zone_id']:0),
                    'statutory_id'=>(!empty($data['statutory_id'])?$data['statutory_id']:0),
                    'source'=>(!empty($data['source'])?$data['source']:'')
                ];
                if(empty($data['id']))
                {
                    $code = $this->party->getLeadCode();
                    $masterData['party_code'] = 'L'.sprintf("%03d",$code);
                }
                if(isset($data['business_type'])){$masterData['business_type'] =$data['business_type'];}
                if(isset($data['created_at'])){$masterData['created_at'] =$data['created_at'];}
                
                $masterData['checkDuplicate']['first_key'] = 'party_name';
                if(!empty($data['contact_phone'])){$masterData['checkDuplicate']['customWhere'] = '((party_name = "'.$data['party_name'].'") or (contact_phone = "'.$data['contact_phone'].'"))';}
                
                $result = $this->store($this->leadMaster, $masterData, 'Lead');
                
                if($result['status'] == 1){
                    $leadId = !empty($data['id'])?$data['id']:$result['insert_id'];
                    $otherDetail = [
                        'lead_id '=>$leadId,
                        'contact_person'=>$data['contact_person'],
                        'party_email'=>$data['party_email'],
                        'party_address'=>$data['party_address'],
                        'registration_type'=>(!empty($data['registration_type'])?$data['registration_type']:''),
                        'gstin'=>(!empty($data['gstin'])?$data['gstin']:'')
                    ];
                    if(isset($data['query_subject'])){$otherDetail['query_subject'] =$data['query_subject'];}
                    if(isset($data['query_product'])){$otherDetail['query_product'] =$data['query_product'];}
                    if(isset($data['query_msg'])){$otherDetail['query_msg'] =$data['query_msg'];}
                    if(isset($data['query_statutory'])){$otherDetail['query_statutory'] =$data['query_statutory'];}
                    if(isset($data['created_at'])){$otherDetail['created_at'] =$data['created_at'];}
                    if(isset($data['cp_1'])){$otherDetail['cp_1'] =$data['cp_1'];}
                    if(isset($data['cp_2'])){$otherDetail['cp_2'] =$data['cp_2'];}
                    if(isset($data['cp_3'])){$otherDetail['cp_3'] =$data['cp_3'];}
                    if(isset($data['cn_1'])){$otherDetail['cn_1'] =$data['cn_1'];}
                    if(isset($data['cn_2'])){$otherDetail['cn_2'] =$data['cn_2'];}
                    if(isset($data['cn_3'])){$otherDetail['cn_3'] =$data['cn_3'];}
                    
                    if(!empty($data['party_image'])){$otherDetail['party_image'] = $data['party_image'];}
                    if(empty($data['id'])){
                        $otherDetail['id'] = "";
                        $result = $this->store($this->lead_detail, $otherDetail, 'Lead');
                    }else{ 
                        $result = $this->edit($this->lead_detail,['lead_id '=>$leadId], $otherDetail, 'Lead'); 
                    }
                    /** Sales Log Entry */
                    if(empty($data['id'])){
                        $approchData = [
                            'id' => '',
                            'log_type' => 1,
                            'lead_id' => $leadId,
                            'ref_id' => $leadId,
                            'party_id' => 0,
                            'executive_id' => (!empty($data['executive_id'])?$data['executive_id']:''),
                            'ref_date' => $app_date,
                            'mode' => (!empty($data['source'])?$data['source']:''),
                            'notes' => $notes
                        ];
                        if(isset($data['created_at'])){$approchData['created_at'] =$data['created_at'];}
                        $this->sales->saveSalesLogs($approchData);	
                    }
                }
                if ($this->db->trans_status() !== FALSE) :
                    $this->db->trans_commit();
                    return $result;
                endif;
            } catch (\Exception $e) {
                $this->db->trans_rollback();
                return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
            }
        }

        public function deleteLead($id){
            try {
                $this->db->trans_begin();
    
                $checkData['columnName'] = ['lead_id'];
                $checkData['value'] = $id;
                $checkData['ignoreTable'] = ["sales_logs","lead_detail"];
                $checkUsed = $this->checkUsage($checkData);
    
                if($checkUsed == true):
                    return ['status'=>0,'message'=>'The Lead is currently in use. you cannot delete it.'];
                endif;
                
                $result = $this->trash($this->leadMaster, ['id' => $id], 'Lead');
                $this->trash($this->lead_detail,['lead_id'=>$id]);
                $this->trash($this->sales_logs,['lead_id'=>$id]);
                
                if ($this->db->trans_status() !== FALSE) :
                    $this->db->trans_commit();
                    return $result;
                endif;
            } catch (\Exception $e) {
                $this->db->trans_rollback();
                return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
            }
        }

        public function changeLeadStatus($data){
            try {
                $this->db->trans_begin();
    
                $result = $this->store($this->leadMaster,['id'=>$data['id'],'party_type'=>$data['party_type'],'is_active'=>$data['is_active']]); 
                $logData = [
                    'id' => '',
                    'log_type' => $data['log_type'],
                    'lead_id' => $data['id'],
                    'ref_id' => $result['id'],
                    'ref_date' => date("Y-m-d"),
                    'notes' => (!empty($data['notes'])?$data['notes']:''),
                    'executive_id ' => $data['executive_id'],
                    'created_by' => $this->loginId,
                    'remark' => !empty($data['remark'])?$data['remark']:'',
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->sales->saveSalesLogs($logData);
    
                if ($this->db->trans_status() !== FALSE) :
                    $this->db->trans_commit();
                    return $result;
                endif;
            } catch (\Exception $e) {
                $this->db->trans_rollback();
                return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
            }
        }

        public function saveExecutive($data){
            try {
                $this->db->trans_begin();
                $result = $this->store($this->leadMaster,['id'=>$data['id'],'executive_id'=>$data['executive_id'],'sales_zone_id'=>$data['sales_zone_id']]);
                $empdData = $this->usersModel->getEmployee(['id'=>$data['executive_id']]);
                $logData = [
                    'id' => '',
                    'log_type' => 10,
                    'lead_id' => $data['id'],
                    'ref_id' => $data['id'],
                    'ref_date' => date("Y-m-d"),
                    'notes' => $this->notes[10].' - '.$empdData->emp_name,
                    'executive_id ' => $data['executive_id'],
                    'created_by' => $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->sales->saveSalesLogs($logData);

                if ($this->db->trans_status() !== FALSE) :
                    $this->db->trans_commit();
                    return $result;
                endif;
            } catch (\Exception $e) {
                $this->db->trans_rollback();
                return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
            }
	    }
	    
	    public function saveLeadImage($data){
            try {
                $this->db->trans_begin();
                $result = $this->edit($this->lead_detail,['lead_id'=>$data['id']],['party_image'=>$data['party_image']]);
                if ($this->db->trans_status() !== FALSE) :
                    $this->db->trans_commit();
                    return $result;
                endif;
            } catch (\Exception $e) {
                $this->db->trans_rollback();
                return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
            }
        }
	
    	public function getLeadAnalysisCount($param=[]){
            $queryData['tableName'] = "lead_master";
            $queryData['select'] = "count(*) as lead_count,party_type";        
            $queryData['leftJoin']['employee_master'] = "lead_master.executive_id = employee_master.id";
            if(!empty($param['executive_id'])){ $queryData['where']['lead_master.executive_id'] = $param['executive_id']; }
            if(!in_array($this->userRole,[1,-1])):
                if($this->leadRights == 2): // Zone Wise Leads Rights
                    $queryData['where']['lead_master.sales_zone_id'] = $this->zoneId;
                    $queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) >0 OR employee_master.id = '.$this->loginId.')';
                elseif($this->leadRights == 1):
                    $queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) >0 OR employee_master.id = '.$this->loginId.')';
                endif;
            endif;
            
            
            if(!empty($param['from_date'])){ $queryData['where']['lead_master.created_at >= '] = date('Y-m-d H:i:s',strtotime($param['from_date'].' 00:00:00')); }
            if(!empty($param['to_date'])){ $queryData['where']['lead_master.created_at <= '] = date('Y-m-d H:i:s',strtotime($param['to_date'].' 23:59:59')); }
            
            if(!empty($param['group_by'])){
                $queryData['select'] .= ",".$param['group_by'];
                $queryData['group_by'][] =$param['group_by'];
                $queryData['group_by'][] ='party_type';
                $queryData['order_by'][$param['group_by']] = 'ASC';
            }
            $result = $this->rows($queryData);
            //$this->printQuery();
            return $result;
        }
        
        public function getLeadCount($param=[]){
            $queryData['tableName'] = "lead_master";
            $queryData['select'] = "(CASE WHEN lead_master.party_type=1 THEN count(*) ELSE 0 END) as wonLeadCount";
            $queryData['select'] .= "(CASE WHEN lead_master.party_type=2 THEN count(*) ELSE 0 END) as newLeadCount";
            $queryData['select'] .= "(CASE WHEN lead_master.party_type=3 THEN count(*) ELSE 0 END) as lostLeadCount";
            
            $queryData['leftJoin']['employee_master'] = "lead_master.executive_id = employee_master.id";
            if(!empty($param['executive_id'])){ $queryData['where']['lead_master.executive_id'] = $param['executive_id']; }
            if(!in_array($this->userRole,[1,-1])):
                if($this->leadRights == 2): // Zone Wise Leads Rights
                    $queryData['where']['lead_master.sales_zone_id'] = $this->zoneId;
                    $queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) >0 OR employee_master.id = '.$this->loginId.')';
                elseif($this->leadRights == 1):
                    $queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) >0 OR employee_master.id = '.$this->loginId.')';
                endif;
            endif;
            
            
            if(!empty($param['from_date'])){ $queryData['where']['lead_master.created_at >= '] = date('Y-m-d H:i:s',strtotime($param['from_date'].' 00:00:00')); }
            if(!empty($param['to_date'])){ $queryData['where']['lead_master.created_at <= '] = date('Y-m-d H:i:s',strtotime($param['to_date'].' 23:59:59')); }
            
            if(!empty($param['group_by'])){
                $queryData['select'] .= ",".$param['group_by'];
                $queryData['group_by'][] =$param['group_by'];
                $queryData['order_by'][$param['group_by']] = 'ASC';
            }
            $result = $this->row($queryData);
            //$this->printQuery();
            return $result;
        }

    /********** End Lead **********/
}
?>