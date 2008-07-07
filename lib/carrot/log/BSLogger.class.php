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
	 * 最終月を返す
	 *
	 * @access public
	 * @return string 最終月をyyyy-mm形式で
	 */
	public function getLastMonth () {
		return $this->getMonths()->getIterator()->getFirst();
	}

	/**
	 * 月の配列を返す
	 *
	 * @access public
	 * @return BSArray 月の配列
	 * @abstract
	 */
	abstract public function getMonths ();

	/**
	 * エントリーを抽出して返す
	 *
	 * @access public
	 * @param string $month yyyy-mm形式の月
	 * @return BSArray エントリーの配列
	 * @abstract
	 */
	abstract public function getEntries ($month);

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