<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage console
 */

/**
 * プロセス関連のユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
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
	static public function getCurrentID () {
		return getmypid();
	}

	/**
	 * pidは存在するか
	 *
	 * @access public
	 * @param integer プロセスID
	 * @return boolean pidが存在するならTrue
	 * @static
	 */
	static public function isExist ($pid) {
		$command = new BSCommandLine('/bin/ps');
		$command->addValue('ax', null);
		if ($command->hasError()) {
			throw new BSConsoleException($command->getResult());
		}

		foreach ($command->getResult() as $line) {
			$fields = preg_split('/ +/', $line);
			if ($fields[0] == $pid) {
				return true;
			}
		}
		return false;
	}
}

/* vim:set tabstop=4 ai: */
?>