<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage xml.xhtml
 */

/**
 * img要素
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSImageElement extends BSXHTMLElement {

	/**
	 * タグ名を返す
	 *
	 * @access public
	 * @return string タグ名
	 */
	public function getTag () {
		return 'img';
	}

	/**
	 * 対象UserAgentを設定
	 *
	 * @access public
	 * @param BSUserAgent $useragent 対象UserAgent
	 */
	public function setUserAgent (BSUserAgent $useragent) {
		parent::setUserAgent($useragent);
		if (!BS_IMAGE_STORABLE && $useragent->hasSupport('image_copyright')) {
			$this->setAttribute('copyright', 'yes');
		}
	}

	/**
	 * 空要素か？
	 *
	 * @access public
	 * @return boolean 空要素ならTrue
	 */
	public function isEmptyElement () {
		return true;
	}

	/**
	 * URLを設定
	 *
	 * @access public
	 * @param mixed $url
	 */
	public function setURL ($url) {
		if ($url instanceof BSHTTPRedirector) {
			$url = $url->getURL()->getContents();
		}
		$this->attributes['src'] = $url;
	}

	/**
	 * alt文字列を設定
	 *
	 * @access public
	 * @param string $value alt文字列
	 */
	public function setAlt ($value) {
		$this->attributes['alt'] = $value;
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
			case 'width':
			case 'height':
			case 'border':
			case 'align':
			case 'class':
			case 'style':
			case 'copyright':
				return parent::setAttribute($name, $value);
			case 'alt':
				return $this->setAlt($value);
			case 'href':
			case 'url':
			case 'src':
				return $this->setURL($value);
		}
	}
}

