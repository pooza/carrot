<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage mobile.carrier
 */

/**
 * Docomo ケータイキャリア
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSDocomoMobileCarrier extends BSMobileCarrier {

	/**
	 * ドメインサフィックスを返す
	 *
	 * @access public
	 * @return string ドメインサフィックス
	 */
	public function getDomainSuffix () {
		return 'docomo.ne.jp';
	}

	/**
	 * GPS情報を取得するリンクを返す
	 *
	 * @access public
	 * @param BSHTTPRedirector $url 対象リンク
	 * @param string $label ラベル
	 * @return BSAnchorElement リンク
	 */
	public function createGPSAnchorElement (BSHTTPRedirector $url, $label) {
		$url = $url->createURL();
		$url['query'] = null;

		$element = new BSAnchorElement;
		$element->setURL($url);
		$element->setBody($label);
		$element->setAttribute('lcs', 'lcs');
		return $element;
	}
}

/* vim:set tabstop=4: */
