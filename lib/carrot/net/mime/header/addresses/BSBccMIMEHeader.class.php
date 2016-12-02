<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.mime.header.addresses
 */

/**
 * BCCヘッダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSBccMIMEHeader extends BSAddressesMIMEHeader {
	protected $name = 'Bcc';

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
