<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage xml.xhtml
 */

/**
 * audio要素
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSAudioElement extends BSXHTMLElement {

	/**
	 * @access public
	 * @param string $name 要素の名前
	 * @param BSUserAgent $useragent 対象UserAgent
	 */
	public function __construct ($name = null, BSUserAgent $useragent = null) {
		parent::__construct($name, $useragent);
		$this->setAttribute('controls', 'controls');
		$this->setAttribute('autobuffer', 'autobuffer');
		$this->createElement('p', 'HTML5 Audioに対応したブラウザをご利用ください。');
	}

	/**
	 * ソースを登録
	 *
	 * @access public
	 * @param BSHTTPRedirector $url メディアのURL
	 */
	public function registerSource (BSHTTPRedirector $url) {
		$element = $this->createElement('source');
		$element->setEmptyElement(true);
		$element->setAttribute('src', $url->getContents());
	}

	/**
	 * URLを設定
	 *
	 * @access public
	 * @param BSHTTPRedirector $url メディアのURL
	 */
	public function setURL (BSHTTPRedirector $url) {
		$this->registerSource($url);
	}
}

/* vim:set tabstop=4: */
