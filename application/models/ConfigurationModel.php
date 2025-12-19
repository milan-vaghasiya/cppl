<?php
class ConfigurationModel extends MasterModel{
    private $salesZone = "sales_zone";
    private $udf = "udf";
    private $master_detail = "master_detail";
    private $business_type = "business_type";
    private $select_master = "select_master";
    private $discount_structure = "discount_structure";
    private $lead_stages = "lead_stages";
    private $countries = "countries";
    private $statutoryDetail = "statutory_detail";

    /********** Sales Zone **********/
        public function getSalesZoneDTRows($data){
            $data['tableName'] = $this->salesZone;

            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "zone_name";
            $data['searchCol'][] = "remark";

            $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

            if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
            return $this->pagingRows($data);
        }

        public function getSalesZone($data){
            $queryData['tableName'] = $this->salesZone;
            $queryData['where']['id'] = $data['id'];
            return $this->row($queryData);
        }

        public function getSalesZoneList($data=array()){
            $data['tableName'] = $this->salesZone;
            $data['select'] = "sales_zone.*";
            if(!empty($data['executive_id'])){
                $data['join']['employee_master'] = "find_in_set(sales_zone.id,employee_master.zone_id) > 0";
                $data['where']['employee_master.id'] = $data['executive_id'];
            }
            $result = $this->rows($data);
            return $result;
        }

