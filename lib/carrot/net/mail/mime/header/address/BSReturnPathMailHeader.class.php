<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail.mime.header.address
 */

/**
 * Return-Pathメールヘッダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSReturnPathMailHeader extends BSAddressMailHeader {

	/**
	 * ヘッダの内容からパラメータを抜き出す
	 *
	 * @access protected
	 */
	protected function parseParameters () {
		BSMailHeader::parseParameters();
		try {
			if (preg_match('/^<?([^>]*)>?$/', $this->contents, $matches)) {
				$this->email = new BSMailAddress($matches[1]);
			}
		} catch (BSMailException $e) {
		}
	}
}

/* vim:set tabstop=4: */
