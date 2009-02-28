<?php
/**
 * @package org.carrot-framework
 * @subpackage console
 */

/**
 * プロセス関連のユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSProcess {

	/**
	 * @access private
	 */
	private function __construct () {
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
	 * pidは存在するか？
	 *
	 * @access public
	 * @param integer プロセスID
	 * @return boolean pidが存在するならTrue
	 * @static
	 */
	static public function isExists ($pid) {
		$command = new BSCommandLine('/bin/ps');
		$command->addValue('ax');
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

/* vim:set tabstop=4: */
