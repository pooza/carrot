<?php
/**
 * @package org.carrot-framework
 * @subpackage log.file
 */

/**
 * ログファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSLogFile extends BSFile {
	private $entries = array();

	/**
	 * ログの内容を返す
	 *
	 * @access public
	 * @return string[][] ログの内容
	 */
	public function getEntries () {
		if (!$this->entries) {
			if ($this->isOpened()) {
				throw new BSFileException('%sは既に開いています。', $this);
			}

			foreach ($this->getLines() as $line) {
				$pattern = '/\[([^]]*)\] \[([^]]*)\] \[([^]]*)\] (.*)/';
				if (!preg_match($pattern, $line, $matches)) {
					continue;
				}
				$this->entries[] = array(
					'date' => $matches[1],
					'remote_host' => $matches[2],
					'priority' => $matches[3],
					'exception' => preg_match('/Exception$/', $matches[3]),
					'message' => $matches[4],
				);
			}
		}
		return $this->entries;
	}

	/**
	 * 基本情報を文字列で返す
	 *
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('ログファイル "%s"', $this->getPath());
	}
}

/* vim:set tabstop=4 ai: */
?>