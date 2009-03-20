<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mime.header.address
 */

/**
 * Return-Pathヘッダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSReturnPathMIMEHeader extends BSAddressMIMEHeader {

	/**
	 * ヘッダの内容からパラメータを抜き出す
	 *
	 * @access protected
	 */
	protected function parseParameters () {
		BSMIMEHeader::parseParameters();
		try {
			if (preg_match('/^<?([^>]*)>?$/', $this->contents, $matches)) {
				$this->email = new BSMailAddress($matches[1]);
			}
		} catch (BSMailException $e) {
		}
	}
}

/* vim:set tabstop=4: */
