<?php

declare(strict_types=1);

namespace Application\Controller;

use Laminas\Authentication\AuthenticationService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Session\Container;
use Laminas\Router\RouteMatch;
use Laminas\View\Model\JsonModel;

use Laminas\Log\Logger;
use Laminas\Log\Writer\Stream;

class LoggerController extends AbstractActionController
{
    private $logger;
    private $ip;

	public function __construct()
	{

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
	
	public function indexAction()
	{   
        /* Check auth */
		$containerUser = new Container('user');
		$container = new Container('HeaderContent');
		$logger = $this->logger;
		
		$request = $this->getRequest();
		$button = '';
		$link = $_SERVER['REDIRECT_URL'];

		if ($request->isPost()){
			$dataPost = $request->getPost()->toArray();
			if (isset($dataPost['comming']) && $dataPost['comming'] == 'frontend') {
				# code...
				$button = $dataPost['button'];
				$link = $dataPost['link'];
			}
		}
		$logger->info($containerUser['Fullname'].' | IP: '.$this->ip.' | '.$button.' | '.$container['HeaderTitle'].' | '.$link.' | '.$_SERVER['REQUEST_METHOD'].' | '.$_SERVER['HTTP_USER_AGENT']);
		
        return new JsonModel();
    }
}