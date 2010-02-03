<?php

require_once 'CSR_Event.php';
require_once 'CSR_Dispatcher.php';

class CSR extends CSR_Event {
	private static $_instance = null;
	
	private $_dispatcher = null;
	private $_targetFunctions = array();
	
	public function getDispatcher() {
		return $this->_dispatcher;
	}
	
	public function getTargetFunctions() {
		return $this->_targetFunctions;
	}

	public function getTargetFunction($pattern) {
		return $this->_targetFunctions[$pattern];
	}

	public function setTargetFunction($pattern, $callback) {
		$this->_targetFunctions[$pattern] = $callback;
	}
	
	private function __construct() {
		$this->addEvent(CSR_EVENT_DISPATCH_ERROR, array($this, 'dispatchErrorHandler'));
	}
	
	public static function getInstance() {
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	/**
	 * Execute CSR
	 * 
	 * <code>
	 * Usage
	 * CSR::execute($routeArray);
	 * </code>
	 * 
	 * @access public
	 * @param array routing array
	 * @return string rendered contents
	 */
	public function execute($routes) {
		if (!is_array($routes)) {
			throw new Exception('The first argument is not array.');
			return false;
		}
		
		try {
			/*=======================================================*/
			/* Application start.                                    */
			/*=======================================================*/
			$this->triggerEvent(CSR_EVENT_APPLICATION_START);


			/*=======================================================*/
			/* Parse Request URI.                                    */
			/*=======================================================*/
			$this->triggerEvent(CSR_EVENT_BEFORE_ROUTING);
			$this->_dispatcher = new CSR_Dispatcher($routes);
			$parsed = $this->_dispatcher->parseRequestURI($_SERVER['REQUEST_URI']);
			if (is_null($parsed)) throw new RequestURIParseException();
			$this->triggerEvent(CSR_EVENT_AFTER_ROUTING);


			/*=======================================================*/
			/* Execute target function.                              */
			/*=======================================================*/
			$this->triggerEvent(CSR_EVENT_BEFORE_EXEC_TARGET_FUNCTION);
			$targetFunction = null;
			foreach ($this->_targetFunctions as $pattern => $function) {
				if (preg_match($pattern, $parsed['target']) !== 0) $targetFunction = $function;
			}
			if (is_null($targetFunction)) throw new MissingTargetFunctionException();
			$result = call_user_func($function);
			$this->triggerEvent(CSR_EVENT_AFTER_EXEC_TARGET_FUNCTION);
			

			/*=======================================================*/
			/* Application end.                                      */
			/*=======================================================*/
			$this->triggerEvent(CSR_EVENT_APPLICATION_END);
		} catch (CSR_Exception $e) {
			$this->triggerEvent(CSR_EVENT_DISPATCH_ERROR, array(array(
				'status'        => $e->getHTTPStatusCode(),
				'message'       => $e->getMessage(),
				'debugHelpFile' => $e->getDebugHelpFilePath()
			)));
		}
	}

	protected function dispatchErrorHandler($params = array()) {
		switch ($params['status']) {
			case 400: $status = '400 Bad Request'                  ; break;
			case 401: $status = '401 Unauthorized'                 ; break;
			case 402: $status = '402 Payment Required'             ; break;
			case 403: $status = '403 Forbidden'                    ; break;
			case 404: $status = '404 Not Found'                    ; break;
			case 405: $status = '405 Method Not Allowed'           ; break;
			case 406: $status = '406 Not Acceptable'               ; break;
			case 407: $status = '407 Proxy Authentication Required'; break;
			case 408: $status = '408 Request Timeout'              ; break;
			case 409: $status = '409 Conflict'                     ; break;
			case 500: $status = '500 Internal Server Error'        ; break;
			case 501: $status = '501 NotImplemented'               ; break;
			case 502: $status = '502 BadGateway'                   ; break;
			case 503: $status = '503 ServiceUnavailable'           ; break;
			case 504: $status = '504 GatewayTimeout'               ; break;
			case 505: $status = '505 HTTPVersionNotSupported'      ; break;
			case 506: $status = '506 VariantAlsoNegotiates'        ; break;
			case 507: $status = '507 InsufficientStorage'          ; break;
			case 510: $status = '510 NotExtended'                  ; break;
			default:
				throw new Exception(sprintf('Unknown HTTP status code [HTTP/1.1 %s].', $params['status']));
				exit();
			break;
		}
		header('HTTP/1.1 ' . $status);
		
		if (isset($params['message'])) {
			$debugMessage = is_array($params['message']) ? $params['message'] : array($params['message']);
		}
		
		if (isset($params['debugHelpFile'])) {
			$debugHelpFile = $params['debugHelpFile'];
		}

		if (file_exists(CSR_ERRORS_DIR . 'template.php')) {
			require CSR_ERRORS_DIR . 'template.php';
		}
		
		exit();
	}

	/*===============================================================*/
	/*                                                               */
	/* Utilities                                                     */
	/*	                                                             */
	/*===============================================================*/
	/**
	 * Define constant
	 * 
	 * @access public
	 * @param string define name
	 * @param string [optional] define value
	 * @return mixed defined string or null
	 */
	public static function define($defineName, $value = null) {
		if (!defined($defineName) && !is_null($value)) define($defineName, $value);
		return defined($defineName)? constant($defineName): null;
	}
	
	/**
	 * モジュールインポートメソッド
	 * 
	 * CSR_MODULES_DIR に配置してあるモジュールをインポートする。
	 * 指定の方法は、
	 * CSR_MODULES_DIR/a/b/c/Foo.php
	 * というモジュールに対して
	 * a.b.c.Foo
	 * もしくは
	 * a.b.c.*
	 * という方法で指定する。
	 * 最後を * で指定した場合、そのディレクトリに含まれる全てのファイルを取得する。
	 * サブディレクトリは検索しない
	 * 
	 * ToDo
	 * モジュールが読み込めなかったとき、原因がわかりにくい
	 * 
	 * Usage
	 * <code>
	 * self::module('package.module.name');
	 * self::module('package.module.*');
	 * </code>
	 * 
	 * @param string package name
	 * @return void
	 */
	public static function module() {
		$packages = func_get_args();
		
		foreach ($packages as $package) {
			$path = str_replace('.', DS, $package);
			if (basename($path) !== '*') {
				include_once CSR_MODULES_DIR . $path . '.php';
				continue;
			}

			$path = dirname($path) . DS;
			if (!is_dir(CSR_MODULES_DIR . $path)) {
				trigger_error(sprintf('Is not directory. [%s]', CSR_MODULES_DIR . $path), E_USER_WARNING);
				continue;
			}

			if (!($dir = opendir(CSR_MODULES_DIR . $path))) {
				trigger_error(sprintf('Could not open the directory. [%s]', CSR_MODULES_DIR . $path), E_USER_WARNING);
				continue;
			}

			while(($entry = readdir($dir)) !== false) {
				if ($entry{0} === '.') continue;

				$info = pathinfo(CSR_MODULES_DIR . $path . $entry);
				if (is_dir(CSR_MODULES_DIR . $path . $entry)) {
					$subPackage = substr($package, 0, -1) . $info['filename'] . '.*';
				} else {
					$subPackage = substr($package, 0, -1) . $info['filename'];
				}
				$result[] = self::module($subPackage);
			}
			closedir($dir);
		}
	}
	
	public static function plugin() {
		$plugins = func_get_args();
		
		foreach ($plugins as $plugin) {
			$pluginDirPath = CSR_PLUGINS_DIR . $plugin;
			
			// Error...
			if (!is_dir($pluginDirPath)) {
				trigger_error(sprintf('Could not find such plugin. [%s]', $plugin));
				continue;
			}
			
			$pluginEntryFilePath = $pluginDirPath . DS . 'entry.php';
			
			// Error...
			if (!file_exists($pluginEntryFilePath)) {
				trigger_error(sprintf('Could not find plugin entry file. [%s/entry.php]', $plugin));
				continue;
			}
			
			require_once $pluginEntryFilePath;
		}
	}
}

require_once 'CSR_Config.php';
require_once CSR_ERRORS_DIR . 'RequestURIParseException.php';
require_once CSR_ERRORS_DIR . 'MissingTargetFunctionException.php';