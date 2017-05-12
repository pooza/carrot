<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage xml.xhtml.object
 */

/**
 * QuickTime用object要素
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
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
		$this->setAttribute('classid', 'clsid:' . BS_MOVIE_QUICKTIME_PLAYER_CLSID);
		$this->setAttribute('type', BSMIMEType::getType('mov'));
		$this->setParameter('controller', 'true');
		$this->setParameter('autoplay', 'false');
		$this->setParameter('scale', 'aspect');
	}

	/**
	 * URLを設定
	 *
	 * @access public
	 * @param BSHTTPRedirector $url メディアのURL
	 */
	public function setURL (BSHTTPRedirector $url) {
		$this->setParameter('src', $url->getContents());
		$this->inner->setAttribute('src', $url->getContents());
		$this->setParameter('qtsrc', $url->getContents());
		$this->inner->setAttribute('qtsrc', $url->getContents());
	}
}

