<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.xhtml
 */

/**
 * WindowsMedia用object要素
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSWindowsMediaObjectElement extends BSObjectElement {

	/**
	 * @access public
	 * @param string $name 要素の名前
	 * @param BSUserAgent $useragent 対象UserAgent
	 */
	public function __construct ($name = null, BSUserAgent $useragent = null) {
		parent::__construct($name, $useragent);
		$this->inner = $this->createElement('embed');
		$this->setAttribute('classid', 'clsid:6BF52A52-394A-11d3-B153-00C04F79FAA6');
		$this->setAttribute('type', BSMIMEType::getType('wmv'));
		$this->setParameter('autostart', '0');
	}

	/**
	 * URLを設定
	 *
	 * @access public
	 * @param BSHTTPRedirector $url FlashムービーのURL
	 */
	public function setURL (BSHTTPRedirector $url) {
		$this->setParameter('url', $url->getContents());
		$this->inner->setAttribute('src', $url->getContents());
	}
}

/* vim:set tabstop=4: */
