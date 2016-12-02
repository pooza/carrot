<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.mime.header
 */

/**
 * Content-Dispositionヘッダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSContentDispositionMIMEHeader extends BSMIMEHeader {
	protected $name = 'Content-Disposition';

	/**
	 * ヘッダの内容からパラメータを抜き出す
	 *
	 * @access protected
	 */
	protected function parse () {
		parent::parse();
		if ($this['filename'] && ($part = $this->getPart()) && !$part->getFileName()) {
			$part->setFileName($this['filename']);
		}
	}
}

/* vim:set tabstop=4: */
