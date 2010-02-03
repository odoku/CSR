<?php
/**
 * Session.php
 * 
 * このファイルにはSessionクラスに関する定義が記述されています。<br/>
 * このファイルを読み込むことによりSessionクラスの使用が可能になります。
 * 
 * @package    modules
 * @subpackage net
 * @author     Masashi Onogawa <m.onogawa@gmail.com>
 * @copyright  Copyright © 2009, shamoo.org
 * @license    shamoo.org License Ver. 1.0
 * @version    1.0
 * @access     public
 */

/**
 * Security Salt
 * セキュリティ対策の為、任意の文字列を指定して下さい。
 */
define('SESSION_SALT', 'omochi');

/**
 * セッションを操作するためのクラスです。
 * 
 * @package    modules
 * @subpackage net
 * @author     Masashi Onogawa <m.onogawa@gmail.com>
 * @copyright  Copyright © 2009, shamoo.org
 * @license    shamoo.org License Ver. 1.0
 * @version    1.0
 * @access     public
 */
class Session {	
	/**
	 * セッションが開始されているかどうかを判断します。
	 * 
	 * <code>
	 * 
	 * if (Session::isStart()) {
	 *     echo 'Yes.';
	 * } else {
	 *     echo 'No.';
	 * }
	 * 
	 * </code>
	 * 
	 * @access     public
	 * @static
	 * @return     開始されている場合はtrueを、されていない場合はfalseを返す。
	 */
	function isStart() {
		return strlen(session_id()) !== 0;
	}

	function name() {
		$args = func_get_args();
		return call_user_func_array('session_name', $args);
	}
	
	/**
	 * セッションを開始します。
	 *
	 * 引数にセッションクッキーのパラメーターを指定する事で
	 * クッキーの有効期限、有効ドメイン等を決められます。
	 * 詳しくはサンプルを参照、またはsession_set_cookie_params関数の
	 * マニュアルを参照して下さい。
	 * 
	 * <code>
	 * 
	 * $params = array(
	 *     'lifetime' => 3600          ,
	 *     'path'     => '/test'       ,
	 *     'domain'   => 'sample.local',
	 *     'secure'   => false
	 * );
	 * Session::start($params);
	 * 
	 * </code>
	 * 
	 * @access     public
	 * @static
	 * @return     開始されている場合はtrueを、されていない場合はfalseを返す。
	 * @param      array[optional] $p セッションクッキーのパラメーターを保持した配列。
	 */
	function start($p = null) {
		if ($p !== null && is_array($p)) {
			$cp       = session_get_cookie_params();
			$lifetime = (isset($p['lifetime']) && !is_null($p['lifetime'])) ?  (int)$p['lifetime'] :  (int)$cp['lifetime'];
			$path     = (isset($p['path'    ]) && !is_null($p['path'    ])) ?       $p['path'    ] :       $cp['path'    ];
			$domain   = (isset($p['domain'  ]) && !is_null($p['domain'  ])) ?       $p['domain'  ] :       $cp['domain'  ];
			$secure   = (isset($p['secure'  ]) && !is_null($p['secure'  ])) ? (bool)$p['secure'  ] : (bool)$cp['secure'  ];
			// $httponly = (isset($p['httponly']) && $p['httponly'] !== null)? (bool) $p['httopnly']: (bool) $cp['httponly'];
			// call_user_func_array('session_set_cookie_params', array($lifetime, $path, $domain, $secure, $httponly));
			call_user_func_array('session_set_cookie_params', array($lifetime, $path, $domain, $secure));
		}
		
		if (is_string($p)) session_name($p);
		
		return session_start();
	}
	
	/**
	 * セッションをIDを再生成します。
	 *
	 * このメソッドはsession_regenerate_id関数をラップしています。
	 * 詳しくはPHPマニュアルを参照して下さい。
	 * 
	 * <code>
	 * 
	 * $params = array(
	 *     'lifetime' => 3600          ,
	 *     'path'     => '/test'       ,
	 *     'domain'   => 'sample.local',
	 *     'secure'   => false
	 * );
	 * Session::start($params);
	 *
	 * Session::restart();
	 * 
	 * </code>
	 * 
	 * @access     public
	 * @static
	 * @return     成功した場合に TRUE を、失敗した場合に FALSE を返します。
	 * @param bool 関連付けられた古いセッションを削除するかどうか。 デフォルトは FALSE です。
	 */
	
