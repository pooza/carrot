<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail.mime.header
 */

/**
 * Dateメールヘッダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSDateMailHeader extends BSMailHeader {
	private $date;

	/**
	 * 実体を返す
	 *
	 * @access public
	 * @return BSDate 実体
	 */
	public function getEntity () {
		return $this->date;
	}

	/**
	 * 内容を設定
	 *
	 * @access public
	 * @param mixed $contents 内容
	 */
	public function setContents ($contents) {
		if ($contents instanceof BSDate) {
			$this->date = $contents;
		} else {
			$this->date = new BSDate($contents);
		}
		$this->contents = $this->date->format('r');
	}
}

/* vim:set tabstop=4: */
