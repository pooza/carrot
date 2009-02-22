<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail.mime.header.address
 */

/**
 * メールアドレスを格納する抽象メールヘッダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSAddressMailHeader extends BSMailHeader {
	private $email;

	/**
	 * 実体を返す
	 *
	 * @access public
	 * @return BSMailAddress 実体
	 */
	public function getEntity () {
		return $this->email;
	}

	/**
	 * 内容を設定
	 *
	 * @access public
	 * @param mixed $contents 内容
	 */
	public function setContents ($contents) {
		if ($contents instanceof BSMailAddress) {
			$this->email = $contents;
			$this->contents = $contents->format();
		} else {
			$this->email = new BSMailAddress($contents);
			$this->contents = $contents;
		}
	}
}

/* vim:set tabstop=4: */
