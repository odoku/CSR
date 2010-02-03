<?php
/**
 * Finder.php
 * 
 * このファイルにはFinderクラスに関する定義が記述されています。<br/>
 * このファイルを読み込むことによりFinderクラスの使用が可能になります。
 * 
 * @package    modules
 * @subpackage file
 * @author     Masashi Onogawa <m.onogawa@gmail.com>
 * @copyright  Copyright © 2009, shamoo.org
 * @license    shamoo.org License Ver. 1.0
 * @version    1.0
 * @access     public
 */

/**
 * ファイルやディレクトリを操作する為のクラス
 * 
 * @package    modules
 * @subpackage file
 * @author     Masashi Onogawa <m.onogawa@gmail.com>
 * @copyright  Copyright © 2009, shamoo.org
 * @license    shamoo.org License Ver. 1.0
 * @version    1.0
 * @access     public
 */
class Finder {
	/**
	 * ファイル、またはディレクトリを削除します。
	 *
	 * Unixコマンドのrmと違い、このメソッドは無条件でディレクトリも
	 * 削除してしまう事に注意して下さい。
	 * 第2引数にUnixタイムスタンプを指定する事により、
	 * 更新日時が指定した時間より古いファイル、またはディレクトリのみを
	 * 削除する事が可能です。
	 *
	 * <code>
	 * 
	 * Finder::remove('/www/remove_dir', strtotime('-1 week'));
	 * 
	 * </code>
	 * 
	 * @access     public
	 * @static
	 * @return     bool 削除に成功した場合はtrue、失敗した場合はfalseを返す。
	 * @param      string $path 削除するファイル、またはディレクトリのパス。
	 * @param      int[optional] $limit Unixタイムスタンプ。
	 */
	function remove($path, $limit = null) {
		$origin = $path;

		// 存在しないパスを指定して場合はWarning
		if (!($path = realpath($path))) {
			trigger_error(sprintf('%s: No such file or directory.', $origin), E_USER_WARNING);
			return false;
		}

		// 日時期限指定がない場合は現在時刻を期限とする
		$limit = (is_null($limit)) ? time() : $limit;

		// 指定されたパスがファイルの場合
		if (is_file($path)) {
			// ファイルの更新日が日時制限より新しい場合は削除しない
			if (filemtime($path) >= $limit) return true;
			if(!unlink($path)) {
				trigger_error(sprintf('%s: Permission denied.', $origin), E_USER_WARNING);
				return false;
			}
			return true;
		}
		
		// 指定されたパスがディレクトリの場合
		else {
			// ファイルの更新日が日時制限より新しい場合は削除しない
			if (filemtime($path) >= $limit) return true;

			// 指定ディレクトリ配下のファイル、ディレクトリを取得し、再帰的に削除処理
			$list = Finder::filelist($path);
			foreach ($list as $resource) {
				if(!Finder::remove($resource, $limit)) {
					trigger_error(sprintf('%s: Directory was unable to delete because it is not empty.', $resource), E_USER_WARNING);
					return false;
				}
			}

			if(!rmdir($path)) {
				trigger_error(sprintf('%s: Permission denied.', $resource), E_USER_WARNING);
				return false;
			}
			return true;
		}
	}

	/**
	 * 指定したパス配下にあるファイル、ディレクトリの絶対パスを取得します。
	 *
	 * 指定するパスはディレクトリのパスでなければなりません。
	 * 指定したパスがファイルの場合は、警告が出され、
	 * 戻り値としてfalseが返されます。
	 * 
	 * <code>
	 *
	 * $list = Finder::filelist();
	 * var_dump($list);
	 * 
	 * </code>
	 *
	 * @access     public
	 * @static
	 * @return     Array 指定したディレクトリ内に存在するファイル、またはディレクトリの絶対パスを含む配列。
	 * @param      string $path 一覧を取得するディレクトリのパス。
	 */
	
	function filelist($path) {
		$origin = $path;

		// 存在しないパスを指定して場合はWarning
		if (!($path = realpath($path))) {
			trigger_error(sprintf('%s: No such file or directory.', $origin), E_USER_WARNING);
			return false;
		}

		// 指定したパスがファイルならばWarning
		if (is_file($path)) {
			trigger_error(sprintf('%s: This is not a directory.', $origin), E_USER_WARNING);
			return false;
		}

		// 指定したパスの読み込み権限がない場合、または何らかの理由でディレクトリがオープンされなかった場合はWarning
		if(!($dir = opendir($path))) {
			trigger_error(sprintf('%s: Permission denied.', $origin), E_USER_WARNING);
			return false;
		}

		$list = array();
		while(($entry = readdir($dir)) !== false) {
			if (strcmp($entry, '.') === 0 || strcmp($entry, '..') === 0) continue;
			$list[] = realpath($path . '/' . $entry);
		}
		closedir($dir);
		return $list;
	}
	
