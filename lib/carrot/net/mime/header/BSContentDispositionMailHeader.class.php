<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mime.header
 */

/**
 * Content-Dispositionメールヘッダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSContentDispositionMailHeader extends BSMailHeader {

	/**
	 * ヘッダの内容からパラメータを抜き出す
	 *
	 * @access protected
	 */
	protected function parseParameters () {
		parent::parseParameters();
		if ($this['filename'] && ($part = $this->getPart()) && !$part->getFileName()) {
			$part->setFileName($this['filename']);
		}
	}
}

/* vim:set tabstop=4: */
