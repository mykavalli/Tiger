<?php

declare(strict_types=1);

namespace Application\Controller;

use Laminas\Authentication\AuthenticationService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Laminas\Db\Adapter\Adapter;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Authentication\Result;
use Laminas\Session\SessionManager;
use Laminas\Session\Storage\ArrayStorage;
use Laminas\Session\Container;
use Laminas\Json\Json;

use Laminas\Mime\Message as MimeMessage;
use Laminas\Mime\Mime;
use Laminas\Mime\Part as MimePart;

use Laminas\Mail\Message;
use Laminas\Mail\Transport\Smtp as SmtpTransport;
use Laminas\Mail\Transport\SmtpOptions;

use Application\Model\AuthenticationTable;

use Laminas\Log\Logger;
use Laminas\Log\Writer\Stream;

use Ramsey\Uuid\Uuid;

class AuthController extends AbstractActionController
{
	private $adapter;
	private $authenticationTable;
	private $logger;
	private $ip;
	
	public function __construct(
		Adapter $adapter, 
		AuthenticationTable $authenticationTable)
	{
		$this->adapter = $adapter;
		$this->authenticationTable = $authenticationTable;

		/**Get log */
		$log = (getcwd().'/public/log/').date("d-m-Y").'.txt';
		if (!file_exists($log)) {
			touch($log);
		}

		$stream = @fopen($log, "a", false);
		$writer = new Stream($stream);
		$logge = new Logger();
		$logge->addWriter($writer);

		$this->logger = $logge;
		
		// Get real visitor IP behind CloudFlare network
		$ipAdd = '';
		if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
			$_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
			$_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
		}
		$client  = @$_SERVER['HTTP_CLIENT_IP'];
		$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		$remote  = $_SERVER['REMOTE_ADDR'];

		if(filter_var($client, FILTER_VALIDATE_IP))
		{
			$ipAdd = $client;
		}
		elseif(filter_var($forward, FILTER_VALIDATE_IP))
		{
			$ipAdd = $forward;
		}
		else
		{
			$ipAdd = $remote;
		}

