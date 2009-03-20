<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mime.header.addresses
 */

/**
 * BCCMIMEヘッダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSBccMIMEHeader extends BSAddressesMIMEHeader {

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
