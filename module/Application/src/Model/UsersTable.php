<?php 
declare(strict_types=1);

namespace Application\Model;

use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Db\Adapter\Adapter;
use Application\Model\Entity\UserEntity;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Paginator\Adapter\DbSelect;
use Laminas\Paginator\Paginator;
use Laminas\Crypt\Password\Bcrypt;

use Laminas\Validator\EmailAddress;

class UsersTable extends AbstractTableGateway {
	protected $adapter;
	protected $table = "tbw_users";
	
	public  function __construct(Adapter $adapter){
		$this->adapter = $adapter;
		$this->initialize();
	}
	
	public function fetchAllUsers($paginated = false)
	{
		$sqlQuery = $this->sql->select()
		->join('tbw_roles', 'tbw_roles.role='.$this->table.'.role', ['role_name'], 'left')
		->join('tbw_branch', 'tbw_branch.branch_code='.$this->table.'.branch_code', ['branch_name'], 'left')
		->join('tbw_department', 'tbw_department.dept_code='.$this->table.'.dept_code', ['dept_name'], 'left')
		->where(['deleted' => '0'])
		->order(['code' => 'asc']);
		
		if($paginated) {	
			$classMethod = new ClassMethodsHydrator();
			$entity      = new UserEntity();
			$resultSet   = new HydratingResultSet($classMethod, $entity);

			$paginatorAdapter = new DbSelect(
				$sqlQuery,
				$this->adapter,
				$resultSet
			);

			$paginator = new Paginator($paginatorAdapter);

			return $paginator;
		}

		$sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);
		$handler = $sqlStmt->execute();
		
