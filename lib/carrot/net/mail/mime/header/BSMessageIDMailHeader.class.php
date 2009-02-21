<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail.mime.header
 */

/**
 * Message-IDメールヘッダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
class BSMessageIDMailHeader extends BSMailHeader {
	private $id;

	/**
	 * 実体を返す
	 *
	 * @access public
	 * @return BSDate 実体
	 */
	public function getEntity () {
		return $this->id;
	}

	/**
	 * 内容を設定
	 *
	 * @access public
	 * @param mixed $contents 内容
	 */
	public function setContents ($contents) {
		if (BSString::isBlank($contents)) {
			$this->id = sprintf(
				'%s.%s@%s',
				BSDate::getNow('YmdHis'),
				BSUtility::getUniqueID(),
				BS_SMTP_HOST
			);
		} else {
			$this->id = $contents;
		}
		$this->contents = '<' . $this->id . '>';
	}
}

/* vim:set tabstop=4: */
