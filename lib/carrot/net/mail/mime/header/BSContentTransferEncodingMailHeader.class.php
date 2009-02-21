<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail.mime.header
 */

/**
 * Content-Transfer-Encodingメールヘッダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
class BSContentTransferEncodingMailHeader extends BSMailHeader {

	/**
	 * 内容を設定
	 *
	 * @access public
	 * @param mixed $contents 内容
	 */
	public function setContents ($contents) {
		if ($contents instanceof BSRenderer) {
			$this->contents = BSMIMEUtility::getContentTransferEncoding($contents);
		} else {
			$this->contents = $contents;
		}
	}
}

/* vim:set tabstop=4: */
