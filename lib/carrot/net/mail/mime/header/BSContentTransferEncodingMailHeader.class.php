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
			$this->contents = self::getContentTransferEncoding($contents);
		} else {
			$this->contents = $contents;
		}
	}

	/**
	 * レンダラーのContent-Transfer-Encodingを返す
	 *
	 * @access public
	 * @param BSRenderer $renderer レンダラー
	 * @return string Content-Transfer-Encoding
	 * @static
	 */
	static public function getContentTransferEncoding (BSRenderer $renderer) {
		if ($renderer instanceof BSTextRenderer) {
			if (strtolower($renderer->getEncoding()) == 'iso-2022-jp') {
				return '7bit';
			}
			return '8bit';
		}
		return 'base64';
	}
}

/* vim:set tabstop=4: */
