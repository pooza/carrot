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
		return openlog('carrot', LOG_PID | LOG_PERROR, LOG_LOCAL6);
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
