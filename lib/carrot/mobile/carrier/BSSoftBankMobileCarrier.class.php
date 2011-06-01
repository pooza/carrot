<?php
/**
 * @package org.carrot-framework
 * @subpackage mobile.carrier
 */

/**
 * SoftBank ケータイキャリア
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSSoftBankMobileCarrier extends BSMobileCarrier {

	/**
	 * ドメインサフィックスを返す
	 *
	 * @access public
	 * @return string ドメインサフィックス
	 */
	public function getDomainSuffix () {
		return 'softbank.ne.jp';
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
		$element->setURL('location:auto?url=' . $url->getContents());
		$element->setBody($label);
		return $element;
	}

	/**
	 * GPS情報を返す
	 *
	 * @access public
	 * @return BSArray GPS情報
	 */
	public function getGPSInfo () {
		$pos = BSRequest::getInstance()->getParameter('pos');
		if (mb_ereg('(N|S)([.[:digit:]]+)(E|W)([.[:digit:]]+)', $pos, $matches)) {
			if ($matches[1] == 'S') {
				$matches[2] *= -1;
			}
			if ($matches[3] == 'W') {
				$matches[4] *= -1;
			}
			return new BSArray(array(
				'lat' => BSGeocodeEntryHandler::dms2deg($matches[2]),
				'lng' => BSGeocodeEntryHandler::dms2deg($matches[4]),
			));
		}
	}
}

/* vim:set tabstop=4: */
