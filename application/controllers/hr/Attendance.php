<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Attendance extends MY_Controller{
    private $indexPage = "hr/attendance/index";
    private $monthlyAttendance = "hr/attendance/month_attendance";
    private $manualAttendance = "hr/attendance/manual_attendance";
	
	public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Attendance";
		$this->data['headData']->controller = "hr/attendance";
		$this->data['headData']->pageUrl = "hr/attendance";		
	}
	
	public function index(){
        $this->data['tableHeader'] = getHrDtHeader('attendance');
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows($from_date='',$to_date=''){ 
        $data = $this->input->post();
        $data['from_date'] = (!empty($from_date)?$from_date:date('Y-m-d'));
        $data['to_date'] = (!empty($to_date)?$to_date:date('Y-m-d'));
        $result = $this->usersModel->getAttendanceDTRows($data);
		
        $sendData = array();$i=1;
		foreach($result['data'] as $row):
			$row->sr_no = $i++;
			$sendData[] = getAttendanceData($row);
		endforeach;
		
        $result['data'] = $sendData;
        $this->printJson($result);
	}
	
    public function addManualAttendence(){
        $this->data['empList'] = $this->usersModel->getEmployeeList();
        $this->load->view($this->manualAttendance,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();
        if(empty($data['type'])){
			$errorMessage['type'] = "Type is required.";
        }
        if(empty($data['emp_id'])){
			$errorMessage['emp_id'] = "Employee is required.";
        }
        if(empty($data['attendance_date'])){
			$errorMessage['attendance_date'] = "Date is required.";
        }
        if(empty($data['punch_date'])){
			$errorMessage['punch_date'] = "Time is required.";
        }
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['shift_id'] = 1;
            $data['punch_date']  = ($data['attendance_date'] . " " .$data['punch_date']);
			if($this->usersModel->checkDuplicateAttendance($data) > 0):
                $this->printJson(['status'=>0,'message'=>"Attendance already added."]);
            else:
                unset($data['attendance_date']);
                $this->printJson($this->usersModel->saveAttendance($data));
            endif; 
        endif;
    }

    public function editManualAttendence(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->usersModel->getManualAttendanceData(['id'=>$data['id']]);
        $this->data['empList'] = $this->usersModel->getEmployeeList($data);
        $this->load->view($this->manualAttendance,$this->data);
    }

    public function deleteManualAttendence(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->usersModel->deleteManualAttendance($id));
        endif;
    }

 	/* Monthly Attendance Report */
 	public function monthlyAttendance(){
		$this->load->view($this->monthlyAttendance, $this->data);
	}
    
    public function getMonthlyReport($jsonData=''){
        if(!empty($jsonData)){$data = (Array) decodeURL($jsonData);}
        else{$data = $this->input->post();}
		
		$data['attendance_status'] = 1;
		$data['is_active'] = 1;
		$data['is_attendance'] = 1;
        $empData = $this->usersModel->getEmployeeList($data);
		
        $lastDay = intVal(date('t',strtotime($data['month'])));
        
		$thead='<tr style="background:#dddddd;"><th style="width:50px;">Code</th><th style="">Emp Name</th><th>Designation</th>';
        
		for($d=1;$d<=$lastDay;$d++):	
            $thead.='<th class="text-center">'.$d.'</th>'; 
        endfor;
        
        $thead.='<th class="text-center">Present <br> Days</th>';
        $thead.='<th class="text-center">Leave</th>';
        $thead.='<th class="text-center">Absent <br> Days</th>';    
        $thead.='<th class="text-center">Total <br> Days</th>';
        $thead.='</tr>';  
       
        $empArray = array_reduce($empData, function($emp, $employee) {
            $emp[$employee->emp_name][] = $employee;
            return $emp;
        }, []);

        $tbody='';$i=0;$lCount = 0;
        foreach($empData as $emp):
            $i++;
            $tbody.='<tr>';
            $tbody.='<td class="text-center" style="vertical-align:middle;font-size:12px;" >'.$emp->emp_code.'</td>';
            $tbody.='<td style="vertical-align:middle;font-size:12px;" >'.$emp->emp_name.'</td>';
            $tbody.='<td style="vertical-align:middle;font-size:12px;" >'.$emp->emp_designation.'</td>';
            
            $totalDays = date("t",strtotime($data['month'])); 
            $holiday = countDayInMonth("Sunday",$data['month']);
            $totalDays -= $holiday; 
            $presentDays = 0;$absentDays = 0;$weekOff = 0;$hd = 0;$wp = 0;$leave = 0;$punchRow='';$whRow='';
			$minLimitPerDay = 3600; // 1 Hour
			$hdLimit = 14400; // 4 Hour
            $totalWH = 0;$lateFine = 0;
            for($d=1;$d<=$lastDay;$d++):
                
                $day=0; $text="A"; $class="bg-danger text-white";$punchDates = array();$statusText = '';
				$dt = str_pad($d, 2, '0', STR_PAD_LEFT);
				$currentDate = date('Y-m-'.$dt,strtotime($data['month']));				
				$dayName = date("D", strtotime($currentDate));
				$empAttendanceLog = $this->usersModel->getPunchByDate(['emp_id' => $emp->id,'from_date' => $currentDate,'to_date' => $currentDate]);
				$empPunches = array_column($empAttendanceLog, 'punch_date');
				$empPunches = sortDates($empPunches,'ASC');
				
				$late = false;
				$lateGraceTimes = array_column($empAttendanceLog, 'late_in');
				$shiftStarts = array_column($empAttendanceLog, 'shift_start');
				$lateFines = array_column($empAttendanceLog, 'late_fine');
				
				$shiftStart = (!empty($shiftStarts[0]) ? date('Y-m-d H:i:s',strtotime($currentDate.' '.$shiftStarts[0])) : "00-00-00 00:00:00");
				$lateGraceTime = (!empty($lateGraceTimes[0]) ? ($lateGraceTimes[0] * 60) : 0);
				if(!empty($empPunches[0]) AND (strtotime($shiftStart) + $lateGraceTime) < strtotime($empPunches[0])){ $late = true; }
				
				$t=1;$wph = Array();$idx=0;$stay_time=0;$twh = 0;$wh=0;$ot=0;$present_status = 'P';$punches = Array();
				foreach($empPunches as $punch)
				{
					$punches[]= date("H:i:s", strtotime($punch));
					$wph[$idx][]=strtotime($punch);
					if($t%2 == 0){$stay_time += floatVal($wph[$idx][1]) - floatVal($wph[$idx][0]);$idx++;}
					
					$t++;
				}
				$wh = $stay_time;
				
				if(count($empPunches) % 2 != 0):
				    $text='M'; 
				    $class = "bg-warning text-white";
				else:
    				if($wh >= $hdLimit):
    					$day = 1;
    					$text = (($late) ? "P" : "P");
    					$class = "text-success";
    				elseif(($wh <= 0) OR ($wh > 0 AND $wh < $minLimitPerDay)):
    					$day = 0;
    					$text = "A"; if($currentDate != date('Y-m-d')){$late = false;}
    					$class="bg-danger text-white";
    					// Check Leave if Absent
    					if($wh <= 0):
    						$leaveData = $this->usersModel->checkLeaveDate(['emp_id' => $emp->id,'approve_by' => 1,'from_date' => $currentDate,'to_date' => $currentDate]);
    						if($leaveData->leave_count > 0):
    							$text = "ON-L";$leave++;
    						endif;
    					endif;
    				elseif($wh >= $minLimitPerDay AND $wh < $hdLimit):
    					$day = 0.5;
    					$text = (($late) ? "HD" : "HD");$hd++;
    					$class = "bg-info text-white";
    				endif;		
                endif;
				
                if(date("D",strtotime(date($d."-m-Y",strtotime($data['month'])))) == "Sun"){
					if($text == "A"){$text = "W";$class = "bg-light text-dark";}
                    if($text == "P"){$text = "WP";$wp++;$class = "bg-light-green text-dark";}
                    if($text == "HD"){$text = "W-HD";$wp++;$class = "bg-success text-white";}
                    if($late){$text = "W";$wp++;}
                    $weekOff ++;
                    $day = 0;
                }
				
				if($late){ $lateFine += (!empty($lateFines[0]) ? $lateFines[0] : 0); $lCount++;}
				
				if($wh > 0)
				{
					$punchRow .= '<td colspan="2" class="text-center "><small>'.implode(' - ',$punches).'</small></td>';
					$whRow = '<td class="text-center '.$class.'" style="font-size:12px;"><small>'.s2hi($wh).'</small></td>';
					
					if($data['report_type'] == 1){
						$tbody .= '<td class="text-center '.$class.'" style="font-size:12px;">'.$text.'</td>';
					}else{
						$tbody .= $whRow;
					}
				}
				else
				{
					$punchRow .= '<td colspan="2" class="text-center '.$class.'"> - </td>';
					
					$tbody .= '<td class="text-center '.$class.'" style="font-size:12px;">'.$text.'</td>';
				}
				
                $presentDays += $day;
				$totalWH += $wh;
            endfor;
			
            $absentDays = (($totalDays - $presentDays) > 0)?($totalDays - $presentDays - $leave):0;
            
            $tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;font-size:12px;" >'.$presentDays.'</td>';
            $tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;font-size:12px;" >'.$leave.'</td>';
            $tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;font-size:12px;" >'.$absentDays.'</td>';
            $tbody .= '<td class="text-center" style="width:45px;vertical-align:middle;font-size:12px;" >'.$totalDays.'</td>'; 
            $tbody .= '</tr>';
        endforeach;
        
        $reportTitle = 'Attendance Report';
        $report_date = $data['month'].' to '.date('t-m-Y',strtotime($data['month']));
		$logo = base_url('assets/images/logo.png');

        $pdfData = '<table id="attendanceTable" class="table table-bordered itemList" repeat_header="1">
                        <thead class="thead-info" id="theadData">'.$thead.'</thead>
                        <tbody id="tbodyData">'.$tbody.'</tbody>
                    </table>';

                
        $htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
                        <tr>
                            <td class="text-uppercase text-left"><img src="'.$logo.'" class="img" style="height:30px;"></td>
                            <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$reportTitle.'</td>
                            <td class="text-uppercase text-right" style="font-size:0.8rem;width:30%">Date : '.$report_date.'</td>
                        </tr>
                    </table>';
					
        $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                    <tr>
                        <td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
                        <td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
                    </tr>
                </table>';

        if(!empty($data['file_type']) && $data['file_type'] == 'PDF')
        {
            $mpdf = new \Mpdf\Mpdf();
            $pdfFileName = 'AttendanceReport_'.str_replace(["/","-"],"_",date('d-m-Y')).'.pdf';          
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetTitle($reportTitle);
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->AddPage('L','','','','',5,5,15,10,3,3,'','','','','','','','','','A4-L');
            $mpdf->WriteHTML($pdfData);	
            ob_clean();	
            $mpdf->Output($pdfFileName, 'I');
        }elseif(!empty($data['file_type']) && $data['file_type'] == 'excel'){
            $pdfData = '<table id="attendanceTable" class="table table-bordered itemList" repeat_header="1" border="1">
                            <thead class="thead-info" id="theadData">'.$thead.'</thead>
                            <tbody id="tbodyData">'.$tbody.'</tbody>
                        </table>';
            $xls_filename='AttendanceReport_'.str_replace(["/","-"],"_",date('d-m-Y')).'.xls';        
										
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename='.$xls_filename);
			header('Pragma: no-cache');
			header('Expires: 0');
	
			echo $pdfData; exit;
        } else { 
            $this->printJson(['status'=>1,'thead'=>$thead, 'tbody'=>$tbody]); 
        }
	}
 	
 	/*
    public function monthlyAttendance(){
		$this->load->view($this->monthlyAttendance, $this->data);
	}

    public function getMonthlyReport($jsonData=''){
		$data = (!empty($jsonData) ? (array) decodeURL($jsonData) : $this->input->post()); 
		$data['is_se'] = "Yes";
		$data['month'] = $data['year'].'-'.$data['month'].'-01';
        $empData = $this->usersModel->getMonthlyAttendance($data);

        $lastDay = intVal(date('t',strtotime($data['month'])));
        $thead='<tr style="background:#dddddd;"><th style="width:50px;">Code</th><th style="width:220px;">Emp Name</th>';
        for($d=1;$d<=$lastDay;$d++):	
            $thead.='<th class="text-center">'.$d.'</th>'; 
        endfor;
        
        $thead.='<th class="text-center">WP/WO</th>';
        $thead.='<th class="text-center">Present <br> Days</th>';
        $thead.='<th class="text-center">Leave</th>';
        $thead.='<th class="text-center">Absent <br> Days</th>';    
        $thead.='<th class="text-center">Total <br> Days</th>';
        $thead.='</tr>';  
       
        $empArray = array_reduce($empData, function($emp, $employee) {
            $emp[$employee->emp_name][] = $employee;
            return $emp;
        }, []);

        $tbody='';$i=0;
        foreach($empArray as $emp=>$employee):
            $i++;
            $tbody.='<tr>';
            $tbody.='<td class="text-center">'.$employee[0]->emp_code.'</td>';
            $tbody.='<td>'.$emp.'</td>';
            
            $totalDays = date("t",strtotime($data['month'])); 
            $holiday = countDayInMonth("Sunday",$data['month']);
            $totalDays -= $holiday; 
            $presentDays = 0;$absentDays = 0;$weekOff = 0;$wp = 0;$l = 0;
            
            for($d=1;$d<=$lastDay;$d++):
                
                $day=0; $text="A"; $class="bg-danger text-white";
                
                if(date("D",strtotime(date($d."-m-Y",strtotime($data['month'])))) == "Sun"){
                    if($text == "A"){$text = "W";}
                    if($text == "P"){$text = "WP";$wp++;$class = "text-success";}
                    if($text == "L"){$text = "WL";$wp++;}
                    $class = "bg-light text-dark";
                    $weekOff ++;
                    $day = 0;
                }else{
                    $text = "";
                    $punch_array = array_column($employee,'punch_date');
                    $leave_array = array_column($employee,'leave_date');

                    $date = date("Y-m-".str_pad($d,2,0,STR_PAD_LEFT),strtotime($data['month']));

                    if(in_array($date,$punch_array)){
                        $text = "P"; $class="bg-success text-white"; $day = 1;
                    }else{
                        if(in_array($date,$leave_array)){
                            $text = "L"; $class="bg-info text-white";
                            $l++;
                        }else{
                            $text = "A"; $class="bg-danger text-white";
                        }
                    }
                }                
                $tbody .= '<td class="text-center '.$class.'">'.$text.'</td>';
                $presentDays += $day;
            endfor;     

            $absentDays = (($totalDays - $presentDays) > 0)?($totalDays - $presentDays):0;
            $tbody .= '<td class="text-center" style="width:45px;">'.$wp.'/'.$weekOff.'</td>';
            $tbody .= '<td class="text-center" style="width:45px;">'.$presentDays.'</td>';
            $tbody .= '<td class="text-center" style="width:45px;">'.$l.'</td>';
            $tbody .= '<td class="text-center" style="width:45px;">'.$absentDays.'</td>';
            $tbody .= '<td class="text-center" style="width:45px;">'.$totalDays.'</td>'; 
            $tbody .= '</tr>';
        endforeach;
        
        $response = '<table class="table item-list-bb" border="1">
            <thead class="bg-light">'.$thead.'</thead>
            <tbody id="tbodyData">'.$tbody.'</tbody>
        </table>';

        if (!empty($data['type']) && $data['type'] == 'EXCEL') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Monthly Attendance');

            $doc = new DOMDocument();
            $doc->loadHTML($response);            
            $table = $doc->getElementsByTagName('table')->item(0);
            $rows = $table->getElementsByTagName('tr');

            $sheet->getStyle('A1:'.\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($d + 6).'1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D3D3D3');

            $rowIndex = 1;
            foreach ($rows as $tr) {
                $cells = $tr->getElementsByTagName('th')->length ? $tr->getElementsByTagName('th') : $tr->getElementsByTagName('td');
                $colIndex = 1;

                foreach ($cells as $td) {
                    $value = $td->textContent;
                    $class = $td->getAttribute('class');

                    $sheet->setCellValueByColumnAndRow($colIndex, $rowIndex, $value);

                    // Apply style based on class
                    $cellCoordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex) . $rowIndex;

                    if (strpos($class, 'bg-danger') !== false) {
                        $sheet->getStyle($cellCoordinate)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('dc3545');
                        $sheet->getStyle($cellCoordinate)->getFont()->getColor()->setRGB('ffffff');
                    } elseif (strpos($class, 'bg-success') !== false) {
                        $sheet->getStyle($cellCoordinate)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('28a745');
                        $sheet->getStyle($cellCoordinate)->getFont()->getColor()->setRGB('ffffff');
                    } elseif (strpos($class, 'bg-info') !== false) {
                        $sheet->getStyle($cellCoordinate)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('17a2b8');
                        $sheet->getStyle($cellCoordinate)->getFont()->getColor()->setRGB('ffffff');
                    } elseif (strpos($class, 'bg-light') !== false) {
                        $sheet->getStyle($cellCoordinate)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('f8f9fa');
                        $sheet->getStyle($cellCoordinate)->getFont()->getColor()->setRGB('000000');
                    }

                    $sheet->getColumnDimensionByColumn($colIndex)->setAutoSize(true);
                    $colIndex++;
                }
                $rowIndex++;
            }
            
            $fileDirectory = realpath(APPPATH . '../assets/uploads/attendance_temp');
            $fileName = '/monthly_attendance_'.time().'.xlsx';

            $writer = new Xlsx($spreadsheet);
            $writer->save($fileDirectory.$fileName);
            header("Content-Type: application/vnd.ms-excel");
            redirect(base_url('assets/uploads/attendance_temp') . $fileName);
        }
        elseif (!empty($data['type']) && $data['type'] == 'PDF') {
            $companyData = $this->masterModel->getCompanyInfo();
            
            $htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
                <tr>
                    <td class="org_title text-center" style="font-size:1.3rem;width:100%">Monthly Attendance</td>
                </tr>
            </table>';

            $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                <tr>
                    <td style="width:50%;"></td>
                    <td style="width:50%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
                </tr>
            </table>';

            $mpdf = new \Mpdf\Mpdf();
            $pdfFileName = 'monthlyAttendance.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
            $mpdf->WriteHTML($stylesheet,1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->AddPage('L','','','','',5,5,15,10,5,0,'','','','','','','','','','A4-L');
            $mpdf->WriteHTML($response);
            $mpdf->Output($pdfFileName,'I');
        }
        else {
            $this->printJson(['status'=>1,'thead'=>$thead,'tbody'=>$tbody]);
        }
	}
	*/
}
?>