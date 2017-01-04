<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage log.logger
 */

/**
 * syslog用ロガー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSSystemLogger extends BSLogger {

	/**
	 * @access public
	 */
	public function __destruct () {
		closelog();
	}

	/**
	 * 初期化
	 *
	 * @access public
	 * @return string 利用可能ならTrue
	 */
	public function initialize () {
		$constants = new BSConstantHandler('LOG');
		if (!$facility = $constants['SYSLOG_FACILITY']) {
			$facility = 'LOCAL6';
		}
		return openlog('carrot', LOG_PID, $constants[$facility]);
	}

	/**
	 * ログを出力
	 *
	 * @access public
	 * @param string $message ログメッセージ
	 * @param string $priority 優先順位
	 */
	public function put ($message, $priority) {
		$format = new BSStringFormat('[%s] [%s] [%s] %s');
		$format[] = $this->getServerHostName();
		$format[] = $priority;
		$format[] = $this->getClientHostName();
		$format[] = $message;
		if ($this->isException($priority)) {
			syslog(LOG_ERR, $format->getContents());
		} else {
			syslog(LOG_NOTICE, $format->getContents());
		}
	}
}

/* vim:set tabstop=4: */
