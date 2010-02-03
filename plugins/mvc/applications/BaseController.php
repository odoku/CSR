<?php

CSR::module('org.shamoo.functions.Shortcuts');

class BaseController extends CSR_Controller {
	public function __construct() {
		// $this->addEvent(CSR_EVENT_BEFORE_ACTION, array($this, '_eventBeforeActionHandler'));
		// $this->addEvent(CSR_EVENT_AFTER_ACTION , array($this, '_eventAfterActionHandler' ));
		// $this->addEvent(CSR_EVENT_BEFORE_RENDER, array($this, '_eventBeforeRenderHandler'));
		// $this->addEvent(CSR_EVENT_AFTER_RENDER , array($this, '_eventAfterRenderHandler' ));
		// $this->addEvent(CSR_EVENT_BEFORE_OUTPUT, array($this, '_eventBeforeOutputHandler'));
		// $this->addEvent(CSR_EVENT_AFTER_OUTPUT , array($this, '_eventAfterOutputHandler' ));
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