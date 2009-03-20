<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mime.header
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
			$contents = $contents->format('r');
		}
		parent::setContents($contents);
	}

	/**
	 * ヘッダの内容からパラメータを抜き出す
	 *
	 * @access protected
	 */
	protected function parseParameters () {
		parent::parseParameters();
		try {
			$this->date = new BSDate($this->contents);
		} catch (BSDateException $e) {
		}
	}
}

/* vim:set tabstop=4: */
