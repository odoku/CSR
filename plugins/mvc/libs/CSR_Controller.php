<?php

class CSR_Controller extends CSR_Event {
	protected $controller = null;
	protected $action = null;
	protected $layout = array();
	protected $_vars  = array();
	
	protected function _setControllerName($name) {
		$this->controller = $name;
	}
	
	protected function &set($name, $var) {
		$this->_vars[$name] = &$var;
		return $var;
	}
	
	public function _executeAction($action, $arguments = array()) {
		if (!method_exists($this, $action)) {
			throw new UndefinedActionException();
			return false;
		}
		if ($action{0} === '_') {
			throw new PrivateActionException();
			return false;
		}
		$this->action = $action;
				
	
		/*===========================================================*/
		/* Action method.                                            */
		/*===========================================================*/
		$this->triggerEvent(CSR_EVENT_BEFORE_ACTION);
		$result = call_user_func_array(array($this, $action), $arguments);
		$this->triggerEvent(CSR_EVENT_BEFORE_ACTION);

		if ($result !== false) {
			/*=======================================================*/
			/* Render view.                                          */
			/*=======================================================*/
			$view = new CSR_View($this->controller, $this->action);
			$this->triggerEvent(CSR_EVENT_BEFORE_RENDER);
			if (is_array($this->layout)) {
				$content = $view->render($this->layout, $this->_vars);
			}
			$this->triggerEvent(CSR_EVENT_AFTER_RENDER, array(&$content));
			
			
			/*=======================================================*/
			/* Output content.                                       */
			/*=======================================================*/
			$this->triggerEvent(CSR_EVENT_BEFORE_OUTPUT);
			echo $content;
			$this->triggerEvent(CSR_EVENT_AFTER_OUTPUT);
		}
		
		return true;
	}
	
	public static function loadController($name) {
		$controllerName = ucwords($name);
		$controllerClassName = $controllerName . 'Controller';
		$controllerClassPath = CSR_CONTROLLERS_DIR . $controllerClassName . '.php';
		if (!file_exists($controllerClassPath)) {
			throw new MissingControllerException();
			return false;
		}
		
		require_once $controllerClassPath;
		$controller = new $controllerClassName();
		$controller->_setControllerName($controllerName);
		return $controller;
	}
	
	// モデルを読み込む
	protected function useModel($modelPath) {
		require CSR_MODELS_DIR . $modelPath . '.php';
	}
}
