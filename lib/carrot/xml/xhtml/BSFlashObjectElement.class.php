<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.xhtml
 */

/**
 * Flash用object要素
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSFlashObjectElement extends BSXHTMLElement {
	private $transparent;

	/**
	 * @access public
	 * @param string $name 要素の名前
	 * @param BSUserAgent $useragent 対象UserAgent
	 */
	public function __construct ($name = null, BSUserAgent $useragent = null) {
		parent::__construct($name, $useragent);
		$this->setAttribute('width', '100%');
		$this->setAttribute('height', '100%');
		$this->setAttribute('type', BSMIMEType::getType('swf'));
		$this->createElement('p', 'Flash Player ' . BS_FLASH_PLAYER_VER . ' 以上が必要です。');
		$this->registerParameterElement('wmode', 'transparent');
	}

	/**
	 * タグ名を返す
	 *
	 * @access public
	 * @return string タグ名
	 */
	public function getTag () {
		return 'object';
	}

	/**
	 * URLを設定
	 *
	 * @access public
	 * @param BSHTTPRedirector $url FlashムービーのURL
	 */
	public function setURL (BSHTTPRedirector $url) {
		if (!BSString::isBlank($this->getAttribute('data'))) {
			throw new BSFlashException('URLが設定済みです。');
		}
		$this->setAttribute('data', $url->getContents());
		$this->registerParameterElement('movie', $url->getContents());
	}

	private function registerParameterElement ($name, $value) {
		$param = $this->createElement('param');
		$param->setAttribute('name', $name);
		$param->setAttribute('value', $value);
	}
}

/* vim:set tabstop=4: */
