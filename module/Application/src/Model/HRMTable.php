<?php 
declare(strict_types=1);

namespace Application\Model;

use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Db\Adapter\Adapter;

class HRMTable extends AbstractTableGateway {
	protected $adapter;
	protected $table = "tbw_hrm_attendance";
	protected $tableUser = "tbw_users";
	protected $tableJob = "tbw_jobs";
	
	public  function __construct(Adapter $adapter){
		$this->adapter = $adapter;
		$this->initialize();
	}

    /**Attendance */
        public function getAllAttendance($data)
        {
            $dbadapter = $this->adapter;
            $query = "SELECT 
                driver,
                u.fullname,
                u.position,
                MONTH(date_report) AS month,
                SUM(CASE WHEN type_work = 0 THEN 1 ELSE 0 END) AS sum_night,
                SUM(CASE WHEN type_work = 1 THEN 1 ELSE 0 END) AS sum_day,
                SUM(CASE WHEN type_work = 3 THEN 1 ELSE 0 END) AS sum_day_PT,
                SUM(CASE WHEN type_work = 2 THEN 1 ELSE 0 END) AS sum_off,
                SUM(CASE WHEN type_work = 4 THEN 1 ELSE 0 END) AS sum_px
            FROM 
                ".$this->table." AS t 
                LEFT JOIN ".$this->tableUser." AS u ON u.id = t.driver
                LEFT JOIN ".$this->tableJob." AS j ON j.id = t.jobs ";

            $filter = "";

            if (isset($data["jobs"]) && $data["jobs"] != ''){
                $filter .= ($filter != '' ? ' AND ' : '')." j.id = '".$data["jobs"]."'";
            }
            if (isset($data["user"]) && $data["user"] != ''){
                $filter .= ($filter != '' ? ' AND ' : '')." driver = '".$data["user"]."'";
            }
            if (isset($data["from"]) && $data["from"] != ''){
                $filter .= ($filter != '' ? ' AND ' : '')." date_report BETWEEN '".$data["from"]."' AND '".$data["to"]."'";
            }
            if (isset($data["att_ver"]) && $data["att_ver"] != ''){
                $filter .= ($filter != '' ? ' AND ' : '')." att_ver = '".$data["att_ver"]."'";
            }
            
            if ($filter != "") {
                $query.= " WHERE ".$filter;
            }

            $query .= " GROUP BY driver ORDER BY fullname ASC ";

            $handler = $dbadapter->query($query, Adapter::QUERY_MODE_EXECUTE);
            
            $data = $handler->toArray();
            if (empty($data)) {
                return null;
            } else {
                return $data;
            }
        }
        public function getAllAttendanceV2($data)
        {
            $dbadapter = $this->adapter;
            $query = "SELECT 
                driver,
                u.fullname,
                u.position,
                MONTH(date_report) AS month,
                SUM(CASE WHEN j.job_name = 'BHX HCM' AND type_work <> 2 AND type_work <> 4 THEN 1 ELSE 0 END) AS sum_bhx_pt,
                SUM(CASE WHEN j.job_name = 'BHX Vị Thanh' AND type_work <> 2 AND type_work <> 4 THEN 1 ELSE 0 END) AS sum_bhx_vt,
                SUM(CASE WHEN j.job_name = 'BHX - BTRE' AND type_work <> 2 AND type_work <> 4 THEN 1 ELSE 0 END) AS sum_bhx,
                SUM(CASE WHEN j.job_name = 'Heo' AND type_work <> 2 AND type_work <> 4 THEN 1 ELSE 0 END) AS sum_heo,
                SUM(CASE WHEN j.job_name = 'Gà NM' AND type_work <> 2 AND type_work <> 4 THEN 1 ELSE 0 END) AS sum_ga_nm,
                SUM(CASE WHEN j.job_name = 'Trứng' AND type_work <> 2 AND type_work <> 4 THEN 1 ELSE 0 END) AS sum_trung,
                SUM(CASE WHEN j.job_name = 'Gà hàng chợ' AND type_work <> 2 AND type_work <> 4 THEN 1 ELSE 0 END) AS sum_ga_cho,

                SUM(CASE WHEN type_work = 2 THEN 1 ELSE 0 END) AS sum_off,
                SUM(CASE WHEN type_work = 4 THEN 1 ELSE 0 END) AS sum_px
            FROM 
                ".$this->table." AS t 
                LEFT JOIN ".$this->tableUser." AS u ON u.id = t.driver
                LEFT JOIN ".$this->tableJob." AS j ON j.id = t.jobs ";

            $filter = "";

            if (isset($data["jobs"]) && $data["jobs"] != ''){
                $filter .= ($filter != '' ? ' AND ' : '')." j.id = '".$data["jobs"]."'";
            }
            if (isset($data["user"]) && $data["user"] != ''){
                $filter .= ($filter != '' ? ' AND ' : '')." driver = '".$data["user"]."'";
            }
            if (isset($data["from"]) && $data["from"] != ''){
                $filter .= ($filter != '' ? ' AND ' : '')." date_report BETWEEN '".$data["from"]."' AND '".$data["to"]."'";
            }
            if (isset($data["att_ver"]) && $data["att_ver"] != ''){
                $filter .= ($filter != '' ? ' AND ' : '')." att_ver = '".$data["att_ver"]."'";
            }
            
            if ($filter != "") {
                $query.= " WHERE ".$filter;
            }

            $query .= " GROUP BY 
                        driver,
                        u.fullname, 
                        u.position, 
                        MONTH(t.date_report)  ORDER BY fullname ASC ";

                        
            $handler = $dbadapter->query($query, Adapter::QUERY_MODE_EXECUTE);
            
            $data = $handler->toArray();
            if (empty($data)) {
                return null;
            } else {
                return $data;
            }
        }
        
