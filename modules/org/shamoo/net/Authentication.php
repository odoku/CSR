<?php
/**
 * Authentication.php
 * 
 * このファイルにはAuthenticationクラスに関する定義が記述されています。<br/>
 * このファイルを読み込むことによりAuthenticationクラスの使用が可能になります。
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
 * PHPでBasic認証、Digest認証機能を提供します。
 * 
 * このクラスを使用する事によって、DBに登録したユーザー情報を元に認証処理を作成する事が可能です。
 * 
 * @package    modules
 * @subpackage net
 * @author     Masashi Onogawa <m.onogawa@gmail.com>
 * @copyright  Copyright © 2009, shamoo.org
 * @license    shamoo.org License Ver. 1.0
 * @version    1.0
 * @access     public
 */
class Authentication {
	/**
	 * Basic認証を行います。
	 *
	 * 第2引数には、IDを引数としパスワード文字列を戻り値とする関数へのコールバックを指定して下さい。
	 * このメソッドはレスポンスヘッダーを吐き出します。
	 * レスポンスボディが出力された後に実行した場合、エラーが出力される事に注意して下さい。
	 *
	 * <code>
	 * 
	 * function getPassword($account) {
	 *     if (strcmp('hoge', $account) === 0) {
	 *         return 'password';
	 *     }
	 *     return false;
	 * }
	 * 
	 * $realm = 'Please input your account name.';
	 * $callback = 'getPassword';
	 * 
	 * if (Authentication::basic($realm, $callback)) {
	 *     echo 'ok.':
	 * } else {
	 *     echo 'failed.';
	 * }
	 * 
	 * </code>
	 * 
	 * @access     public
	 * @static
	 * @return     true 認証に成功した場合はtrueを、失敗した場合はfalseを返します。
	 * @param      string $realm realm文字列。
	 * @param      callback $callback パスワード取得関数へのコールバック 
	 */
	function basic($realm, $callback) {
		if (
			!array_key_exists('PHP_AUTH_USER', $_SERVER) ||
			!($password = call_user_func_array($callback, array($_SERVER['PHP_AUTH_USER']))) ||
			strcmp($_SERVER['PHP_AUTH_PW'], $password) !== 0
		) {
			header(sprintf('WWW-Authenticate: Basic realm="%s"', $realm));
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * Digest認証を行います。
	 *
	 * 第2引数には、IDを引数としパスワード文字列を戻り値とする関数へのコールバックを指定して下さい。
	 * このメソッドはレスポンスヘッダーを吐き出します。
	 * レスポンスボディが出力された後に実行した場合、エラーが出力される事に注意して下さい。
	 *
	 * PHPがApacheのモジュールとして動作していない場合、このメソッドはは正常に動作しません。
	 *
	 * <code>
	 * 
	 * function getPassword($account) {
	 *     if (strcmp('hoge', $account) === 0) {
	 *         return 'password';
	 *     }
	 *     return false;
	 * }
	 * 
	 * $realm = 'Please input your account name.';
	 * $callback = 'getPassword';
	 * 
	 * if (Authentication::digest($realm, $callback)) {
	 *     echo 'ok.':
	 * } else {
	 *     echo 'failed.';
	 * }
	 * 
	 * </code>
	 * 
	 * @access     public
	 * @static
	 * @return     true 認証に成功した場合はtrueを、失敗した場合はfalseを返します。
	 * @param      string $realm realm文字列。
	 * @param      callback $callback パスワード取得関数へのコールバック 
	 */
	function digest($realm, $callback) {
		$digest = Authentication::__getDigest();
		if(
			$digest === false ||
			!($password = call_user_func_array($callback, array($digest['Digest username']))) ||
			strcmp($digest['response'], Authentication::__generateDigestHash($digest, $realm, $password)) !== 0
		) {
			header(sprintf(
				'WWW-Authenticate: Digest realm="%s" ,qop="auth" ,nonce="%s", opaque="%s", algorithm="MD5"',
				$realm, uniqid(rand(), true), md5($realm)
			));
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * ブラウザから送信されたDigest情報を取得します。
	 *
	 * PHPがApacheのモジュールとして動作していない場合、このメソッドはは正常に動作しません。
	 *
	 * @access     private
	 * @static
	 * @return     array 認証に成功した場合はDigest情報が保持された配列を、失敗した場合はfalseを返します。
	 */
	function __getDigest() {
		$headers = apache_request_headers();
		if (!array_key_exists('Authorization', $headers)) return false;
		
		preg_match_all('/([\w ]+)="?([^",]*)"?/', $headers['Authorization'], $matchies);
		$digest = array();
		for ($i = 0; $i < count($matchies[0]); $i++) {
			$digest[trim($matchies[1][$i])] = trim($matchies[2][$i]);
		}
		return $digest;
	}
	
	/**
	 * Digestハッシュを生成します。
	 *
	 * @access     private
	 * @static
	 * @return     array 認証に成功した場合はDigest情報が保持された配列を、失敗した場合はfalseを返します。
	 * @param      array $digest ブラウザから送信されたDigest情報を保持した配列。
	 * @param      string $realm realm文字列。
	 * @param      string $password パスワード文字列。
	 */
	function __generateDigestHash($digest, $realm, $password) {
		$hash1 = md5(sprintf("%s:%s:%s", $digest['Digest username'], $realm, $password));
		$hash2 = md5(sprintf("%s:%s", $_SERVER['REQUEST_METHOD'], $digest['uri']));
		$hash2 = md5(sprintf(
			"%s:%s:%s:%s:%s:%s",
			$hash1,
			$digest['nonce' ],
			$digest['nc'    ],
			$digest['cnonce'],
			$digest['qop'   ],
			$hash2
		));
		
		return $hash2;
	}
}