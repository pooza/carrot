<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage log.file
 */

/**
 * ファイル用ロガー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSFileLogger extends BSLogger {
	private $file;

	/**
	 * コンストラクタ
	 *
	 * @access public
	 */
	public function __construct () {
		$dir = new BSLogDirectory(BS_VAR_DIR . '/log'); //BSDirectoryFinderは使わない。

		$name = BSDate::getNow('Y-m-d');
		if (!$this->file = $dir->getEntry($name)) {
			$this->file = $dir->createEntry($name);
			$this->file->setMode(0666);
		}
		$this->file->open('a');
	}

	/**
	 * デストラクタ
	 *
	 * @access public
	 */
	public function __destruct () {
		$this->file->close();
	}

	/**
	 * ログを出力する
	 *
	 * @access public
	 * @param string $message ログメッセージ
	 * @param string $priority 優先順位
	 */
	public function put ($message, $priority = self::DEFAULT_PRIORITY) {
		$message = array(
			'[' . BSDate::getNow('Y-m-d H:i:s') . ']',
			'[' . BSController::getInstance()->getClientHost()->getName() . ']',
			'[' . $priority . ']',
			BSString::convertEncoding($message),
		);
		$this->file->putLine(implode(' ', $message));
	}
}

/* vim:set tabstop=4 ai: */
?>