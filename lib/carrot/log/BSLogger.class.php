<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage log
 */

/**
 * 抽象ロガー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 * @abstract
 */
abstract class BSLogger {
	const DEFAULT_PRIORITY = 'Info';

	/**
	 * ログを出力する
	 *
	 * @access public
	 * @param string $message ログメッセージ
	 * @param string $priority 優先順位
	 * @abstract
	 */
	abstract public function put ($message, $priority = self::DEFAULT_PRIORITY);

	/**
	 * 例外か？
	 *
	 * @access protected
	 * @param string $priority 優先順位
	 * @return boolean 例外ならTrue
	 */
	protected function isException ($priority) {
		return preg_match('/Exception$/', $priority);
	}
}

/* vim:set tabstop=4 ai: */
?>