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
			foreach ($this->getLines() as $line) {
				if (!mb_ereg('([0-9]{1,3}\.){3}[0-9]{1,3}', $line, $matches)) {
					continue;
				}
				$remoteaddr = $matches[0];
				$line = mb_ereg_replace('\[(client|server) [^\]]+\] ', null, $line);
				$fields = new BSArray(mb_split('\s+', $line));
				$date = BSDate::create($fields[0]);
				$fields->removeParameter(0);
				$fields->removeParameter(1);
				$fields->removeParameter(2);
				$this->entries[] = [
					'date' => $date->format(),
					'remote_host' => (new BSHost($remoteaddr))->resolveReverse(),
					'exception' => mb_ereg('Exception', $line),
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

