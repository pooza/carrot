<?php
/**
 * @package org.carrot-framework
 * @subpackage log
 */

/**
 * 抽象ロガー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSLogger {
	const DEFAULT_PRIORITY = 'Info';

	/**
	 * 初期化
	 *
	 * @access public
	 * @return string 利用可能ならTrue
	 * @abstract
	 */
	abstract public function initialize ();

	/**
	 * ログを出力
	 *
	 * @access public
	 * @param string $message ログメッセージ
	 * @param string $priority 優先順位
	 * @abstract
	 */
	abstract public function put ($message, $priority = self::DEFAULT_PRIORITY);

	/**
	 * 直近日を返す
	 *
	 * @access public
	 * @return BSDate 直近日
	 */
	public function getLastDate () {
		if ($month = $this->getDates()->getIterator()->getFirst()) {
			if ($date = $month->getIterator()->getFirst()) {
				return new BSDate($date);
			}
		}
	}

	/**
	 * 日付の配列を返す
	 *
	 * @access public
	 * @return BSArray 日付の配列
	 */
	public function getDates () {
		throw new BSLogException('%sはgetDatesに対応していません。', get_class($this));
	}

	/**
	 * エントリーを抽出して返す
	 *
	 * @access public
	 * @param string BSDate 対象日付
	 * @return BSArray エントリーの配列
	 */
	public function getEntries (BSDate $date) {
		throw new BSLogException('%sはgetEntriesに対応していません。', get_class($this));
	}

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

/* vim:set tabstop=4: */
