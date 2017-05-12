<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage service.google.sitemap
 */

/**
 * Google sitemap.xml サイトマップ文書（ケータイむけ）
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @link http://www.google.com/support/webmasters/bin/answer.py?hl=ja&answer=34648
 */
class BSMobileSiteMapXMLDocument extends BSSiteMapXMLDocument {

	/**
	 * @access public
	 * @param string $name 要素の名前
	 */
	public function __construct ($name = null) {
		parent::__construct('urlset');
		$this->setAttribute('xmlns:mobile', 'http://www.google.com/schemas/sitemap-mobile/1.0');
	}

	/**
	 * 登録を加える
	 *
	 * @access public
	 * @param BSHTTPRedirector $url 対象URL
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSXMLElement 追加されたurl要素
	 */
	public function register (BSHTTPRedirector $url, BSParameterHolder $params = null) {
		$element = parent::register($url, $params);
		$element->addElement(new BSXMLElement('mobile:mobile'));
		return $element;
	}
}

