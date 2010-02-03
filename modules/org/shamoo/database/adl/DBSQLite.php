<?php
/**
 * DBSQLite.php
 * 
 * このファイルにはDBSQLiteクラスに関する定義が記述されています。<br/>
 * このファイルを読み込むことによりDBSQLiteクラスの使用が可能になります。
 * 
 * @package    modules
 * @subpackage DBSQLite
 * @author     Masashi Onogawa <m.onogawa@gmail.com>
 * @copyright  Copyright © 2009, shamoo.org
 * @license    shamoo.org License Ver. 1.0
 * @version    1.0
 * @access     public
 */

require_once 'AbstractDB.php';

/**
 * SQLite用のドライバークラスです。
 *
 * @package    modules
 * @subpackage adl
 * @author     Masashi Onogawa <m.onogawa@gmail.com>
 * @copyright  Copyright © 2009, shamoo.org
 * @license    shamoo.org License Ver. 1.0
 * @version    1.0
 * @access     public
 */
class DBSQLite extends AbstractDB {

	/////////////////////////////////////////////////////////////////
	// Constructor                                                 //
	/////////////////////////////////////////////////////////////////
	function __construct($config) { parent::__construct($config); }
	
	/////////////////////////////////////////////////////////////////
	// Methods                                                     //
	/////////////////////////////////////////////////////////////////
	/**
	 * データベースとの接続を開始します。
	 *
	 * オブジェクト生成時にデータベースとの接続を自動的に確立する為、このメソッドを明示的に呼び出す必要はありません。
	 *
	 * @access     public
	 * @return     bool 接続に成功した場合はtrueを、失敗した場合はfalseを返します。
	 * @param      array $config データベース接続情報を保持した配列を渡します。
	 */
	function connect($config) {
		if (!($this->_connection = sqlite_open($config['database']))) return false;
	
		$this->_isConnected = true;
		return true;
	}
	
	/**
	 * データベースとの接続を破棄します。
	 *
	 * PHPはスクリプト終了時に自動的にデータベースとの接続を破棄する為、このメソッドを明示的に呼び出す必要はありません。
	 *
	 * @access     public
	 * @return     bool 切断に成功した場合はtrueを、失敗した場合はfalseを返します。
	 */
	function disconnect() {
		$this->_isConnected = false;
		return sqlite_close($this->_connection);
	}
	
	/**
	 * データベースの文字コードを指定します。
	 *
	 * <code>
	 * 
	 * $db = ADL::create($config);
	 * $db->setEncoding('UTF-8');
	 *
	 * </code>
	 *
	 * @access     public
	 * @return     bool 文字コードの指定に成功した場合はtrueを、失敗した場合はfalseを返します。
	 * @param      string $encode 指定する文字コードを文字列として渡します。
	 */
	function setEncoding($encoding) {
		return sqlite_set_client_encoding($this->_connection, $encoding);
	}
	
	/**
	 * データベースの文字コードを取得します。
	 *
	 * <code>
	 * 
	 * $db = ADL::create($config);
	 * $encode = $db->getEncoding();
	 *
	 * </code>
	 *
	 * @access     public
	 * @return     string 指定されている文字コードを文字列で返します。
	 */
	function getEncoding() {
		return sqlite_client_encoding($this->_connection);
	}
	
	/**
	 * トランザクションを開始します。
	 *
	 * インスタンス生成時、データベースとの接続開始と共にこのメソッドは呼び出されます。
	 * ユーザーが明示的にこのメソッドを呼び出す必要はありません。
	 *
	 * @access     public
	 * @return     bool トランザクションが開始された場合にtrue、失敗した場合にはfalseを返す。
	 */
	function beginTransaction() {
		return is_resource($this->query('begin'));
	}
	
	/**
	 * コミットします。
	 *
	 * <code>
	 * 
	 * $query = sprintf("insert into test(id, name) values (%d, '%s')", $id, $name);
	 *
	 * $db = ADL::create($config);
	 * $db->execute($query);
	 *
	 * $db->commit();
	 *
	 * </code>
	 *
	 * @access     public
	 * @return     bool コミットが成功した場合にtrue、失敗した場合にはfalseを返す。
	 */
	function commit() {
		if (!is_resource($this->query('commit'))) return false;
		if (!$this->beginTransaction()) return false;
		return true;
	}