		if(!$handler) {
			return null;
		}
		return $handler;
	}

	public function fetchAllUsersV2()
	{
		$dbadapter = $this->adapter;
		$query = "SELECT 
			t.*,
			dept_name
		FROM
			".$this->table." AS t
		LEFT JOIN tbw_department AS d ON d.dept_code = t.dept_code	
		WHERE deleted = 0 ";

		$handler = $dbadapter->query($query, Adapter::QUERY_MODE_EXECUTE);
		
		$data = $handler->toArray();
		if (empty($data)) {
			return null;
		} else {
			return $data;
		}
	}
	
	public function fetchAllUserByGet($dataSearch, $paginated)
	{
		$sqlQuery = $this->sql->select()
		->join('tbw_roles', 'tbw_roles.role='.$this->table.'.role', ['role_name'], 'left')
		->join('tbw_branch', 'tbw_branch.branch_code='.$this->table.'.branch_code', ['branch_name'], 'left')
		->join('tbw_department', 'tbw_department.dept_code='.$this->table.'.dept_code', ['dept_name'], 'left');
		if (isset($dataSearch['fullname']) && $dataSearch['fullname'] != '') {
			$sqlQuery->where->like('fullname', '%'.$dataSearch['fullname'].'%');
		}
		if (isset($dataSearch['email']) && $dataSearch['email'] != '') {
			$sqlQuery->where->like('email', '%'.$dataSearch['email'].'%');
		}
		if (isset($dataSearch['code']) && $dataSearch['code'] != '') {
			$sqlQuery->where->like('code', '%'.$dataSearch['code'].'%');
		}
		if (isset($dataSearch['dept_code']) && $dataSearch['dept_code'] != '') {
			$sqlQuery->where([$this->table.'.dept_code' => $dataSearch['dept_code']]);
		}
		if (isset($dataSearch['branch']) && $dataSearch['branch'] != '') {
			$sqlQuery->where([$this->table.'.branch_code' => $dataSearch['branch']]);
		}
		$sqlQuery->where(['deleted' => '0'])
		->order(['code' => 'asc']);
		if($paginated) {	
			$classMethod = new ClassMethodsHydrator();
			$entity      = new UserEntity();
			$resultSet   = new HydratingResultSet($classMethod, $entity);

			$paginatorAdapter = new DbSelect(
				$sqlQuery,
				$this->adapter,
				$resultSet
			);

			$paginator = new Paginator($paginatorAdapter);

			return $paginator;
		}
		$sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);
		$handler = $sqlStmt->execute();
		
		if(!$handler) {
			return null;
		}
		return $handler;
	}

	public function getAllUserActive()
	{
		$dbadapter = $this->adapter;
		$query = "SELECT 
			t.*
		FROM
			".$this->table." AS t WHERE deleted = 0 AND active = 1 ";

		$handler = $dbadapter->query($query, Adapter::QUERY_MODE_EXECUTE);
		
		$data = $handler->toArray();
		if (empty($data)) {
			return null;
		} else {
			return $data;
		}
	}

	public function getAllUsers()
	{
		$dbadapter = $this->adapter;
		$query = "SELECT 
			t.*
		FROM
			".$this->table." AS t ";

		$handler = $dbadapter->query($query, Adapter::QUERY_MODE_EXECUTE);
		
		$data = $handler->toArray();
		if (empty($data)) {
			return null;
		} else {
			return $data;
		}
	}
	
	public function fetchAllUsersManageItem()
	{
		$sqlQuery = $this->sql->select()
		->join('tbw_roles', 'tbw_roles.role='.$this->table.'.role', ['role_name', 'role_manage_item'])
		->join('tbw_branch', 'tbw_branch.branch_code='.$this->table.'.branch_code', ['branch_name'])
		->join('tbw_department', 'tbw_department.dept_code='.$this->table.'.dept_code', ['dept_name'])
		->where(['deleted' => '0', 'role_manage_item' => '1'])
		->order(['code' => 'asc']);
		$sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);
		$handler = $sqlStmt->execute();
		
		if(!$handler) {
			return null;
		}
		return $handler;
	}
	
	public function fetchUsersByRoleManage($username, $columnRole)
	{
		$sqlQuery = $this->sql->select()
		->join('tbw_roles', 'tbw_roles.role='.$this->table.'.role', ['role_name', $columnRole])
		->where(['deleted' => '0', 'username' => $username, $columnRole => '1']);
		$sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);
		$handler = $sqlStmt->execute();
		
		if(!$handler) {
			return null;
		}
		
		return $handler;
	}
	
	public function fetchOneUsersByRoleManage($username, $columnRole)
	{
		$sqlQuery = $this->sql->select()
		->join('tbw_roles', 'tbw_roles.role='.$this->table.'.role', ['role_name', $columnRole])
		->where(['deleted' => '0', 'username' => $username, $columnRole => '1']);
		$sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);
		$handler = $sqlStmt->execute()->current();
		
		if(!$handler) {
			return null;
		}
		
		return $handler;
	}
	
	public function fetchOneUsersByRoleAndDept($role, $dept)
	{
		$sqlQuery = $this->sql->select()
		->where(['deleted' => '0', 'role' => $role, 'dept_code' => $dept]);
		$sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);
		$handler = $sqlStmt->execute()->current();
		
		if(!$handler) {
			return null;
		}
		
		return $handler;
	}
	
	public function fetchAllOperators()
	{
		$dbadapter = $this->adapter;
		$query = "SELECT
			* 
		FROM
			`tbw_users` AS p 
		WHERE p.deleted = '0' AND p.username <> 'administrator' AND p.username NOT LIKE '%baove%' ORDER BY fullname ASC";
		
		$handler = $dbadapter->query($query, Adapter::QUERY_MODE_EXECUTE);
		$data = $handler->toArray();
		if (empty($data)) {
			return false;
		} else {
			if (count($data) == 1) {
				return $data[0];
			} else {
				return $data;
			}
		}
	}
	
	public function fetchAllUsersReviewer($type)
	{
		$sqlQuery = $this->sql->select()
		->join('tbw_roles', 'tbw_roles.role='.$this->table.'.role', ['role', 'role_name'])
		->join('tbw_department', 'tbw_department.dept_code='.$this->table.'.dept_code', ['dept_name'])
		->where(['role_reviewer_'.$type => '1', 'active' => '1'])
		->order(['code' => 'asc']);
		$sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);
		$handler = $sqlStmt->execute();
		
		if(!$handler) {
			return null;
		}
		return $handler;
	}
	
	public function fetchAccountByEmail(string $email)
	{
		$sqlQuery = $this->sql->select()
		->join('tbw_roles', 'tbw_roles.role='.$this->table.'.role', ['role', 'role_name'])
		->join('tbw_branch', 'tbw_branch.branch_code='.$this->table.'.branch_code', ['branch_code','branch_name'])
		->join('tbw_department', 'tbw_department.dept_code='.$this->table.'.dept_code', ['dept_code','dept_name'])
		->where(['email' => $email, 'deleted' => '0']);
		$sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);
		$handler = $sqlStmt->execute()->current();
		
		if(!$handler) {
			return null;
		}
		
		$classMethod = new ClassMethodsHydrator();
		$entity      = new UserEntity();
		$classMethod->hydrate($handler, $entity);
		
		return $entity;
	}

	public function checkCustom($data)
	{
		$dbadapter = $this->adapter;
		$query = "SELECT 
			t.fullname,
			t.username,
			t.canteen_card,
			t.gender,
			t.code,
			d.dept_name
		FROM
		tbw_users AS t 
		LEFT JOIN tbw_department AS d ON d.dept_code = t.dept_code
		WHERE ".$data['column']." = '".$data['value']."' AND t.deleted = '0'";
	
		$handler = $dbadapter->query($query, Adapter::QUERY_MODE_EXECUTE);
		
		$data = $handler->toArray();
		if (empty($data)) {
			return null;
		} else {
			return $data;
		}
	}

	public function checkCustomList($data)
	{
		$dbadapter = $this->adapter;
		$query = "SELECT 
			t.*
		FROM
		tbw_users AS t 
		WHERE  ";

		$filter = ' active = 1 AND deleted = 0 AND username <> '."'administrator'";

		if ($data != null) {
			if (isset($data['headofdept'])) {
				$filter .= " AND ((dept_code = '".$data['headofdept']."' AND position > ".$data['position'].") OR (position > ".$data['position'].") AND position < 8)";
			} else {
				foreach ($data as $key => $value) {
					$filter .= ' AND '.$key." ".$value;
				}
			}
		}

		$query.= $filter." ORDER BY SUBSTRING_INDEX(fullname, ' ', -1)";
		
		$handler = $dbadapter->query($query, Adapter::QUERY_MODE_EXECUTE);
		
		$data = $handler->toArray();
		if (empty($data)) {
			return null;
		} else {
			return $data;
		}
	}
	
	public function getAccountByUsername($username)
	{
		$sqlQuery = $this->sql->select()
		->join('tbw_roles', 'tbw_roles.role='.$this->table.'.role', ['role', 'role_name'])
		->join('tbw_branch', 'tbw_branch.branch_code='.$this->table.'.branch_code', ['branch_code','branch_name'])
		->join('tbw_department', 'tbw_department.dept_code='.$this->table.'.dept_code', ['dept_code','dept_name'])
		->where(['username' => $username, 'deleted' => '0']);
		$sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);
		$handler = $sqlStmt->execute()->current();
		
		$data = $handler->toArray();
		if (empty($data)) {
			return null;
		} else {
			return $data[0];
		}
	}
	
	public function fetchAccountByUsername($username)
	{
		$sqlQuery = $this->sql->select()
		->join('tbw_roles', 'tbw_roles.role='.$this->table.'.role', ['role', 'role_name'])
		->join('tbw_branch', 'tbw_branch.branch_code='.$this->table.'.branch_code', ['branch_code','branch_name'])
		->join('tbw_department', 'tbw_department.dept_code='.$this->table.'.dept_code', ['dept_code','dept_name'])
		->where(['username' => $username, 'deleted' => '0']);
		$sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);
		$handler = $sqlStmt->execute()->current();
		
		if(!$handler) {
			return null;
		}
		
		$classMethod = new ClassMethodsHydrator();
		$entity      = new UserEntity();
		$classMethod->hydrate($handler, $entity);
		
		return $entity;
	}
	
	public function fetchAccountByCodeauto($code_auto)
	{
		$dbadapter = $this->adapter;
		$query = "SELECT *
		FROM
			".$this->table."
		WHERE code_auto = '".$code_auto."' ";
		
		$handler = $dbadapter->query($query, Adapter::QUERY_MODE_EXECUTE);
		
		$data = $handler->toArray();
		if (empty($data)) {
			return null;
		} else {
			return $data[0];
		}
	}
	
	public function fetchAccountById($id)
	{
		$sqlQuery = $this->sql->select()
		->join('tbw_roles', 'tbw_roles.role='.$this->table.'.role', ['role', 'role_name'])
		->join('tbw_branch', 'tbw_branch.branch_code='.$this->table.'.branch_code', ['branch_code','branch_name'])
		->join('tbw_department', 'tbw_department.dept_code='.$this->table.'.dept_code', ['dept_code','dept_name'])
		->where(['user_id' => $id, 'deleted' => '0']);
		
		$sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);
		$handler = $sqlStmt->execute()->current();
		
		if(!$handler) {
			return null;
		}
		
		$classMethod = new ClassMethodsHydrator();
		$entity      = new UserEntity();
		$classMethod->hydrate($handler, $entity);
		
		return $entity;
	}

	public function fetchAllOperatorsBySetting($group)
	{
		$dbadapter = $this->adapter;
		$query = "SELECT *
		FROM
			".$this->table."
		WHERE deleted = '0' AND dept_code IN (".$group.") ORDER BY fullname ASC ";
		
		$handler = $dbadapter->query($query, Adapter::QUERY_MODE_EXECUTE);
		$data = $handler->toArray();
		if (empty($data)) {
			return false;
		} else {
			if (count($data) == 1) {
				return $data[0];
			} else {
				return $data;
			}
		}
	}
	
	public function saveUserCard(array $post)
	{
		if (isset($post['branch_contract'])){
			$values['branch_contract'] = $post['branch_contract'];
		}
		
		if (isset($post['canteen_card'])){
			$values['canteen_card'] = $post['canteen_card'];
		}
		
		if (isset($post['active'])){
			$values['active'] = $post['active'];
		}
		
		if (isset($post['deleted'])){
			$values['deleted'] = $post['deleted'];
		}
		
		$sqlQuery = $this->sql->update()->set($values)->where(['code' => $post['code']]);
		
		$sqlStm = $this->sql->prepareStatementForSqlObject($sqlQuery);
		$sqlStm->execute();
		return null;
	}
	
	public function saveUsers(array $data)
	{
		$dbadapter = $this->adapter;
		$bcrypt = new Bcrypt();
		/* Convert data */
		$values['modified'] = date('Y-m-d h:i:s');
		
		if (isset($data['user_id']) && $data['user_id'] != 0 && $data['user_id'] != null){
			$values['user_id'] = $data['user_id'];
		}
		
		if (isset($data['fullname'])){
			$values['fullname'] = mb_convert_case($data['fullname'], MB_CASE_TITLE, 'UTF-8');
		}
		
		if (isset($data['nickname'])){
			$values['nickname'] = trim($data['nickname']);
		}

		$validator = new EmailAddress();
		
		if (isset($data['email']) && $validator->isValid(trim($data['email']))){
			$values['email'] = trim($data['email']);
		}
		
		if (isset($data['position'])){
			$values['position'] = $data['position'];
		}
		
		if (isset($data['group_worker'])){
			$values['group_worker'] = $data['group_worker'];
		}
		
		if (isset($data['code'])){
			$values['code'] = str_replace("'", "", trim($data['code']));
		}
		
		if (isset($data['canteen_card'])){
			$values['canteen_card'] = trim($data['canteen_card']);
		}
		
		if (isset($data['code_auto'])){
			$values['code_auto'] = trim($data['code_auto']);
		}
		
		if (isset($data['birthday'])){
			$values['birthday'] = $data['birthday'];
		}
		
		if (isset($data['gender'])){
			$values['gender'] = $data['gender'];
		}
		if (isset($data['tel'])){
			$values['tel'] = $data['tel'];
		}
		if (isset($data['address'])){
			$values['address'] = $data['address'];
		}
		if (isset($data['mobile'])){
			$values['mobile'] = $data['mobile'];
		}
		if (isset($data['website'])){
			$values['website'] = $data['website'];
		}
		if (isset($data['fax'])){
			$values['fax'] = $data['fax'];
		}
		
		if (isset($data['role'])){
			$values['role'] = $data['role'];
		}
		
		if (isset($data['branch_code'])){
			$values['branch_code'] = $data['branch_code'];
		}
		
		if (isset($data['branch_contract'])){
			$values['branch_contract'] = $data['branch_contract'];
		}elseif (isset($data['branch_code'])){
			$values['branch_contract'] = $data['branch_code'];
		}
		
		if (isset($data['dept_code'])){
			$values['dept_code'] = $data['dept_code'];
		}
		
		if (isset($data['active'])){
			$values['active'] = $data['active'];
		}
		
		if (isset($data['photo'])){
			$values['photo'] = $data['photo'];
		}
		
		if (isset($data['password']) && $data['password'] != ''){
			$values['password'] = $bcrypt->create($data['password']);

			$data['date_change_password'] = date("Y-m-d 23:59:59", strtotime("+90 day"));
		}
		
		if (isset($data['date_change_password'])){
			$values['date_change_password'] = $data['date_change_password'];
		}
		
		if (isset($data['username'])){
			$values['username'] = trim($data['username'] != '' ? $data['username'] : $values['code_auto']);
		}
		
		if (isset($data['deleted'])){
			$values['deleted'] = $data['deleted'];
		}
		
		if (isset($data['created'])){
			$values['created'] = $data['created'];
		}
		if (isset($data['approval_user'])){
			$values['approval_user'] = $data['approval_user'] == 'on' || $data['approval_user'] == '1' ? 1 : $data['approval_user'];
		}
		
		// $values['approval_user'] = isset($data['approval_user']) && ($data['approval_user'] == 'on' || $data['approval_user'] == '1') ? 1 : 0 ;
		
		if (isset($data['request_pass_token']) && $data['request_pass_token'] != ''){
			$values['request_pass_token'] = $data['request_pass_token'] != '' ? $bcrypt->create($data['request_pass_token']) : '';
		}
		
		if (isset($data['request_pass_expired_date'])){
			$values['request_pass_expired_date'] = $data['request_pass_expired_date'] != '' ? $data['request_pass_expired_date'] : '';
		}
		
		/* Start save */
		// if (!isset($data['user_id']) || $data['user_id'] == 0 || $data['user_id'] == null) {
		// 	/* Add new data */
		// 	$checkUsername = $this->fetchAccountByUsername($values['username']);
		// 	if ($checkUsername == null) {
		// 		$sqlQuery = $this->sql->insert()->values($values);
		// 	}
		// }
		// else {
		// 	/* Check exists data */
		// 	$dataCheck = $this->fetchAccountById($data['user_id']);
		// 	if ($dataCheck != null) {
		// 		$sqlQuery = $this->sql->update()->set($values)->where(['user_id' => $data['user_id']]);
		// 	}
		// }
		
		// if (isset($sqlQuery)) {
		// 	$sqlStm = $this->sql->prepareStatementForSqlObject($sqlQuery);
		// 	$sqlStm->execute();
		// 	return null;
		// }

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
		
	public function updateUser(array $data)
	{
		$dbadapter = $this->adapter;
		/* Convert data */
		if (isset($data['user_id'])){
			$values['user_id'] = $data['user_id'];
		}
		
		if (isset($data['fullname'])){
			$values['fullname'] = mb_convert_case($data['fullname'], MB_CASE_TITLE, 'UTF-8');
		}
		
		if (isset($data['nickname'])){
			$values['nickname'] = trim($data['nickname']);
		}
		
		if (isset($data['email'])){
			$values['email'] = trim($data['email']);
		}
		
		if (isset($data['position'])){
			$values['position'] = $data['position'];
		}
		
		if (isset($data['group_worker'])){
			$values['group_worker'] = $data['group_worker'];
		}
		
		if (isset($data['code'])){
			$values['code'] = str_replace("'", "", trim($data['code']));
		}
		
		if (isset($data['canteen_card'])){
			$values['canteen_card'] = trim($data['canteen_card']);
		}
		
		$values['code_auto'] = isset($data['code_auto']) && $data['code_auto'] != '' ? $data['code_auto'] : 'UCA'.(time()+1);
		
		if (isset($data['birthday'])){
			$values['birthday'] = $data['birthday'];
		}
		
		if (isset($data['gender'])){
			$values['gender'] = $data['gender'];
		}
		if (isset($data['tel'])){
			$values['tel'] = $data['tel'];
		}
		if (isset($data['address'])){
			$values['address'] = $data['address'];
		}
		if (isset($data['mobile'])){
			$values['mobile'] = $data['mobile'];
		}
		if (isset($data['website'])){
			$values['website'] = $data['website'];
		}
		if (isset($data['fax'])){
			$values['fax'] = $data['fax'];
		}
		
		if (isset($data['role'])){
			$values['role'] = $data['role'];
		}
		
		if (isset($data['branch_code'])){
			$values['branch_code'] = $data['branch_code'];
		}
		
		if (isset($data['dept_code'])){
			$values['dept_code'] = $data['dept_code'];
		}
		
		if (isset($data['active'])){
			$values['active'] = $data['active'];
		}
		
		if (isset($data['photo'])){
			$values['photo'] = $data['photo'];
		}
		
		if (isset($data['password']) && $data['password'] != ''){
			$values['password'] = $bcrypt->create($data['password']);
		}
		
		if (isset($data['deleted'])){
			$values['deleted'] = $data['deleted'];
		}
		
		if (isset($data['request_pass_token']) && $data['request_pass_token'] != ''){
			$values['request_pass_token'] = $data['request_pass_token'] != '' ? $bcrypt->create($data['request_pass_token']) : '';
		}
		
		if (isset($data['request_pass_expired_date'])){
			$values['request_pass_expired_date'] = $data['request_pass_expired_date'] != '' ? $data['request_pass_expired_date'] : '';
		}
		
		if (isset($data['date_login'])){
			$values['date_login'] = $data['date_login'];
		}
		
		if (isset($data['date_change_password'])){
			$values['date_change_password'] = $data['date_change_password'];
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
		
	public function updateUserByList($data, $listId)
	{
		$dbadapter = $this->adapter;
		/* Convert data */
		if (isset($data['active'])){
			$values['active'] = $data['active'];
		}
				
		if (isset($data['deleted'])){
			$values['deleted'] = $data['deleted'];
		}
		
		if (isset($data['date_login'])){
			$values['date_login'] = $data['date_login'];
		}
		
		if (isset($data['date_change_password'])){
			$values['date_change_password'] = $data['date_change_password'];
		}

		if ($values != null){
			/* Start save */
			$query = "UPDATE ".$this->table." SET ";

			$update = "";
			foreach ($values as $key => $value) {
				$update .= ($update != ''  ? ', ' : '').($key." = '".$value."'");
			}
		
			$query .= $update." WHERE user_id IN ".$listId;
		
			$handler = $dbadapter->query($query, Adapter::QUERY_MODE_EXECUTE);
		}
		return null;
	}
	
	public function deleteData($id)
	{
		$sqlQuery = $this->sql->update()->set(['deleted' => 1])->where(['user_id' => $id]);
		$sqlStm = $this->sql->prepareStatementForSqlObject($sqlQuery);
		$sqlStm->execute();
		return null;
	}
}