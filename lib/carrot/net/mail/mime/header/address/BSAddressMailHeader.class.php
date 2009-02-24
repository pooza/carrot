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
	protected $email;

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
			$contents = $contents->format();
		}
		parent::setContents($contents);
	}

	/**
	 * 内容を追加
	 *
	 * @access public
	 * @param string $contents 内容
	 */
	public function appendContents ($contents) {
		if ($contents instanceof BSMailAddress) {
			$contents = $contents->format();
		}
		parent::appendContents($contents);
	}

	/**
	 * ヘッダの内容からパラメータを抜き出す
	 *
	 * @access protected
	 */
	protected function parseParameters () {
		parent::parseParameters();
		try {
			$this->email = new BSMailAddress($this->contents);
		} catch (BSMailException $e) {
		}
	}
}

/* vim:set tabstop=4: */
