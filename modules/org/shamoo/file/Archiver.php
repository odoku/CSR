<?php
/**
 * Archive.php
 * 
 * このファイルにはArchiveクラスに関する定義が記述されています。<br/>
 * このファイルを読み込むことによりArchiveクラスの使用が可能になります。
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
 * zipなどのアーカイブを扱う為のクラス
 * 
 * @package    modules
 * @subpackage file
 * @author     Masashi Onogawa <m.onogawa@gmail.com>
 * @copyright  Copyright © 2009, shamoo.org
 * @license    shamoo.org License Ver. 1.0
 * @version    1.0
 * @access     public
 */
class Archiver {
	/**
	 * 指定したファイル、またはディレクトリをzip形式で圧縮します。
	 *
	 * このメソッドは内部でUNIXのzipコマンドを呼び出しています。
	 *
	 * <code>
	 * 
	 * Archiver::zip('./sample.jpg');
	 * 
	 * </code>
	 * 
	 * @access     public
	 * @static
	 * @return     bool 成功した場合はtrue、失敗した場合はfalseを返す
	 * @param      string $path 圧縮するファイル、またはディレクトリのパス
	 */
	function zip($path) {
		if (!file_exists($path)) {
			trigger_error(sprintf('%s: No such file or directory.', $path), E_USER_WARNING);
			return false;
		}

		$path     = realpath($path);
		$dirname  = dirname ($path);
		$basename = basename($path);
		
		$cmd = array();
		$cmd[] = escapeshellcmd(sprintf('cd %s', $dirname));
		$cmd[] = escapeshellcmd(sprintf('zip -r %s.zip %s', $basename, $basename));
		$cmd[] = escapeshellcmd(sprintf('cd -'));
		exec(implode('; ', $cmd), $output, $return);
		
		return $return == 0;
	}
	
	/**
	 * zipファイルを解凍します
	 *
	 * このメソッドは内部でUNIXのzipコマンドを呼び出しています。
	 *
	 * <code>
	 * 
	 * Archiver::unzip('./sample.jpg.zip');
	 * 
	 * </code>
	 * 
	 * @access     public
	 * @static
	 * @return     bool 成功した場合はtrue、失敗した場合はfalseを返す
	 * @param      string $path 解凍するzipファイルのパス
	 */
	
	function unzip($path) {
		if (!file_exists($path)) {
			trigger_error(sprintf('%s: No such file or directory.', $path), E_USER_WARNING);
			return false;
		}
		
		$path      = realpath($path);
		$dirname   = dirname ($path);
		$basename  = basename($path);

		if (file_exists($dirname . '/' . preg_replace('/\.zip$/i', '', $basename))) {
			trigger_error(sprintf('%s: The file already exists.', $dirname . '/' . preg_replace('/\.zip$/i', '', $basename)), E_USER_WARNING);
			return false;
		}
		
		$cmd = array();
		$cmd[] = escapeshellcmd(sprintf('cd %s', $dirname));
		$cmd[] = escapeshellcmd(sprintf('unzip %s', $basename));
		$cmd[] = escapeshellcmd(sprintf('cd -'));
		exec(implode('; ', $cmd), $output, $return);
		
		return $return == 0;
	}
}