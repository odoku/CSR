<?php

class PrivateActionException extends CSR_Exception {
	public function __construct($message = null, $HTTPStatusCode = null, $debugHelpFilePath = null) {
		$message           = (!is_null($message          )) ? $message          : '指定されたアクションは実行する事が出来ません。';
		$HTTPStatusCode    = (!is_null($HTTPStatusCode   )) ? $HTTPStatusCode   : 403;
		$debugHelpFilePath = (!is_null($debugHelpFilePath)) ? $debugHelpFilePath: CSR_MVC_DEBUG_HELP_DIR . 'private_action.php';

		parent::__construct($message, $HTTPStatusCode, $debugHelpFilePath);
	}
}