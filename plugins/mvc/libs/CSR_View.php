<?php

class CSR_View extends CSR_Event {
	private $_layouts = array();
	private $_controller = null;
	private $_action = null;
	private $_vars = array();
	private $_content = '';

	public function __construct($controller, $action) {
		$this->_controller = $controller;
		$this->_action = $action;
	}
	
	function render($layouts, $vars) {
		$this->_vars = $vars;
		
		if (count($layouts) === 0) {
			$layouts[] = CSR_VIEWS_DIR . CSR_LAYOUT_FILE_NAME;
			$layouts[] = CSR_VIEWS_DIR . $this->_controller . DS . $this->_action . '.html';
		}
		$this->_layouts = $layouts;
		
		ob_start();
		$this->content();
		$this->_content = ob_get_clean();

		return $this->_content;
	}
	
	function content() {
		$path = array_shift($this->_layouts);
		if (is_null($path)) {
			return false;
		}
		
		if (!file_exists($path)) {
			trigger_error(sprintf('No such layout file [%s].', $path), E_USER_WARNING);
			return false;
		}
		
		foreach ($this->_vars as $key => $value) $$key = &$value;
		require_once $path;
		return true;
	}
}
