<?php
/**
 * AbstractDB.php
 * 
 * このファイルにはAbstractDBクラスに関する定義が記述されています。<br/>
 * このファイルを読み込むことによりAbstractDBクラスの使用が可能になります。
 * 
 * @package    modules
 * @subpackage AbstractDB
 * @author     Masashi Onogawa <m.onogawa@gmail.com>
 * @copyright  Copyright © 2009, shamoo.org
 * @license    shamoo.org License Ver. 1.0
 * @version    1.0
 * @access     public
 */

/**
 * 各ADLドライバークラスの親クラスとなる抽象クラスです。
 *
 * このクラスをインスタンス化して使用する事はありません。
 * ADL::createメソッドを使用してADLオブジェクトを生成して下さい。
 *
 * @package    modules
 * @subpackage adl
 * @author     Masashi Onogawa <m.onogawa@gmail.com>
 * @copyright  Copyright © 2009, shamoo.org
 * @license    shamoo.org License Ver. 1.0
 * @version    1.0
 * @access     public
 * @abstract
 */
class AbstractDB {
	/////////////////////////////////////////////////////////////////
	// Instance variables                                          //
	/////////////////////////////////////////////////////////////////
	/**
	 * データベースへの接続情報を保持した配列を格納する変数です。
	 * @var array
	 */
	var $_config = array();
	
	/**
	 * コネクションリソースを保持する変数です。
	 * @var resource
	 */
	var $_connection = null;
	
	/**
	 * コネクションが確立されているかどうかを示す変数です
	 * @var bool
	 */
	var $_isConnected = false;


	/////////////////////////////////////////////////////////////////
	// Constructor                                                 //
	/////////////////////////////////////////////////////////////////
	/**
	 * PHP4用コンストラクタ
	 *
	 * PHP4で実行される場合に呼び出されるコンストラクタです。
	 * このコンストラクタは内部でAbstractDB::__construct()を呼び出しています。
	 * コンストラクタの動作はAbstractDB::__construct()を参照して下さい。
	 * また、register_shutdown_function()をコールしており
	 * PHP4上で擬似的なデストラクタの挙動を提供します。
	 *
	 * @access     public
	 * @return     object ADLオブジェクトを返します。
	 * @param      array $config DBへの接続情報を保持した配列を渡します。
	 */
	function AbstractDB() {
		if (method_exists($this, '__destruct')) {
			register_shutdown_function (array(&$this, '__destruct'));
		}

		$args = func_get_args();
		call_user_func_array(array(&$this, '__construct'), $args);
	}

	/**
	 * コンストラクタ
	 *
	 * このコンストラクタは呼び出されるとデータベースへの接続を開始します。
	 *
	 * @access     public
	 * @return     object ADLオブジェクトを返します。
	 * @param      array $config DBへの接続情報を保持した配列を渡します。
	 */
	function __construct($config) {
		$this->_config = $config;
		
		// Connect to the database
		$this->connect($config);
		
		// Set Encoding
		if (array_key_exists('encoding', $config) && !empty($config['encoding'])) {
			$this->setEncoding($config['encoding']);
		}
		
		// Begin transaction
		$this->beginTransaction();
	}
	
	
	/////////////////////////////////////////////////////////////////
	// Destructor                                                  //
	/////////////////////////////////////////////////////////////////
	/**
	 * デストラクタ
	 *
	 * オブジェクトが破棄される前にデータベースに対して、ロールバック
	 * またはコミットを実行します。
	 * このデストラクタはPHP4で実行する場合でも動作します。
	 * 動作する詳しい理由に関してはAbstractDB()を参照して下さい。
	 *
	 * @access     public
	 */
	function __destruct() {
		// Commit or Rollback
		if (array_key_exists('autoCommit', $this->_config) && $this->_config['autoCommit'] === false) {
			$this->rollback();
		} else {
			$this->commit();
		}
	}
	
	
	/////////////////////////////////////////////////////////////////
	// Getter & Setter                                             //
	/////////////////////////////////////////////////////////////////
	/**
	 * データベースへの接続情報を保持した配列を返します。
	 *
	 * @access     public
	 * @return     array DBへの接続情報を保持した配列を返します。
	 */
	function getConfig() {
		return $this->_config;
	}

	/**
	 * コネクションリソースを返します。
	 *
	 * @access     public
	 * @return     resource コネクションリソースを返します。
	 */
	function getConnection() {
		return $this->_connection;
	}

