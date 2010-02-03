<?php

require_once CSR_MVC_APP_DIR . 'BaseController.php';

class IndexController extends BaseController {
	public function __construct () {
		parent::__construct();
	}

	public function index() {
		// $this->layout = array(CSR_VIEWS_DIR . 'Index' . DS . 'index.html');
		$this->set('pageTitle', 'Welcome to CSR version2.0 !!');
	}
}