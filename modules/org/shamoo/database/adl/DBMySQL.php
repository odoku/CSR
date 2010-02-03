<?php
/**
 * DBMySQL.php
 * 
 * このファイルにはDBMySQLクラスに関する定義が記述されています。<br/>
 * このファイルを読み込むことによりDBMySQLクラスの使用が可能になります。
 * 
 * @package    modules
 * @subpackage DBMySQL
 * @author     Masashi Onogawa <m.onogawa@gmail.com>
 * @copyright  Copyright © 2009, shamoo.org
 * @license    shamoo.org License Ver. 1.0
 * @version    1.0
 * @access     public
 */

require_once 'AbstractDB.php';

/**
 * MySQL用のドライバークラスです。
 *
 * @package    modules
 * @subpackage adl
 * @author     Masashi Onogawa <m.onogawa@gmail.com>
 * @copyright  Copyright © 2009, shamoo.org
 * @license    shamoo.org License Ver. 1.0
 * @version    1.0
 * @access     public
 */
class DBMySQL extends AbstractDB {

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
		if (!array_key_exists('host', $config) || empty($config['host'])) $config['host'] = 'localhost';
		if (!array_key_exists('port', $config) || empty($config['port'])) $config['port'] = '3306';
		
		$this->_connection = mysql_connect(
			$config['host'] . ':' . $config['port'],
			$config['username'],
			$config['password']
		);
		
		if ($this->_connection === false) {
			return !trigger_error('Failed to connect to the MySQL.', E_USER_WARNING);
		}
		if (!mysql_select_db($config['database'], $this->_connection)) {
			return !trigger_error('Failed to select database.', E_USER_WARNING);
		}
		
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
		return mysql_close($this->_connection);
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
		return mysql_set_charset($encoding, $this->_connection);
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
		return mysql_client_encoding($this->_connection);
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
		if (!$this->query('start transaction')) return false;
		return true;
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
		if (!$this->query('commit work')) return false;
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
		if (!$this->query('rollback work')) return false;
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
		// mysql_free_result($this->_resource);
		$resource = mysql_query($query, $this->_connection);
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
		
		// Select文とか
		else if (is_resource($resource)) {
			$results = array();
			if (is_null($callback)) {
				while ($row = mysql_fetch_assoc($resource)) $results[] = $row;
			} else {
				while ($row = mysql_fetch_assoc($resource)) {
					$results[] = call_user_func($callback, $row);
				}
			}
			if (mysql_num_rows($resource) > 0) mysql_data_seek($resource, 0);
		}
		
		// Insert文とか
		else {
			$results = mysql_affected_rows($this->_connection);
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
		return mysql_real_escape_string($value, $this->_connection);
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
		return mysql_real_escape_string($value, $this->_connection);
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
		return $value;
	}
	
	/**
	 * テーブルの定義情報を取得します。
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
		if (is_null($schema)) $schema = $this->_config['database'];
		
		$query = sprintf("
			select
				sc.column_name as \"field\",
				sc.data_type as \"type\",
				case
					when sc.data_type in('integer', 'float', 'numeric') then sc.numeric_precision
					else sc.character_octet_length
				end as \"size\",
				sc.is_nullable as \"null\",
				case
					when t1.constraint_type = 'PRIMARY KEY' then 'YES'
					else 'NO'
				end as \"primary\",
				case
					when t1.constraint_type = 'UNIQUE' then 'YES'
					else 'NO'
				end as \"unique\",
				case
					when sc.column_default is null then 'NO'
					else 'YES'
				end as \"default\"
			from
				information_schema.columns as sc
			left join (
				select
					kcu.table_catalog,
					kcu.table_schema,
					kcu.table_name,
					kcu.column_name,
					tc.constraint_type
				from
					information_schema.key_column_usage as kcu
				inner join
					information_schema.table_constraints as tc
					on  kcu.table_schema  = tc.table_schema
					and kcu.table_name    = tc.table_name
					and kcu.constraint_name = tc.constraint_name
					and tc.constraint_type != 'FOREIGN KEY'
			) as t1
				on  sc.table_schema = t1.table_schema
				and sc.table_name   = t1.table_name
				and sc.column_name  = t1.column_name
			where
				sc.table_schema = '%s'
			and
				sc.table_name = '%s'
			order by
				sc.ordinal_position asc",
			$this->escapeString($schema),
			$this->escapeString($table )
		);
		$result = $this->execute($query);
		
		return $result;
	}

	/**
	 * テーブルのリレーション情報を取得します。
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
		if (is_null($schema)) $schema = $this->config['database'];
	
		$query = sprintf("
			select
				referenced_column_name as `column`,
				table_name             as `referenced_table`,
				column_name            as `referenced_column`
			from
				information_schema.KEY_COLUMN_USAGE
			where
				referenced_table_schema = '%s'
			and
				referenced_table_name = '%s'",
			$this->escapeString($schema),
			$this->escapeString($table )
		);
		$result1 = $this->execute($query);

		$query = sprintf("
			select
				column_name            as `column`,
				referenced_table_name  as `referenced_table`,
				referenced_column_name as `referenced_column`
			from
				information_schema.KEY_COLUMN_USAGE
			where
				table_schema = '%s'
			and
				table_name = '%s'
			and
				referenced_table_name is not null
			and
				referenced_column_name is not null
			",
			$this->escapeString($schema),
			$this->escapeString($table )
		);
		$result2 = $this->execute($query);

		$result = array_merge($result1, $result2);
		return $result;
	}
}