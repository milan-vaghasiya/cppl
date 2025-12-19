
<?php
class ExpenseModel extends MasterModel
{
    private $expense_manager = "expense_manager";	

    /********** Expense **********/
        public function getNextExpNo(){
            $data['tableName'] = $this->expense_manager;
            $data['select'] = "MAX(exp_no) as exp_no";
            $data['where']['YEAR(exp_date)'] = date("Y");
            $data['where']['MONTH(exp_date)'] = date("m");
            $maxNo = $this->specificRow($data)->exp_no;
            $nextExpNo = (!empty($maxNo)) ? ($maxNo + 1) : 1;
            return $nextExpNo;
        }

        public function getExpenseDTRows($data){
            $data['tableName'] = $this->expense_manager;
            $data['select'] = "expense_manager.*,select_master.label as expense_label,(CASE WHEN exp_source = 2 THEN party_master.party_name ELSE employee_master.emp_name END) as exp_by_name";
            $data['leftJoin']['employee_master'] = "employee_master.id = expense_manager.exp_by_id";
            $data['leftJoin']['party_master'] = "party_master.id = expense_manager.exp_by_id";
            $data['leftJoin']['select_master'] = "select_master.id = expense_manager.exp_type";

            if(empty($data['status'])) { $data['where']['expense_manager.status'] = 0; }
            else{ $data['where']['expense_manager.status'] = $data['status']; }
            
            if($data['party_id'] != 'ALL'){$data['where']['expense_manager.exp_by_id'] = $data['party_id'];}
            if($data['emp_id'] != 'ALL' && !empty($data['emp_id'])){$data['where']['expense_manager.exp_by_id'] = $data['emp_id'];}
            if(!empty($data['from_date'])){$data['customWhere'][] = "expense_manager.exp_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";}

            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "DATE_FORMAT(exp_date,'%d-%m-%Y')";
            $data['searchCol'][] = "exp_number";
            $data['searchCol'][] = "employee_master.emp_name";
            $data['searchCol'][] = "select_master.label";
            $data['searchCol'][] = "expense_manager.location";
            $data['searchCol'][] = "demand_amount";
            $data['searchCol'][] = "amount";
            $data['searchCol'][] = "DATE_FORMAT(created_at,'%d-%m-%Y')";
            $data['searchCol'][] = "expense_manager.rej_reason";

            $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

            if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
            return $this->pagingRows($data);
        }

        public function getExpenseData($data=[]){
            $queryData['tableName'] = $this->expense_manager;
            $queryData['select'] = "expense_manager.*,employee_master.emp_name,party_master.party_name,(CASE WHEN expense_manager.status = 0 THEN demand_amount ELSE amount END) as amount,select_master.label as expense_label";
            $queryData['leftJoin']['employee_master'] = "employee_master.id = expense_manager.exp_by_id";
            $queryData['leftJoin']['party_master'] = "party_master.id = expense_manager.exp_by_id";
            $queryData['leftJoin']['select_master'] = "select_master.id = expense_manager.exp_type";

            if(!empty($data['status'])) { $queryData['where']['expense_manager.status'] = $data['status']; }
            if(!empty($data['ids'])) { $queryData['where_in']['expense_manager.id'] = str_replace("~", ",", $data['ids']); }
            return $this->rows($queryData);
        }

        public function getExpense($data){
            $queryData['tableName'] = $this->expense_manager;
            $queryData['select'] = "expense_manager.*,employee_master.emp_name,party_master.party_name,(CASE WHEN expense_manager.status = 0 THEN demand_amount ELSE amount END) as amount,select_master.label as expense_label";
            $queryData['leftJoin']['employee_master'] = "employee_master.id = expense_manager.exp_by_id";
            $queryData['leftJoin']['party_master'] = "party_master.id = expense_manager.exp_by_id";
            $queryData['leftJoin']['select_master'] = "select_master.id = expense_manager.exp_type";

            $queryData['where']['expense_manager.id'] = $data['id'];
            return $this->row($queryData);
        }

        public function saveExpense($data){
            try{
                $this->db->trans_begin();

                $result = $this->store($this->expense_manager,$data,'Expense');

                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Exception $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }

        public function saveBulkExpenseApproval($data){
            try{
                $this->db->trans_begin();

                $expData = $this->getExpenseData(['ids'=>$data['ids']]);

                foreach($expData as $row):
                    $approveData = [
                        'id' => $row->id,
                        'amount' => $row->demand_amount,
                        'approved_by' => $this->loginId,
                        'approved_at' => date('Y-m-d H:i:s'),
                        'status' => 1,
                        'updated_by' => $this->loginId,
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    $this->store($this->expense_manager, $approveData);
                endforeach;

                $result = ['status'=>1,'message'=>"All Expense Approved Successfully."];

                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Exception $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }
    /********** End Expense **********/
}
?>