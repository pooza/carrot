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
	private $flashvars;

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
		$this->setParameter('wmode', 'transparent');
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

	private function setParameter ($name, $value) {
		foreach ($this->elements as $index => $element) {
			if (($element->getName() == 'param') && ($element->getAttribute('name') == $name)) {
				$this->elements->removeParameter($index);
			}
		}
		if (BSString::isBlank($value)) {
			return;
		}
		$param = $this->createElement('param');
		$param->setAttribute('name', $name);
		$param->setAttribute('value', $value);
	}
}

/* vim:set tabstop=4: */
