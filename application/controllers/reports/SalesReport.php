<?php
class SalesReport extends MY_Controller
{
    private $empActivity = "report/sales_report/emp_activity";
    private $visitHistory = "report/sales_report/visit_history";
    private $order_monitoring = "report/sales_report/order_monitoring";
    private $sales_register = "report/sales_report/sales_register";
    private $sales_analysis = "report/sales_report/sales_analysis";
    private $lead_register = "report/sales_report/lead_register";
    private $executive_analysis = "report/sales_report/executive_analysis"; 
    private $sales_target = "report/sales_report/sales_target";    
    private $sales_expense = "report/sales_report/sales_expense";  
    private $customerOrderMonitoring = "report/sales_report/customer_order_monitoring";
    
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Sales Report";
		$this->data['headData']->controller = "reports/salesReport";
	}
	
     /* Employee Activity Report */
    public function empActivity(){
        $this->data['pageHeader'] = 'EMPLOYEE ACTIVITY';
        $this->data['salesExecutives'] = $this->usersModel->getEmployeeList(['is_se'=>"Yes"]);
		$this->data['API_KEY'] = 'AIzaSyACJW3ouSsTuZserlw3FRHIC2MWbppIuJ4';
        $this->load->view($this->empActivity,$this->data);
    }

    public function getEmpActivity(){
        $data = $this->input->post();
        $errorMessage = array();
		if(empty($data['emp_id']))
			$errorMessage['emp_id'] = "Employee is Required";
		if(empty($data['activity_date']))
			$errorMessage['activity_date'] = "Date is Required";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$prevLocation ="";$totalDistance = 0;
            $logData = $this->locationLog->getLocationLogs($data['activity_date'],$data['emp_id']);
			$activityLog = '';$locationLog = Array();$letterPoint = 'B';$tp = ['','Check In','Check Out','Visit Start','Visit End'];
            if(!empty($logData))
			{
				foreach($logData as $row)
				{
					$activityTime = date('d M Y H:i:s',strtotime($row->log_time));
					$title = (!empty($row->party_name)) ? $row->party_name : $tp[$row->log_type];
					$tpText = (!in_array($row->log_type,[1,2])) ? '<span class="float-end" style="width:15%">'.$tp[$row->log_type].'</span>' : '';
					$distance = 0;	
					if(!empty($row->location) AND !empty($prevLocation)):
						$distance = getDistanceOpt($prevLocation,$row->location);
					endif;
					$totalDistance += $distance;
					$prevLocation = $row->location;
					$travel_date = (in_array($row->log_type,[1,2]) ? '<p class="text-muted fs-12 mb-0"><b>Travel By : </b>'.(!empty($row->travel_by) ? $row->travel_by : "").'</p><p class="text-muted fs-12 mb-0"><b>Meter Reading : </b>'.(!empty($row->meter) ? $row->meter : 0).'</p>' : "");
					$activityLog .= '<div class="activity-info">
                                        <div class="icon-info-activity"><i class="fas fa-map-marker-alt1 bg-soft-danger">'.$letterPoint.'</i></div>
                                        <div class="activity-info-text mt-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="m-0 text-uppercase text-primary">'.$title.'</h6>
                                                '.$tpText.'
                                            </div>
                                            <h5 class="text-muted fs-12"><i class="fas fa-clock"></i> '.$activityTime.'</h5>
                                            '.$travel_date.'
                                            <p class="text-muted fs-12">'.$row->address.'<br>'.$row->location.'</p>
											<span class="text-warning" style="width:15%">[ Distance : '.$totalDistance.' Km. ]</span>
                                        </div>
                                    </div>';
					$locationLog[] = $row->location;
					$letterPoint++;
				}
			}
            $this->printJson(['status'=>1, 'locationLog'=>json_encode($locationLog),'activityLog'=>$activityLog, 'totalDistance'=>round($totalDistance,2)]);
        endif;
    }

    /* Visit History Report */
	public function visitHistory(){
        $this->data['pageHeader'] = 'VISIT HISTORY';
  		$this->data['headData']->pageTitle = "VISIT HISTORY";
        $this->data['salesExecutives'] = $this->usersModel->getEmployeeList(['is_se'=>"Yes"]);
		$this->data['customerData'] = $this->party->getpartyList($this->loginId);
        $this->load->view($this->visitHistory,$this->data);
    }

    public function getVisitHistory($jsonData=''){
        if(!empty($jsonData)){$postData = (Array) json_decode(urldecode(base64_decode($jsonData)));}
        else{$postData = $this->input->post();}
        $orderData = $this->salesReportModel->getVisitHistory($postData);

        $i=1; $tbody="";
        foreach($orderData as $row):
            $d1 = new DateTime($row->start_at);
            $d2 = new DateTime($row->end_at);
            $interval = $d1->diff($d2);
            $diffInSeconds = $interval->s;
            $diffInMinutes = $interval->i; 
            $diffInHours   = $interval->h;
            $duration=($diffInHours*60)+$diffInMinutes+($diffInSeconds/60);

            $imgFile = '';
            /*
    	    if(!empty($row->img_file)):
    	        $imgPath = base_url('assets/uploads/visit_log/'.$row->img_file);
                // $imgFile = '<a href="'.$imgPath.'" data-toggle="lightbox" target="_blank">
                //             <img src="'.$imgPath.'" width="20" height="20" style="border-radius:0%;border: 0px solid #ccc;padding:3px;">
                //         </a>';
                $imgFile='<div class="picture-item" >
                            <a href="'.$imgPath.'" class="lightbox" >
                                <img src="'.$imgPath.'" alt="" class="img-fluid"  width="20" height="20"   style="border-radius:0%;border: 0px solid #ccc;padding:3px;"/>
                            </a> 
                            </div> ';
    		endif;
            */
            
            $tbody .= '<tr>
                <td>'.$i++.'</td>
                <td>'.date("d-m-Y h:i:s A",strtotime($row->start_at)).'</td>
                <td>'.$row->party_name.'</td>
                <td>'.$row->contact_person.'</td>
                <td>'.$row->contact_phone.'</td>
                <td>'.$row->purpose.'</td>
                <td>'.$row->discussion_points.'</td>
                <td class="text-wrap text-left">'.$row->s_add.'</td>
                <td >'.$imgFile.'</td>
                <td>'. number_format($duration,2).'</td>';                
            $tbody .= '</tr>';
        endforeach;     
     
        $reportTitle = 'VISIT HISTORY';
        $report_date = date('d-m-Y',strtotime($postData['from_date'])).' to '.date('d-m-Y',strtotime($postData['to_date']));
        $thead = (empty($jsonData)) ? '<tr class="text-center"><th colspan="10">'.$reportTitle.' ('.$report_date.')</th></tr>' : '';
        $thead .= '<tr>
                        <th style="min-width:25px;" height="30">#</th>
                        <th style="min-width:25px;">Date</th>
                        <th style="min-width:25px;">Party Name</th>
                        <th style="min-width:100px;">Contact Person</th>
                        <th style="min-width:100px;">Purpose</th>
                        <th style="min-width:100px;">Discussion Points</th>		
                        <th style="min-width:100px;">Address</th>					
                        <th style="min-width:50px;">Attachment</th>		
                        <th style="min-width:100px;">Duration<br><small>(Minutes)</small></th>						
                </tr>';

        $companyData = $this->salesReportModel->getCompanyInfo();
        $logoFile = (!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
        $logo = base_url('assets/images/' . $logoFile);
        
        $pdfData = '<table id="commanTable" class="table table-bordered poItemList" repeat_header="1">
                            <thead class="thead-info" id="theadData">'.$thead.'</thead>
                            <tbody id="receivableData">'.$tbody.'</tbody>
                        </table>';
        $htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
                        <tr>
                            <td class="text-uppercase text-left" style="font-size:1rem;width:30%">'.$reportTitle.'</td>
                            <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$companyData->company_name.'</td>
                            <td class="text-uppercase text-right" style="font-size:1rem;width:30%">Date : '.$report_date.'</td>
                        </tr>
                    </table>
                    <table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
                        <tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
                    </table>';
        $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                    <tr>
                        <td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
                        <td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
                    </tr>
                </table>';
			
        if(!empty($postData['file_type'] == 'PDF'))
        {
            $mpdf = new \Mpdf\Mpdf();
            $filePath = realpath(APPPATH . '../assets/uploads/');
            $pdfFileName = $filePath.'/CashBook.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetWatermarkImage($logo, 0.08, array(120, 120));
            $mpdf->showWatermarkImage = true;
            $mpdf->SetTitle($reportTitle);
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->AddPage('L','','','','',5,5,20,10,3,3,'','','','','','','','','','A4-L');
            $mpdf->WriteHTML($pdfData); 
            ob_clean();
            $mpdf->Output($pdfFileName, 'I');
        }
        else { $this->printJson(['status'=>1, 'tbody'=>$tbody]); }
    } 
    
    public function getPartyList(){
        $data = $this->input->post();
        $partyData=""; 
        if($data['party_type'] == 2){
            $partyData = $this->party->getLeadList();
        }else{
            $partyData = $this->party->getPartyList(['party_type'=>1]);
        }
        $options = '<option value="">All Party</option>';
        if(!empty($partyData)){
            foreach($partyData as $row){
                $options .= '<option value="'.$row->id.'">'.$row->party_name.'</option>';
            }
        }
        $this->printJson(['status'=>1, 'options'=>$options]);
    }
    
    /* Order Monitoring Report */
    public function orderMonitoring(){
        $this->data['pageHeader'] = 'ORDER MONITORING REPORT';
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-t"));
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>1]);
        $this->load->view($this->order_monitoring,$this->data);
    }

    public function getOrderMonitoringData(){
        $postData = $this->input->post(); $postData['order_report'] = 1; $postData['group_by'] = 'so_trans.id';
        $postData['customWhere'] = "so_master.trans_date BETWEEN '".$postData['from_date']."' AND '".$postData['to_date']."'";
        $result = $this->sales->getSalesOrderItems($postData);

        $i=1; $tbody="";
        foreach($result as $row):
            $tbody .= '<tr>
                <td>'.$i.'</td>
                <td>'.formatDate($row->trans_date).'</td>
                <td>'.$row->trans_number.'</td>
                <td>'.$row->party_name.'</td>
                <td>'.$row->item_name.'</td>
                <td>'.floatval($row->qty).'</td>
                <td>'.formatDate($row->dispatch_date).'</td>
                <td>'.$row->ref_no.'</td>
                <td>'.(!empty($row->dispatch_qty) ? floatval($row->dispatch_qty) : '').'</td>
            </tr>';
            $i++;
        endforeach; 
    
        $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    }

    /* Sales Register Report */
    public function salesRegister($startDate="",$endDate=""){
        $this->data['pageHeader'] = 'SALES REGISTER REPORT';
        $this->data['startDate'] = (!empty($startDate))?$startDate:getFyDate(date("Y-m-01"));
        $this->data['endDate'] = (!empty($endDate))?$endDate:getFyDate(date("Y-m-d"));
        $this->data['stateList'] = $this->configuration->getStatutoryDetail(['group_by'=>'state']);
        $this->load->view($this->sales_register,$this->data);
    }

    public function getSalesRegisterData(){
        $data = $this->input->post();
        $result = ($data['report_type'] == 1)?$this->salesReportModel->getSalesRegisterData($data):$this->salesReportModel->getSalesRegisterDataItemWise($data);

        $thead = '<tr>
            <th>#</th>';

        if($data['report_type'] == 2):
            $thead .= '<th>Item Name</th>';
            $thead .= '<th>Qty.</th>';
            $thead .= '<th>Price</th>';
            $thead .= '<th>Amount</th>';
        else:
            $thead .= '<th>SO Date</th>';
            $thead .= '<th>SO No.</th>';
            $thead .= '<th>Party Name</th>';
            $thead .= '<th>Gst No.</th>';
            $thead .= '<th>Total Amount</th>';
        endif;

        $thead .= '<th>Disc. Amount</th>
            <th>Taxable Amount</th>
            <th>GST Amount</th>
            <th>Net Amount</th>
        </tr>';

        $tbody=''; $i=1;        
        $totalAmount = $totalDiscAmount = $totalTaxableAmount = $totalGstAmount = $totalNetAmount = 0;

        if($data['report_type'] == 1):
            foreach($result as $row):
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.formatDate($row->trans_date).'</td>
                    <td>'.$row->trans_number.'</td>
                    <td class="text-left">'.$row->party_name.'</td>
                    <td class="text-left">'.$row->gstin.'</td>
                    <td>'.floatVal($row->total_amount).'</td>
                    <td>'.floatVal($row->disc_amount).'</td>
                    <td>'.floatVal($row->taxable_amount).'</td>
                    <td>'.floatVal($row->gst_amount).'</td>
                    <td>'.floatVal($row->net_amount).'</td>
                </tr>';

                $totalAmount += $row->total_amount;
                $totalDiscAmount += $row->disc_amount;
                $totalTaxableAmount += $row->taxable_amount;
                $totalGstAmount += $row->gst_amount;
                $totalNetAmount += $row->net_amount;
            endforeach;
        else:
            foreach($result as $row):
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td class="text-left">'.$row->item_name.'</td>
                    <td>'.floatVal($row->qty).'</td>
                    <td>'.floatVal($row->price).'</td>
                    <td>'.floatVal($row->amount).'</td>
                    <td>'.floatVal($row->disc_amount).'</td>
                    <td>'.floatVal($row->taxable_amount).'</td>
                    <td>'.floatVal($row->gst_amount).'</td>
                    <td>'.floatVal($row->net_amount).'</td>
                </tr>';

                $totalAmount += $row->amount;
                $totalDiscAmount += $row->disc_amount;
                $totalTaxableAmount += $row->taxable_amount;
                $totalGstAmount += $row->gst_amount;
                $totalNetAmount += $row->net_amount;
            endforeach;
        endif;

        $tfoot = '<tr>
            <th colspan="'.(($data['report_type'] == 1)?5:4).'" class="text-right">Total</th>
            <th>'.floatVal($totalAmount).'</th>
            <th>'.floatVal($totalDiscAmount).'</th>
            <th>'.floatVal($totalTaxableAmount).'</th>
            <th>'.floatVal($totalGstAmount).'</th>
            <th>'.floatVal($totalNetAmount).'</th>
        </tr>';

        $this->printJson(['status'=>1,'thead'=>$thead,'tbody'=>$tbody,'tfoot'=>$tfoot]);
    }

    /* Sales Analysis Report */
    public function salesAnalysis(){
        $this->data['pageHeader'] = 'SALES ANALYSIS REPORT';
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-t"));
        $this->data['salesExecutives'] = $this->usersModel->getEmployeeList(['is_se'=>"Yes"]);
        $this->data['bTypeList'] = $this->configuration->getBusinessTypeList(); 
        $this->load->view($this->sales_analysis,$this->data);
    }

    public function getSalesAnalysisData(){
        $data = $this->input->post();
        $result = $this->salesReportModel->getSalesAnalysisData($data);

        $thead = $tbody = $tfoot = ''; $i=1;
        if($data['report_type'] == 1):
            $thead .= '<tr>
                <th>#</th>
                <th class="text-left">Customer Name</th>
                <th class="text-right">Taxable Amount</th>
                <th class="text-right">GST Amount</th>
                <th class="text-right">Net Amount</th>
            </tr>';

            $taxableAmount = $gstAmount = $netAmount = 0;
            foreach($result as $row):
                $tbody .= '<tr>
                    <td>'.$i.'</td>
                    <td class="text-left">'.$row->party_name.'</td>
                    <td class="text-right">'.floatval($row->taxable_amount).'</td>
                    <td class="text-right">'.floatval($row->gst_amount).'</td>
                    <td class="text-right">'.floatval($row->net_amount).'</td>
                </tr>';
                $i++;
                $taxableAmount += floatval($row->taxable_amount);
                $gstAmount += floatval($row->gst_amount);
                $netAmount += floatval($row->net_amount);
            endforeach;

            $tfoot .= '<tr>
                <th colspan="2" class="text-right">Total</th>
                <th class="text-right">'.$taxableAmount.'</th>
                <th class="text-right">'.$gstAmount.'</th>
                <th class="text-right">'.$netAmount.'</th>
            </tr>';
        else:
            $thead .= '<tr>
                <th>#</th>
                <th class="text-left">Item Name</th>
                <th class="text-right">Qty.</th>
                <th class="text-right">Price</th>
                <th class="text-right">Taxable Amount</th>
            </tr>';

            $totalQty = $taxableAmount = 0;
            foreach($result as $row):
                $tbody .= '<tr>
                    <td>'.$i.'</td>
                    <td class="text-left">'.$row->item_name.'</td>
                    <td class="text-right">'.floatVal($row->qty).'</td>
                    <td class="text-right">'.floatVal($row->price).'</td>
                    <td class="text-right">'.floatVal($row->taxable_amount).'</td>
                </tr>';
                $i++;
                $totalQty += floatval($row->qty);
                $taxableAmount += floatval($row->taxable_amount);
            endforeach;

            $tfoot .= '<tr>
                <th colspan="2" class="text-right">Total</th>
                <th class="text-right">'.$totalQty.'</th>
                <th></th>
                <th class="text-right">'.$taxableAmount.'</th>
            </tr>';
        endif;

        $this->printJson(['status'=>1,'thead'=>$thead,'tbody'=>$tbody,'tfoot'=>$tfoot]);
    }

    /****** Lead Register */
	public function leadRegister(){
		$this->data['headData']->pageTitle = "Lead Register";
        $this->data['pageHeader'] = 'Lead Register';
        $this->data['salesExecutives'] = $this->usersModel->getEmployeeList(['is_se'=>"Yes"]);
        $this->data['stateList'] = $this->configuration->getStatutoryDetail(['group_by'=>'state']);
        $this->data['bTypeList'] = $this->configuration->getBusinessTypeList();
        $this->data['leadStages'] = $this->configuration->getLeadStagesList();
        $this->load->view($this->lead_register,$this->data);
    }

    public function getLeadRegister(){
        $postData = $this->input->post();
        $parameter = [
            'party_type'=>$postData['party_type'],
            'executive_id'=>(($postData['executive_id'] != 'ALL')?$postData['executive_id']:''),
            'business_type'=>(($postData['business_type'] != 'ALL')?$postData['business_type']:''),
            'state'=>(($postData['state'] != 'ALL')?$postData['state']:''),
            'district'=>(($postData['district'] != 'ALL')?$postData['district']:''),
            'statutory_id'=>(($postData['statutory_id'] != 'ALL')?$postData['statutory_id']:''),
            'from_date'=>$postData['from_date'],
            'to_date'=>$postData['to_date']
        ];
        $leadList = $this->party->getLeadList($parameter);
        $i=1; $tbody="";
        foreach($leadList as $row):
            $leadParam = "{'postData':{'lead_id':".$row->id.",'party_id':".$row->party_id."},'modal_id':'modal-md','fnedit':'getLeadDetails','title':'Lead Details','button' : 'close'}";
            $party_name = '<a href="javascript:void(0)" class="mt-0 font-13 fw-bold" onclick="edit('.$leadParam.');" data-msg="View Lead Details" flow="down"><span class="lable">'.$row->party_name.'</span></a>';
            $wa_number = (!empty($row->whatsapp_no) ? ", ".$row->whatsapp_no : "");
            $tbody .= '<tr>
                <td>'.$i++.'</td>
                <td>'.$party_name.'</td>
                <td>'.$row->executive.'</td>
                <td>'.$row->contact_person.'</td>
                <td>'.$row->contact_phone.$wa_number.'</td>
                <td>'.$row->business_type.'</td>
                <td class="text-wrap text-left">'.$row->state.', '.$row->district.'</td>
                <td >'.$row->taluka.'</td>';                
            $tbody .= '</tr>';
        endforeach;     
     
        $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    }  

    public function getLeadDetails(){
        $data = $this->input->post();     
        $this->data['partyData'] = $partyData = $this->party->getLead(['id'=>$data['lead_id']]); 
        $this->data['salesLog'] = $salesLog = $this->getSalesLog($data,'NA');
        $this->load->view('report/sales_report/lead_detail',$this->data);
    }

    public function getSalesLog($param = [],$fnCall = "Ajax"){
        $postData = $this->input->post();
		if(!empty($param)){$fnCall = 'Outside';$postData = $param;} 
        $slData = $this->sales->getSalesLog($postData);
        
		$salesLog = '';
		if(!empty($slData))
		{
			foreach($slData as $row)
			{
				$salesLog.= '<div class="activity-info">
								<div class="icon-info-activity"><i class="'.$this->iconClass[$row->log_type].'"></i></div>
								<div class="activity-info-text">
									<div class="d-flex justify-content-between align-items-center">
										<h6 class="m-0 fs-13">'.$this->logTitle[$row->log_type].'</h6>
                                       
										<span class="text-muted w-30 d-block font-12">
										'.date("d F",strtotime($row->created_at)).'</span>
									</div>
									<p class=" m-1 font-12"><i class="fa fa-user"></i> '.$row->creator.'</p>
									<p class="text-muted m-1 font-12">'.$row->notes.'</p>
								</div>
							</div>';
			}
		}
		if($fnCall == 'Ajax'){$this->printJson(['salesLog'=>$salesLog]);}
		else{return $salesLog;}
    }

    /****** End  Register*/

    /* Executive Analysis Report */
    public function executiveAnalysis(){
		$this->data['headData']->pageTitle = "EXECUTIVE ANALYSIS REPORT";
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-t"));
        $this->data['salesExecutives'] = $this->usersModel->getEmployeeList();
        $this->data['bTypeList'] = $this->configuration->getBusinessTypeList();
        $this->data['stateList'] = $this->configuration->getStatutoryDetail(['group_by'=>'state']); 
        $this->load->view($this->executive_analysis,$this->data);
    }

    public function getExecutiveAnalysisData(){
        $data = $this->input->post();
        $result = $this->salesReportModel->getExecutiveAnalysisData($data);
        $i=1; $tbody='';
        foreach($result as $row):
            $tbody .= '<tr>
                <td>'.$row->emp_name.'</td>
                <td>'.$row->total_visit.'</td>
                <td>'.$row->total_new_lead.'</td>
                <td>'.$row->total_enq.'</td>
                <td>'.$row->total_ord.'</td>
                <td>'.$row->sales_value.'</td> ';                
            $tbody .= '</tr>';
        endforeach;     
     
        $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    }

    public function targetVsAchieve(){
        $this->data['pageHeader'] = 'Target V/S Achievement ';
        $this->data['zoneList'] = $this->configuration->getSalesZoneList();
        $this->data['monthData'] = $this->getMonthListFY();
        $this->load->view($this->sales_target, $this->data);
    }

    public function getTargetRows(){
		$postData = $this->input->post();
        $errorMessage = array();
		
        if(empty($postData['target_month']))
            $errorMessage['target_month'] = "Month is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $postData['zone_id'] = (!empty($postData['zone_id']) && $postData['zone_id'] == 'ALL')?'':$postData['zone_id'];
			$resultData = $this->sales->getTargetData($postData); //$this->sales->printQuery();
            $targetData = "";
			if(!empty($resultData)):
                $i=1;
				foreach($resultData as $row):
                    $total_new_lead = !empty($row->new_lead)?$row->new_lead:0;
                    $sales_amount = !empty($row->sales_amount)?$row->sales_amount:0;
                    $leadAcheive = !empty($row->achieve_new_lead)?$row->achieve_new_lead:0;
                    $salesAcheive = !empty($row->achived_amount)?$row->achived_amount:0;
                    $total_new_visit = !empty($row->new_visit)?$row->new_visit:0;
                    $visitAcheive = !empty($row->achieve_new_visit)?$row->achieve_new_visit:0;
                  
                    $leadRatio = 0;$salesRatio = 0;$visitRatio = 0;
                    if($leadAcheive > 0 && $total_new_lead > 0){ $leadRatio = ($leadAcheive*100)/$total_new_lead; }
                    if($salesAcheive > 0 && $salesAcheive > 0){ $salesRatio = ($salesAcheive*100)/$sales_amount; }
                    if($visitAcheive > 0 && $total_new_visit > 0){ $visitRatio = ($visitAcheive*100)/$total_new_visit; }
                    
                    $targetData .= '<tr>';
                    $targetData .= '<td>'.$i++.'</td>';
                    $targetData .= '<td>'.'['.$row->emp_code.'] '.$row->emp_name.'</td>';
                    $targetData .= '<td>'.$total_new_lead.'</td>';
                    $targetData .= '<td>'.$leadAcheive.'</td>';
                    $targetData .= '<td>'.round($leadRatio,2).'%</td>';
                    $targetData .= '<td>'.$total_new_visit.'</td>';
                    $targetData .= '<td>'.$visitAcheive.'</td>';
                    $targetData .= '<td>'.round($visitRatio,2).'%</td>';
                    $targetData .= '<td>'.$sales_amount.'</td>';
                    $targetData .= '<td>'.$salesAcheive.'</td>';
                    $targetData .= '<td>'.round($salesRatio,2).'%</td>';
                    $targetData .= '</tr>';
                       
				endforeach;
			endif;
            $this->printJson(['status'=>1,'targetData'=>$targetData]);
		endif;
    }
    
    /* Sales Analysis Report */
    public function salesMonthlyAnalysis(){
        $this->data['pageHeader'] = 'SALES ANALYSIS REPORT';
        $this->data['salesExecutives'] = $this->usersModel->getEmployeeList(['is_se'=>"Yes"]);
        $this->load->view('report/sales_report/monthly_sales_analysis',$this->data);
    }

    public function getSalesMonthlyAnalysisData(){
        $data = $this->input->post();
        if($data['report_type'] == 1){
            $data['group_by'] = 'item_master.category_id';
        }elseif($data['report_type'] == 2){
            $data['group_by'] = 'party_master.sales_zone_id';
        }elseif($data['report_type'] == 3){
            $data['group_by'] = 'party_master.source';
        }elseif($data['report_type'] == 4){
            $data['group_by'] = 'party_master.business_type';
        }
        $result = $this->salesReportModel->getSalesMonthlyAnalysisData($data);
        $tbodyData = "";$tfootData ="";
        if(!empty($result)){
            foreach($result as $row){
                $tbodyData .= '<tr>
                                <th>'.$row->category_name.'</th>
                                <td>'.$row->apr_amt.'</td>
                                <td>'.$row->may_amt.'</td>
                                <td>'.$row->jun_amt.'</td>
                                <td>'.$row->jul_amt.'</td>
                                <td>'.$row->aug_amt.'</td>
                                <td>'.$row->sep_amt.'</td>
                                <td>'.$row->oct_amt.'</td>
                                <td>'.$row->nov_amt.'</td>
                                <td>'.$row->dec_amt.'</td>
                                <td>'.$row->jan_amt.'</td>
                                <td>'.$row->feb_amt.'</td>
                                <td>'.$row->mar_amt.'</td>
                            </tr>';
            }
            $tfootData = '<tr>
                        <th></th>
                        <th>'.array_sum(array_column($result,'apr_amt')).'</th>
                        <th>'.array_sum(array_column($result,'may_amt')).'</th>
                        <th>'.array_sum(array_column($result,'jun_amt')).'</th>
                        <th>'.array_sum(array_column($result,'jul_amt')).'</th>
                        <th>'.array_sum(array_column($result,'aug_amt')).'</th>
                        <th>'.array_sum(array_column($result,'sep_amt')).'</th>
                        <th>'.array_sum(array_column($result,'oct_amt')).'</th>
                        <th>'.array_sum(array_column($result,'nov_amt')).'</th>
                        <th>'.array_sum(array_column($result,'dec_amt')).'</th>
                        <th>'.array_sum(array_column($result,'jan_amt')).'</th>
                        <th>'.array_sum(array_column($result,'feb_amt')).'</th>
                        <th>'.array_sum(array_column($result,'mar_amt')).'</th>
                     </tr>';
        }
        $this->printJson(['status'=>1,'soData'=>$result,'tbodyData'=>$tbodyData,'tfootData'=>$tfootData]);
    }

    public function leadAnalysis(){
        $this->data['pageHeader'] = 'LEAD ANALYSIS REPORT';
        $this->data['salesExecutives'] = $this->usersModel->getEmployeeList(['is_se'=>"Yes"]);
        $this->load->view('report/sales_report/lead_analysis',$this->data);
    }

    /* Sales Analysis Report */
    public function executivePerformance(){
        $this->data['pageHeader'] = 'Executive Performance';
        $this->load->view('report/sales_report/executive_performance',$this->data);
    }

    public function getExecutivePerformanceData(){
        $data = $this->input->post();
        $result = $this->salesReportModel->getExecutivePerformanceData($data);
        $tbodyData = "";$tfootData ="";
        if(!empty($result)){
            foreach($result as $row){
                $tbodyData .= '<tr>
                                <th>'.$row->emp_name.'</th>
                                <td>'.$row->apr_amt.'</td>
                                <td>'.$row->may_amt.'</td>
                                <td>'.$row->jun_amt.'</td>
                                <td>'.$row->jul_amt.'</td>
                                <td>'.$row->aug_amt.'</td>
                                <td>'.$row->sep_amt.'</td>
                                <td>'.$row->oct_amt.'</td>
                                <td>'.$row->nov_amt.'</td>
                                <td>'.$row->dec_amt.'</td>
                                <td>'.$row->jan_amt.'</td>
                                <td>'.$row->feb_amt.'</td>
                                <td>'.$row->mar_amt.'</td>
                            </tr>';
            }
            $tfootData = '<tr>
                        <th></th>
                        <th>'.array_sum(array_column($result,'apr_amt')).'</th>
                        <th>'.array_sum(array_column($result,'may_amt')).'</th>
                        <th>'.array_sum(array_column($result,'jun_amt')).'</th>
                        <th>'.array_sum(array_column($result,'jul_amt')).'</th>
                        <th>'.array_sum(array_column($result,'aug_amt')).'</th>
                        <th>'.array_sum(array_column($result,'sep_amt')).'</th>
                        <th>'.array_sum(array_column($result,'oct_amt')).'</th>
                        <th>'.array_sum(array_column($result,'nov_amt')).'</th>
                        <th>'.array_sum(array_column($result,'dec_amt')).'</th>
                        <th>'.array_sum(array_column($result,'jan_amt')).'</th>
                        <th>'.array_sum(array_column($result,'feb_amt')).'</th>
                        <th>'.array_sum(array_column($result,'mar_amt')).'</th>
                     </tr>';
        }
        $this->printJson(['status'=>1,'performData'=>$result,'tbodyData'=>$tbodyData,'tfootData'=>$tfootData]);
    }
    
    /* Expense Analysis Report */
    public function salesExpense(){
        $this->data['pageHeader'] = 'SALES EXPENSE ANALYSIS REPORT';
        $this->data['startDate'] = getFyDate(date("Y-m-01"));
        $this->data['endDate'] = getFyDate(date("Y-m-t"));
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>1]);
        $this->load->view($this->sales_expense,$this->data);
    }

    public function getsalesExpenseData(){
        $data = $this->input->post();
        $result = $this->salesReportModel->getSalesExpenseAnalysisData($data);
        $i=1; $tbody="";
        foreach($result as $row):
            $expenseRatio = floatval($row->expense_amount) * 100 / floatval($row->net_amount);
            $tbody .= '<tr>
                <td>'.$i++.'</td>
                <td>'.$row->party_name.'</td>
                <td>'.floatval($row->net_amount).'</td>
                <td>'.floatval($row->expense_amount).'</td>
                <td>'.round($expenseRatio,2).'</td>
            </tr>';
        endforeach; 
        $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    }
    
    /* Appointment Register Report */
    public function appointmentRegister(){
		$this->data['headData']->pageTitle = "APPOINTMENT REGISTER REPORT";
        $this->data['DT_TABLE'] = true;
        $this->data['startDate'] = date("Y-m-01");
        $this->data['endDate'] = date("Y-m-d");
        $this->data['salesExecutives'] = $this->usersModel->getEmployeeList(['is_se'=>"Yes"]);
        $this->load->view("report/sales_report/appointment_register",$this->data);
    }

    public function getAppointmentRegister(){
        $data = $this->input->post();

        $result = $this->salesReportModel->getAppointmentRegister($data);
        $i=1; $tbody='';
        if(!empty($result)):
            foreach($result as $row):
                $daysDiff = '';
				$respond_date = (!empty($row->updated_at))? $row->updated_at : date('Y-m-d');
                if(!empty($row->ref_date) AND !empty($respond_date)){
                    $ref_date = new DateTime($row->ref_date);
                    $resDate = new DateTime($respond_date);
                    $due_days = $ref_date->diff($resDate)->format("%r%a");
                    $daysDiff = ($due_days > 0) ? $due_days : 'On Time';
                }
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.formatDate($row->ref_date).'</td>
                    <td>'.$row->emp_name.'</td>
                    <td>'.$row->party_name.'</td>
                    <td>'.$row->mode.'</td>
                    <td>'.$row->notes.'</td>
                    <td>'.$row->remark.'</td>
                    <td>'.formatDate($row->updated_at).'</td>
                    <td>'.$daysDiff.'</td>';
                $tbody .= '</tr>';
            endforeach; 
        endif;  
        $this->printJson(['status'=>1,'tbody'=>$tbody]);
    }

    /* FollowUp Register Report */
    public function followUpRegister(){
		$this->data['headData']->pageTitle = "FOLLOWUP REGISTER REPORT";
        $this->data['DT_TABLE'] = true;
        $this->data['startDate'] = date("Y-m-01");
        $this->data['endDate'] = date("Y-m-d");
        $this->data['bTypeList'] = $this->configuration->getBusinessTypeList();
        $this->data['partyList'] = $this->party->getLeadList(); 
        $this->load->view("report/sales_report/followup_register",$this->data);
    }

    public function getFollowUpRegister(){
        $data = $this->input->post();
        $result = $this->salesReportModel->getFollowUpRegister($data);
		$i=1;$tbody='';
        if(!empty($result)):
            foreach($result as $row):
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.formatDate($row->created_at).'</td>
                    <td>'.$row->emp_name.'</td>
                    <td>'.$row->party_name.'</td>
                    <td>'.$row->business_type.'</td>
                    <td>'.$row->notes.'</td>
                    </tr>';
            endforeach; 
        endif; 
        
        $this->printJson(['status'=>1,'tbody'=>$tbody]);
    }
    
    /*Customer Order Monitoring Report*/
	public function customerOrderMonitoring(){
		$this->data['headData']->pageTitle = "Customer Order Monitoring";
        $this->data['pageHeader'] = 'Customer Order Monitoring';
		$this->data['empList'] = $this->usersModel->getEmployeeList();
        $this->load->view($this->customerOrderMonitoring,$this->data);
    }

    public function getCustOrdMonitoring(){
        $data = $this->input->post();
        $soData = $this->salesReportModel->getCustOrdMonitoring($data);
		
        $i=1; $tbody="";
        foreach($soData as $row):
            $tbody .= '<tr>
                <td>'.$i++.'</td>
                <td>'.formatDate($row->trans_date).'</td>
                <td>'.$row->inv_no.'</td>
                <td>'.(!empty($row->party_code) ? '['.$row->party_code.'] '.$row->party_name : $row->party_name).'</td>
                <td>'.(!empty($row->created_code) ? '['.$row->created_code.'] '.$row->created_name : $row->created_name).'</td>
                <td>'.(!empty($row->emp_code) ? '['.$row->emp_code.'] '.$row->emp_name : $row->emp_name).'</td>
                <td>'.floatVal($row->net_amount).'</td>
				<td >'.floatVal($row->amount).'</td>';
            $tbody .= '</tr>';
        endforeach;     
     
        $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    }
    
}
?>