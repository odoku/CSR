<?php

class CSR_Dispatcher {
	/**
	 * @var array
	 * @access private
	 */
	private $_routes = array();
	/**
	 * @var array 
	 * @access private
	 */
	private $_parsed = array();
	/**
	 * @var string
	 * @access private
	 */
	private $_request = '';
	
	/**
	 * Construct
	 * 
	 * @access public
	 * @param array [optional] routes
	 */
	public function __construct($routes = array()) {
		$this->_routes = $routes;
	}

	/**
	 * Push route method
	 * 
	 * @access public
	 * @param string routing string
	 * @param string target string
	 * @return int routes length
	 */
	public function push($route, $target) {
		if (array_key_exists($route, $this->_routes)) unset($this->_routes[$route]);
		$this->_routes[$route] = $target;
		return count($this->_routes);
	}
	/**
	 * Set route method
	 * 
	 * @access public
	 * @param array routes
	 * @return void
	 */
	public function setRoute($routes) {
		$this->_routes = $routes;
	}
	
	public function getRoutes() {
		return $this->_routes;
	}
	/**
	 * Get parsed parameters
	 * 
	 * Usage
	 * <code>
	 * $dispatcher = &new CSR_Dispatcher(array(
	 * 	'/foo/bar/:num/:num/([a-zA-Z])/(.?.ml)/:any@html' => 'foobar',
	 * 	'/' => '/index.php'
	 * ));
	 * $dispatcher->parseRequest('/csr1.0/foo/bar/123/4567/c/html/some_file_name.html?item=1255&color=0xf6d4e9');
	 * var_dump($dispatcher->getParsedParams());
	 * // array(7) {
	 * //   ["getVars"]=>
	 * //   array(2) {
	 * //     ["item"]=>
	 * //     string(4) "1255"
	 * //     ["color"]=>
	 * //     string(8) "0xf6d4e9"
	 * //   }
	 * //   ["matchedRequestRegex"]=>
	 * //   string(68) "/foo/bar/([0-9]+)/([0-9]+)/([a-zA-Z])/(.?.ml)/([A-Za-z0-9_-]+)\.html"
	 * //   ["matchedRoute"]=>
	 * //   array(1) {
	 * //     ["/foo/bar/:num/:num/([a-zA-Z])/(.?.ml)/:any@html"]=>
	 * //     string(6) "foobar"
	 * //   }
	 * //   ["arguments"]=>
	 * //   array(3) {
	 * //     [0]=>
	 * //     string(3) "123"
	 * //     [1]=>
	 * //     string(4) "4567"
	 * //     [2]=>
	 * //     string(14) "some_file_name"
	 * //   }
	 * //   ["captured"]=>
	 * //   array(3) {
	 * //     [0]=>
	 * //     string(44) "/foo/bar/123/4567/c/html/some_file_name.html"
	 * //     [1]=>
	 * //     string(1) "c"
	 * //     [2]=>
	 * //     string(4) "html"
	 * //   }
	 * //   ["target"]=>
	 * //   string(6) "foobar"
	 * //   ["extension"]=>
	 * //   string(4) "html"
	 * // }
	 * </code>
	 * 
	 * @access public
	 * @return array GET parameters
	 */
	public function getParsedParams() {
		return $this->_parsed;
	}
	/**
	 * Get GET request parameters
	 * 
	 * Usage
	 * <code>
	 * $dispatcher = &new CSR_Dispatcher(array(
	 * 	'/foo/bar/:num/:num/([a-zA-Z])/(.?.ml)/:any@html' => 'foobar',
	 * 	'/' => '/index.php'
	 * ));
	 * $dispatcher->parseRequest('/foo/bar/123/4567/c/html/some_file_name.html?item=1255&color=0xf6d4e9');
	 * var_dump($dispatcher->getGetParams());
	 * // array(2) {
	 * //   ["item"]=>
	 * //   string(4) "1255"
	 * //   ["color"]=>
	 * //   string(8) "0xf6d4e9"
	 * // }
	 * </code>
	 * 
	 * @access public
	 * @return array GET parameters
	 */
	public function getGetParams() {
		return $this->_parsed['getVars'];
	}
	/**
	 * Get arguments
	 * 
	 * Usage
	 * <code>
	 * $dispatcher = &new CSR_Dispatcher(array(
	 * 	'/foo/bar/:num/:num/([a-zA-Z])/(.?.ml)/:any@html' => 'foobar',
	 * 	'/' => '/index.php'
	 * ));
	 * $dispatcher->parseRequest('/foo/bar/123/4567/c/html/some_file_name.html?item=1255&color=0xf6d4e9');
	 * var_dump($dispatcher->getArguments());
	 * // array(3) {
	 * //   [0]=>
	 * //   string(3) "123"
	 * //   [1]=>
	 * //   string(4) "4567"
	 * //   [2]=>
	 * //   string(14) "some_file_name"
	 * // }
	 * </code>
	 * 
	 * @access public
	 * @return array arguments
	 */
	public function getArguments() {
		return $this->_parsed['arguments'];
	}

