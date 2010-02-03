<?php

require_once 'CSR_MVC_Config.php';
require_once 'CSR_Model.php';
require_once 'CSR_View.php';
require_once 'CSR_Controller.php';

require_once CSR_MVC_ERRORS_DIR . 'MissingControllerException.php';
require_once CSR_MVC_ERRORS_DIR . 'UndefinedActionException.php';
require_once CSR_MVC_ERRORS_DIR . 'PrivateActionException.php';

$_csr = CSR::getInstance();
$_mvc = CSR_MVC::getInstance($_csr);
$_csr->setTargetFunction(CSR_MVC_TARGET_REGEXP, array($_mvc, 'execute'));

class CSR_MVC {
	private static $_instance = null;
	private $csr = null;
	
	private function __construct($csr) {
		$this->csr = $csr;
	}
	
	public static function getInstance($csr = null) {
		if (is_null(self::$_instance)) {
			self::$_instance = new CSR_MVC($csr);
		}
		return self::$_instance;
	}
	
	public function execute() {
		$dispatcher = $this->csr->getDispatcher();
		
		$target = str_replace(CSR_MVC_PREFIX, '', $dispatcher->getTarget());
		list($controllerName, $actionName) = explode('/', $target);
		
		$controller = CSR_Controller::loadController($controllerName);
		$controller->_executeAction($actionName, $dispatcher->getArguments());
	}
}