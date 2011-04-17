<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.xhtml
 */

/**
 * XHTMLの要素
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSXHTMLElement extends BSXMLElement {
	protected $tag;
	protected $useragent;
	protected $styles;
	protected $styleClasses;
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

		$this->styles = new BSCSSSelector;
		$this->styleClasses = new BSArray;
		parent::__construct($name);

		if (!$useragent) {
			$useragent = BSRequest::getInstance()->getUserAgent();
		}
		$this->setUserAgent($useragent);
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
	 * 対象UserAgentを返す
	 *
	 * @access public
	 * @return BSUserAgent 対象UserAgent
	 */
	public function getUserAgent () {
		return $this->useragent;
	}

	/**
	 * 対象UserAgentを設定
	 *
	 * @access public
	 * @param BSUserAgent $useragent 対象UserAgent
	 */
	public function setUserAgent (BSUserAgent $useragent) {
		$this->useragent = $useragent;
	}

	/**
	 * IDを返す
	 *
	 * @access public
	 * @return string ID
	 */
	public function getID () {
		return $this->attributes['id'];
	}

	/**
	 * IDを設定
	 *
	 * @access public
	 * @param string $id ID
	 */
	public function setID ($id) {
		if (BSString::isBlank($id)) {
			return;
		}
		$this->attributes['id'] = $id;
	}

	/**
	 * スタイルを返す
	 *
	 * @access public
	 * @param string $name スタイル名
	 * @return string スタイル値
	 */
	public function getStyle ($name) {
		return $this->styles[$name];
	}

	/**
	 * スタイルを設定
	 *
	 * @access public
	 * @param string $name スタイル名
	 * @param string $value スタイル値
	 */
	public function setStyle ($name, $value) {
		if (BSString::isBlank($value)) {
			$this->styles->removeParameter($name);
		} else {
			$this->styles[$name] = $value;
		}
		$this->contents = null;
	}

	/**
	 * スタイルを全て返す
	 *
	 * @access public
	 * @return BSCSSSelector スタイル
	 */
	public function getStyles () {
		return $this->styles;
	}

	/**
	 * スタイルを置き換え
	 *
	 * @access public
	 * @param mixed $styles スタイル
	 */
	public function setStyles ($styles) {
		if ($styles instanceof BSCSSSelector) {
			$this->styles = $styles;
		} else {
			$this->styles->clear();
			$this->styles->setContents($styles);
		}
		$this->contents = null;
	}

	/**
	 * CSSクラスを登録
	 *
	 * @access public
	 * @param mixed $classes クラス名、又はその配列
	 */
	public function registerStyleClass ($classes) {
		if (!is_array($classes) && !($classes instacneof BSParameterHolder)) {
			$classes = mb_split('(,| +)', $classes);
		}
		foreach ($classes as $class) {
			$this->styleClasses->push($class);
		}
		$this->styleClasses->uniquize();
		$this->styleClasses->trim();
	}

	/**
	 * コンテナの配置を設定して返す
	 *
	 * @access protected
	 * @param string $value 配置
	 * @return BSXHTMLElement ラッパー要素
	 */
	public function setAlignment ($value) {
		if ($this->getUserAgent()->isMobile()) {
			if ($container instanceof BSDivisionElement) {
				$container = $this;
			} else {
				$container = $this->wrap(new BSDivisionElement);
			}
			$container->setAttribute('align', $value);
		} else {
			if ($value == 'center') {
				$container = $this->wrap(new BSDivisionElement);
				$container->setStyle('width', '100%');
			} else {
				$container = $this;
			}
			$container->registerStyleClass($value);
		}
		return $container;
	}

	/**
	 * 内容をXMLで返す
	 *
	 * @access public
	 * @return string XML要素
	 */
	public function getContents () {
		if ($this->styles->count()) {
			$this->attributes['style'] = $this->styles->getContents();
		} else {
			$this->attributes->removeParameter('style');
		}
		if ($this->styleClasses->count()) {
			$this->attributes['class'] = $this->styleClasses->join(' ');
		} else {
			$this->attributes->removeParameter('class');
		}
		return parent::getContents();
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
			case 'id':
			case 'container_id':
				return $this->setID($value);
			case 'styles':
			case 'style':
				return $this->setStyles($value);
			case 'class':
			case 'style_class':
				return $this->registerStyleClass($value);
		}
		parent::setAttribute($name, $value);
	}

	/**
	 * 上位のタグでくくって返す
	 *
	 * @access public
	 * @param BSXMLElement $parent 上位の要素
	 * @return BSXMLElement 上位の要素
	 */
	public function wrap (BSXMLElement $parent) {
		$parent = parent::wrap($parent);
		$parent->setUserAgent($this->useragent);
		return $parent;
	}
}

/* vim:set tabstop=4: */
