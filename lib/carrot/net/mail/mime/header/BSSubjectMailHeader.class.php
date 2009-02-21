<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail.mime.header
 */

/**
 * Subjectメールヘッダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
class BSSubjectMailHeader extends BSMailHeader {

	/**
	 * 実体を返す
	 *
	 * @access public
	 * @return string 実体
	 */
	public function getEntity () {
		if (BS_DEBUG) {
			return '[TEST] ' . $this->getContents();
		}
		return $this->getContents();
	}
}

/* vim:set tabstop=4: */
