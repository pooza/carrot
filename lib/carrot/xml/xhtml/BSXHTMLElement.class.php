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
			$name = $this->getTag();
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
		if (!is_array($classes) && !($classes instanceof BSParameterHolder)) {
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
	 * @return BSDivisionElement ラッパー要素
	 */
	public function setAlignment ($value) {
		$wrapper = $this->createWrapper();
		if ($this->getUserAgent()->isMobile()) {
			$wrapper->setAttribute('align', $value);
		} else {
			if ($value == 'center') {
				$wrapper->setStyle('width', '100%');
			}
			$wrapper->registerStyleClass($value);
		}
		return $wrapper;
	}

	/**
	 * コンテナのキャプションを設定
	 *
	 * @access public
	 * @param string $value キャプション
	 * @param integer $height 高さ
	 * @return BSDivisionElement ラッパー要素
	 */
	public function setCaption ($value, $height = 32) {
		$wrapper = $this->createWrapper();
		if (!BSString::isBlank($value)) {
			$element = $wrapper->addElement(new BSDivisionElement);
			if ($this->getUserAgent()->isMobile()) {
				$element->setAttribute('align', 'center');
				$element = $element->createElement('font');
				$element->setAttribute('size', '-1');
				$element->setAttribute('color', '#888888');
				$value .= '<br/><br/>';
			} else {
				$element->registerStyleClass('caption');
				if ($wrapper->getStyle('height')) {
					$wrapper->setStyle('height', $wrapper->getStyle('height') + $height);
				}
			}
			$element->setBody($value);
		}
		return $wrapper;
	}

	/**
	 * コンテナの説明文を設定
	 *
	 * @access public
	 * @param string $value 説明文
	 * @param BSArray $tags スマートタグのクラス名の配列
	 * @param integer $height 高さ
	 * @return BSDivisionElement ラッパー要素
	 */
	public function setDescription ($value, BSArray $tags = null, $height = 64) {
		if (BSString::isBlank($value)) {
			return $this;
		}
		if ($tags) {
			$value = BSSmartTag::parse($value, $tags, new BSArray);
		}

		$wrapper = $this->createWrapper();
		$element = $wrapper->addElement(new BSDivisionElement);
		if ($this->getUserAgent()->isMobile()) {
			$element->setAttribute('align', 'left');
			$element = $element->createElement('font');
			$element->setAttribute('size', '-1');
			$element->setAttribute('color', '#888888');
		} else {
			$element->registerStyleClass('description');
			$element->registerStyleClass('clearfix');
			if (($style = $wrapper->getStyle('height')) && ($style != 'auto')) {
				$wrapper->setStyle('height', $wrapper->getStyle('height') + $height);
			}
		}
		$element->setBody($value);
		return $wrapper;
	}

	/**
	 * div要素のラッパーを返す
	 *
	 * @access protected
	 * @return BSDivisionElement ラッパー要素
	 */
	protected function createWrapper () {
		return $this->wrap(new BSDivisionElement);
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
	 * 子要素を生成して返す
	 *
	 * @access public
	 * @param string $name 要素名
	 * @param string $body 要素の本文
	 * @return BSXMLElement 要素
	 */
	public function createElement ($name, $body = null) {
		$element = $this->addElement(new BSXHTMLElement($name, $this->getUserAgent()));
		$element->setBody($body);
		return $element;
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
		$parent->setUserAgent($this->getUserAgent());
		return $parent;
	}
}

/* vim:set tabstop=4: */