	/**
	 * ディレクトリを作成します。
	 * 
	 * パーミッションのデフォルトは0755です。
	 * 指定したパス既にが存在した場合は警告が出され、
	 * 戻り値としてfalseが返されます。
	 * 
	 * <code>
	 * 
	 * Finder::mkdir('/www/new_dir');
	 * 
	 * </code>
	 * 
	 * @access     public
	 * @static
	 * @return     bool ディレクトリの作成に成功した場合はtrue、失敗した場合はfalseを返します。
	 * @param      string $path 作成するディレクトリのパス。
	 * @param      int[optional] $permission 作成するディレクトリのアクセス権。
	 */
	function mkdir($path, $permission = 0755) {
		// 存在しないディレクトリを指定して場合はWarning
		if (!file_exists(dirname($path))) {
			trigger_error(sprintf('%s: The directory does not exists.', dirname($path)), E_USER_WARNING);
			return false;
		}
		
		// 既にファイル、またはディレクトリが存在している場合はWarning
		if(file_exists($path)) {
			trigger_error(sprintf('%s: The directory has already existed.', $path), E_USER_WARNING);
			return false;
		}
		
		return mkdir($path, $permission);
	}
	
	/**
	 * ファイル、またはディレクトリをコピーします。
	 *
	 * ディレクトリをコピーする場合、配下のファイルおよびディレクトリも再帰的にコピーされます。
	 * ただし、全てのコピーが正常に行われた場合に限りtrueを返します。
	 * 
	 * <code>
	 * 
	 * Finder::copy('./orign.txt', './copy.txt');
	 * 
	 * </code>
	 * 
	 * @access     public
	 * @static
	 * @return     bool コピーに成功した場合はtrue、失敗した場合はfalseを返す。
	 * @param      string $origin コピー元となるファイル、またはディレクトリのパス。
	 * @param      string $copyTo コピー先のパス。
	 */
	function copy($origin, $copyTo) {
		// 存在しないパスを指定して場合はWarning（複製元）
		if (!file_exists($origin)) {
			trigger_error(sprintf('%s: No such file or directory.', $origin), E_USER_WARNING);
			return false;
		}
		
		// 複製先パスの末尾が / の場合は、指定パスの配下に複製元と同じ名前のディレクトリを作成
		if (substr($copyTo, -1, 1) === '/') {
			$filename = basename($origin);
			$createTo = substr($copyTo, -1, 1);
		} else {
			$filename = basename($copyTo);
			$createTo = dirname($copyTo);
		}
	
		// 存在しないディレクトリを指定して場合はWarning（複製先）
		if (!file_exists($createTo)) {
			trigger_error(sprintf('%s: The directory does not exists.', $createTo), E_USER_WARNING);
			return false;
		}
		
		// 複製元がファイルならば、コピーしてリターン
		if(is_file($origin)) return copy($origin, $createTo . '/' . $filename);
		
		// 複製元ファイルの一覧を取得
		$list = Finder::filelist($origin);

		// 複製先にディレクトリを作成
		if (!Finder::mkdir($createTo . '/' . $filename)) {
			trigger_error(sprintf('%s: Directory could not create.', dirname($copyTo)), E_USER_WARNING);
			return false;
		}
		
		$result = true;
		foreach($list as $resource) {
			$result = $result && Finder::copy($resource, $createTo . '/' . $filename . '/' . basename($resource));
		}
		
		return $result;
	}
	
	/**
	 * ファイル、またはディレクトリを移動します。
	 * 
	 * <code>
	 * 
	 * Finder::move('./orign.txt', './moved.txt');
	 * 
	 * </code>
	 * 
	 * @access     public
	 * @static
	 * @return     bool 移動に成功した場合はtrue、失敗した場合はfalseを返す。
	 * @param      string $origin 移動元となるファイル、またはディレクトリのパス。
	 * @param      string $moveTo 移動先のパス。
	 */
	function move($origin, $moveTo) {
		// 存在しないパスを指定して場合はWarning（複製元）
		if (!file_exists($origin)) {
			trigger_error(sprintf('%s: No such file or directory.', $origin), E_USER_WARNING);
			return false;
		}
		
		// 複製先パスの末尾が / の場合は、指定パスの配下に複製元と同じ名前のディレクトリを作成
		if (substr($moveTo, -1, 1) === '/') {
			$filename = basename($origin);
			$createTo = substr($moveTo, -1, 1);
		} else {
			$filename = basename($moveTo);
			$createTo = dirname($moveTo);
		}
	
		// 存在しないディレクトリを指定して場合はWarning（複製先）
		if (!is_dir($createTo)) {
			trigger_error(sprintf('%s: The directory does not exists.', $createTo), E_USER_WARNING);
			return false;
		}

		// 移動元、移動先が同じディレクトリ内の場合はリネーム
		if (realpath(dirname($origin)) === realpath($createTo)) {
			return rename($origin, $createTo . '/' . $filename);
		} else {
			return Finder::copy($origin, $moveTo) && Finder::remove($origin);
		}
	}
	
