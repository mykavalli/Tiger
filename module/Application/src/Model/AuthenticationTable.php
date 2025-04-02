<?php 
declare(strict_types=1);

namespace Manager\Model\Table;

use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Db\Adapter\Adapter;
use Manager\Model\Entity\UserEntity;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Paginator\Adapter\DbSelect;
use Laminas\Paginator\Paginator;
use Laminas\Crypt\Password\Bcrypt;

use Laminas\Validator\EmailAddress;

class AuthenticationTable extends AbstractTableGateway {
	protected $adapter;
	protected $table = "tbw_authentication";
	
	public  function __construct(Adapter $adapter){
		$this->adapter = $adapter;
		$this->initialize();
	}

    public function getAllUser($data)
    {
        $dbadapter = $this->adapter;
        $query = "SELECT *
        FROM 
            ".$this->table." AS t ";

        $filter = "";

        if (isset($data["fullname"]) && $data["fullname"] != ''){
            $filter .= ($filter != '' ? ' AND ' : '')." fullname LIKE '%".$data["fullname"]."%'";
        }
        
        
        if ($filter != "") {
            $query.= " WHERE ".$filter;
        }

        $data = $handler->toArray();
        if (empty($data)) {
            return null;
        } else {
            return $data;
        }
    }
        
    public function getOneUser($username)
    {
        $dbadapter = $this->adapter;
        $query = "SELECT *
        FROM
            ".$this->table." WHERE username = '".$username."'";

        $handler = $dbadapter->query($query, Adapter::QUERY_MODE_EXECUTE);
        
        $data = $handler->toArray();
        if (empty($data)) {
            return null;
        } else {
            return $data[0];
        }
    }
	
	public function saveUsers(array $data)
	{
		$dbadapter = $this->adapter;
		$bcrypt = new Bcrypt();
		/* Convert data */
		
		if (isset($data['fullname'])){
			$values['fullname'] = mb_convert_case($data['fullname'], MB_CASE_TITLE, 'UTF-8');
		}

		$validator = new EmailAddress();
		
		if (isset($data['email']) && $validator->isValid(trim($data['email']))){
			$values['email'] = trim($data['email']);
		}
		
		if (isset($data['role'])){
			$values['role'] = $data['role'];
		}
		
		if (isset($data['active'])){
			$values['active'] = $data['active'];
		}
		
		if (isset($data['password']) && $data['password'] != ''){
			$values['password'] = $bcrypt->create($data['password']);

			$data['date_change_password'] = date("Y-m-d 23:59:59", strtotime("+90 day"));
		}
		
		if (isset($data['username'])){
			$values['username'] = trim($data['username'] != '' ? $data['username'] : $values['code_auto']);
		}
		if (isset($data['reset_pw_token']) && $data['reset_pw_token'] != ''){
			$values['reset_pw_token'] = $data['reset_pw_token'] != '' ? $bcrypt->create($data['reset_pw_token']) : '';
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
}