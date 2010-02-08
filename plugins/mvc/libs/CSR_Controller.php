<?php

class CSR_Controller extends CSR_Event {
	/*===============================================================*/
	/* Event Constants                                               */
	/*===============================================================*/
	/**
	 * Constant before action event
	 */
	const BEFORE_ACTION = 'before_action';
	/**
	 * Constant after action event
	 */
	const AFTER_ACTION = 'after_action';
	/**
	 * Constant before render event
	 */
	const BEFORE_RENDER = 'before_render';
	/**
	 * Constant after render event
	 */
	const AFTER_RENDER = 'after_render';
	/**
	 * Constant before output event
	 */
	const BEFORE_OUTPUT = 'before_output';
	/**
	 * Constant after output event
	 */
	const AFTER_OUTPUT = 'after_output';


	/*===============================================================*/
	/* Instance Variables                                            */
	/*===============================================================*/
	protected $controller = null;
	protected $action = null;
	protected $layout = array();
	protected $_vars  = array();
	

	/*===============================================================*/
	/* Getter & Setter methods                                       */
	/*===============================================================*/
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
		$this->triggerEvent(self::BEFORE_ACTION);
		$result = call_user_func_array(array($this, $action), $arguments);
		$this->triggerEvent(self::BEFORE_ACTION);

		if ($result !== false) {
			/*=======================================================*/
			/* Render view.                                          */
			/*=======================================================*/
			$view = new CSR_View($this->controller, $this->action);
			$this->triggerEvent(self::BEFORE_RENDER);
			if (is_array($this->layout)) {
				$content = $view->render($this->layout, $this->_vars);
			}
			$this->triggerEvent(self::AFTER_RENDER, array(&$content));
			
			
			/*=======================================================*/
			/* Output content.                                       */
			/*=======================================================*/
			$this->triggerEvent(self::BEFORE_OUTPUT);
			echo $content;
			$this->triggerEvent(self::AFTER_OUTPUT);
		}
		
		return true;
	}
	
	public static function loadController($name) {
		$controllerName = ucwords($name);
		$controllerClassName = $controllerName . 'Controller';
		$controllerClassPath = CSR_MVC_CONTROLLERS_DIR . $controllerClassName . '.php';
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
		require CSR_MVC_MODELS_DIR . $modelPath . '.php';
	}
}
