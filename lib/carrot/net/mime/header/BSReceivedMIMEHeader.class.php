<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mime.header
 */

/**
 * ReceivedMIMEヘッダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSReceivedMIMEHeader extends BSMIMEHeader {
	private $servers = array();

	/**
	 * 実体を返す
	 *
	 * @access public
	 * @return mixed 実体
	 */
	public function getEntity () {
		return new BSArray($this->servers);
	}

	/**
	 * 内容を返す
	 *
	 * @access public
	 * @return string 内容
	 */
	public function getContents () {
		return $this->getEntity()->join("\n");
	}

	/**
	 * 内容を設定
	 *
	 * @access public
	 * @param mixed $contents 内容
	 */
	public function setContents ($contents) {
		parent::setContents($contents);
		$this->servers[] = $this->contents;
	}

	/**
	 * 内容を追加
	 *
	 * @access public
	 * @param string $contents 内容
	 */
	public function appendContents ($contents) {
		parent::appendContents($contents);
		$this->servers[count($this->servers) - 1] = $this->contents;
	}

	/**
	 * 複数行を許容するか？
	 *
	 * @access public
	 * @return boolean 許容ならばTrue
	 */
	public function isMultiple () {
		return true;
	}

	/**
	 * 可視か？
	 *
	 * @access public
	 * @return boolean 可視ならばTrue
	 */
	public function isVisible () {
		return false;
	}
}

/* vim:set tabstop=4: */