		$this->ip = $ipAdd;
	}

	public function notFoundAction()
    {
        // Handle the redirect or display a custom error view
        // You can choose to redirect the user or display a custom error view
        // For example, you can use the following line to redirect to the homepage:
        // return $this->redirect()->toRoute('home');

        // Alternatively, you can display a custom error view
        $viewModel = new ViewModel();
        $viewModel->setTemplate('error/404'); // Customize the template name as per your project
        return $viewModel;
    }
	
	public function loginAction()
	{
		$this->layout('layout/layout_auth');
		$auth = new AuthenticationService();
		$containerUser = new Container('user');
		if ($auth->hasIdentity()){
			return $this->redirect()->toRoute('manage-attendance-v2');
		}
		
		$request = $this->getRequest();
		if ($request->isPost())
		{

			$data = $request->getPost()->toArray();
			
			$authAdapter = new CredentialTreatmentAdapter($this->adapter);
			$authAdapter->setTableName($this->authenticationTable->getTable())
			->setIdentityColumn('username')
			->setCredentialColumn('password')
			->getDbSelect()->where(['active' => 1]);
			
			$authAdapter->setIdentity($data['username']);
			# password hashing class
			$hash = new Bcrypt();
			
			$info = $this->authenticationTable->getOneByUsername($data['username']);

			if ($info != null) 
			{
				// $userRole = $this->rolesTable->fetchRoleByColumn('role', $info->getRole());
				# now compare password from form input with that already in the table
				if($hash->verify($data['password'], $info['password'])) {
					$authAdapter->setCredential($info['password']);
				} else {
					$authAdapter->setCredential(''); # why? to gracefully handle errors
				}
			}
			else 
			{
				$this->flashMessenger()->addErrorMessage('Đăng nhập thất bại, vui lòng thử lại !');
				return $this->redirect()->refresh(); # refresh the page to show error
			}

			if ($info['active'] != '1') 
			{
				$this->flashMessenger()->addErrorMessage('Tài khoản đang tạm khóa !');
				return $this->redirect()->refresh(); # refresh the page to show error
			}
			
			$authResult = $auth->authenticate($authAdapter);
			
			switch ($authResult->getCode()) {
				case Result::FAILURE_IDENTITY_NOT_FOUND:
					$this->flashMessenger()->addErrorMessage('Unknow account!');
					return $this->redirect()->refresh(); # refresh the page to show error
					break;
					
				case Result::FAILURE_CREDENTIAL_INVALID:
					$this->flashMessenger()->addErrorMessage('Incorrect Password!');
					return $this->redirect()->refresh(); # refresh the page to show error
					break;
					
				case Result::SUCCESS:
					if(isset($data['recall']) && $data['recall'] == 'on') {
						$ssm = new SessionManager();
						$ttl = 1814400; # time for session to live
						$ssm->rememberMe($ttl);
					}

					// $setting = $this->settingTable->fetchAllSetting();
					// $lsSetting = [];
					// if ($setting != null) {
						// foreach ($setting as $key => $value) {
						// 	if ($value['setting_value'] != null) {
						// 		$lsSetting[$value['setting_name']] = json_decode($value['setting_value'], true);
						// 	}
						// }

						/**Get job schedule action */
						// $actionSchedule = isset($lsSetting['access_group_product']) ? $lsSetting['access_group_product'] : null;

						$storage = $auth->getStorage();
						$storage->write($authAdapter->getResultRowObject(null, ['created', 'modified']));

						// $file = dirname(dirname(dirname(__FILE__))).DS.'data'.DS.'tpl'.DS.'language.json';
						// $file = file_get_contents($file);
						// $phpNative = Json::decode($file, true);
						
						$container = new Container('user');
						$container['Fullname'] = $info['fullname'];
						$container['Username'] = $info['username'];
						$container['Email'] = $info['email'];
						// $container['Role'] = $info->getRole();
						// $container['language'] = 'language_vn';

						// $container['Translate'] = $phpNative;

						// if (isset($userRole)) {
						// 	$container['RoleAccess'] = $userRole;
						// }
						// if ($accessGroupProduct != null) {
						// 	$container['AccessGroupProduct'] = $accessGroupProduct;
						// 	$container['ActionSchedule'] = isset($actionSchedule['product-action'][$info->getDeptCode()]) ? 'On' : 'Off';
						// }
					// }

					# let us now create the profile route and we will be done
					$link = $containerUser['Link'];

					$logger = $this->logger;
					$logger->info($containerUser['Fullname'].' | IP: '.$this->ip.' | IP: '.$this->ip.' | Login | '.$_SERVER['REDIRECT_URL'].' | '.$_SERVER['REQUEST_METHOD'].' | '.$_SERVER['HTTP_USER_AGENT']);
					
					if ($containerUser['Link'] != ''){
						$containerUser['Link'] = '';
						return $this->redirect()->toUrl((isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].$link);
					} else {
						return $this->redirect()->toRoute('manage-attendance-v2');
					}
					break;
					
				default:
					$this->flashMessenger()->addErrorMessage('Authentication failed. Try again');
					return $this->redirect()->refresh(); # refresh the page to show error
					break;
			}
		}
		return new ViewModel();
	}
	
	public function logoutAction()
	{
		$auth = new AuthenticationService();
		$containerUser = new Container('user');

		$logger = $this->logger;
		$logger->info($containerUser['Fullname'].' | IP: '.$this->ip.' | Logout | '.$_SERVER['REDIRECT_URL'].' | '.$_SERVER['REQUEST_METHOD'].' | '.$_SERVER['HTTP_USER_AGENT']);

		if ($auth->hasIdentity()){
			$auth->clearIdentity();
		}
		$this->flashMessenger()->addErrorMessage('Bạn đã đăng xuất khỏi hệ thống !');
		return $this->redirect()->toRoute('login');
	}
	
	public function forgotAction()
	{
		$this->layout('layout/layout_auth');
		$auth = new AuthenticationService();
		$containerUser = new Container('user');
		if ($auth->hasIdentity()){
			$auth->clearIdentity();
		}

		$logger = $this->logger;
		$logger->info($containerUser['Fullname'].' | IP: '.$this->ip.' | Forgot pass | '.$_SERVER['REDIRECT_URL'].' | '.$_SERVER['REQUEST_METHOD'].' | '.$_SERVER['HTTP_USER_AGENT']);
		

		/**Get request post */
		$request = $this->getRequest();
		if ($request->isPost()){
			$dataPost = $request->getPost()->toArray();
			$checkUser = $this->authenticationTable->getOneByEmail($dataPost['email']);
			if ($checkUser != null){
				#Set mail to send
                $codeGen = Uuid::uuid4();
                
				$token = $codeGen->toString();

				$dataUpdate['reset_pw_token'] = $token;
				$dataUpdate['id'] = $checkUser['id'];
				$dataUpdate['active'] = '0';
				$updateUser = null;
				$updateUser = $this->authenticationTable->saveUsers($dataUpdate);

				// $checkSetting = $this->settingTable->fetchSettingByColumnArray('setting_name', 'admin_email');
				// if ($updateUser == null && $checkSetting != null && $checkSetting['setting_value'] != null && is_array(json_decode($checkSetting['setting_value'], true))){
				// 	$lstMailTo = '';
				// 	$lstMailCc = '';
				// 	$lstMailBcc = '';
				// 	$subject = 'Đặt lại mật khẩu';

				// 	$settingValue = json_decode($checkSetting['setting_value'], true);
				// 	$actual_link = "https://asm-internal.danonfoods.com/password/reset?token=".$token.'&code='.$checkUser->getUserId();
					
				// 	/**Get content mail */
				// 	$file = dirname(dirname(dirname(__FILE__))).DS.'data'.DS.'tpl'.DS.'forgot.tpl';
				// 	$file = file_get_contents($file);
				// 	$content = str_replace('#NAME#', $checkUser->getFullname(), $file);
				// 	$content = str_replace('#LINK#', $actual_link, $content);
					
				// 	$lstMailTo .= ($lstMailTo != '' ? ';' :'').($checkUser->getEmail());

				// 	$codeGen = Uuid::uuid4();
				// 	$job = 'JobMail_'.($codeGen->toString()).'.txt';
			
				// 	$jobMail = (getcwd().'/public/form/jobs/').$job;
				// 	if (!file_exists($jobMail)) {
				// 		touch($jobMail);
				// 	}

				// 	$fileHandle = fopen($jobMail, 'w');
				// 	fwrite($fileHandle, $content);
				// 	fclose($fileHandle);

				// 	if ($lstMailTo != '') {
				// 		$this->settingTable->saveJobMail([
				// 			'to' => $lstMailTo,
				// 			'cc' => $lstMailCc,
				// 			'bcc' => $lstMailBcc,
				// 			'subject' => $subject,
				// 			'content' => $job,
				// 			'create_date' => date("Y-m-d H:i:s")
				// 		]);
				// 	}

				// 	$this->flashMessenger()->addSuccessMessage('Gửi yêu cầu thành công. Vui lòng kiểm tra email !');
				// 	return $this->redirect()->toRoute('login');
				// }
				// else {
				// 	$this->flashMessenger()->addErrorMessage('Không kết nối được hệ thống !');
				// 	return $this->redirect()->toRoute('recover-password');
				// }
			}
			else {
				$this->flashMessenger()->addErrorMessage('Không tồn tại email !');
				return $this->redirect()->toRoute('recover-password');
			}
		}

		return new ViewModel();
	}

	public function resetAction()
	{
		$this->layout('layout/layout_auth');
		$token = isset($_GET['token']) ? $_GET['token'] : null;
		$code = isset($_GET['code']) ? $_GET['code'] : null;
		// $bcrypt = new Bcrypt();
		$checkUser = $this->authenticationTable->getOneById($code);
		$request = $this->getRequest();
		if ($token != null && $code != null) {
			/**Check user */
			if ($checkUser != null) {
				if($token == $checkUser['reset_pw_token']) {

					$logger = $this->logger;
					$logger->info($checkUser->getFullname().' | Reset password | '.$_SERVER['REDIRECT_URL'].' | '.$_SERVER['REQUEST_METHOD'].' | '.$_SERVER['HTTP_USER_AGENT']);
                
                    if ($request->isGet()){
                        $this->flashMessenger()->addSuccessMessage('Vui lòng nhập mật khẩu mới !');
                        return new ViewModel();
                    }
                    if ($request->isPost()){
                        $dataPost = $request->getPost()->toArray();
                        $check = $this->authenticationTable->saveUsers([
                            'id' => $dataPost['code'],
                            'reset_pw_token' => '',
                            'active' => '1',
                            'password' => $dataPost['password']
                        ]);
                        if ($check == null) {
                            $this->flashMessenger()->addSuccessMessage('Thay đổi mật khẩu thành công !');
                        }
                        else {
                            $this->flashMessenger()->addErrorMessage('Lỗi trong quá trình thay đổi mật khẩu !');
                        }
                        return $this->redirect()->toRoute('login');
                    }
				}
				else {
					$this->flashMessenger()->addErrorMessage('Token không chính xác !');
					return $this->redirect()->toRoute('login');
				}
			}
			else {
				$this->flashMessenger()->addErrorMessage('Không tồn tại mã token/code !');
				return $this->redirect()->toRoute('login');
			}
		}
		else {
			$this->flashMessenger()->addErrorMessage('Mã token/code rỗng !');
			return $this->redirect()->toRoute('login');
		}
	}
	
	public function profileAction()
	{
		 
        /* Check auth */
		$auth = new AuthenticationService();
		$containerUser = new Container('user');
		$containerUser['Link'] = $_SERVER['REQUEST_URI'] != '/roles/check' ? $_SERVER['REQUEST_URI'] : '';
		if (!$auth->hasIdentity()){
			return $this->redirect()->toRoute('login');
		}
		
        /* Check role */
        $routeMatch = $this->getEvent()->getRouteMatch();
		$routeName = $routeMatch->getMatchedRouteName();

		// $checkAccess = $this->rolesTable->checkRole($containerUser['Role'], $routeName);
		$allUser = $this->authenticationTable->getAllUser();
		// $allBranch = $this->branchTable->fetchAllBranch();
				
		$container = new Container('HeaderContent');
		$container['HeaderTitle'] = $containerUser['Translate'][$containerUser['language']]['menu_personal_info'];

		if ($allUser != null){
			foreach ($allUser as $user) {
				$lstUser[$user['username']] = $user['fullname'];
			}
		}
		if ($allBranch != null){
			foreach ($allBranch as $branch) {
				$lstBranch[$branch['branch_code']] = $branch['branch_name'];
			}
		}

		$positions = '';
		foreach ($this->positionTable->fetchAllData() as $position) {
			# code...
			if ($position['position_code'] == $containerUser['Position']) {
				# code...
				$positions = $position['position_name'];
			}
		}
		
		if (!$checkAccess)
		{
			$this->flashMessenger()->addErrorMessage($containerUser['Translate'][$containerUser['language']]['content_not_allow_access']);
			return $this->redirect()->toRoute('manage-attendance-v2');
		}

		$logger = $this->logger;
		$logger->info($containerUser['Fullname'].' | IP: '.$this->ip.' | '.$container['HeaderTitle'].' | '.$_SERVER['REDIRECT_URL'].' | '.$_SERVER['REQUEST_METHOD'].' | '.$_SERVER['HTTP_USER_AGENT']);


		$request = $this->getRequest();
		if ($request->isPost()){
			$data = $request->getPost()->toArray();
			
			if (isset($data['change_image'])){
				$fileName = $_FILES['avatar']['name'];
				$tmpName = $_FILES['avatar']['tmp_name'];
				move_uploaded_file($tmpName, 'public/img/user/'.$fileName);
				$data['photo'] = $fileName;
				$this->getAllUser->saveUsers($data);
				$containerUser['Photo'] = $fileName;

				return $this->redirect()->toRoute('profile');
			}
			if (isset($data['change_profile'])) {
				$data['birthday'] = date('Y-m-d 00:00:00', strtotime(str_replace('/','-',$data['birthday'])));
				$this->getAllUser->saveUsers($data);
				$containerUser['Fullname'] = $data['fullname'];
				$containerUser['Gender'] = $data['gender'];
				return $this->redirect()->toRoute('profile');
			}
		}
		
		$user = $this->getAllUser->getOneByUsername($containerUser['Username']);
		
		return new ViewModel([
			'positions' => $positions,
			'user' => $user,
			'depts' => $this->deptTable->fetchAllDept(),
			'itemManager' => count($this->itemsTable->fetchLikeItem('item_current_manager', $containerUser['Username']))
		]);
	}

	public function deleteOldFileAction()
	{		
		$directory = getcwd().'/public/downloads/production/';
		$days = 0;
		$timestamp = strtotime("-{$days} days");
		$files = scandir($directory);
		
		foreach ($files as $file) {
			$filePath = $directory.$file;
			if ($file != '' && is_file($filePath)) {
				if (($timestamp - filemtime($filePath) > 86400 ? $timestamp - filemtime($filePath) : 0)/86400 > 0 ) {
					unlink($filePath);
				}
			}
		}

		return false;
	}
}