	/**
	 * コネクションが確立されているかどうかを返します。
	 *
	 * @access     public
	 * @return     bool コネクションが確立されているかどうかを返します。
	 */
	function isConnected() {
		return $this->_isConnected;
	}


	/////////////////////////////////////////////////////////////////
	// Public methods                                              //
	/////////////////////////////////////////////////////////////////
	/**
	 * SQL文を実行し、結果を返します。
	 *
	 * 実行されるSQL文が結果セットを返す場合（SELECT文等）は、
	 * 取得したレコードを配列に格納して返します。
	 * 実行されるSQL文が結果セットを返さない場合（UPDATE,INSERT等）は、
	 * SQL文実行によって影響を受けたレコード数を返します。
	 * 複数のSQL文を一度に実行した場合は、最後に実行した分の結果が返されます。
	 *
	 * 取得したレコードを配列に格納する際に予め特定の処理を行いたい場合は、
	 * 実行したい処理を記述した関数へのコールバックを第二引数に渡して下さい。
	 *
	 * <code>
	 * 
	 * // 結果セットを持つSQL文の実行
	 * 
	 * $query = "select * from test";
	 *
	 * $db = ADL::create($config);
	 * $rows = $db->execute($query);
	 *
	 * foreach ($rows as $row) {
	 *     echo $row['name'];
	 * }
	 *
	 * </code>
	 *
	 * <code>
	 * 
	 * // 結果セットを持たないSQL文の実行
	 * 
	 * $query = sprintf("insert into test(id, name) values (%d, '%s')", $id, $name);
	 *
	 * $db = ADL::create($config);
	 * if ($db->execute($query) > 0) {
	 *     echo 'ok.';
	 * } eles {
	 *     echo 'failed.';
	 * }
	 *
	 * </code>
	 *
	 * @access     public
	 * @return     mixed 結果セットを持つSQL文の場合は配列を、結果セットを持たないSQL文の場合は影響を受けたレコード数を返します。SQL文の実行に失敗した場合は共にfalseを返します。
	 * @param      string 実行するSQL文。
	 * @param      callback[optional] fetchを行う際に取得したレコードに対して行う処理をコールバック関数として渡します。デフォルトはnullです。
	 */
	function execute($query, $callback = null) {
		$resource = $this->query($query);
		$result = $this->fetch($resource, $callback);
		
		return $result;
	}
	
	
	/////////////////////////////////////////////////////////////////
	// Abstract methods                                            //
	/////////////////////////////////////////////////////////////////
	/**
	 * データベースとの接続を開始します。
	 *
	 * オブジェクト生成時にデータベースとの接続を自動的に確立する為、このメソッドを明示的に呼び出す必要はありません。
	 *
	 * このメソッドは抽象メソッドです。
	 * サブクラスでオーバーライドされていない場合はエラーになります。
	 *
	 * @access     public
	 * @return     bool 接続に成功した場合はtrueを、失敗した場合はfalseを返します。
	 * @param      array $config データベース接続情報を保持した配列を渡します。
	 */
	function connect($config) {
		trigger_error(sprintf('Abstruct method [%s] is not defined', __FUNCTION__), E_USER_ERROR);
	}

	/**
	 * データベースとの接続を破棄します。
	 *
	 * PHPはスクリプト終了時に自動的にデータベースとの接続を破棄する為、このメソッドを明示的に呼び出す必要はありません。
	 *
	 * このメソッドは抽象メソッドです。
	 * サブクラスでオーバーライドされていない場合はエラーになります。
	 *
	 * @access     public
	 * @return     bool 切断に成功した場合はtrueを、失敗した場合はfalseを返します。
	 */
	function disconnect() {
		trigger_error(sprintf('Abstruct method [%s] is not defined', __FUNCTION__), E_USER_ERROR);
	}