	public function getRequest() {
		return $this->_parsed['request'];
	}

	public function getMatchedRoute() {
		return $this->_parsed['matchedRoute'];
	}

	/**
	 * Get captures
	 * 
	 * Usage
	 * <code>
	 * $dispatcher = &new CSR_Dispatcher(array(
	 * 	'/foo/bar/:num/:num/([a-zA-Z])/(.?.ml)/:any@html' => 'foobar',
	 * 	'/' => '/index.php'
	 * ));
	 * $dispatcher->parseRequest('/csr1.0/foo/bar/123/4567/c/html/some_file_name.html?item=1255&color=0xf6d4e9');
	 * var_dump($dispatcher->getCaptures());
	 * // array(3) {
	 * //   [0]=>
	 * //   string(44) "/foo/bar/123/4567/c/html/some_file_name.html"
	 * //   [1]=>
	 * //   string(1) "c"
	 * //   [2]=>
	 * //   string(4) "html"
	 * // }
	 * </code>
	 * 
	 * @access public
	 * @return array captured values
	 */
	function getCaptures() {
		return $this->_parsed['captured'];
	}
	/**
	 * Get target
	 * 
	 * Usage
	 * <code>
	 * $dispatcher = &new CSR_Dispatcher(array(
	 * 	'/foo/bar/:num/:num/([a-zA-Z])/(.?.ml)/:any@html' => 'foobar',
	 * 	'/' => '/index.php'
	 * ));
	 * $dispatcher->parseRequest('/csr1.0/foo/bar/123/4567/c/html/some_file_name.html?item=1255&color=0xf6d4e9');
	 * var_dump($dispatcher->getTarget());
	 * // string(6) "foobar"
	 * </code>
	 * 
	 * @access public
	 * @return mixed parsed target
	 */
	public function getTarget() {
		return $this->_parsed['target'];
	}
	/**
	 * Get extension
	 * 
	 * Usage
	 * <code>
	 * $dispatcher = &new CSR_Dispatcher(array(
	 * 	'/foo/bar/:num/:num/([a-zA-Z])/(.?.ml)/:any@html' => 'foobar',
	 * 	'/' => '/index.php'
	 * ));
	 * $dispatcher->parseRequest('/csr1.0/foo/bar/123/4567/c/html/some_file_name.html?item=1255&color=0xf6d4e9');
	 * var_dump($dispatcher->getExtension());
	 * // string(4) "html"
	 * </code>
	 * 
	 * @access public
	 * @return string extension
	 */
	public function getExtension() {
		return $this->_parsed['extension'];
	}
	
