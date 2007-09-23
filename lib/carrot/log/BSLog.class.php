<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage log
 */

/**
 * 簡易ログ出力
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSLog {

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		// インスタンス化は禁止
	}

	/**
	 * ログ出力
	 *
	 * @access public
	 * @static
	 */
	public static function put ($message, $priority = 'Info') {
		$name = BSDate::getNow('Y-m-d');
		if (!$log = self::getDirectory()->getEntry($name)) {
			$log = self::getDirectory()->createEntry($name);
		}
		$log->open('a');
		$log->putLine(self::getMessage($message, $priority));
		$log->close();

		openlog('carrot', LOG_PID | LOG_PERROR, LOG_LOCAL6);
		if (preg_match('/Exception$/', $priority)) {
			syslog(LOG_ERR, $message);
		} else {
			syslog(LOG_NOTICE, $message);
		}
		closelog();
	}

	/**
	 * ログファイルのディレクトリを取得する
	 *
	 * @access private
	 * @return BSLogDirectory ログファイルのディレクトリ
	 * @static
	 */
	private static function getDirectory () {
		return BSController::getInstance()->getDirectory('log');
	}

	/**
	 * ログメッセージを返す
	 *
	 * @access public
	 * @static
	 */
	public static function getMessage ($message, $priority) {
		$message = array(
			'[' . BSDate::getNow('Y-m-d H:i:s') . ']',
			'[' . BSController::getInstance()->getClientHost()->getName() . ']',
			'[' . $priority . ']',
			BSString::convertEncoding($message),
		);
		return implode(' ', $message);
	}
}

/* vim:set tabstop=4 ai: */
?>