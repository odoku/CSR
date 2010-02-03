<?php

class CSR_Event {
	/**
	 * Event collection
	 * @var array
	 * @access protected
	 */
	protected $_events = array();
	
	/**
	 * Event add method
	 * 
	 * Usage
	 * <code>
	 * function handler1() {
	 * 	echo "run!";
	 * }
	 * function handler2() {
	 * 	echo "walk";
	 * }
	 * $event = &new CSR_EventObject();
	 * $event->addEvent('event_name', 'handler1');
	 * $event->triggerEvent('event_name');// run!
	 * $event->addEvent('event_name', 'handler2');
	 * $event->triggerEvent('event_name');// run!walk
	 * </code>
	 * 
	 * @param string event name
	 * @param mixed event handler
	 * @return void
	*/
	public function addEvent($event, $handler) {
		if (!isset($this->_events[$event])) $this->_events[$event] = array();
		$this->_events[$event][] = $handler;
	}
	
	/**
	 * Event remove method
	 * 
	 * Usage
	 * <code>
	 * function handler1() {
	 * 	echo "run!";
	 * }
	 * $event = &new CSR_EventObject();
	 * $event->addEvent('event_name', 'handler1');
	 * $event->triggerEvent('event_name');// run!
	 * $event->removeEvent('event_name', 'handler1');
	 * $event->triggerEvent('event_name');// (nothing to output)
	 * </code>
	 * 
	 * @param string event name
	 * @param mixed event handler
	 * @return void
	 */
	public function removeEvent($event, $handler) {
		if (isset($this->_events[$event])) {
			foreach ($this->_events[$event] as $index => $e) {
				if ($handler == $e) unset($this->_events[$event][$index]);
			}
		}
	}
	
	/**
	 * Events remove method
	 * 
	 * A method to delete all the target events.
	 * 
	 * Usage
	 * <code>
	 * function handler1() {
	 * 	echo "run!";
	 * }
	 * $event = &new CSR_EventObject();
	 * $event->addEvent('event_name', 'handler1');
	 * $event->addEvent('event_name', 'handler1');
	 * $event->addEvent('event_name', 'handler1');
	 * $event->triggerEvent('event_name');// run!run!run!
	 * $event->removeEvents('event_name');
	 * $event->triggerEvent('event_name');// (nothing to output)
	 * </code>
	 * 
	 * @param string event name
	 * @return void
	 */
	public function removeEvents($event) {
		unset($this->_events[$event]);
	}
	
	/**
	 * Execute event
	 * 
	 * Usage
	 * <code>
	 * function handler1() {
	 * 	echo "run!";
	 * }
	 * $event = &new CSR_EventObject();
	 * $event->addEvent('event_name', 'handler1');
	 * $event->triggerEvent('event_name');// run!
	 * </code>
	 * 
	 * @param string event name
	 * @param array parameters
	 * @return array event result
	 */
	public function triggerEvent($event, $params = array()) {
		if (array_key_exists($event, $this->_events)) {
			$args = func_get_args();

			$results = array();
			foreach ($this->_events[$event] as $handler) {
				$results[] = call_user_func_array($handler, $params);
			}
			return $results;
		}
		
		return false;
	}
}