	function restart($deleteOldSession = false) {
		if ($deleteOldSession) {
			session_unset();
			$oldID  = session_id();
			$result = session_regenerate_id();
			unlink(session_save_path() . 'sess_' . $oldID);
		} else {
			$result = session_regenerate_id();
		}
		
		return $result;
	}
	
	/**
	 * セッションを完全に破棄します
	 * 
	 * サーバー側のセッション、およびクライアントに
	 * 保存されているクッキーの期限を強制的に切らせて
	 * セッション情報を完全に破棄します。
	 * 
	 * <code>
	 * 
	 * Session::start();
	 * Session::destroy();
	 * 
	 * </code>
	 * 
	 * @access     public
	 * @static
	 * @return     破棄に成功した場合はtrueを、失敗した場合はfalseを返す。
	 */
	function destroy() {
		if (Session::isStart()) {
			$_SESSION = array();
			if (isset($_COOKIE[session_name()]))
			    setcookie(session_name(), '', time() - 42000, '/');
			session_destroy();
			return true;
		} else {
			trigger_error('The session was not starting.', E_USER_WARNING);
			return false;
		}
	}
	
	/**
	 * 現在のセッションIDを取得します
	 * 
	 * <code>
	 * 
	 * Session::start();
	 * 
	 * echo Session::sid();
	 * 
	 * </code>
	 *
	 * @access     public
	 * @static
	 * @return     string 取得に成功した場合はIDを返し、失敗した場合はfalseを返す
	 */
	function sid() {
		$sid = session_id();
		if ($sid !== '') {
			return $sid;
		} else {
			trigger_error('The session was not starting.', E_USER_WARNING);
			return false;
		}
	}
	
	/**
	 * セッション名を取得します
	 * 
	 * <code>
	 * 
	 * Session::start();
	 * 
	 * echo Session::getSessionName();
	 * 
	 * </code>
	 *
	 * @access     public
	 * @static
	 * @return     string 取得に成功した場合はセッション名を返し、失敗した場合はfalseを返す
	 */
	function getSessionName() {
		if (Session::isStart()) {
			return session_name();
		} else {
			trigger_error('The session was not starting.', E_USER_WARNING);
			return falsewcccccccccs;
		}
	}
	
	/**
	 * セッションに値をセットします
	 * 
	 * <code>
	 * 
	 * Session::start();
	 * 
	 * Session::set('hoge', 100);
	 * 
	 * </code>
	 *
	 * @access     public
	 * @static
	 * @return     mixed セットする値を返します。
	 * @param      string $key 値に対するキー名
	 * @param      mixed $value セットする値
	 */
	function set($key, $value) {
		if (Session::isStart()) {
			$_SESSION[$key] = $value;
		} else {
			trigger_error('The session was not starting.', E_USER_WARNING);
		}
		return $value;
	}
	
	/**
	 * セッションにセットされている値を取得します
	 * 
	 * <code>
	 * 
	 * Session::start();
	 * Session::set('hoge', 100);
	 * 
	 * echo Session::get('hoge');
	 * 
	 * </code>
	 *
	 * @access     public
	 * @static
	 * @return     mixed 取得出来た場合はその値が返され、キー名が存在しない場合、セッションが開始されていない場合はnullを返す
	 * @param      string $key 取得する値のキー名
	 */
	function get($key) {
		if (Session::isStart()) {
			if (isset($_SESSION[$key])) return $_SESSION[$key];
			else {
				return null;
			}
		} else {
			trigger_error('The session was not starting.', E_USER_WARNING);
		}
		return null;
	}

	/**
	 * セッションにセットされている値を削除します
	 * 
	 * <code>
	 * 
	 * Session::start();
	 * Session::set('hoge', 100);
	 * 
	 * Session::delete('hoge');
	 * 
	 * </code>
	 *
	 * @access     public
	 * @static
	 * @param      string $key 削除する値のキー名
	 */
	function delete($key) {
		if (Session::isStart()) {
			if (isset($_SESSION[$key])) {
				unset($_SESSION[$key]);
			}
		} else {
			trigger_error('The session was not starting.', E_USER_WARNING);
		}
	}
	
