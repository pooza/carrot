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
				$fields = new BSArray(mb_split('\s+', $line));
				$date = BSDate::create($fields[0]);
				if (mb_ereg('\[client ([.0-9]+):[0-9]+\] (AH[0-9]+): (.*)$', $line, $matches)) {
					$remoteaddr = $matches[1];
					$priority = $matches[2];
					$exception = false;
					$message = $matches[3];
				} else if (mb_ereg('\[client ([.0-9]+):[0-9]+\] (.*)$', $line, $matches)) {
					$remoteaddr = $matches[1];
					$priority = null;
					$exception = false;
					$message = $matches[2];
				} else {
					$remoteaddr = $fields[5];
					$priority = $fields[4];
					$exception = mb_ereg('Exception$', $priority);
					foreach (range(0, 5) as $i) {
						$fields->removeParameter($i);
					}
					$message = $fields->join(' ');
				}
				$this->entries[] = [
					'date' => $date->format(),
					'remote_host' => (new BSHost($remoteaddr))->resolveReverse(),
					'priority' => $priority,
					'exception' => $exception,
					'message' => $message,
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
