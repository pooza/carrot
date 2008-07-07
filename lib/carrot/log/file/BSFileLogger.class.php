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
	private $directory;

	/**
	 * コンストラクタ
	 *
	 * @access public
	 */
	public function __construct () {

		$name = BSDate::getNow('Y-m-d');
		if (!$this->file = $this->getDirectory()->getEntry($name)) {
			$this->file = $this->getDirectory()->createEntry($name);
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
	 * ログディレクトリを返す
	 *
	 * @access public
	 * @return BSLogDirectory ログディレクトリ
	 */
	public function getDirectory () {
		if (!$this->directory) {
			//BSDirectoryFinderは使わない。
			$this->directory = new BSLogDirectory(BS_VAR_DIR . '/log');
		}
		return $this->directory;
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

	/**
	 * 月の配列を返す
	 *
	 * @access public
	 * @return BSArray 月の配列
	 */
	public function getMonths () {
		$months = new BSArray;
		foreach ($this->getDirectory() as $file) {
			try {
				$date = new BSDate($file->getBaseName());
			} catch (BSDateException $e) {
			}
			$months[$date->format('Y-m')] = $date->format('Y-m');
		}
		$months->sort(BSArray::SORT_VALUE_DESC);
		return $months;
	}

	/**
	 * エントリーを抽出して返す
	 *
	 * @access public
	 * @param string $month yyyy-mm形式の月
	 * @return BSArray エントリーの配列
	 */
	public function getEntries ($month) {
		$entries = new BSArray;
		foreach ($this->getDirectory() as $file) {
			$entries->setParameters($file->getContents());
		}
		return $entries;
	}
}

/* vim:set tabstop=4 ai: */
?>