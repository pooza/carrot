<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage log
 */

/**
 * ログファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSLogFile extends BSFile {
	private $logs = array();

	/**
	 * ログの内容を返す
	 *
	 * @access public
	 * @return string[][] ログの内容
	 */
	public function getContents () {
		if (!$this->logs) {
			if ($this->isOpened()) {
				throw new BSFileException('%sは既に開いています。', $this);
			}

			foreach ($this->getLines() as $line) {
				$pattern = '/\[([^]]*)\] \[([^]]*)\] \[([^]]*)\] (.*)/';
				if (!preg_match($pattern, $line, $matches)) {
					continue;
				}
				$this->logs[] = array(
					'date' => $matches[1],
					'host' => $matches[2],
					'type' => $matches[3],
					'exception' => preg_match('/Exception$/', $matches[3]),
					'description' => $matches[4],
				);
			}
		}
		return $this->logs;
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