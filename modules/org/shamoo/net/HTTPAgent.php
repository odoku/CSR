<?php
/**
 * HTTPAgent.php
 * 
 * このファイルにはHTTPAgentクラスに関する定義が記述されています。<br/>
 * このファイルを読み込むことによりHTTPAgentクラスの使用が可能になります。
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
 * レスポンスヘッダーを操作するクラスです。
 * 
 * @package    modules
 * @subpackage net
 * @author     Masashi Onogawa <m.onogawa@gmail.com>
 * @copyright  Copyright © 2009, shamoo.org
 * @license    shamoo.org License Ver. 1.0
 * @version    1.0
 * @access     public
 */
class HTTPAgent {
	/**
	 * リダイレクトヘッダーをレスポンスとして返します
	 * 
	 * 第一引数で指定したURLへリダイレクトを行うヘッダー情報
	 * をレスポンスとして返します。
	 * 
	 * 第二引数はヘッダー情報を送信した後、スクリプトの処理を
	 * 継続するかどうかを指定します。
	 * デフォルトでは処理の継続は行われず、redirectメソッドを
	 * 呼び出した時点でスクリプトの処理は終了します。
	 * 
	 * 第三引数にはHTTPステータスコードを指定します。
	 * デフォルト、または空文字を指定した場合は302が指定されます。
	 * サポートされているステータスコードは300～305、307までの
	 * 番号です。
	 * 
	 * <code>
	 * 
	 * HTTPAgent::redirect('http://redirect.com');
	 * 
	 * </code>
	 * 
	 * @access     public
	 * @static
	 * @return     リダイレクトヘッダーが送信できた場合はtrueを返し、失敗した場合はfalseを返す。
	 * @param      String $url リダイレクト先のURL。
	 * @param      bool[optional] $exit スクリプト処理を継続するかどうか。
	 * @param      int[optional] $status レスポンスとして返すHTTPステータスコード。
	 */
	function redirect($url, $exit = true, $status = 302) {
		if (headers_sent()) return false;
		
		switch ($status) {
			case 300: $status = 'HTTP/1.1 300 Multiple Choices'  ; break;
			case 301: $status = 'HTTP/1.1 301 Moved Permanently' ; break;
			case 302: $status = 'HTTP/1.1 302 Found'             ; break;
			case 303: $status = 'HTTP/1.1 303 See Other'         ; break;
			case 304: $status = 'HTTP/1.1 304 Not Modified'      ; break;
			case 305: $status = 'HTTP/1.1 305 Use Proxy'         ; break;
			case 307: $status = 'HTTP/1.1 307 Temporary Redirect'; break;
			case 401: $status = 'HTTP/1.1 401 Unauthorized'      ; break;
			case 403: $status = 'HTTP/1.1 403 Forbidden'         ; break;
			default : $status = 'HTTP/1.1 302 Found'             ; break;
		}
		header($status);
		header('Location: ' . $url);
		
		if ($exit) exit();
		
		return true;
	}
	
	/**
	 * リダイレクトヘッダーをレスポンスとして返します
	 * 
	 * 第一引数で指定したURLへリダイレクトを行うヘッダー情報
	 * をレスポンスとして返します。
	 * 
	 * 第二引数にはディレイを秒単位で指定します。
	 * 
	 * <code>
	 * 
	 * HTTPAgent::refresh('http://redirect.com', 10);
	 * 
	 * </code>
	 * 
	 * @access     public
	 * @static
	 * @return     リダイレクトヘッダーが送信できた場合はtrueを返し、失敗した場合はfalseを返す。
	 * @param      String $url リダイレクト先のURL。
	 * @param      bool[optional] $delay リダイレクト開始までの秒数。
	 */
	function refresh($url, $delay = 5) {
		if (headers_sent()) return false;
		header("Refresh: $delay; URL=$url");
		return true;
	}
	
	/**
	 * ダウンロードヘッダーをレスポンスとして返します
	 * 
	 * ダウンロードさせるファイルのパスを第1引数に指定して下さい。
	 * ダウンロード実行後に処理を継続する場合は、第2引数にfalseを指定して下さい。
	 * 
	 * <code>
	 * 
	 * HTTPAgent::refresh('http://redirect.com', 10);
	 * 
	 * </code>
	 * 
	 * @access     public
	 * @static
	 * @return     リダイレクトヘッダーが送信できた場合はtrueを返し、失敗した場合はfalseを返す。
	 * @param      String $url リダイレクト先のURL。
	 * @param      bool[optional] $exit スクリプト処理を継続するかどうか。
	 */
	function download($filepath, $exit = true) {
		if (headers_sent()) return false;

		if (!file_exists($filepath)) return !trigger_error(sprintf('File [%s] does not exists'  . "\n", $filepath, E_USER_WARNING));
		if (!is_readable($filepath)) return !trigger_error(sprintf('Can not open the file [%s]' . "\n", $filepath, E_USER_WARNING));
		
		switch(true) {
			case strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false:
				$filename = mb_convert_encoding(basename($filepath), 'SJIS', mb_detect_encoding($filepath));
			break;
			case strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') !== false:
				$filename = '';
			break;
			default:
				$filename = basename($filepath);
			break;
		}
		
		header(sprintf('Content-Disposition: inline; filename="%s"', $filename));
	    header(sprintf('Content-Length: %s', filesize($filepath)));
	    header('Content-Type: application/octet-stream');
		echo file_get_contents($filepath);
	
		if ($exit) exit();
		return true;
	}
	
	/**
	 * ヘッダーのAccept情報を取得します。
	 * 
	 * <code>
	 * 
	 * $accepts = HTTPAgent::getHeaderAccept();
	 * 
	 * </code>
	 * 
	 * @access     public
	 * @static
	 * @return     Accept情報を保持している配列。
	 */
	function getHeaderAccept() {
		$accepts = isset($_SERVER['HTTP_ACCEPT'])? $_SERVER['HTTP_ACCEPT']: '*/*';
		// 整形
		$accepts = explode(',', $accepts);
		foreach ($accepts as $key => $value) $accepts[$key] = trim($accepts[$key]);
		$accepts = explode(';', implode(',', $accepts));
		foreach ($accepts as $key => $value) $accepts[$key] = trim($accepts[$key]);
		$accepts = explode(',', implode(';', $accepts));
		
		$acceptArray = array();
		$acceptSeparator = ';q=';
		
		foreach ($accepts as $a) {
			$a = trim($a);
			if (strpos($a, $acceptSeparator) !== false) {
				$aa = array(
					'qvalue' => (float)substr($a, strpos($a, $acceptSeparator) + strlen($acceptSeparator)),
					'mediatype' => substr($a, 0, strpos($a, $acceptSeparator))
				);
			} else {
				$aa = array(
					'qvalue' => 1,
					'mediatype' => $a
				);
			}
			if (strpos($aa['mediatype'], ';') !== false) $aa['metaqvalue'] = 0;
			else if ($aa['mediatype'] === '*/*') $aa['metaqvalue'] = 3;
			else if (strpos($aa['mediatype'], '*')) $aa['metaqvalue'] = 2;
			else $aa['metaqvalue'] = 1;
			$acceptArray[] = $aa;
		}
		foreach ($acceptArray as $key => $row) {
			$qvalue[$key]  = $row['qvalue'];
			$metaqvalue[$key] = $row['metaqvalue'];
		}
		array_multisort($qvalue, SORT_DESC, $metaqvalue, SORT_ASC, $acceptArray);
		for ($i = count($acceptArray); $i-- > 0;) unset($acceptArray[$i]['metaqvalue']);
		return $acceptArray;
	}
}