        public function getAllDetailAttendance($data)
        {
            $dbadapter = $this->adapter;
            $query = "SELECT 
                t.driver,
                t.jobs,
                t.date_report,
                t.type_work,
                u.fullname,
                u.position,
                j.job_name
            FROM 
                ".$this->table." AS t 
                LEFT JOIN ".$this->tableUser." AS u ON u.id = t.driver
                LEFT JOIN ".$this->tableJob." AS j ON j.id = t.jobs ";

            $filter = "";

            if (isset($data["jobs"]) && $data["jobs"] != ''){
                $filter .= ($filter != '' ? ' AND ' : '')." t.jobs = '".$data["jobs"]."'";
            }
            if (isset($data["user"]) && $data["user"] != ''){
                $filter .= ($filter != '' ? ' AND ' : '')." t.driver = '".$data["user"]."'";
            }
            if (isset($data["from"]) && $data["from"] != ''){
                $filter .= ($filter != '' ? ' AND ' : '')." date_report BETWEEN '".$data["from"]."' AND '".$data["to"]."'";
            }
            if (isset($data["att_ver"]) && $data["att_ver"] != ''){
                $filter .= ($filter != '' ? ' AND ' : '')." att_ver = '".$data["att_ver"]."'";
            }
            
            if ($filter != "") {
                $query.= " WHERE ".$filter;
            }

            $query .= " ORDER BY  job_name ASC, date_report ASC ";

            $handler = $dbadapter->query($query, Adapter::QUERY_MODE_EXECUTE);
            
            $data = $handler->toArray();
            if (empty($data)) {
                return null;
            } else {
                return $data;
            }
        }
        
        public function getOneAttendance($id)
        {
            $dbadapter = $this->adapter;
            $query = "SELECT *
            FROM
                ".$this->table." WHERE id = '".$id."'";

            $handler = $dbadapter->query($query, Adapter::QUERY_MODE_EXECUTE);
            
            $data = $handler->toArray();
            if (empty($data)) {
                return null;
            } else {
                return $data[0];
            }
        }
        
