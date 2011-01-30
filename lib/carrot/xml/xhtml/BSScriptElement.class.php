<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.xhtml
 */

/**
 * script要素
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSScriptElement extends BSXHTMLElement {

	/**
	 * @access public
	 * @param string $name 要素の名前
	 * @param BSUserAgent $useragent 対象UserAgent
	 */
	public function __construct ($name = null, BSUserAgent $useragent = null) {
		parent::__construct($name, $useragent);
		$this->setAttribute('type', 'text/javascript');

		if (!$this->useragent->isMobile()) {
			$this->setAttribute('charset', 'utf-8');
		}
	}
}

/* vim:set tabstop=4: */
