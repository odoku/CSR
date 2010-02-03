<?php

require_once 'CSR_Exception.php';

class MissingTargetFunctionException extends CSR_Exception {
	public function __construct($message = null, $HTTPStatusCode = null, $debugHelpFilePath = null) {
		$message           = (!is_null($message          )) ? $message          : 'ルーティングされたターゲットは処理されませんでした。';
		$HTTPStatusCode    = (!is_null($HTTPStatusCode   )) ? $HTTPStatusCode   : 500;
		$debugHelpFilePath = (!is_null($debugHelpFilePath)) ? $debugHelpFilePath: CSR_DEBUG_HELP_DIR . 'missing_target_function.php';

		parent::__construct($message, $HTTPStatusCode, $debugHelpFilePath);
	}
}