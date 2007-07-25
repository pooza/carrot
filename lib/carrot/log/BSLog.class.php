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
		if ($log = self::getDirectory()->getEntry(BSDate::getNow('Y-m-d'))) {
			$log->open('a');
		} else {
			$log = self::getDirectory()->createEntry(BSDate::getNow('Y-m-d'));
		}
		$log->putLine(self::getMessage($message, $priority));
		$log->close();

		if (BSController::getInstance()->isCLI()) {
			print self::getMessage($message, $priority) . "\n";
		}
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