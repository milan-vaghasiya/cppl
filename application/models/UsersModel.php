<?php
class UsersModel extends MasterModel{
    private $designationMaster = "emp_designation";
    private $empMaster = "employee_master";
    private $attendance_log = "attendance_log";
    private $leaveMaster = "leave_master";

    /********** Designation **********/
        public function getDesignationDTRows($data){
            $data['tableName'] = $this->designationMaster;
            
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "title";

            $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

            if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
            return $this->pagingRows($data);
        }

        public function getDesignations($data=array()){
            $queryData['tableName'] = $this->designationMaster;
            return $this->rows($queryData);
        }

        public function getDesignation($data){
            $queryData['tableName'] = $this->designationMaster;
            $queryData['where']['id'] = $data['id'];
            return $this->row($queryData);
        }

        public function saveDesignation($data){
            try{
                $this->db->trans_begin();

                $data['checkDuplicate'] = ['title'];
                $result = $this->store($this->designationMaster,$data,'Designation');

                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Exception $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }
    /********** End Designation **********/

    /********** Users **********/
        public function getEmployeeDTRows($data){
            $data['tableName'] = $this->empMaster;
            $data['select'] = "employee_master.*,emp_designation.title as emp_designation";
            $data['leftJoin']['emp_designation'] = "employee_master.emp_designation = emp_designation.id";
            //$data['where']['employee_master.emp_role !='] = "-1";
            $data['customWhere'][] = "employee_master.emp_role NOT IN (5,-1)";

            if($data['status']==0):
                $data['where']['employee_master.is_active']=1;
            else:
                $data['where']['employee_master.is_active']=0;
            endif;
            if(!in_array($this->userRole,[1,-1])):
                if($this->leadRights == 2): // Zone Wise Leads Rights
                    $queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) >0 OR employee_master.id = '.$this->loginId.')';
                elseif($this->leadRights == 1):
                    $queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) >0 OR employee_master.id = '.$this->loginId.')';
                endif;
            endif;
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "employee_master.emp_name";
            $data['searchCol'][] = "employee_master.emp_code";
            $data['searchCol'][] = "emp_designation.title";
            $data['searchCol'][] = "employee_master.emp_contact";
            
            $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;
            if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
            
            return $this->pagingRows($data);
        }

        public function getEmployeeList($data=array()){
            $queryData['tableName'] = $this->empMaster;
            $queryData['select'] = "employee_master.*,emp_designation.title as emp_designation, shift_master.shift_name, shift_master.late_in, shift_master.late_fine, shift_master.shift_start";
            $queryData['leftJoin']['emp_designation'] = "employee_master.emp_designation = emp_designation.id";
            $queryData['leftJoin']['shift_master'] = "employee_master.shift_id = shift_master.id";

            if(!empty($data['executive_target'])){
                $queryData['select'] .= ",executive_targets.id as target_id,executive_targets.new_lead,executive_targets.sales_amount,GROUP_CONCAT(sales_zone.zone_name) as zone_name,executive_targets.new_visit";
                $queryData['leftJoin']['executive_targets'] = "executive_targets.emp_id = employee_master.id AND executive_targets.target_month = '".$data['month']."'";
                $queryData['leftJoin']['sales_zone'] = ' find_in_set(sales_zone.id,employee_master.zone_id) > 0 ';
               
                $queryData['group_by'][]='employee_master.id';
            }
            
            if(!empty($data['emp_role'])):
                $queryData['where_in'] = $data['emp_role'];
            endif;

            if(!empty($data['zone_id'])):
                $queryData['where']['find_in_set("'.$data['zone_id'].'", employee_master.zone_id ) >'] = 0;
            endif;

            if(!empty($data['emp_designation'])):
                $queryData['where']['emp_designation'] = $data['emp_designation'];
            endif;

            if(!empty($data['is_active'])):
                $queryData['where_in']['is_active'] = $data['is_active'];
            endif;

            if(!empty($data['is_se'])):
                $queryData['where']['is_se'] = $data['is_se'];
            endif;

            if(!empty($data['emp_role'])):
                $queryData['where_in'] = $data['emp_role'];
            endif;

            if(empty($data['all'])):
                $queryData['where']['employee_master.emp_role !='] = "-1";
            endif;
            
            if(!empty($data['is_attendance'])):
                $queryData['where']['is_attendance'] = $data['is_attendance'];
            endif;
            
            if(!empty($data['executive_ids'])):
				$queryData['customWhere'][] = '(find_in_set(employee_master.id,"'.$data['executive_ids'].'")) > 0';
			endif;            
            
            /*if(!in_array($this->userRole,[1,-1])){
                $queryData['customWhere'][] = 'find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) >0 OR employee_master.id = '.$this->loginId;
            }*/
            if(!in_array($this->userRole,[1,-1,5])):
                if($this->leadRights == 2): // Zone Wise Leads Rights
                    $queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) >0 OR employee_master.id = '.$this->loginId.')';
                elseif($this->leadRights == 1):
                    $queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) >0 OR employee_master.id = '.$this->loginId.')';
                endif;
            endif;
            return $this->rows($queryData);
        }

        public function getEmployee($data){
            $queryData['tableName'] = $this->empMaster;
            $queryData['select'] = "employee_master.*,emp_designation.title as designation_name";
            $queryData['leftJoin']['emp_designation'] = "employee_master.emp_designation = emp_designation.id";
            $queryData['where']['employee_master.id'] = $data['id'];
            return $this->row($queryData);
        }

        public function saveEmployee($data){
            try{
                $this->db->trans_begin();

                // if(empty($data['id'])):
                    $data['emp_psc'] = $data['emp_password'];
                    $data['emp_password'] = md5($data['emp_password']); 
                // endif;
                $data['super_auth_id'] = "";
                if(!empty($data['auth_id'])){
                    $authData = $this->getEmployee(['id'=>$data['auth_id']]);
                    $data['super_auth_id'] = ((!empty($authData->super_auth_id))?$authData->super_auth_id.',':'').$data['auth_id'];
                }
                $data['travel_by'] = (!empty($data['travel_by']) ? implode(",",$data['travel_by']) : "");
                $result =  $this->store($this->empMaster,$data,'Employee');

                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Exception $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }        
        }

        public function activeInactive($postData){
            try{
                $this->db->trans_begin();

                $result = $this->store($this->empMaster,$postData,'');
                $result['message'] = "Employee ".(($postData['is_active'] == 1)?"Activated":"De-activated")." successfully.";
                
                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Exception $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }

        public function changePassword($data){
            try{
                $this->db->trans_begin();

                if(empty($data['id'])):
                    return ['status'=>2,'message'=>'Somthing went wrong...Please try again.'];
                endif;

                $empData = $this->getEmployee(['id'=>$data['id']]);
                if(md5($data['old_password']) != $empData->emp_password):
                    return ['status'=>0,'message'=>['old_password'=>"Old password not match."]];
                endif;

                $postData = ['id'=>$data['id'],'emp_password'=>md5($data['new_password']),'emp_psc'=>$data['new_password']];
                $result = $this->store($this->empMaster,$postData);
                $result['message'] = "Password changed successfully.";

                if($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Exception $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }

        public function resetPassword($id){
            try{
                $this->db->trans_begin();

                $data['id'] = $id;
                $data['emp_psc'] = '123456';
                $data['emp_password'] = md5($data['emp_psc']); 
                
                $result = $this->store($this->empMaster,$data);
                $result['message'] = 'Password Reset successfully.';

                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Exception $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }
    /********** End Users **********/

    /********** Attendance **********/
        public function getEmployeeData(){
            $data['tableName'] = $this->attendance_log;
            $data['where']['emp_id'] = $this->loginId;
            $data['where']['DATE(punch_date)'] = date("Y-m-d");
            $data['order_by']['punch_date'] = "DESC";
            $data['limit'] = 1;
            return $this->row($data);
        }

        public function getEmpLogData(){
            $data['tableName'] = $this->attendance_log;
            $data['where']['emp_id'] = $this->loginId;
            $data['order_by']['punch_date'] = "DESC";
            return $this->rows($data);
        }

        public function saveAttendance($data){
            try{
                $this->db->trans_begin();
                
                $result = $this->store($this->attendance_log,$data,'Attendance Log');
                
                // Insert Location Log
                $locLog = Array();
                $locLog['log_type'] = ($data['type'] == 'IN') ? 1 : 2;
                $locLog['emp_id'] = $this->loginId;
                $locLog['log_time'] = $data['punch_date'];
                $locLog['location'] = (!empty($data['start_location'])?$data['start_location']:'');
                $locLog['address'] = (!empty($data['loc_add'])?$data['loc_add']:'');
                $llResult = $this->saveLocationLog($locLog);

                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Exception $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }

        public function getPunchByDate($param = []){
            $data['tableName'] = $this->attendance_log;
            $data['select'] = "attendance_log.*,employee_master.emp_code,employee_master.emp_name, shift_master.shift_name, shift_master.late_in, shift_master.late_fine, shift_master.shift_start";
            $data['leftJoin']['employee_master'] = "employee_master.id = attendance_log.emp_id AND employee_master.is_active = 1";
            $data['leftJoin']['shift_master'] = "shift_master.id = attendance_log.shift_id";
            if(!empty($param['from_date'])){$data['where']['DATE(attendance_log.punch_date) >= '] = $param['from_date'];}
            if(!empty($param['to_date'])){$data['where']['DATE(attendance_log.punch_date) <= '] = $param['to_date'];}
            if(!empty($param['report_date'])){$data['where']['DATE(attendance_log.punch_date)'] = $param['report_date'];}
            if(!empty($param['emp_id'])){$data['where']['attendance_log.emp_id'] = $param['emp_id'];}
            
			$data['order_by']['attendance_log.punch_date'] = 'ASC';
			
            if(!in_array($this->userRole,[1,-1])):
                if($this->leadRights == 2): // Zone Wise Leads Rights
                    $queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) >0 OR employee_master.id = '.$this->loginId.')';
                elseif($this->leadRights == 1):
                    $queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) >0 OR employee_master.id = '.$this->loginId.')';
                endif;
            endif;
            return $this->rows($data);
        }
        
        public function checkDuplicateAttendance($data){  
            $queryData['tableName'] = $this->attendance_log;
            $queryData['where']['type'] = $data['type'];
            $queryData['where']['emp_id'] = $data['emp_id'];
            $queryData['where']['DATE(punch_date)'] = date('Y-m-d',strtotime($data['punch_date']));
            
            if(!empty($data['id'])) { 
                $queryData['where']['id != '] = $data['id'];
            }
            
            $queryData['resultType'] = "numRows";
            return $this->specificRow($queryData);
        }

        public function getAttendanceDTRows($data){ 
            $data['tableName'] = $this->attendance_log;
            $data['select'] = "attendance_log.*,employee_master.emp_code,employee_master.emp_name";
            $data['leftJoin']['employee_master'] = "employee_master.id = attendance_log.emp_id AND employee_master.is_active = 1";
            $data['leftJoin']['shift_master'] = "shift_master.id = attendance_log.shift_id";
            
            if(!empty($data['from_date'])){$data['where']['DATE(attendance_log.punch_date) >= '] = $data['from_date'];}
            if(!empty($data['to_date'])){$data['where']['DATE(attendance_log.punch_date) <= '] = $data['to_date'];}
			$data['order_by']['employee_master.emp_code'] = 'ASC';
			$data['order_by']['attendance_log.punch_date'] = 'ASC';
			
            if(!in_array($this->userRole,[1,-1])):
                if($this->leadRights == 2): // Zone Wise Leads Rights
                    $queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) >0 OR employee_master.id = '.$this->loginId.')';
                elseif($this->leadRights == 1):
                    $queryData['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) >0 OR employee_master.id = '.$this->loginId.')';
                endif;
            endif;
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "employee_master.emp_code";
            $data['searchCol'][] = "employee_master.emp_name";
            $data['searchCol'][] = "attendance_log.type";
            $data['searchCol'][] = "DATE_FORMAT(attendance_log.punch_date,'%d-%m-%Y %H:%i:%s')";
            $data['searchCol'][] = "attendance_log.loc_add";
            $data['searchCol'][] = "";

            $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

            if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
            return $this->pagingRows($data);
        } 

        public function getManualAttendanceData($data){
            $data['tableName'] = $this->attendance_log;
            $data['where']['id'] = $data['id'];
            return $this->row($data);
        }

        public function deleteManualAttendance($id){
            try{
                $this->db->trans_begin();

                $result = $this->trash($this->attendance_log,['id'=>$id],'Manual Attendance');

                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Throwable $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }
        
        public function getMonthlyAttendance($param = []){
            $data['tableName'] = $this->empMaster;
            $data['select'] = "employee_master.*,DATE(aLog.punch_date) as punch_date,lm.leave_date";

            $data['leftJoin']['(SELECT punch_date,emp_id FROM attendance_log WHERE is_delete = 0 AND MONTH(punch_date) = "'.date('m',strtotime($param['month'])).'" AND YEAR(punch_date) = "'.date('Y',strtotime($param['month'])).'" GROUP BY DATE(punch_date),emp_id) as aLog'] = "employee_master.id = aLog.emp_id AND employee_master.is_active = 1";

            $data['leftJoin']['(SELECT leave_date,emp_id FROM leave_master WHERE is_delete = 0 AND approve_by > 0 AND leave_date >= "'.$param['month'].'" AND MONTH(leave_date) = "'.date('m',strtotime($param['month'])).'" AND YEAR(leave_date) = "'.date('Y',strtotime($param['month'])).'" GROUP BY leave_date,emp_id) as lm'] = "employee_master.id = lm.emp_id AND employee_master.is_active = 1";
            
            if(!empty($param['is_se'])):
                $data['where']['employee_master.is_se'] = $param['is_se'];
            endif;
            
            $data['where']['employee_master.emp_role !='] = "-1";
            $data['order_by']['employee_master.emp_code'] = "ASC";
            return $this->rows($data);
        }
        
    /********** End Attendance **********/
    
    /********** leave **********/
        public function getLeaveDTRows($data){
            $data['tableName'] = $this->leaveMaster;
            $data['select'] = "leave_master.*,employee_master.emp_name,select_master.label";
            $data['leftJoin']['employee_master'] = "employee_master.id = leave_master.emp_id";
            $data['leftJoin']['select_master'] = "select_master.id = leave_master.leave_type_id";

            if($data['login_emp_id'] != 1):
                //$data['where']['leave_master.emp_id'] = $data['login_emp_id'];
            endif;

            if($data['status'] == 2){
                $data['where']['leave_master.approve_by >'] = 0;
            }else{
                $data['where']['leave_master.approve_by'] = 0;
            }

            if(!in_array($this->userRole,[1,-1])):
                $data['customWhere'][] = '(find_in_set("'.$this->loginId.'", employee_master.super_auth_id ) >0 OR employee_master.id = '.$this->loginId.')';
            endif;
            
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "employee_master.emp_name";
            $data['searchCol'][] = "DATE_FORMAT(leave_master.start_date,'%d-%m-%Y')";
            $data['searchCol'][] = "DATE_FORMAT(leave_master.end_date,'%d-%m-%Y')";
            $data['searchCol'][] = "leave_master.total_days";
            $data['searchCol'][] = "select_master.label";
            $data['searchCol'][] = "leave_master.remark";

            $columns =array(); foreach($data['searchCol'] as $row): $columns[] = $row; endforeach;

            if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
            $result = $this->pagingRows($data);
            return $result;
        }

        public function checkDuplicateLeave($leave_date,$emp_id,$id=""){
            $month = date('m',strtotime($leave_date));
            $year = date('Y',strtotime($leave_date));
            $data['tableName'] = $this->leaveMaster;
            $data['where']['leave_date'] = $leave_date;
            $data['where']['emp_id'] = $emp_id;
            $data['where']['MONTH(leave_master.leave_date)'] = $month;
            $data['where']['YEAR(leave_master.leave_date)'] = $year ;
            if(!empty($id))
                $data['where']['id !='] = $id;
            return $this->numRows($data);
        }

        public function getLeave($data){
            $queryData['tableName'] = $this->leaveMaster;
            $queryData['where']['id'] = $data['id'];
            return $this->row($queryData);
        }

        public function saveLeave($data){
            try{
                $this->db->trans_begin();

                // if($this->checkDuplicateLeave($data['leave_date'],$data['emp_id'],$data['id']) > 0):
                //     $errorMessage['leave_date'] = "Leave date is duplicate.";
                //     return ['status'=>0,'message'=>$errorMessage];
                // else:
                    $result = $this->store($this->leaveMaster,$data,'Leave');

                    if ($this->db->trans_status() !== FALSE):
                        $this->db->trans_commit();
                        return $result;
                    endif;
                // endif;
            }catch(\Exception $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }

        public function approveLeave($data){
            try{
                $this->db->trans_begin();
                
                $result = $this->store($this->leaveMaster,$data,'Leave');

                if ($this->db->trans_status() !== FALSE):
                    $this->db->trans_commit();
                    return $result;
                endif;
            }catch(\Exception $e){
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
            }	
        }
        
        public function checkLeaveDate($param=[]){
            $queryData['tableName'] = $this->leaveMaster;
            $queryData['select'] = "IFNULL(SUM(total_days),'0') as leave_count";
            
            if(!empty($param['approve_by'])):
                $queryData['where']['leave_master.approve_by'] = $param['approve_by'];
            endif;
            
            if(!empty($param['emp_id'])):
                $queryData['where']['leave_master.emp_id'] = $param['emp_id'];
            endif;
            
            if(!empty($param['auth_by'])):
                $queryData['where']['leave_master.auth_by'] = $param['auth_by'];
            endif;
            
            if(!empty($param['from_date']) AND !empty($param['to_date'])):
                $queryData['where']['DATE(leave_master.start_date) <= '] = $param['to_date'];
                $queryData['where']['DATE(leave_master.end_date) >= '] = $param['from_date'];
            endif;
            
            return $this->row($queryData);
        }
        
        public function getDealerList($data = array()){
    		$queryData = array();
    		$queryData['tableName']  = $this->empMaster;
    		$queryData['select'] = "employee_master.id,employee_master.emp_code,employee_master.emp_name";
    
    		if(!empty($data['executive_ids'])):
    			$queryData['customWhere'][] = "FIND_IN_SET('".$data['executive_ids']."',employee_master.executive_ids) > 0";
    		endif;
    		
    		if(!empty($data['single_rows'])){
    			return $this->row($queryData);
    		}else{			
    			return $this->rows($queryData);
    		}
    	}
    /********** End Leave**********/
}
?>