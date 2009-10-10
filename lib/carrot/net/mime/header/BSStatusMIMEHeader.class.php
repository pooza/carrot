<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mime.header
 */

/**
 * Statusヘッダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSStatusMIMEHeader extends BSMIMEHeader {

	/**
	 * 内容を設定
	 *
	 * @access public
	 * @param mixed $contents 内容
	 */
	public function setContents ($contents) {
		if (mb_ereg('^([[:digit:]]{3}) ', $contents, $matches)) {
			return $this->setContents($matches[1]);
		} else if (!is_numeric($contents)) {
			throw new BSHTTPException('ステータス"%s"は正しくありません。', $contents);
		}

		$this['code'] = $contents;
		if (BSString::isBlank($status = BSHTTP::getStatus($contents))) {
			throw new BSHTTPException('ステータス"%s"は正しくありません。', $contents);
		}
		parent::setContents($status);
	}
}

/* vim:set tabstop=4: */
