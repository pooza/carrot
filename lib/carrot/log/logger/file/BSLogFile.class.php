<?php
/**
 * @package org.carrot-framework
 * @subpackage log.logger.file
 */

/**
 * ログファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSLogFile extends BSFile {
	private $entries = [];

	/**
	 * バイナリファイルか？
	 *
	 * @access public
	 * @return boolean バイナリファイルならTrue
	 */
	public function isBinary () {
		return false;
	}

	/**
	 * ログの内容を返す
	 *
	 * @access public
	 * @return string[][] ログの内容
	 */
	public function getEntries () {
		if (!$this->entries) {
			if ($this->isOpened()) {
				throw new BSFileException($this . 'は既に開いています。');
			}

			foreach ($this->getLines() as $line) {
				$pattern = '\[([^]]*)\] \[([^]]*)\] \[([^]]*)\] (.*)';
				if (!mb_ereg($pattern, $line, $matches)) {
					continue;
				}
				$this->entries[] = [
					'date' => $matches[1],
					'remote_host' => $matches[2],
					'priority' => $matches[3],
					'exception' => mb_ereg('Exception$', $matches[3]),
					'message' => $matches[4],
				];
			}
		}
		return $this->entries;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('ログファイル "%s"', $this->getShortPath());
	}
}

/* vim:set tabstop=4: */
