<?php

$_timer = new Timer();

$_csr = CSR::getInstance();
$_csr->addEvent(CSR::APPLICATION_START, array($_timer, 'start' ));
$_csr->addEvent(CSR::APPLICATION_END  , array($_timer, 'elapse'));

class Timer {
	private $_startTime   = 0;
	
	public function start() {
		list($usec, $sec) = explode(" ", microtime());
		$this->_startTime = ((float)$usec + (float)$sec);		
	}

	public function elapse() {
		list($usec, $sec) = explode(" ", microtime());
		$elapse = ((float)$usec + (float)$sec);
		
		echo sprintf('Execute time : [%s]', $elapse - $this->_startTime) . PHP_EOL;
	}
}