        public function saveSalesZone($data){
            try{
                $this->db->trans_begin();

                if(!empty($data['zone_name'])){
                    $data['checkDuplicate'] = ['zone_name'];
                }
                $result = $this->store($this->salesZone,$data,'Sales Zone');

                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Throwable $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }
    /********** End Sales Zone **********/

    /********** Custom Field **********/ 
        public function getCustomFieldDTRows($data){
            $data['tableName'] = $this->udf;
            $data['where']['type'] = $data['type'];
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "field_name";
            $data['searchCol'][] = "field_type";
        
            $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
            if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
            return $this->pagingRows($data);
        }

        public function getNextFieldIndex($param=[]){
            $data['select'] = "MAX(field_idx) as field_idx";
            $data['where']['type'] = $param['type'];
            $data['tableName'] = $this->udf;
            $field_idx = $this->specificRow($data)->field_idx;
            $field_idx = $field_idx + 1;
            return $field_idx;
        } 
        
        public function saveCustomField($data){
            try{
                $this->db->trans_begin();
                
                $data['checkDuplicate'] = ['field_name'];                     
                $result = $this->store($this->udf,$data,'Field');
    
                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Exception $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }
        }

        public function getCustomFieldDetail($param = []){
            $data['tableName'] = $this->udf;
            if(!empty($param['field_name'])){$data['where']['field_name'] = $param['field_name'];}
            if(!empty($param['id'])){$data['where']['id'] = $param['id'];}
            return $this->row($data);
        }
    
        public function getCustomFieldList($param = []){
            $data['tableName'] = $this->udf;
            if(!empty($param['type'])){$data['where']['type'] = $param['type'];}
            return $this->rows($data);
        }
        
        /********** Master Options **********/        
            public function saveMasterOption($data){
                try{
                    $this->db->trans_begin();
                    
                    $data['checkDuplicate'] = ['title'];
                    $result = $this->store($this->master_detail,$data,'Title');          

                    if ($this->db->trans_status() !== FALSE):
                        $this->db->trans_commit();
                        return $result;
                    endif;
                }catch(\Exception $e){
                    $this->db->trans_rollback();
                    return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
                }
                
            }

            public function getMasterList($param = []){
                $data['tableName'] = $this->master_detail;
                if(!empty($param['type'])){$data['where']['type'] = $param['type'];}
                return $this->rows($data);
            }
        /********** End Master Options **********/

    /********** End Custom Field **********/

    /********** Business Type **********/
        public function getBusinessTypeDTRows($data){
            $data['tableName'] = $this->business_type;
            $data['select'] = "business_type.*,(case when business_type.parent_id = 0 then 'N/A' else bType.type_name end) as parentType";
            $data['leftJoin']['business_type bType'] = "bType.id  = business_type.parent_id";

            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "type_name";
            $data['searchCol'][] = "type_name";
            $data['searchCol'][] = "remark";

            $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

            if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
            return $this->pagingRows($data);
        }

        public function getBusinessType($data){
            $queryData['tableName'] = $this->business_type;
            $queryData['select'] = "business_type.*,(case when business_type.parent_id = 0 then 'N/A' else bType.type_name end) as parentType";
            $queryData['leftJoin']['business_type bType'] = "bType.id  = business_type.parent_id";
            if(!empty($data['id'])){$queryData['where']['business_type.id'] = $data['id'];}
            if(!empty($data['type_name'])){$queryData['where']['business_type.type_name'] = $data['type_name'];}
            return $this->row($queryData);
        }

        public function getBusinessTypeList(){
            $queryData['tableName'] = $this->business_type;
            $queryData['select'] = "business_type.*,(case when business_type.parent_id = 0 then 'N/A' else bType.type_name end) as parentType";
            $queryData['leftJoin']['business_type bType'] = "bType.id  = business_type.parent_id";

            return $this->rows($queryData);
        }

        public function saveBusinessType($data){
            try{
                $this->db->trans_begin();

                $result = $this->store($this->business_type,$data,'Business Type');

                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Exception $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }
    /********** End Business Type **********/

    /********** Dynamic Option **********/
        public function getSelectOptionDTRows($data){
            $data['tableName'] = $this->select_master;
            $data['where']['type'] = $data['type'];
            
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "label";
            $data['searchCol'][] = "remark";

            $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

            if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
            return $this->pagingRows($data);
        }

        public function getSelectOption($data){
            $queryData['tableName'] = $this->select_master;
            $queryData['where']['id'] = $data['id'];
            return $this->row($queryData);
        }

        public function getSelectOptionList($data){
            $queryData['tableName'] = $this->select_master;
            $queryData['where']['type'] = $data['type'];
            return $this->rows($queryData);
        }

        public function saveSelectOption($data){
            try{
                $this->db->trans_begin();

                $result = $this->store($this->select_master,$data,'Select Option');

                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Exception $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }
    /********** End Dynamic Option **********/

    /********** Discount Structure **********/
        public function getDiscountStructureDTRows($data){
            $data['tableName'] = $this->discount_structure;
            $data['select'] = "discount_structure.*";
            $data['group_by'][] = "discount_structure.structure_name";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "discount_structure.structure_name";

            $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

            if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
            return $this->pagingRows($data);
        }  

        public function saveDiscountStructure($data){
            try{
                $this->db->trans_begin();

                foreach($data['category_id'] as $category_id){
                    $discArray = [
                        'id'=>$data['id'][$category_id],
                        'structure_name'=>$data['structure_name'],
                        'is_default'=>$data['is_default'],
                        'master_id'=>(!empty($data['master_id']) ? $data['master_id'] : 0),
                        'type'=>$data['type'],
                        'category_id'=>$category_id,
                        'discount'=>$data['cat_discount'][$category_id],
                        'kg_price'=>$data['kg_price'][$category_id],
                    ];
                    $result = $this->store($this->discount_structure,$discArray);
                }

                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Exception $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }

        public function getDiscountData($param){
            $data['tableName'] = $this->discount_structure;
            $data['select'] = "discount_structure.*";
            if(!empty($param['structure_name'])){
                $data['where']['structure_name'] = $param['structure_name'];
            }
            if(!empty($param['category_id'])){
                $data['where']['category_id'] = $param['category_id'];
            }
            if(!empty($param['id'])){
                $data['where']['id'] = $param['id'];
            }
            if(!empty($param['type'])){
                $data['where']['type'] = $param['type'];
            }
            if(!empty($param['group_by'])){
                $data['group_by'][] = $param['group_by'];
            }
            if(!empty($param['single_row'])){
                return $this->row($data);
            }else{
                return $this->rows($data);
            }
            
        }
    /********** End Discount Structure **********/

    /********** Lead Stages **********/
        public function getNextSequenceNo(){
            $queryData['tableName'] = $this->lead_stages;
            $queryData['select'] = "sequence as next_seq_no";
            $queryData['where']['stage_type'] = 'Lost';
            // $queryData['customWhere'][] = "sequence > 1 AND sequence < 9";
            return $this->row($queryData)->next_seq_no;
        }

        public function getNextLogType(){
            $queryData['tableName'] = $this->lead_stages;
            $queryData['select'] = "MAX(log_type) as max_log_type";
            $max_log_type = $this->row($queryData)->max_log_type;
            return (!empty($max_log_type)?$max_log_type+1:14);
        }

        public function getLeadStagesDTRows($data){
            $data['tableName'] = $this->lead_stages;
            $data['order_by']['sequence'] = 'ASC';

            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "stage_type";

            $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

            if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
            return $this->pagingRows($data);
        }

        public function getLeadStage($data){
            $queryData['tableName'] = $this->lead_stages;
            $queryData['where']['id'] = $data['id'];
            return $this->row($queryData);
        }

        public function getLeadStagesList($data=[]){
            $queryData['tableName'] = $this->lead_stages;
            if(!empty($data['stage_type'])) { $queryData['where']['stage_type'] = $data['stage_type']; }
            if(!empty($data['not_in'])) { $queryData['where_not_in']['id'] = $data['not_in']; }
            $queryData['order_by']['sequence'] ='ASC';
            return $this->rows($queryData);
        }

        public function saveLeadStages($data){
            try{
                $this->db->trans_begin();
                
                $data['checkDuplicate'] = ['stage_type'];    
                if(empty($data['id'])){
                    $data['log_type'] = $this->getNextLogType();
                }
                $result = $this->store($this->lead_stages, $data, 'Lead Stage');

                if(empty($data['id'])){
                    $this->edit($this->lead_stages, ['stage_type'=>'Lost'], ['sequence'=>($data['sequence']+1)]);
                }
                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Throwable $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }

        public function deleteLeadStages($id){
            try{
                $this->db->trans_begin();

                $stageData = $this->getLeadStage(['id'=>$id]);
                $result = $this->trash($this->lead_stages, ['id'=>$id], 'Lead Stage');

                $setData = array();
                $setData['tableName'] = $this->lead_stages;
                $setData['where']['sequence > '] = $stageData->sequence;
                $setData['set']['sequence'] = 'sequence, -1';
                $this->setValue($setData);
                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Throwable $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }
    /********** End Lead Stages **********/

    /********** Statutory Detail **********/
        public function getCountriesDTRows($data){
            $data['tableName'] = $this->countries;

            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "name";

            $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

            if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
            return $this->pagingRows($data);
        }

        public function getStatutoryDTRows($data){
            $data['tableName'] = $this->statutoryDetail;
            $data['select'] = "statutory_detail.*,countries.name";
            $data['leftJoin']['countries'] = "countries.id = statutory_detail.country_id";

            if(!empty($data['type']) && $data['type'] == 'State'){
                $data['group_by'][] = "state";

                $data['searchCol'][] = "";
                $data['searchCol'][] = "";
                $data['searchCol'][] = "countries.name";            
                $data['searchCol'][] = "state";            
                $data['searchCol'][] = "state_code";            
            }
            elseif(!empty($data['type']) && $data['type'] == 'District'){
                $data['group_by'][] = "district";

                $data['searchCol'][] = "";
                $data['searchCol'][] = "";
                $data['searchCol'][] = "countries.name";            
                $data['searchCol'][] = "state";            
                $data['searchCol'][] = "state_code";            
                $data['searchCol'][] = "district";            
            }
            elseif(!empty($data['type']) && $data['type'] == 'Taluka'){
                $data['group_by'][] = "taluka";

                $data['searchCol'][] = "";
                $data['searchCol'][] = "";
                $data['searchCol'][] = "countries.name";            
                $data['searchCol'][] = "state";            
                $data['searchCol'][] = "state_code";            
                $data['searchCol'][] = "district";            
                $data['searchCol'][] = "taluka";            
            }


            $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

            if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
            return $this->pagingRows($data);
        }

        public function saveStatutory($data){
            try{
                $this->db->trans_begin();
                
                $type = ""; 
                if(!empty($data['type'])){$type = $data['type'];unset($data['type']);}

                if(!empty($type))
                {                
                    $data['checkDuplicate'] = ['state'];
                    if($type == 'District'){
                        $data['checkDuplicate'][] = 'district';
                    }
                    if($type == 'Taluka'){
                        $data['checkDuplicate'][] = 'district';
                        $data['checkDuplicate'][] = 'taluka';
                    }
                    $result = $this->store($this->statutoryDetail, $data, $type);
                }
                else
                {
                    $data['checkDuplicate'] = ['name'];
                    $result = $this->store($this->countries, $data, 'Country');
                }

                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Exception $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }

        public function getStatutoryDetail($data=array()){
            $queryData['tableName'] = $this->statutoryDetail;
            $queryData['select'] = 'statutory_detail.*';
            if(!empty($data['group_concat'])){
                $queryData['select'] .= ',GROUP_CONCAT(state) as states,GROUP_CONCAT(district) as districts,GROUP_CONCAT(taluka) as talukas';
            }
            if(!empty($data['id'])){$queryData['where']['id'] = $data['id'];}
            if(!empty($data['ids'])){$queryData['where_in']['id'] = $data['ids'];}
            if(!empty($data['country_id'])){$queryData['where']['country_id'] = $data['country_id'];}
            if(!empty($data['state'])){$queryData['where']['state'] = $data['state'];}
            if(!empty($data['district'])){$queryData['where']['district'] = $data['district'];}
            if(!empty($data['districts'])){$queryData['where_in']['district'] = $data['districts'];}
            if(!empty($data['taluka'])){$queryData['where']['taluka'] = $data['taluka'];}
            if(!empty($data['group_by'])){
                $queryData['group_by'][] = $data['group_by'];
            }
            if(isset($data['single_row']) && $data['single_row'] == 1){
                return $this->row($queryData);
            }else{
                return $this->rows($queryData);
            }
            
        }

        public function getCountries(){
            $queryData['tableName'] = $this->countries;
            $queryData['order_by']['name'] = "ASC";
            return $this->rows($queryData);
        }

        public function getCountry($data){
            $queryData['tableName'] = $this->countries;
            $queryData['where']['id'] = $data['id'];
            return $this->row($queryData);
        }
    /********** End Statutory Detail **********/

    /********** Get Currency List **********/
        public function getCurrencyList(){
            $queryData['tableName'] = 'currency';
            return $this->rows($queryData);
        }
    /********** End Currency List **********/
}
?>