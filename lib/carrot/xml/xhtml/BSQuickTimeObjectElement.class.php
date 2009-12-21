<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.xhtml
 */

/**
 * QuickTime用object要素
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSQuickTimeObjectElement extends BSObjectElement {

	/**
	 * @access public
	 * @param string $name 要素の名前
	 * @param BSUserAgent $useragent 対象UserAgent
	 */
	public function __construct ($name = null, BSUserAgent $useragent = null) {
		parent::__construct($name, $useragent);
		$this->inner = $this->createElement('embed');
		$this->setAttribute('classid', 'clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B');
		$this->setAttribute('type', BSMIMEType::getType('mov'));
		$this->setParameter('controller', 'true');
		$this->setParameter('autoplay', 'false');
		$this->setParameter('scale', 'aspect');
	}

	/**
	 * URLを設定
	 *
	 * @access public
	 * @param BSHTTPRedirector $url FlashムービーのURL
	 */
	public function setURL (BSHTTPRedirector $url) {
		$this->setParameter('src', $url->getContents());
		$this->inner->setAttribute('src', $url->getContents());
	}
}

/* vim:set tabstop=4: */
