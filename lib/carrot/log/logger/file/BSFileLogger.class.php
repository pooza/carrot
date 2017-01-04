<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage log.logger.file
 */

/**
 * ファイル用ロガー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSFileLogger extends BSLogger {
	private $dates;
	private $entries;
	private $file;

	/**
	 * @access public
	 */
	public function __destruct () {
		$this->file->close();
	}

	/**
	 * 初期化
	 *
	 * @access public
	 * @return string 利用可能ならTrue
	 */
	public function initialize () {
		try {
			$name = BSDate::getNow('Y-m-d');
			if (!$this->file = $this->getDirectory()->getEntry($name)) {
				$this->file = $this->getDirectory()->createEntry($name);
				$this->file->setMode(0666);
			}
			$this->file->open('a');
			return true;
		} catch (BSFileException $e) {
			return false;
		}
	}

	/**
	 * ログディレクトリを返す
	 *
	 * @access public
	 * @return BSLogDirectory ログディレクトリ
	 */
	public function getDirectory () {
		return BSFileUtility::getDirectory('log');
	}

	/**
	 * ログを出力
	 *
	 * @access public
	 * @param string $message ログメッセージ
	 * @param string $priority 優先順位
	 */
	public function put ($message, $priority) {
		$this->file->putLine(implode(' ', [
			'[' . date('Y-m-d H:i:s') . ']',
			'[' . $this->getClientHostName() . ']', 
			'[' . $priority . ']',
			$message,
		]));
	}

	/**
	 * 日付の配列を返す
	 *
	 * @access public
	 * @return BSArray 日付の配列
	 */
	public function getDates () {
		if (!$this->dates) {
			$this->dates = new BSArray;
			foreach ($this->getDirectory() as $file) {
				if (!$date = BSDate::create($file->getBaseName())) {
					continue;
				}
				$month = $date->format('Y-m');
				if (!$this->dates[$month]) {
					$this->dates[$month] = new BSArray;
				}
				$this->dates[$month][$date->format('Y-m-d')] = $date->format('Y-m-d(ww)');
			}
		}
		return $this->dates;
	}

	/**
	 * エントリーを抽出して返す
	 *
	 * @access public
	 * @param string BSDate 対象日付
	 * @return BSArray エントリーの配列
	 */
	public function getEntries (BSDate $date) {
		if (!$this->entries) {
			$this->entries = new BSArray;
			if ($month = $this->getDates()->getParameter($date->format('Y-m'))) {
				if ($month->hasParameter($name = $date->format('Y-m-d'))) {
					$file = $this->getDirectory()->getEntry($name);
					$this->entries->setParameters($file->getEntries());
				}
			}
		}
		return $this->entries;
	}
}

/* vim:set tabstop=4: */
