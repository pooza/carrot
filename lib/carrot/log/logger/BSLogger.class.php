<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage log.logger
 */

/**
 * 抽象ロガー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
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
	abstract public function put ($message, $priority);

	/**
	 * サーバアントホスト名を返す
	 *
	 * BSController::getInstance()->getHost()->getName() が利用できない状況がある
	 *
	 * @access protected
	 * @return string サーバホスト名
	 */
	protected function getServerHostName () {
		return $_SERVER['SERVER_NAME'];
	}

	/**
	 * クライアントホスト名を返す
	 *
	 * BSRequest::getInstance()->getHost()->getName() が利用できない状況がある
	 *
	 * @access protected
	 * @return string クライアントホスト名
	 */
	protected function getClientHostName () {
		foreach (['HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $key) {
			if (isset($_SERVER[$key]) && ($value = $_SERVER[$key])) {
				try {
					return trim(mb_split('[:,]', $value)[0]);
				} catch (Exception $e) {
					return $value;
				}
			}
		}
	}

	/**
	 * 直近日を返す
	 *
	 * @access public
	 * @return BSDate 直近日
	 */
	public function getLastDate () {
		if ($month = $this->getDates()->getIterator()->getFirst()) {
			if ($date = $month->getIterator()->getFirst()) {
				return BSDate::create($date);
			}
		}
		return BSDate::create();
	}

	/**
	 * 日付の配列を返す
	 *
	 * @access public
	 * @return BSArray 日付の配列
	 */
	public function getDates () {
		throw new BSLogException(get_class($this) . 'はgetDatesに対応していません。');
	}

	/**
	 * エントリーを抽出して返す
	 *
	 * @access public
	 * @param string BSDate 対象日付
	 * @return BSArray エントリーの配列
	 */
	public function getEntries (BSDate $date) {
		throw new BSLogException(get_class($this) . 'はgetEntriesに対応していません。');
	}

	/**
	 * 例外か？
	 *
	 * @access protected
	 * @param string $priority 優先順位
	 * @return boolean 例外ならTrue
	 */
	protected function isException ($priority) {
		return mb_ereg('Exception$', $priority);
	}
}

/* vim:set tabstop=4: */
