<?php

class UndefinedActionException extends CSR_Exception {
	public function __construct($message = null, $HTTPStatusCode = null, $debugHelpFilePath = null) {
		$message           = (!is_null($message          )) ? $message          : '指定されたアクションは存在しません。';
		$HTTPStatusCode    = (!is_null($HTTPStatusCode   )) ? $HTTPStatusCode   : 500;
		$debugHelpFilePath = (!is_null($debugHelpFilePath)) ? $debugHelpFilePath: CSR_MVC_DEBUG_HELP_DIR . 'undefined_action.php';

		parent::__construct($message, $HTTPStatusCode, $debugHelpFilePath);
	}
}