
<?php
class SalesReportModel extends MasterModel
{
    private $visits = "visits";
    private $so_master = "so_master";
    private $so_trans = "so_trans";
	
    /* Visit History Data */
    public function getVisitHistory($data){
        $queryData = array();
		$queryData['tableName'] = $this->visits;
        $queryData['select'] = "visits.*,(CASE WHEN visits.party_id > 0 THEN party_master.party_name ELSE lead_master.party_name END) AS party_name,(CASE WHEN visits.party_id > 0 THEN party_master.contact_phone ELSE lead_master.contact_phone END) AS contact_phone ";
		$queryData['leftJoin']['party_master'] = "party_master.id = visits.party_id";
		$queryData['leftJoin']['lead_master'] = "lead_master.id = visits.lead_id";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = visits.created_by";
        $queryData['customWhere'][] = "start_at BETWEEN '".date('Y-m-d H:i:s',strtotime($data['from_date'].' 00:00:00'))."' AND '".date('Y-m-d H:i:s',strtotime($data['to_date'].' 23:59:59'))."'";

        if($data['party_type'] == 2)
        {
            $queryData['where']['visits.lead_id >'] = 0;
            if(!empty($data['party_id'])){$queryData['where']['lead_master.id'] = $data['party_id'];}
		    if(!empty($data['sales_executive'])){$queryData['where']['visits.created_by'] = $data['sales_executive'];}
        }
        else
        {
            $queryData['where']['visits.party_id >'] = 0;
            if(!empty($data['party_id'])){$queryData['where']['party_master.id'] = $data['party_id'];}
            if(!empty($data['sales_executive'])){$queryData['where']['visits.created_by'] = $data['sales_executive'];}
        }
        if(!in_array($this->userRole,[1,-1])):
            $data['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) >0 OR employee_master.id = '.$this->loginId.')';
        endif;
		$result = $this->rows($queryData);
		return $result;
    }
    
    /* Sales Register Data */
    public function getSalesRegisterData($data){
        $queryData['tableName'] = $this->so_master;
        $queryData['select'] = 'so_master.*,party_master.party_name,party_udf.gstin,statutory_detail.state,statutory_detail.district,statutory_detail.taluka';
        $queryData['leftJoin']['party_master'] = "party_master.id = so_master.party_id";
        $queryData['leftJoin']['party_udf'] = "party_udf.party_id = party_master.id";
        $queryData['leftJoin']['statutory_detail'] = "statutory_detail.id = party_master.statutory_id";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = so_master.sales_executive";
        $queryData['where']['so_master.entry_type'] = 1;

        $queryData['where']['so_master.trans_date >='] = $data['from_date'];
        $queryData['where']['so_master.trans_date <='] = $data['to_date'];

        if($data['state'] != 'ALL'):
            $queryData['where']['statutory_detail.state'] = $data['state'];
        endif;

        if($data['district'] != 'ALL' && !empty($data['district'])):
            $queryData['where']['statutory_detail.district'] = $data['district'];
        endif;

        if($data['taluka'] != 'ALL' && !empty($data['taluka'])):
            $queryData['where']['statutory_detail.id'] = $data['taluka'];
        endif;
        if(!in_array($this->userRole,[1,-1])):
            $queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) >0 OR employee_master.id = '.$this->loginId.')';
        endif;
        $queryData['order_by']['trans_date'] = 'ASC';
        return $this->rows($queryData);
    }

    public function getSalesRegisterDataItemWise($data){
        $queryData['tableName'] = $this->so_trans;
        $queryData['select'] = "so_trans.*,so_master.trans_date,so_master.entry_type,statutory_detail.state,statutory_detail.district,statutory_detail.taluka,item_master.item_name";

        $queryData['leftJoin']['so_master'] = "so_master.id = so_trans.trans_main_id";
        $queryData['leftJoin']['party_master'] = "party_master.id = so_master.party_id";
        $queryData['leftJoin']['party_udf'] = "party_udf.party_id = party_master.id";
        $queryData['leftJoin']['statutory_detail'] = "statutory_detail.id = party_master.statutory_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = so_trans.item_id";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = so_master.sales_executive";
        $queryData['where']['so_master.entry_type'] = 1;

        $queryData['where']['so_master.trans_date >='] = $data['from_date'];
        $queryData['where']['so_master.trans_date <='] = $data['to_date'];

        if($data['state'] != 'ALL'):
            $queryData['where']['statutory_detail.state'] = $data['state'];
        endif;

        if($data['district'] != 'ALL' && !empty($data['district'])):
            $queryData['where']['statutory_detail.district'] = $data['district'];
        endif;

        if($data['taluka'] != 'ALL' && !empty($data['taluka'])):
            $queryData['where']['statutory_detail.id'] = $data['taluka'];
        endif;
        if(!in_array($this->userRole,[1,-1])):
            $queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) >0 OR employee_master.id = '.$this->loginId.')';
        endif;

        $queryData['order_by']['so_master.trans_date']='ASC';
        $queryData['order_by']['so_master.id']='ASC';

        return $this->rows($queryData);
    }

    /* Sales Analysis Data */
    public function getSalesAnalysisData($data){
        $queryData = array();
        if($data['report_type'] == 1):
            $queryData['tableName'] = "so_master";
            $queryData['select'] = "party_master.party_name,SUM(taxable_amount) as taxable_amount,SUM(gst_amount) as gst_amount,SUM(net_amount) as net_amount";
            $queryData['leftJoin']['party_master'] = "party_master.id = so_master.party_id";
            $queryData['leftJoin']['employee_master'] = "employee_master.id = so_master.sales_executive";

            $queryData['where']['trans_date >='] = $data['from_date'];
            $queryData['where']['trans_date <='] = $data['to_date'];

            if($data['business_type'] != 'ALL'):
                $queryData['where']['party_master.business_type'] = $data['business_type'];
            endif;

            if($data['executive_id'] != 'ALL'):
                $queryData['where']['so_master.sales_executive'] = $data['executive_id'];
            endif;
             if(!in_array($this->userRole,[1,-1])):
                $queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) >0 OR employee_master.id = '.$this->loginId.')';
            endif;
        

            $queryData['group_by'][] = 'so_master.party_id';
            $queryData['order_by']['SUM(taxable_amount)'] = $data['order_by'];

            $result = $this->rows($queryData);
        else:
            $queryData['tableName'] = "so_trans";
            $queryData['select'] = "item_master.item_name,SUM(so_trans.qty) as qty,SUM(so_trans.taxable_amount) as taxable_amount,ROUND((SUM(so_trans.taxable_amount) / SUM(so_trans.qty)),2) as price";
            $queryData['leftJoin']['so_master'] = "so_trans.trans_main_id = so_master.id";
            $queryData['leftJoin']['item_master'] = "item_master.id = so_trans.item_id";
            $queryData['leftJoin']['employee_master'] = "employee_master.id = so_master.sales_executive";

            $queryData['where']['so_master.trans_date >='] = $data['from_date'];
            $queryData['where']['so_master.trans_date <='] = $data['to_date'];

            if(!in_array($this->userRole,[1,-1])):
                $queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) >0 OR employee_master.id = '.$this->loginId.')';
            endif;
        
            $queryData['group_by'][] = 'so_trans.item_id';
            $queryData['order_by']['SUM(so_trans.taxable_amount)'] = $data['order_by'];

            $result = $this->rows($queryData);
        endif;
       
        return $result;
    }

    /* Executive Analysis Data */
    public function getExecutiveAnalysisData($data){
        $queryData = array();
        $zoneWhere = (($data['zone_id'] != 'ALL') ? ' AND lead_master.sales_zone_id = "'.$data['zone_id'].'"' : '');
        $bissTypeWhere = (($data['business_type'] != 'ALL') ? ' AND lead_master.business_type = "'.$data['business_type'].'"' : '');

        $queryData['tableName'] = "employee_master";
        $queryData['select'] = "employee_master.emp_name,SUM(salesLog.total_new_lead) as total_new_lead,salesLog.lead_id,salesLog.created_at,lead_master.business_type,SUM(visit.total_visit) as total_visit,SUM(seMaster.total_enq) as total_enq,SUM(soMaster.total_ord) as total_ord,SUM(soMaster.sales_value) as sales_value";
        
		$queryData['leftJoin']['(SELECT 
                count(*) as total_new_lead,sales_logs.executive_id,sales_logs.lead_id,sales_logs.created_at
            FROM 
                sales_logs 
            LEFT JOIN lead_master on lead_master.id = sales_logs.lead_id
            WHERE 
                sales_logs.log_type = 1 AND sales_logs.lead_id > 0 AND sales_logs.is_delete = 0 AND 
                DATE(sales_logs.created_at) BETWEEN "'.$data['from_date'].'" AND "'.$data['to_date'].'" '.$zoneWhere.$bissTypeWhere.' 
            GROUP BY 
                sales_logs.executive_id) as salesLog'] = "salesLog.executive_id = employee_master.id";
        
        $queryData['leftJoin']['lead_master'] = "lead_master.id = salesLog.lead_id";

        $queryData['leftJoin']['(SELECT count(visits.lead_id) as total_visit,created_by,created_at FROM visits WHERE is_delete = 0 AND DATE(created_at) BETWEEN "'.$data['from_date'].'" AND "'.$data['to_date'].'" GROUP BY created_by) as visit'] = "visit.created_by = employee_master.id";

        $queryData['leftJoin']['(SELECT count(se_trans.id) as total_enq,sales_executive,trans_date FROM se_master LEFT JOIN se_trans ON se_master.id = se_trans.trans_main_id WHERE se_master.is_delete = 0 AND DATE(trans_date) BETWEEN "'.$data['from_date'].'" AND "'.$data['to_date'].'" GROUP BY sales_executive) as seMaster'] = "seMaster.sales_executive = employee_master.id";

        $queryData['leftJoin']['(SELECT count(so_trans.id) as total_ord,SUM(so_trans.taxable_amount) as sales_value,sales_executive,trans_date FROM so_master LEFT JOIN so_trans ON so_master.id = so_trans.trans_main_id WHERE so_master.is_delete = 0 AND DATE(trans_date) BETWEEN "'.$data['from_date'].'" AND "'.$data['to_date'].'" GROUP BY sales_executive) as soMaster'] = "soMaster.sales_executive = employee_master.id";

        if($data['business_type'] != 'ALL'):
            $queryData['where']['lead_master.business_type'] = $data['business_type'];
        endif;

        if($data['zone_id'] != 'ALL'):
            $queryData['where']['lead_master.sales_zone_id'] = $data['zone_id'];
        endif;

        if(!in_array($this->userRole,[1,-1])):
            $queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) >0 OR employee_master.id = '.$this->loginId.')';
        endif;
        
        $queryData['where']['DATE(salesLog.created_at) >='] = $data['from_date'];
        $queryData['where']['DATE(salesLog.created_at) <='] = $data['to_date'];
		$queryData['group_by'][] = 'salesLog.executive_id';

        return $this->rows($queryData);        
    }

    public function getSalesMonthlyAnalysisData($data){
        $queryData = array();
        $queryData['tableName'] = "so_trans";
        $queryData['select'] = "SUM(CASE WHEN MONTH(so_master.trans_date) = 1  THEN so_trans.taxable_amount ELSE 0 END) AS jan_amt,
                                SUM(CASE WHEN MONTH(so_master.trans_date) = 2  THEN so_trans.taxable_amount ELSE 0 END) AS feb_amt,
                                SUM(CASE WHEN MONTH(so_master.trans_date) = 3  THEN so_trans.taxable_amount ELSE 0 END) AS mar_amt,
                                SUM(CASE WHEN MONTH(so_master.trans_date) = 4  THEN so_trans.taxable_amount ELSE 0 END) AS apr_amt,
                                SUM(CASE WHEN MONTH(so_master.trans_date) = 5  THEN so_trans.taxable_amount ELSE 0 END) AS may_amt,
                                SUM(CASE WHEN MONTH(so_master.trans_date) = 6  THEN so_trans.taxable_amount ELSE 0 END) AS jun_amt,
                                SUM(CASE WHEN MONTH(so_master.trans_date) = 7  THEN so_trans.taxable_amount ELSE 0 END) AS jul_amt,
                                SUM(CASE WHEN MONTH(so_master.trans_date) = 8  THEN so_trans.taxable_amount ELSE 0 END) AS aug_amt,
                                SUM(CASE WHEN MONTH(so_master.trans_date) = 9  THEN so_trans.taxable_amount ELSE 0 END) AS sep_amt,
                                SUM(CASE WHEN MONTH(so_master.trans_date) = 10  THEN so_trans.taxable_amount ELSE 0 END) AS oct_amt,
                                SUM(CASE WHEN MONTH(so_master.trans_date) = 11  THEN so_trans.taxable_amount ELSE 0 END) AS nov_amt,
                                SUM(CASE WHEN MONTH(so_master.trans_date) = 12  THEN so_trans.taxable_amount ELSE 0 END) AS dec_amt,
                                (CASE WHEN  ".$data['report_type']."=1 THEN item_category.category_name ELSE (CASE WHEN  ".$data['report_type']." = 2 THEN IFNULL(zone_name,'NIL') ELSE (CASE WHEN  ".$data['report_type']." = 3 THEN party_master.source ELSE party_master.business_type END)  END) END) AS category_name";
        $queryData['leftJoin']['item_master'] = "item_master.id = so_trans.item_id";
        $queryData['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        $queryData['leftJoin']['so_master'] = "so_trans.trans_main_id = so_master.id ";
        $queryData['leftJoin']['party_master'] = "party_master.id = so_master.party_id ";
        $queryData['leftJoin']['sales_zone'] = "sales_zone.id = party_master.sales_zone_id ";

        $queryData['where']['so_master.trans_date >='] = $this->startYearDate;
        $queryData['where']['so_master.trans_date <='] = $this->endYearDate;
        if(!empty($data['sales_executive'])){$queryData['where']['so_master.sales_executive'] =$data['sales_executive'] ;}
        $queryData['group_by'][] = $data['group_by'];
        $result = $this->rows($queryData);
       
        return $result;
    }
    
    public function getExecutivePerformanceData($data){
        $queryData = array();
        $queryData['tableName'] = "employee_master";
        $queryData['select'] = "employee_master.emp_name,
                                SUM(CASE WHEN trans_date >= '".$this->startYearDate."' AND trans_date <= '".$this->endYearDate."' AND MONTH(so_master.trans_date) = 1  THEN so_master.taxable_amount ELSE 0 END) AS jan_amt,
                                SUM(CASE WHEN trans_date >= '".$this->startYearDate."' AND trans_date <= '".$this->endYearDate."' AND MONTH(so_master.trans_date) = 2  THEN so_master.taxable_amount ELSE 0 END) AS feb_amt,
                                SUM(CASE WHEN trans_date >= '".$this->startYearDate."' AND trans_date <= '".$this->endYearDate."' AND MONTH(so_master.trans_date) = 3  THEN so_master.taxable_amount ELSE 0 END) AS mar_amt,
                                SUM(CASE WHEN trans_date >= '".$this->startYearDate."' AND trans_date <= '".$this->endYearDate."' AND MONTH(so_master.trans_date) = 4  THEN so_master.taxable_amount ELSE 0 END) AS apr_amt,
                                SUM(CASE WHEN trans_date >= '".$this->startYearDate."' AND trans_date <= '".$this->endYearDate."' AND MONTH(so_master.trans_date) = 5  THEN so_master.taxable_amount ELSE 0 END) AS may_amt,
                                SUM(CASE WHEN trans_date >= '".$this->startYearDate."' AND trans_date <= '".$this->endYearDate."' AND MONTH(so_master.trans_date) = 6  THEN so_master.taxable_amount ELSE 0 END) AS jun_amt,
                                SUM(CASE WHEN trans_date >= '".$this->startYearDate."' AND trans_date <= '".$this->endYearDate."' AND MONTH(so_master.trans_date) = 7  THEN so_master.taxable_amount ELSE 0 END) AS jul_amt,
                                SUM(CASE WHEN trans_date >= '".$this->startYearDate."' AND trans_date <= '".$this->endYearDate."' AND MONTH(so_master.trans_date) = 8  THEN so_master.taxable_amount ELSE 0 END) AS aug_amt,
                                SUM(CASE WHEN trans_date >= '".$this->startYearDate."' AND trans_date <= '".$this->endYearDate."' AND MONTH(so_master.trans_date) = 9  THEN so_master.taxable_amount ELSE 0 END) AS sep_amt,
                                SUM(CASE WHEN trans_date >= '".$this->startYearDate."' AND trans_date <= '".$this->endYearDate."' AND MONTH(so_master.trans_date) = 10  THEN so_master.taxable_amount ELSE 0 END) AS oct_amt,
                                SUM(CASE WHEN trans_date >= '".$this->startYearDate."' AND trans_date <= '".$this->endYearDate."' AND MONTH(so_master.trans_date) = 11  THEN so_master.taxable_amount ELSE 0 END) AS nov_amt,
                                SUM(CASE WHEN trans_date >= '".$this->startYearDate."' AND trans_date <= '".$this->endYearDate."' AND MONTH(so_master.trans_date) = 12  THEN so_master.taxable_amount ELSE 0 END) AS dec_amt";
        $queryData['leftJoin']['so_master'] = "so_master.sales_executive = employee_master.id";
        $queryData['where']['employee_master.is_se'] = 'Yes';
		$queryData['group_by'][] = 'employee_master.id';

        return $this->rows($queryData);        
    }
    
    /* Expense Analysis Report */
    public function getSalesExpenseAnalysisData($data){
        $queryData['tableName'] = "so_trans";
        $queryData['select'] = "so_master.party_id,party_master.party_name,SUM(so_trans.net_amount) as net_amount, expense.expense_amount";
        $queryData['leftJoin']['so_master'] = "so_trans.trans_main_id = so_master.id";
        $queryData['leftJoin']["(SELECT SUM(amount) as expense_amount,exp_by_id FROM expense_manager WHERE exp_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."' GROUP BY exp_by_id) as expense"] = "expense.exp_by_id = so_master.party_id";
        $queryData['leftJoin']['party_master'] = "party_master.id  = so_master.party_id";
        $queryData['customWhere'][] = "so_master.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        if(!empty($data['party_id'])):
            $queryData['where']['so_master.party_id'] = $data['party_id'];
        else:
            $queryData['where']['so_master.party_id !='] = 0;
        endif;
        $queryData['group_by'][] = 'so_master.party_id';
        $result = $this->rows($queryData);        
        return $result;
    }
    
    /* Appointment Register Data */
    public function getAppointmentRegister($data){ 
        $queryData = array();
        $queryData['tableName'] = "sales_logs";
        $queryData['select'] = "sales_logs.id,sales_logs.ref_date,sales_logs.log_type,sales_logs.notes,sales_logs.remark,sales_logs.updated_at,sales_logs.mode,sales_logs.lead_id,lead_master.party_name ,employee_master.emp_name,sales_logs.created_by";

        $queryData['leftJoin']['lead_master'] = "lead_master.id = sales_logs.lead_id";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = sales_logs.executive_id";
        
        if(!empty($data['executive_id'])):
            $queryData['where']['sales_logs.executive_id'] = $data['executive_id'];
        endif;
        
        if(!empty($data['mode'])):
            $queryData['where']['sales_logs.mode'] = $data['mode'];
        endif;

        if(!empty($data['status'])) {
            if($data['status'] == 1){
                $queryData['customWhere'][] = 'sales_logs.updated_at IS NULL';
            }elseif($data['status'] == 2){
                $queryData['customWhere'][] = 'sales_logs.updated_at IS NOT NULL';
            }elseif($data['status'] == 3){ 
                $queryData['customWhere'][] = 'DATE(sales_logs.ref_date) < DATE(sales_logs.updated_at)';
            }
        }

        if(!empty($data['from_date'])){
            $queryData['where']['DATE(sales_logs.ref_date) >='] = $data['from_date'];
        }

        if(!empty($data['to_date'])){
            $queryData['where']['DATE(sales_logs.ref_date) <='] = $data['to_date'];
        }
        
        $queryData['where']['sales_logs.log_type'] = 3;

		$queryData['order_by']['sales_logs.ref_date'] = 'ASC';

        $result = $this->rows($queryData);
        return $result;
    }

    /*  Followup Register Data*/
    public function getFollowUpRegister($data){
        $queryData = array();
        $queryData['tableName'] = "sales_logs";
        $queryData['select'] = "sales_logs.id,sales_logs.created_at,lead_master.executive_id,sales_logs.notes,lead_master.party_type,sales_logs.lead_id,lead_master.party_name,employee_master.emp_name,lead_master.business_type";
        $queryData['leftJoin']['lead_master'] = "lead_master.id = sales_logs.lead_id";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = lead_master.executive_id";

		if(!empty($data['business_type'])){
            $queryData['where']['lead_master.business_type'] = $data['business_type'];
        }
        if(!empty($data['from_date'])){
            $queryData['where']['DATE(sales_logs.created_at) >='] = $data['from_date'];
        }
        if(!empty($data['to_date'])){
            $queryData['where']['DATE(sales_logs.created_at) <='] = $data['to_date'];
        }
        if(!empty($data['party_id'])){
            $queryData['where']['sales_logs.lead_id'] = $data['party_id'];
        }
        $queryData['where']['sales_logs.log_type'] = 2;

        $result = $this->rows($queryData);
        return $result;
    }
    
    /*Customer Order Monitoring Report*/
	public function getCustOrdMonitoring($data = array()){
		$queryData['tableName'] = "sales_order";
		$queryData['select'] = "sales_order.*, lead_master.party_code, lead_master.party_name, employee_master.emp_code, employee_master.emp_name, em.emp_code as created_code,em.emp_name as created_name";
		$queryData['leftJoin']['lead_master'] = "lead_master.id = sales_order.party_id";
		$queryData['leftJoin']['employee_master em'] = "em.id = sales_order.created_by";
		$queryData['leftJoin']['employee_master'] = "employee_master.id = sales_order.distributor_id";
		
		if(!empty($data['from_date']) && !empty($data['to_date'])){ $queryData['customWhere'][] = "sales_order.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'"; }
		
		if(!empty($data['emp_id'])){
			$queryData['where']['sales_order.created_by'] = $data['emp_id'];
		}
		
		$queryData['order_by']['sales_order.trans_date'] = "DESC";
		
		return $this->rows($queryData);
	}
    
}
?>