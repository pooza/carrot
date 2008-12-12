<?php
/**
 * @package org.carrot-framework
 * @subpackage log
 */

/**
 * syslog用ロガー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSSystemLogger extends BSLogger {

	/**
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
	 * @access public
	 */
	public function __destruct () {
		closelog();
	}

	/**
	 * ログを出力
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
}

/* vim:set tabstop=4: */
