<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.mime.header.address
 */

/**
 * Return-Pathヘッダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSReturnPathMIMEHeader extends BSAddressMIMEHeader {
	protected $name = 'Return-Path';

	/**
	 * ヘッダの内容からパラメータを抜き出す
	 *
	 * @access protected
	 */
	protected function parse () {
		BSMIMEHeader::parse();
		if (mb_ereg('^<?([^>]*)>?$', $this->contents, $matches)) {
			$this->email = BSMailAddress::create($matches[1]);
		}
	}
}

