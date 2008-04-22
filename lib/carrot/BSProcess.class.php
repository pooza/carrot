<?php
/**
 * @package jp.co.b-shock.carrot
 */

/**
 * プロセス関連のユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSProcess.class.php 191 2008-04-14 13:30:35Z pooza $
 */
class BSProcess {

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		// インスタンス化は禁止
	}

	/**
	 * 現在のプロセスIDを返す
	 *
	 * @access public
	 * @static
	 */
	public static function getCurrentID () {
		return getmypid();
	}

	/**
	 * pidは存在するか
	 *
	 * @access public
	 * @param integer プロセスID
	 * @return boolean pidが存在するならTrue
	 * @static
	 * @todo あんまり外部コマンドに頼りたくないなぁ...
	 */
	public static function isExist ($pid) {
		foreach (explode("\n", shell_exec('ps ax')) as $process) {
			$fields = preg_split('/ +/', trim($process));
			if ($fields[0] == $pid) {
				return true;
			}
		}
		return false;
	}
}

/* vim:set tabstop=4 ai: */
?>