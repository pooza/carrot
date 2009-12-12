<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.xhtml
 */

/**
 * XHTMLの要素
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSXHTMLElement extends BSXMLElement {
	protected $tag;
	protected $useragent;
	protected $raw = true;

	/**
	 * @access public
	 * @param string $name 要素の名前
	 * @param BSUserAgent $useragent 対象UserAgent
	 */
	public function __construct ($name = null, BSUserAgent $useragent = null) {
		if (BSString::isBlank($name)) {
			if (BSString::isBlank($name = $this->getTag())) {
				throw new BSXMLException('XHTMLのエレメント名が正しくありません。');
			}
		}
		parent::__construct($name, $useragent);
		if ($useragent) {
			$this->useragent = $useragent;
		} else {
			$this->useragent = BSRequest::getInstance()->getUserAgent();
		}
	}

	/**
	 * タグ名を返す
	 *
	 * @access public
	 * @return string タグ名
	 */
	public function getTag () {
		if (!$this->tag) {
			if (mb_ereg('^BS(.*)Element$', get_class($this), $matches)) {
				$this->tag = BSString::toLower($matches[1]);
			}
		}
		return $this->tag;
	}

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性名
	 * @param mixed $value 属性値
	 */
	public function setAttribute ($name, $value) {
		switch ($name) {
			case 'container_id':
				$name = 'id';
				break;
			case 'style':
				if ($value instanceof BSCSSSelector) {
					$value = $value->getContents();
				}
				break;
			case 'style_class':
				$name = 'class';
				//↓そのまま実行
			case 'class':
				if (BSString::isBlank($value) || $this->useragent->isMobile()) {
					return;
				}
				if ($value instanceof BSArray) {
					$value = $value->join(' ');
				}
				break;
		}
		parent::setAttribute($name, $value);
	}
}

/* vim:set tabstop=4: */
