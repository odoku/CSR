<?php

require_once 'CSR_Exception.php';

class RequestURIParseException extends CSR_Exception {
	public function __construct($message = null, $HTTPStatusCode = null, $debugHelpFilePath = null) {
		$message           = (!is_null($message          )) ? $message          : '指定されたURIは存在しません。';
		$HTTPStatusCode    = (!is_null($HTTPStatusCode   )) ? $HTTPStatusCode   : 404;
		$debugHelpFilePath = (!is_null($debugHelpFilePath)) ? $debugHelpFilePath: CSR_DEBUG_HELP_DIR . 'dispatch_error.php';

		parent::__construct($message, $HTTPStatusCode, $debugHelpFilePath);
	}
}