	/**
	 * ファイル、またはディレクトリがあるかどうかを判断します。
	 * 
	 * <code>
	 * 
	 * if (Finder::exists('./sample.txt')) {
	 *     echo 'true';
	 * } else {
	 *     echo 'false';
	 * }
	 * 
	 * </code>
	 * 
	 * @access     public
	 * @static
	 * @return     bool 存在する場合はtrueを、存在しない場合はfalseを返す。
	 * @param      string $path 判断を行うファイル、またはディレクトリのパス。
	 */
	function exists($path) {
		return file_exists($path);
	}
	
	/**
	 * ディレクトリかどうかを判断します。
	 * 
	 * <code>
	 * 
	 * if (Finder::isDir('./sample')) {
	 *     echo 'true';
	 * } else {
	 *     echo 'false';
	 * }
	 * 
	 * </code>
	 * 
	 * @access     public
	 * @static
	 * @return     bool ディレクトリの場合はtrueを、ディレクトリでない場合はfalseを返す。
	 * @param      string $path 判断をするディレクトリのパス。
	 */
	function isDir($path) {
		return is_dir($path);
	}

	/**
	 * ファイルかどうかを判断します。
	 * 
	 * <code>
	 * 
	 * if (Finder::isDir('./sample')) {
	 *     echo 'true';
	 * } else {
	 *     echo 'false';
	 * }
	 * 
	 * </code>
	 * 
	 * @access     public
	 * @static
	 * @return     bool ファイルの場合はtrueを、ファイルでない場合はfalseを返す。
	 * @param      string $path 判断をするファイルのパス。
	 */
	function isFile($path) {
		return is_file($path);
	}
	
	/**
	 * ファイルを読み込みます。
	 * 
	 * <code>
	 * 
	 * $data = Finder::read('./sample.txt');
	 * echo $data;
	 *
	 * </code>
	 * 
	 * @access     public
	 * @static
	 * @return     string 読み込んだファイルの内容を返します。失敗した場合はfalseを返します。
	 * @param      string $path 読み込むファイルのパス。
	 */
	function &read($path) {
		$origin = $path;
		
		// 存在しないパスを指定して場合はWarning（複製元）
		if (!file_exists($origin)) {
			trigger_error(sprintf('%s: No such file or directory.', $origin), E_USER_WARNING);
			return false;
		}
		
		$data = file_get_contents($path);
		return $data;
	}
	
	/**
	 * データをファイルに書き込みます。
	 * 
	 * <code>
	 * 
	 * $data = 'hogehoge';
	 * Finder::write($data);
	 *
	 * </code>
	 * 
	 * @access     public
	 * @static
	 * @return     bool 書き込みに成功した場合はtrueを、失敗した場合はfalseを返す。
	 * @param      string $path データの保存先パス。
	 * @param      string $data 書き込みを行うデータ。
	 */
	function write($path, $data) {
		$filename = basename($path);
		$createTo = dirname($path);
		
		// 存在しないディレクトリを指定して場合はWarning（複製元）
		if (!is_dir($createTo)) {
			trigger_error(sprintf('%s: The directory does not exists.', $createTo), E_USER_WARNING);
			return false;
		}
		
		if (!($fp = fopen($path, 'wb'))) {
			trigger_error(sprintf('%s: Permission Denied.', $createTo . '/' . $filename), E_USER_WARNING);
			return false;
		}
		
		if (fwrite($fp, $data) === false) {
			trigger_error(sprintf('%s: Write error.', $createTo . '/' . $filename), E_USER_WARNING);
			return false;
		}
		
		if (!fclose($fp)) {
			trigger_error(sprintf('%s: File closing error.', $createTo . '/' . $filename), E_USER_WARNING);
			return false;
		}
		
		return true;
	}
}