	/**
	 * ロールバックします。
	 *
	 * <code>
	 * 
	 * $query = sprintf("insert into test(id, name) values (%d, '%s')", $id, $name);
	 *
	 * $db = ADL::create($config);
	 * $db->execute($query);
	 *
	 * $db->rollback();
	 *
	 * </code>
	 *
	 * @access     public
	 * @return     bool ロールバックが成功した場合にtrue、失敗した場合にはfalseを返す。
	 */
	function rollback() {
		if (!is_resource($this->query('rollback'))) return false;
		if (!$this->beginTransaction()) return false;
		return true;
	}
	
	/**
	 * SQL文を実行し、結果リソースを返します。
	 *
	 * <code>
	 * 
	 * $query = sprintf("select * from test where id = %d", $id);
	 *
	 * $db = ADL::create($config);
	 * $resource = $db->query($query);
	 *
	 * </code>
	 *
	 * @access     public
	 * @return     resource|bool 結果リソース、またはtrueを返します。SQL文の実行に失敗した場合はfalseを返します。
	 * @param      string $query 実行するSQL文。
	 */
	function query($query) {
		$resource = sqlite_query($this->_connection, $query, SQLITE_ASSOC);
		return $resource;
	}

	/**
	 * 結果リソースを渡し、レコードを取得します。
	 *
	 * <code>
	 * 
	 * $query = sprintf("select * from test where id = %d", $id);
	 *
	 * $db = ADL::create($config);
	 * $resource = $db->query($query);
	 * $rows = $db->fetch($resource);
	 *
	 * </code>
	 *
	 * @access     public
	 * @return     array 結果リソース、またはtrueを返します。SQL文の実行に失敗した場合はfalseを返します。
	 * @param      resource|bool $resource レコードを取得する結果リソース。
	 * @param      callback[optional] $callback 1レコード取得毎に実行する処理へのコールバック。
	 */
	function fetch($resource, $callback = null) {
		$results = null;
		if ($resource === false) { return false; }

		if (is_null($callback)) {
			$results = sqlite_fetch_all($resource, SQLITE_ASSOC);
		} else {
			$results = array();
			while ($row = sqlite_fetch_array($resource)) {
				$results[] = call_user_func($callback, $row);
			}
			sqlite_fetch_array($resource);
		}

		return $results;
	}

	/**
	 * 渡された文字列をデータベース用にエスケープします。
	 *
	 * <code>
	 * 
	 * $db = ADL::create($config);
	 *
	 * $value = 'abc123-/"789xyz';
	 * $value = $db->escapeString($value);
	 *
	 * $query = sprintf("select * from test where value = '%s'", $value);
	 * $rows = $db->execute($query);
	 *
	 * </code>
	 *
	 * @access     public
	 * @return     string エスケープされた文字列。
	 * @param      string $value エスケープする文字列。
	 */
	function escapeString($value) {
		return sqlite_escape_string($value);
	}

	/**
	 * 渡されたバイト文字列をデータベース用にエスケープします。
	 *
	 * <code>
	 * 
	 * $db = ADL::create($config);
	 *
	 * $binary = file_get_contents('sample.jpg');
	 * $binary = $db->escapeBytes($binary);
	 *
	 * $query = sprintf("update test set image = '%s' where id = 5", $binary);
	 * $db->execute($query);
	 *
	 * </code>
	 *
	 * @access     public
	 * @return     string エスケープされたバイト文字列。
	 * @param      string $value エスケープするバイト文字列。
	 */
	function escapeBytes($value) {
		return sqlite_udf_encode_binary($value);
	}

	/**
	 * エスケープされたバイト文字列を元に戻します。
	 *
	 * <code>
	 * 
	 * $db = ADL::create($config);
	 * $rows = $db->execute('select * from test limit 1');
	 *
	 * $binary = $db->unescapeBytes($rows[0]['image']);
	 * file_put_contents('sample.jpg', $binary);
	 *
	 * </code>
	 *
	 * @access     public
	 * @return     string アンエスケープされたバイト文字列。
	 * @param      string $value エスケープされたバイト文字列。
	 */
	function unescapeBytes($value) {
		return sqlite_udf_decode_binary($value);
	}
}