	/**
	 * Parse request method
	 * 
	 * request から routes を走査し、
	 * 適合した route を返す。
	 * 
	 * @access public
	 * @param string request uri
	 * @return array parsed params
	 */
	public function parseRequestURI($request) {
		$request = $this->_requestOptimize($request);

		$parsed = array();
		$parsedGetVars = $this->_parseGetVars($request);
		$parsed['request'] = $parsedGetVars['request'];
		$parsed['getVars'] = $parsedGetVars['getVars'];

		foreach ($this->_routes as $path => $target) {
			/*
			 * Extension
			 */
			$original = $path;
			if (strpos($path, '@') !== false) {
				list($path, $extension) = explode('@', $path);
				$extension = '\.' . $extension;
			} else {
				$extension = '';
			}
			$regex = $path;
			
			/*
			 * Arguments
			 */
			$regex = $this->_wildToRegex(':num', $regex, '[0-9]+');
			$regex = $this->_wildToRegex(':any', $regex, '[^/]+');
			/*
			 * Default
			 */
			// $regex = str_replace(':controller', '(?P<controller>[a-zA-Z][a-zA-Z0-9_-]+)', $regex);
			// $regex = str_replace(':action', '(?P<action>[a-zA-Z_][a-zA-Z0-9_-]+)', $regex);
			/*
			 * Match!
			 */
			$matches = array();
			if (preg_match('#^' . $regex . $extension . '$#', $parsed['request'], $matches)) {
				$parsed['matchedRequestRegex'] = $regex . $extension;
				$parsed['matchedRoute'       ] = array($original => $target);
				
				// $capturesArray = array();
				$argumentsArray = array();
				// $nextIsPair = false;
				while ($match = each($matches)) {
					if (!is_int($match['key'])) {
						// $nextIsPair = true;
						// // Controller
						// if ($match['key'] === 'controller') {
						// 	$target = str_replace(':controller', $match['value'], $target);
						// 	continue;
						// }
						// // Action
						// if ($match['key'] === 'action') {
						// 	$target = str_replace(':action', $match['value'], $target);
						// 	continue;
						// }

						// Arguments
						if (stripos($match['key'], 'arg') !== false) {
							$argumentsArray[] = urldecode($match['value']);						
							continue;
						}
					}
					// // Regex captures
					// if (!$nextIsPair) {
					// 	$capturesArray[] = $match['value'];
					// 	$nextIsPair = false;
					// }
				}
				// Fix controller|action $x
				// foreach ($capturesArray as $index => $r) {
				// 	$target = str_replace('$' . $index, $r, $target);
				// }
				
				$parsed['target'] = $target;
				// $parsed['captured'] = $capturesArray;
				$parsed['arguments'] = $argumentsArray;
				$parsed['extension'] = ltrim((strpos($matches[0], '.') === false)? null: substr($matches[0], strpos($matches[0], '.')), '.');

				$this->_parsed = $parsed;

				return $this->_parsed;
			}
		}
	}
	
	/**
	 * Request optimize method
	 * 
	 * @access private
	 * @param string reuest strings
	 * @return string optimized request strings
	 */
	private function _requestOptimize($request) {
		/*
		 * Delete basedir span from request
		 */
		$baseDir = dirname($_SERVER['SCRIPT_NAME']);
		if (!empty($baseDir)) {
			$request = substr_replace($request, '', strpos($request, $baseDir), strlen($baseDir));
		}
		/*
		 * Request first character must be "/"
		 */
		if ($request{0} !== '/') {
			$request = '/' . $request;
		}
		
		return $request;
	}
	/**
	 * Divide parameter and request from request
	 * 
	 * @access private
	 * @param string get variables include rquest
	 * @return array devided request
	 */
	private function _parseGetVars($request) {
		$getVars = array();
		if (strpos($request, '?') !== false || strpos($request, '&') !== false) {
			$requests = explode('?', $request);
			$request = $requests[0];
			
			if (strlen($requests[1]) !== 0) {
				$getParams = explode('&', $requests[1]);
				foreach ($getParams as $var) {
					list($name, $val) = explode('=', $var);
					$getVars[$name] = $val;
				}
			}
		}
		
		return array(
			'request' => $request,
			'getVars' => $getVars
		);
	}
	
	private function _wildToRegex($wild, $subject, $replaceRegExp) {
		static $i = 0;
		if (strpos($subject, $wild) !== false) {
			$subject = substr_replace($subject, sprintf('(?P<arg%s>%s)', $i++, $replaceRegExp), strpos($subject, $wild), strlen($wild));
			$subject = $this->_wildToRegex($wild, $subject, $replaceRegExp);
		}
		return $subject;
	}
}