	/**
	 * データベースの文字コードを指定します。
	 *
	 * このメソッドは抽象メソッドです。
	 * サブクラスでオーバーライドされていない場合はエラーになります。
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
	function setEncoding($encode) {
		trigger_error(sprintf('Abstruct method [%s] is not defined', __FUNCTION__), E_USER_ERROR);
	}

	/**
	 * データベースの文字コードを取得します。
	 *
	 * このメソッドは抽象メソッドです。
	 * サブクラスでオーバーライドされていない場合はエラーになります。
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
		trigger_error(sprintf('Abstruct method [%s] is not defined', __FUNCTION__), E_USER_ERROR);
	}

	/**
	 * トランザクションを開始します。
	 *
	 * インスタンス生成時、データベースとの接続開始と共にこのメソッドは呼び出されます。
	 * ユーザーが明示的にこのメソッドを呼び出す必要はありません。
	 *
	 * このメソッドは抽象メソッドです。
	 * サブクラスでオーバーライドされていない場合はエラーになります。
	 *
	 * @access     public
	 * @return     bool トランザクションが開始された場合にtrue、失敗した場合にはfalseを返す。
	 */
	function beginTransaction() {
		trigger_error(sprintf('Abstruct method [%s] is not defined', __FUNCTION__), E_USER_ERROR);
	}

	/**
	 * コミットします。
	 *
	 * このメソッドは抽象メソッドです。
	 * サブクラスでオーバーライドされていない場合はエラーになります。
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
		trigger_error(sprintf('Abstruct method [%s] is not defined', __FUNCTION__), E_USER_ERROR);
	}

	/**
	 * ロールバックします。
	 *
	 * このメソッドは抽象メソッドです。
	 * サブクラスでオーバーライドされていない場合はエラーになります。
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
		trigger_error(sprintf('Abstruct method [%s] is not defined', __FUNCTION__), E_USER_ERROR);
	}
	
	/**
	 * SQL文を実行し、結果リソースを返します。
	 *
	 * このメソッドは抽象メソッドです。
	 * サブクラスでオーバーライドされていない場合はエラーになります。
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
		trigger_error(sprintf('Abstruct method [%s] is not defined', __FUNCTION__), E_USER_ERROR);
	}
	
	/**
	 * 結果リソースを渡し、レコードを取得します。
	 *
	 * このメソッドは抽象メソッドです。
	 * サブクラスでオーバーライドされていない場合はエラーになります。
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
		trigger_error(sprintf('Abstruct method [%s] is not defined', __FUNCTION__), E_USER_ERROR);
	}
	
	/**
	 * 渡された文字列をデータベース用にエスケープします。
	 *
	 * このメソッドは抽象メソッドです。
	 * サブクラスでオーバーライドされていない場合はエラーになります。
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
		trigger_error(sprintf('Abstruct method [%s] is not defined', __FUNCTION__), E_USER_ERROR);
	}
	
	/**
	 * 渡されたバイト文字列をデータベース用にエスケープします。
	 *
	 * このメソッドは抽象メソッドです。
	 * サブクラスでオーバーライドされていない場合はエラーになります。
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
		trigger_error(sprintf('Abstruct method [%s] is not defined', __FUNCTION__), E_USER_ERROR);
	}
	
	/**
	 * エスケープされたバイト文字列を元に戻します。
	 *
	 * このメソッドは抽象メソッドです。
	 * サブクラスでオーバーライドされていない場合はエラーになります。
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
		trigger_error(sprintf('Abstruct method [%s] is not defined', __FUNCTION__), E_USER_ERROR);
	}
	
	/**
	 * テーブルの定義情報を取得します。
	 *
	 * このメソッドは抽象メソッドです。
	 * サブクラスでオーバーライドされていない場合はエラーになります。
	 *
	 * <code>
	 * 
	 * $db = ADL::create($config);
	 * $describe = $db->describe('test');
	 *
	 * </code>
	 *
	 * @access     public
	 * @return     array テーブルの定義情報を配列で返します。
	 * @param      string $table 定義情報を取得するテーブル名。
	 * @param      string[optional] $schema スキーマ名、またはデータベース名。
	 */
	function describe($table, $schema = null) {
		trigger_error(sprintf('Abstruct method [%s] is not defined', __FUNCTION__), E_USER_ERROR);
	}
	
	/**
	 * テーブルのリレーション情報を取得します。
	 *
	 * このメソッドは抽象メソッドです。
	 * サブクラスでオーバーライドされていない場合はエラーになります。
	 *
	 * <code>
	 * 
	 * $db = ADL::create($config);
	 * $relations = $db->relations('test');
	 *
	 * </code>
	 *
	 * @access     public
	 * @return     array テーブルのリレーション情報を配列で返します。
	 * @param      string $table リレーション情報を取得するテーブル名。
	 * @param      string[optional] $schema スキーマ名、またはデータベース名。
	 */
	function relations($table, $schema = null) {
		trigger_error(sprintf('Abstruct method [%s] is not defined', __FUNCTION__), E_USER_ERROR);
	}
}