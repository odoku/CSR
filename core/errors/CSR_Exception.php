<?php

class CSR_Exception extends Exception {
	private $_HTTPStatusCode    = null;
	private $_debugHelpFilePath = null;
	
	public function __construct($message, $HTTPStatusCode = 404, $debugHelpFilePath = null) {
		parent::__construct($message);
		
		$this->_HTTPStatusCode    = $HTTPStatusCode;
		$this->_debugHelpFilePath = $debugHelpFilePath;
	}
	
	public function getHTTPStatusCode() {
		return $this->_HTTPStatusCode;
	}
	
	public function getDebugHelpFilePath() {
		return $this->_debugHelpFilePath;
	}
}