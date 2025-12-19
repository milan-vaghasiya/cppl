<?php
class DashboardModel extends MasterModel{
    
    public function sendSMS($mobiles,$message){
        
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"http://sms.scubeerp.in/sendSMS?");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "username=9427235336&message=".$message."&sendername=NTVBIT&smstype=TRANS&numbers=".$mobiles."&apikey=7d37fc6d-a141-4f81-9d79-159cf37c3342");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec($ch);
		curl_close ($ch);
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
            $queryData['order_by'][$param['group_by']] = 'ASC';
        }
        $result = $this->rows($queryData);
        //$this->printQuery();
        return $result;
    }
    
}
?>