<?php
/**
 * ADL.php
 * 
 * このファイルにはADLクラスに関する定義が記述されています。<br/>
 * このファイルを読み込むことによりADLクラスの使用が可能になります。
 * 
 * @package    modules
 * @subpackage ADL
 * @author     Masashi Onogawa <m.onogawa@gmail.com>
 * @copyright  Copyright © 2009, shamoo.org
 * @license    shamoo.org License Ver. 1.0
 * @version    1.0
 * @access     public
 */

/**
 * PHPからDatabaseを操作するADLオブジェクトを生成する為のファクトリークラスです。
 *
 * PHPからDatabaseを操作するADLオブジェクトを生成する為のファクトリークラスです。
 * 現在のバージョンでは MySQL、 PostgreSQL、 SQLite に対応しています。
 * ただし、PHP4ではSQLiteは未対応の為、使用する事が出来ません。
 *
 * @package    modules
 * @subpackage adl
 * @author     Masashi Onogawa <m.onogawa@gmail.com>
 * @copyright  Copyright © 2009, shamoo.org
 * @license    shamoo.org License Ver. 1.0
 * @version    1.0
 * @access     public
 */
class ADL {
	/**
	 * ADLオブジェクトを生成します。
	 * 
	 * 接続情報を保持した配列を渡し、データベースの操作を行うADLオブジェクトを生成します。
	 * 引数として渡す配列に関しては、サンプルを参照して下さい。
	 * 
	 * <code>
	 * 
	 * // MySQLを使用する場合 
	 * 
	 * $dbConfig = array(
	 *     'driver'    => 'mysql'    , // postgresql, mysql, sqlite のいずれか
	 *     'host'      => 'localhost', // 接続先のホスト名、またはIP
	 *     'database'  => 'db_name'  , // 使用するDBの名前
	 *     'username'  => 'root'     , // 接続ユーザー名
	 *     'password'  => 'root'     , // 接続ユーザーに対するパスワード
	 *     'port'      => '3306'     , // 接続ポート番号
	 *     'encoding'  => 'utf8'     , // クライアントの文字コード
	 *     'autoCommit'=> true       , // オートコミットの有効化
	 * );
	 * 
	 * $db = &ADL::create($dbConfig);
	 * 
	 * </code>
	 * 
	 * <code>
	 * 
	 * // PostgreSQLを使用する場合 
	 * 
	 * $dbConfig = array(
	 *     'driver'    => 'postgresql', // postgresql, mysql, sqlite のいずれか
	 *     'host'      => 'localhost' , // 接続先のホスト名、またはIP
	 *     'database'  => 'db_name'   , // 使用するDBの名前
	 *     'username'  => 'root'      , // 接続ユーザー名
	 *     'password'  => 'root'      , // 接続ユーザーに対するパスワード
	 *     'port'      => '3306'      , // 接続ポート番号
	 *     'encoding'  => 'utf8'      , // クライアントの文字コード
	 *     'autoCommit'=> true        , // オートコミットの有効化
	 * );
	 * 
	 * $db = &ADL::create($dbConfig);
	 * 
	 * </code>
	 * 
	 * <code>
	 * 
	 * // SQLiteを使用する場合 
	 * 
	 * $dbConfig = array(
	 *     'driver'    => 'sqlite'     , // postgresql, mysql, sqlite のいずれか
	 *     'database'  => './SQLite.db', // データベースファイルのパス
	 *     'encoding'  => 'utf8'       , // クライアントの文字コード
	 *     'autoCommit'=> true         , // オートコミットの有効化
	 * );
	 * 
	 * $db = &ADL::create($dbConfig);
	 * 
	 * </code>
	 *
	 * @static
	 * @access     public
	 * @return     object ADLオブジェクトを返します。
	 * @param      array $config DBへの接続情報を保持した配列を渡します。
	 */
	function &create($config) {
		static $__instance;
		if (!is_null($__instance)) return $__instance;
		
		// Parameter checks
		if (!is_array($config)) {
			trigger_error('The first argument specifies an array.' . PHP_EOL, E_USER_WARNING);
			return null;
		}
		if (!isset($config['driver'])) {
			trigger_error('Driver has not been specified.' . PHP_EOL, E_USER_WARNING);
			return null;
		}

		$driver = $config['driver'];
		unset($config['driver']);
		switch (substr(PHP_VERSION, 0, 1)) {
			case 5:
				switch ($driver) {
					case 'mysql'     : $class = 'DBMySQL'     ; break;
					case 'postgresql': $class = 'DBPostgreSQL'; break;
					case 'sqlite'    : $class = 'DBSQLite'    ; break;
					default          : trigger_error(sprintf('The specified driver [%s] is invalid.', $driver), E_USER_WARNING); return null; break;
				}
				break;
			case 4:
				switch ($driver) {
					case 'mysql'     : $class = 'DBMySQL'     ; break;
					case 'postgresql': $class = 'DBPostgreSQL'; break;
					default          : trigger_error(sprintf('The specified driver [%s] is invalid.', $driver), E_USER_WARNING); return null; break;
				}
				break;
			default:
				trigger_error(sprintf('PHP ver.%s is not supported', PHP_VERSION), E_USER_WARNING); return null;
				break;
		}
		require_once $class . '.php';

		$__instance = new $class($config);
		return $__instance;
	}
}
