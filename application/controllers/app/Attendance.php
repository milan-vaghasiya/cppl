<?php
class Attendance extends MY_Controller
{	
    private $attendance_view = "app/attendance_view";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Attendance";
		$this->data['headData']->controller = "app/attendance";
		$this->data['headData']->pageUrl = "app/attendance";
	}
	
	public function index(){
		$this->data['headData']->appMenu = "app/attendance";
        $this->data['empData'] = $this->usersModel->getEmployeeData();
        $this->data['logData'] = $this->usersModel->getEmpLogData();
        $this->data['employeeData'] = $this->usersModel->getEmployee(['id' => $this->loginId]);
        $this->load->view($this->attendance_view,$this->data);
    }

	public function saveAttendance(){
        $data = $this->input->post();
		
		if(!empty($data['travel_by']) && $data['travel_by'] != "Other"){
		    if(empty($data['meter'])){
		        $errorMessage['meter'] = "Meter Reading is required.";
		    }
		}
			
		
		if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
			$attachment = "";
			if(!empty($_FILES['img_file'])):
				if($_FILES['img_file']['name'] != null || !empty($_FILES['img_file']['name'])):
					$this->load->library('upload');
					$this->load->library('image_lib');
					
					$_FILES['userfile']['name']     = $_FILES['img_file']['name'];
					$_FILES['userfile']['type']     = $_FILES['img_file']['type'];
					$_FILES['userfile']['tmp_name'] = $_FILES['img_file']['tmp_name'];
					$_FILES['userfile']['error']    = $_FILES['img_file']['error'];
					$_FILES['userfile']['size']     = $_FILES['img_file']['size'];
					
					$imagePath = realpath(APPPATH . '../assets/uploads/attendance_log/');
					$config = ['file_name' => $this->loginId."_".$data['type']."_".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path' => $imagePath];

					$this->upload->initialize($config);
					if (!$this->upload->do_upload()):
						$errorMessage['img_file'] = $this->upload->display_errors();
					else:
						$uploadData = $this->upload->data();
						$attachment = $uploadData['file_name'];
						
						$imgConfig['image_library'] = 'gd2';
						$imgConfig['source_image'] = $uploadData['full_path'];
						$imgConfig['maintain_ratio'] = TRUE;
						$imgConfig['width'] = 640;
						$imgConfig['height'] = 480;
						$imgConfig['quality'] = "50%";

						$this->image_lib->clear();
						$this->image_lib->initialize($imgConfig);

						if (!$this->image_lib->resize()) :
							$errorMessage['img_file'] .= $fileName . " => " . $this->image_lib->display_errors();
						endif;
						
						if(!empty($errorMessage['img_file'])):
							if (file_exists($imagePath . '/' . $attachment)) : unlink($imagePath . '/' . $attachment); $attachment = ""; endif;
						endif;
						
						$data['img_file'] = $attachment;
					endif;
				endif;
			endif;

			$data['emp_id'] = $this->loginId;
			$data['punch_date'] = date("Y-m-d H:i:s");
			$data['start_at'] = date("Y-m-d H:i:s");
			$data['start_location'] = ((!empty($data['s_lat']) AND !empty($data['s_lon'])) ? $data['s_lat'].','.$data['s_lon'] : NULL);
			unset($data['s_lat'],$data['s_lon']);
			$data['loc_add']='';
			if(!empty($data['start_location']))
			{
				$add = $this->callcUrl(['callURL'=>'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$data['start_location'].'&key='.GMAK]);
				$add = (!empty($add) ? json_decode($add) : new StdClass);
				$data['loc_add'] = (isset($add->results[0]->formatted_address) ? $add->results[0]->formatted_address : "");
			}

			$this->printJson($this->usersModel->saveAttendance($data));
		endif;
    }
}
?>