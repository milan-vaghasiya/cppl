<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getHrDtHeader($page){
    /* Designation Header */
    $data['designation'][] = ["name"=>"Action","sortable"=>"FALSE","textAlign"=>"center"];
	$data['designation'][] = ["name"=>"#","sortable"=>"FALSE","textAlign"=>"center"];
    $data['designation'][] = ["name"=>"Designation Name"];
    // $data['designation'][] = ["name"=>"Remark"];

    /* Employee Header */
    $data['employees'][] = ["name"=>"Action"];
	$data['employees'][] = ["name"=>"#","textAlign"=>'center']; 
    $data['employees'][] = ["name"=>"Employee Name"];
    $data['employees'][] = ["name"=>"Employee Code","textAlign"=>'center'];
    $data['employees'][] = ["name"=>"Designation"];
    $data['employees'][] = ["name"=>"Contact No.","textAlign"=>'center'];
    
    /* Leave Header */
    $data['leave'][] = ["name"=>"Action","textAlign"=>'center'];
    $data['leave'][] = ["name"=>"#","textAlign"=>'center']; 
    $data['leave'][] = ["name"=>"Employee Name","textAlign"=>'center'];
    $data['leave'][] = ["name"=>"From Date","textAlign"=>'center'];
    $data['leave'][] = ["name"=>"To Date","textAlign"=>'center'];
    $data['leave'][] = ["name"=>"Leave Days","textAlign"=>'center'];
    $data['leave'][] = ["name"=>"Leave Type","textAlign"=>'center'];
    $data['leave'][] = ["name"=>"Remark","textAlign"=>'center'];
    
    /* Attendance Header */
    $data['attendance'][] = ["name"=>"Action","textAlign"=>'center'];
    $data['attendance'][] = ["name"=>"#","textAlign"=>'center']; 
    $data['attendance'][] = ["name"=>"Code","textAlign"=>'center'];
    $data['attendance'][] = ["name"=>"Emp Name","textAlign"=>'center'];
    $data['attendance'][] = ["name"=>"Type","textAlign"=>'center'];
    $data['attendance'][] = ["name"=>"Punch Time","textAlign"=>'center'];
    $data['attendance'][] = ["name"=>"Meter Reading","textAlign"=>'center'];
    $data['attendance'][] = ["name"=>"Location","textAlign"=>'center'];
    $data['attendance'][] = ["name"=>"Image","textAlign"=>'center'];

    return tableHeader($data[$page]);
}

/* Designation Table Data */
function getDesignationData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Designation'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'right_modal', 'form_id' : 'editDesignation', 'title' : 'Update Designation'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="mdi mdi-square-edit-outline" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->title];
}

/* Employee Table Data */
function getEmployeeData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Employee'}";
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'right_modal_lg', 'form_id' : 'editEmployee', 'title' : 'Update Employee'}";
    
    $leaveButton = '';$addInDevice = '';$activeButton = '';$empRelieveBtn = '';$editButton = '';$deleteButton = '';    
    if($data->is_active == 1):
        $activeParam = "{'postData':{'id' : ".$data->id.", 'is_active' : 0},'fnsave':'activeInactive','message':'Are you sure want to De-Active this Employee?'}";
        $activeButton = '<a class="btn btn-youtube permission-modify" href="javascript:void(0)" datatip="De-Active" flow="down" onclick="confirmStore('.$activeParam.');"><i class="fa fa-ban"></i></a>';  

        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="mdi mdi-square-edit-outline" ></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    else:
        $activeParam = "{'postData':{'id' : ".$data->id.", 'is_active' : 1},'fnsave':'activeInactive','message':'Are you sure want to Active this Employee?'}";
        $activeButton = '<a class="btn btn-success permission-remove" href="javascript:void(0)" datatip="Active" flow="down" onclick="confirmStore('.$activeParam.');"><i class="fa fa-check"></i></a>';            
    endif;
    
    $CI = & get_instance();
    $userRole = $CI->session->userdata('role');

    $resetPsw='';
    if(in_array($userRole,[-1,1])):
        $resetParam = "{'postData':{'id' : ".$data->id."},'fnsave':'resetPassword','message':'Are you sure want to Change ".$data->emp_name." Password?'}";
        $resetPsw='<a class="btn btn-danger" href="javascript:void(0)" onclick="confirmStore('.$resetParam.');" datatip="Reset Password" flow="down"><i class="fa fa-key"></i></a>';
    endif;
    
    $action = getActionButton($resetPsw.$leaveButton.$addInDevice.$activeButton.$empRelieveBtn.$editButton.$deleteButton);

    return [$action,$data->sr_no,$data->emp_name,$data->emp_code,$data->emp_designation,$data->emp_contact];
}

/* Leave Table Data */
function getLeaveData($data){
    $approveBtn = $editButton = $deleteButton = "";
    if(empty($data->approve_by)):
        $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Leave'}";
        $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'right_modal', 'form_id' : 'editLeave', 'title' : 'Update Leave'}";

        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="mdi mdi-square-edit-outline" ></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';

        $approveParam = "{'postData':{'id' : ".$data->id."},'fnsave':'approveLeave','message':'Are you sure want to Approve this Leave?'}";
        $approveBtn ='<a class="btn btn-primary" href="javascript:void(0)" onclick="confirmStore('.$approveParam.');" datatip="Approve" flow="down"><i class="fa fa-check"></i></a>';
    endif;
	
	$action = getActionButton($approveBtn.$editButton.$deleteButton);
    return [$action,$data->sr_no,$data->emp_name,(!empty($data->start_date) ? date('d-m-Y',strtotime($data->start_date)) : ''),(!empty($data->end_date) ? date('d-m-Y',strtotime($data->end_date)) : ''),floatval($data->total_days),$data->label,$data->remark];
}


/* Attendance Table Data */
function getAttendanceData($data){
    $deleteParam = "{'postData':{'id' : ".$data->id."},'fndelete' : 'deleteManualAttendence','message' : 'Attendance'}";    
    $editParam = "{'postData':{'id' : ".$data->id."},'modal_id' : 'right_modal', 'form_id' : 'editAttendance', 'title' : 'Update Attendance','fnedit' : 'editManualAttendence'}";

    $editButton = $deleteButton = "";
    //if($data->punch_type == 2){
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="mdi mdi-square-edit-outline" ></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="mdi mdi-trash-can-outline"></i></a>';
    //}

    $imgFile = '';
    if (!empty($data->img_file) && !empty($data->punch_date)) {
        $punchMonth   = date('Y-m', strtotime($data->punch_date));
        $currentMonth = date('Y-m');
        $lastMonth    = date('Y-m', strtotime('-1 month'));

        if ($punchMonth == $currentMonth || $punchMonth == $lastMonth) {
            $imgPath = base_url('assets/uploads/attendance_log/'.$data->img_file);
            $imgFile='<div class="picture-item">
                <a href="'.$imgPath.'" class="lightbox" target="_blank">
                    <img src="'.$imgPath.'" alt="" class="img-fluid" width="20" height="20" style="border-radius:0%;border: 0px solid #ccc;padding:3px;"/>
                </a> 
            </div>';
        }
    }

	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->emp_code,$data->emp_name,$data->type,date('d-m-Y H:i:s',strtotime($data->punch_date)),$data->meter,$data->loc_add,$imgFile];
}
?>