<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.xhtml.object
 */

/**
 * Flash用object要素
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSFlashObjectElement extends BSObjectElement {
	protected $flashvars;

	/**
	 * @access public
	 * @param string $name 要素の名前
	 * @param BSUserAgent $useragent 対象UserAgent
	 */
	public function __construct ($name = null, BSUserAgent $useragent = null) {
		parent::__construct($name, $useragent);
		$this->flashvars = new BSWWWFormRenderer;
		$this->setAttribute('width', '100%');
		$this->setAttribute('height', '100%');
		$this->setAttribute('type', BSMIMEType::getType('swf'));
		$this->createElement('p', 'Flash Player ' . BS_FLASH_PLAYER_VER . ' 以上が必要です。');
	}

	/**
	 * URLを設定
	 *
	 * @access public
	 * @param BSHTTPRedirector $url メディアのURL
	 */
	public function setURL (BSHTTPRedirector $url) {
		$this->setAttribute('data', $url->getContents());
		$this->setParameter('movie', $url->getContents());
	}

	/**
	 * FlashVarsを返す
	 *
	 * @access public
	 * @param string $name 変数の名前
	 * @return string 変数の値
	 */
	public function getFlashVar ($name) {
		return $this->flashvars[$name];
	}

	/**
	 * FlashVarsを設定
	 *
	 * @access public
	 * @param string $name 変数の名前
	 * @param string $value 変数の値
	 */
	public function setFlashVar ($name, $value) {
		$this->flashvars[$name] = $value;
		$this->contents = null;
	}

	/**
	 * 内容をXMLで返す
	 *
	 * @access public
	 * @return string XML要素
	 */
	public function getContents () {
		$this->setParameter('FlashVars', $this->flashvars->getContents());
		return parent::getContents();
	}
}

/* vim:set tabstop=4: */
