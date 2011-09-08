<?php
/**
 * @package org.carrot-framework
 * @subpackage service.google.sitemap
 */

/**
 * Google sitemap.xml サイトマップ文書
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @link http://www.google.com/support/webmasters/bin/answer.py?hl=ja&answer=183668
 */
class BSSiteMapXMLDocument extends BSXMLDocument {

	/**
	 * @access public
	 * @param string $name 要素の名前
	 */
	public function __construct ($name = null) {
		parent::__construct('urlset');
		$this->setDirty(true);
		$this->setNamespace('http://www.sitemaps.org/schemas/sitemap/0.9');
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
		$element = $this->addElement(new BSXMLElement('url'));
		$element->createElement('loc', $url->getURL()->getContents());
		if ($params) {
			foreach ($params as $key => $value) {
				if (!BSString::isBlank($value)) {
					if ($key == 'lastmod') {
						$date = BSDate::create($value);
						$value = $date->format(DateTime::W3C);
					}
					$element->createElement($key, $value);
				}
			}
		}
		return $element;
	}
}

/* vim:set tabstop=4: */
