<?php

CSR::module('org.shamoo.functions.Shortcuts');

class BaseController extends CSR_Controller {
	public function __construct() {
		// $this->addEvent(self::BEFORE_ACTION, array($this, '_eventBeforeActionHandler'));
		// $this->addEvent(self::AFTER_ACTION , array($this, '_eventAfterActionHandler' ));
		// $this->addEvent(self::BEFORE_RENDER, array($this, '_eventBeforeRenderHandler'));
		// $this->addEvent(self::AFTER_RENDER , array($this, '_eventAfterRenderHandler' ));
		// $this->addEvent(self::BEFORE_OUTPUT, array($this, '_eventBeforeOutputHandler'));
		// $this->addEvent(self::AFTER_OUTPUT , array($this, '_eventAfterOutputHandler' ));
	}

	protected function _eventBeforeActionHandler() {
		echo 'Before Action!' . PHP_EOL;
	}

	protected function _eventAfterActionHandler() {
		echo 'After Action!' . PHP_EOL;
	}
	
	public function _eventBeforeRenderHandler() {
		echo 'Before Render!' . PHP_EOL;
	}
	
	public function _eventAfterRenderHandler($content) {
		echo 'After Render!' . PHP_EOL;
	}
	
	public function _eventBeforeOutputHandler() {
		echo 'Before Output!' . PHP_EOL;
	}
	
	public function _eventAfterOutputHandler() {
		echo 'After Output!' . PHP_EOL;
	}
}