        public function saveDataAttendance(array $data)
        {
            $dbadapter = $this->adapter;
            /* Convert data */
            if (isset($data['id'])){
                $values['id'] = $data['id'];
            }
            if (isset($data['code'])){
                $values['code'] = $data['code'];
            }
            if (isset($data['driver'])){
                $values['driver'] = $data['driver'];
            }
            if (isset($data['jobs'])){
                $values['jobs'] = $data['jobs'];
            }
            if (isset($data['date_report'])){
                $values['date_report'] = $data['date_report'];
            }
            if (isset($data['time_create'])){
                $values['time_create'] = $data['time_create'];
            }
            if (isset($data['type_work'])){
                $values['type_work'] = $data['type_work'];
            }
            if (isset($data['att_ver'])){
                $values['att_ver'] = $data['att_ver'];
            }

            if ($values != null){
                /* Start save */
                $query = "INSERT INTO ".$this->table."  ( ";

                $col = '';
                $val = '';
                $update = "";
                foreach ($values as $key => $value) {
                    $col .= ($col != '' ? ',' : '').$key;
                    $val .= ($val != '' ? ',' : '')."'".$value."'";

                    $update .= ($update != ''  ? ', ' : '').($key." = '".$value."'");
                }

                $query .= $col.') values('.$val.')';
            
                $query .= " ON DUPLICATE KEY UPDATE ".$update;
            
                $handler = $dbadapter->query($query, Adapter::QUERY_MODE_EXECUTE);
            }
            return null;
        }

        public function deleteAttendance($id)
        {
            $dbadapter = $this->adapter;
            if ($id != ''){
                /* Start save */
                $query = "DELETE FROM ".$this->table."  WHERE id = '".$id."' ";
            
                $dbadapter->query($query, Adapter::QUERY_MODE_EXECUTE);
            }
            return null;
        }

        public function deleteAttendanceByDriver($dataDel)
        {
            $dbadapter = $this->adapter;
            $query = "DELETE FROM ".$this->table."  WHERE driver = '".$dataDel['driver']."' AND att_ver = '".$dataDel['att_ver']."' AND (date_report BETWEEN '".$dataDel['from']."' AND '".$dataDel['to']."') ";
        
            $dbadapter->query($query, Adapter::QUERY_MODE_EXECUTE);

            return null;
        }

        public function deleteAttendanceByMonth($dataDel)
        {
            $dbadapter = $this->adapter;
            $query = "DELETE FROM ".$this->table."  WHERE (date_report BETWEEN '".$dataDel['from']."' AND '".$dataDel['to']."') AND att_ver = '".$dataDel['att_ver']."' ";

            $dbadapter->query($query, Adapter::QUERY_MODE_EXECUTE);

            return null;
        }
    /**Attendance */
    
    /**User */
        public function getAllUser($data)
        {
            $dbadapter = $this->adapter;
            $query = "SELECT  *
            FROM 
                ".$this->tableUser." AS t ";

            $filter = "";

            if (isset($data["user"]) && $data["user"] != ''){
                $filter .= ($filter != '' ? ' AND ' : '')." id = '".$data["user"]."'";
            }
            if (isset($data["status"]) && $data["status"] != ''){
                $filter .= ($filter != '' ? ' AND ' : '')." status = '".$data["status"]."'";
            }
            
            if ($filter != "") {
                $query.= " WHERE ".$filter;
            }

            $query .= " GROUP BY id ORDER BY fullname ASC ";

            $handler = $dbadapter->query($query, Adapter::QUERY_MODE_EXECUTE);
            
            $data = $handler->toArray();
            if (empty($data)) {
                return null;
            } else {
                return $data;
            }
        }
        
        public function getOneUser($id)
        {
            $dbadapter = $this->adapter;
            $query = "SELECT *
            FROM
                ".$this->tableUser." WHERE id = '".$id."'";
    
            $handler = $dbadapter->query($query, Adapter::QUERY_MODE_EXECUTE);
            
            $data = $handler->toArray();
            if (empty($data)) {
                return null;
            } else {
                return $data[0];
            }
        }
        
