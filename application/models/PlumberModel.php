<?php
class PlumberModel extends MasterModel{
    private $plumberMaster = "plumber_master";

    public function getDTRows($data){
        $data['tableName'] = $this->plumberMaster;

        $data['select'] = "plumber_master.id,plumber_master.name,plumber_master.phone_no,plumber_master.address,plumber_master.adhar_no,party_master.party_name";

        $data['leftJoin']['party_master'] = "party_master.id = plumber_master.party_id";
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "plumber_master.name";
        $data['searchCol'][] = "plumber_master.phone_no";
        $data['searchCol'][] = "plumber_master.address";
        $data['searchCol'][] = "plumber_master.adhar_no";
		
		$columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		
        return $this->pagingRows($data);
    }

    public function getPlumber($data){
        $queryData = array();
        $queryData['tableName'] = $this->plumberMaster;
        $result = $this->row($queryData);
        return $result;
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            $result = $this->store($this->plumberMaster,$data,'Plumber');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Throwable $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
    
    public function getPlumberData($data = array()){
        $queryData['tableName'] = $this->plumberMaster;
		$queryData['select'] = "plumber_master.*,party_master.party_name";
		$queryData['leftJoin']['party_master'] = "party_master.id = plumber_master.party_id";
		
		if(!empty($data['id']))
			$queryData['where']['plumber_master.id'] = $data['id'];
		
		if(!empty($data['loginId']) && !in_array($this->userRole,[1,-1]))
			$queryData['where']['plumber_master.created_by'] = $data['loginId'];
		
		if(!empty($data['single_rows'])){
			return $this->row($queryData);
		}else{
			return $this->rows($queryData);
		}
	}

}
?>