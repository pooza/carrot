<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage log
 */

/**
 * syslog用ロガー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSSystemLogger extends BSLogger {

	/**
	 * コンストラクタ
	 *
	 * @access public
	 */
	public function __construct () {
		$constants = BSConstantHandler::getInstance();
		if ($facility = $constants['LOG_SYSLOG_FACILITY']) {
			$facility = $constants[$facility];
		} else {
			$facility = LOG_LOCAL6;
		}
		openlog('carrot', LOG_PID | LOG_PERROR, $facility);
	}

	/**
	 * デストラクタ
	 *
	 * @access public
	 */
	public function __destruct () {
		closelog();
	}

	/**
	 * ログを出力する
	 *
	 * @access public
	 * @param string $message ログメッセージ
	 * @param string $priority 優先順位
	 */
	public function put ($message, $priority = self::DEFAULT_PRIORITY) {
		$message = sprintf('[%s] %s', $priority, $message);

		if ($this->isException($priority)) {
			syslog(LOG_ERR, $message);
		} else {
			syslog(LOG_NOTICE, $message);
		}
	}

	/**
	 * 月の配列を返す
	 *
	 * @access public
	 * @return BSArray 月の配列
	 */
	public function getMonths () {
		$month = BSDate::getNow('Y-m');
		return new BSArray(array($month => $month));
	}

	/**
	 * エントリーを抽出して返す
	 *
	 * @access public
	 * @param string $month yyyy-mm形式の月
	 * @return BSArray エントリーの配列
	 */
	public function getEntries ($month) {
		return new BSArray;
	}
}

/* vim:set tabstop=4 ai: */
?>