        public function saveDataUser(array $data)
        {
            $dbadapter = $this->adapter;
            /* Convert data */
            if (isset($data['id'])){
                $values['id'] = $data['id'];
            }
            if (isset($data['fullname'])){
                $values['fullname'] = $data['fullname'];
            }
            if (isset($data['position'])){
                $values['position'] = $data['position'];
            }
            if (isset($data['status'])){
                $values['status'] = $data['status'];
            }

            if ($values != null){
                /* Start save */
                $query = "INSERT INTO ".$this->tableUser."  ( ";

                $col = '';
                $val = '';
                $update = "";
                foreach ($values as $key => $value) {
                    $col .= ($col != '' ? ',' : '').$key;
                    $val .= ($val != '' ? ',' : '')."'".$value."'";

                    $update .= ($update != ''  ? ', ' : '').($key." = '".$value."'");
                }

                $query .= $col.') values('.$val.')';
            
                $query .= " ON DUPLICATE KEY UPDATE ".$update;
            
                $handler = $dbadapter->query($query, Adapter::QUERY_MODE_EXECUTE);
            }
            return null;
        }
        
        public function deleteDataUser($id)
        {
            $dbadapter = $this->adapter;
            if ($id != ''){
                /* Start save */
                $query = "DELETE FROM ".$this->tableUser."  WHERE id = '".$id."' ";
            
                $dbadapter->query($query, Adapter::QUERY_MODE_EXECUTE);
            }
            return null;
        }
    /**User */
    
    /**Job */
        public function getAllJob($search)
        {
            $dbadapter = $this->adapter;
            $query = "SELECT  *
            FROM 
                ".$this->tableJob." AS t ";

            $filter = "";

            if (isset($search["jobs"]) && $search["jobs"] != ''){
                $filter .= ($filter != '' ? ' AND ' : '')." id = '".$search["jobs"]."'";
            }
            if (isset($search["job_name"]) && $search["job_name"] != ''){
                $filter .= ($filter != '' ? ' AND ' : '')." job_name LIKE '%".$search["job_name"]."%'";
            }
            if (isset($search["att_ver"]) && $search["att_ver"] != ''){
                $filter .= ($filter != '' ? ' AND ' : '')." job_ver = '".$search["att_ver"]."'";
            }
            
            if ($filter != "") {
                $query.= " WHERE ".$filter;
            }

            $query .= " GROUP BY id ORDER BY job_ver ";

            $handler = $dbadapter->query($query, Adapter::QUERY_MODE_EXECUTE);
            
            $data = $handler->toArray();
            if (empty($data)) {
                return null;
            } else {
                return $data;
            }
        }
            
        public function getOneJob($id)
        {
            $dbadapter = $this->adapter;
            $query = "SELECT *
            FROM
                ".$this->tableJob." WHERE id = '".$id."'";

            $handler = $dbadapter->query($query, Adapter::QUERY_MODE_EXECUTE);
            
            $data = $handler->toArray();
            if (empty($data)) {
                return null;
            } else {
                return $data[0];
            }
        }
        
        public function saveDataJob(array $data)
        {
            $dbadapter = $this->adapter;
            /* Convert data */
            if (isset($data['id'])){
                $values['id'] = $data['id'];
            }
            if (isset($data['job_name'])){
                $values['job_name'] = $data['job_name'];
            }
            if (isset($data['type_works'])){
                $values['type_works'] = $data['type_works'];
            }
            if (isset($data['job_ver'])){
                $values['job_ver'] = $data['job_ver'];
            }

            if ($values != null){
                /* Start save */
                $query = "INSERT INTO ".$this->tableJob."  ( ";

                $col = '';
                $val = '';
                $update = "";
                foreach ($values as $key => $value) {
                    $col .= ($col != '' ? ',' : '').$key;
                    $val .= ($val != '' ? ',' : '')."'".$value."'";

                    $update .= ($update != ''  ? ', ' : '').($key." = '".$value."'");
                }

                $query .= $col.') values('.$val.')';
            
                $query .= " ON DUPLICATE KEY UPDATE ".$update;
            
                $handler = $dbadapter->query($query, Adapter::QUERY_MODE_EXECUTE);
            }
            return null;
        }
    /**Job */
}