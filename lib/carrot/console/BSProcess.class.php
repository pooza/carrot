<?php
/**
 * @package org.carrot-framework
 * @subpackage console
 */

/**
 * プロセス関連のユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
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
	 * プロセス名からpidを返す
	 *
	 * @access public
	 * @param string $name プロセス名
	 * @return integer プロセスが存在するなら、そのpid
	 * @static
	 */
	static public function getID ($name) {
		$command = new BSCommandLine('bin/pgrep');
		$command->push($name);
		$command->setDirectory(BSFileUtility::getDirectory('proctools'));
		if ($command->hasError()) {
			$message = new BSStringFormat('実行時エラーです。(%s)');
			$message[] = $command->getContents();
			throw new BSConsoleException($message);
		}

		if ($result = $command->getResult()) {
			return (int)$result[0];
		}
	}

	/**
	 * 許可されたユーザーを返す
	 *
	 * @access public
	 * @return BSArray ユーザー名の配列
	 * @static
	 */
	static public function getAllowedUsers () {
		$users = new BSArray;
		foreach (array('_', '') as $prefix) {
			foreach (array('www', 'nobody') as $name) {
				$users[] = $prefix . $name;
			}
		}
		return $users;
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
		$command->push('ax');
		if ($command->hasError()) {
			throw new BSConsoleException($command->getResult());
		}

		foreach ($command->getResult() as $line) {
			$fields = mb_split(' +', trim($line));
			if ($fields[0] == $pid) {
				return true;
			}
		}
		return false;
	}
}

/* vim:set tabstop=4: */
