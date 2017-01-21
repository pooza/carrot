<?php
/**
 * @package jp.co.b-shock.carrot
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
				$fields = new BSArray(mb_split('\s+', $line));
				$date = BSDate::create($fields[0]);
				$remoteaddr = $fields[5];
				$priority = $fields[4];
				foreach (range(0, 5) as $i) {
					$fields->removeParameter($i);
				}
				$this->entries[] = [
					'date' => $date->format(),
					'remote_host' => (new BSHost($remoteaddr))->resolveReverse(),
					'priority' => $priority,
					'exception' => mb_ereg('Exception$', $fields[4]),
					'message' => $fields->join(' '),
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
