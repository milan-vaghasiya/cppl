<?php
class LocationLogModel extends MasterModel{
    private $locationLog = "location_log";
	private $visits = "visits";

    public function getLocationLogs($date,$emp_id){
        $queryData['tableName'] = $this->locationLog;
        $queryData['select'] = "location_log.*,(CASE WHEN location_log.party_id > 0 THEN party_master.party_name ELSE lead_master.party_name END) as party_name,attendance_log.meter,attendance_log.travel_by";
        $queryData['leftJoin']['party_master'] = "party_master.id = location_log.party_id";
        $queryData['leftJoin']['lead_master'] = "lead_master.id = location_log.lead_id";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = location_log.emp_id";
        $queryData['leftJoin']['attendance_log'] = "attendance_log.emp_id = location_log.emp_id AND attendance_log.punch_date = location_log.log_time";
        $queryData['where']['DATE(location_log.log_time)'] = $date;
        $queryData['where']['location_log.emp_id'] = $emp_id;
        $queryData['order_by']['location_log.log_time'] = 'ASC';
        $queryData['group_by'][] = 'location_log.lead_id,location_log.log_type';
        $locationLogs = $this->rows($queryData);
		return $locationLogs;
    }

    public function save($data){
        return $this->store($this->locationLog,$data);
    }
}
?>