<?php
/**
 * @package org.carrot-framework
 * @subpackage mobile.carrier
 */

/**
 * Au ケータイキャリア
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSAuMobileCarrier extends BSMobileCarrier {

	/**
	 * ドメインサフィックスを返す
	 *
	 * @access public
	 * @return string ドメインサフィックス
	 */
	public function getDomainSuffix () {
		return 'ezweb.ne.jp';
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
		$url = clone $url->getURL();
		$url['query'] = null;

		$query = new BSWWWFormRenderer;
		$query['url'] = $url->getContents();
		$query['ver'] = 1;
		$query['datum'] = 0;
		$query['unit'] = 0;
		$query['acry'] = 0;
		$query['number'] = 0;

		$element = new BSAnchorElement;
		$element->setURL('device:gpsone?' . $query->getContents());
		$element->setBody($label);
		return $element;
	}
}

/* vim:set tabstop=4: */
