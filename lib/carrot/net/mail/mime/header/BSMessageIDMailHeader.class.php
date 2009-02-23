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
				BSNumeric::getRandom(),
				BS_SMTP_HOST
			);
		} else {
			preg_match('/^<?([^>]*)>?$/', $contents, $matches);
			$this->id = $matches[1];
		}
		$this->contents = '<' . $this->id . '>';
	}
}

/* vim:set tabstop=4: */