	/**
	 * セッションにセットされている値を取得し、削除します
	 * 
	 * <code>
	 * 
	 * Session::start();
	 * Session::set('hoge', 100);
	 * 
	 * echo Session::extract('hoge');
	 * 
	 * </code>
	 *
	 * @access     public
	 * @static
	 * @return     mixed 取得出来た場合はその値が返され、キー名が存在しない場合、セッションが開始されていない場合はnullを返す
	 * @param      string $key 削除する値のキー名
	 */
	function extract($key) {
		if (Session::isStart()) {
			if (isset($_SESSION[$key])) {
				$value = $_SESSION[$key];
				unset($_SESSION[$key]);
				return $value;
			}
			else {
				return null;
			}
		} else {
			trigger_error('The session was not starting.', E_USER_WARNING);
		}
		return null;
	}

	
	/**
	 * セッションに指定したキー名を持つ値があるかどうかを判断します
	 * 
	 * <code>
	 * 
	 * Session::start();
	 * Session::set('hoge', 100);
	 * 
	 * $result = Session::exists('hoge');
	 * 
	 * var_dump($result);
	 * 
	 * </code>
	 *
	 * @access     public
	 * @static
	 * @return     bool セッションにキーが存在する場合はtrueを返し、存在しない場合はfalseを返す
	 * @param      string $key 判断する値のキー名
	 */
	function exists($key) {
		if (Session::isStart()) {
			return array_key_exists($key, $_SESSION);
		} else {
			trigger_error('The session was not starting.', E_USER_WARNING);
			return false;
		}
	}
	
	/**
	 * セッションに保持されている値をダンプします
	 * 
	 * <code>
	 * 
	 * Session::start();
	 * Session::set('hoge', 100);
	 * Session::set('hogehoge', 10000);
	 * 
	 * Session::dump();
	 * 
	 * </code>
	 * 
	 * @access     public
	 * @static
	 */
	function dump() {
		if (Session::isStart()) {
			var_dump($_SESSION);
		} else {
			trigger_error('The session was not starting.', E_USER_WARNING);
		}
	}
	
	/**
	 * ワンタイムチケットを発行します
	 * 
	 * チケットは発行されると同時にセッションに保持されます。
	 * 戻り値として返されるチケットは暗号化を行ったチケットです。
	 * verifyOneTimeTicketメソッドを実行、またはセッションを破棄しない限り、
	 * チケットはセッションに残り続けるので注意して下さい。
	 * 
	 * <code>
	 * 
	 * Session::start();
	 * 
	 * $ticket = Session::generateOneTimeTicket('hoge', 100);
	 * 
	 * </code>
	 *
	 * @access     public
	 * @static
	 * @return     string ワンタイムチケットの文字列を返します。セッションが開始されていない場合はfalseを返します。
	 */
	function generateOneTimeTicket() {
		if (Session::isStart()) {
			$ticket = sha1(array_sum(explode(" ", microtime())));
			$_SESSION['__cs_one_time_ticket'] = $ticket;
			return sha1(SESSION_SALT. $ticket);			
		} else {
			trigger_error('The session was not starting.', E_USER_WARNING);
			return false;
		}
	}
	
	/**
	 * ワンタイムチケットを評価します
	 * 
	 * 渡されたワンタイムチケットが正当なものか判断します。
	 * 判断後、セッションに保持されているチケットは削除されます。
	 * 
	 * <code>
	 * 
	 * Session::start();
	 * 
	 * $ticket = Session::generateOneTimeTicket('hoge', 100);
	 * 
	 * </code>
	 * 
	 * @access     public
	 * @static
	 * @return     チケットが正しいものであればtrueを返し、不正なもの、またはチケットが発行されていなければfalseを返す。
	 * @param      string $token 評価を行うチケット
	 */
	function verifyOneTimeTicket($token) {
		if (Session::isStart()) {
			if (isset($_SESSION['__cs_one_time_ticket'])) {
				$ticket = $_SESSION['__cs_one_time_ticket'];
				unset($_SESSION['__cs_one_time_ticket']);
				return sha1(SESSION_SALT. $ticket) === $token;
			}
		} else {
			trigger_error('The session was not starting.', E_USER_WARNING);
		}
		
		return false;
	}
}