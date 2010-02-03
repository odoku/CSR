<?php
/**
 * Shortcuts.php
 * 
 * このファイルには既存関数へのショートカット関数が宣言されています。<br/>
 * このファイルを読み込むことにより各ショートカット関数の使用が可能になります。
 * 
 * @package    modules
 * @subpackage functions
 * @author     Masashi Onogawa <m.onogawa@gmail.com>
 * @copyright  Copyright © 2009, shamoo.org
 * @license    shamoo.org License Ver. 1.0
 * @version    1.0
 * @access     public
 */

/**
 * array()のショートカット
 * 
 * <code>
 * 
 * $array = a(0, 1, 2);
 * 
 * </code>
 * 
 * @return     array 作成した配列
 * @param      mixed ... 配列に入れる値
 * 
 */
function a() {
	$args = func_get_args();
	return $args;
}


/**
 * echoのショートカット
 * 
 * <code>
 * 
 * e('Hello world!');
 * 
 * </code>
 * 
 * @return     void
 * @param      mixed $arg 出力する値
 * 
 */
function e($arg) {
	echo $arg;
}


/**
 * htmlspecialchars()のショートカット
 * 
 * <code>
 * 
 * echo h($_POST['message']);
 * 
 * </code>
 * 
 * @return     string 変換後の文字列
 * @param      string $string 変換する文字列
 * 
 */
function h($string) {
	return htmlspecialchars($string, ENT_QUOTES);
}


/**
 * print_r()のショートカット
 * 
 * <code>
 * 
 * pr(array('apple' => 'リンゴ', 'orange' => 'みかん'));
 * 
 * </code>
 * 
 * @return     void
 * @param      mixed ... 出力したい値
 * 
 */
function pr($var) {
	$args = func_get_args();
	echo "<pre>";
	call_user_func_array('print_r', $args);
	echo "</pre>";
	
}


/**
 * var_dumpのショートカット
 * 
 * <code>
 * 
 * vd(array('apple' => 'リンゴ', 'orange' => 'みかん'));
 * 
 * </code>
 * 
 * @return     void
 * @param      mixed ... 出力したい値
 * 
 */
function vd() {
	$args = func_get_args();
	echo "<pre>";
	call_user_func_array('var_dump', $args);
	echo "</pre>";
}