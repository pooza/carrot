<?php
/**
 * @package org.carrot-framework
 * @subpackage mobile.carrier
 */

/**
 * Docomo ケータイキャリア
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSDocomoMobileCarrier extends BSMobileCarrier {
	const LIST_FILE_NAME = 'docomo_agents.xml';

	/**
	 * @access public
	 */
	public function __construct () {
		parent::__construct();

		$file = BSFileUtility::getDirectory('config')->getEntry(self::LIST_FILE_NAME);
		if (!$file->getSerialized()) {
			$agents = new BSArray;
			$xml = new BSXMLDocument;
			$xml->setContents($file->getContents());
			foreach ($xml->getElements() as $element) {
				$agents[$element->getName()] = $element->getAttributes()->getParameters();
			}
			$agents->sort(BSArray::SORT_KEY_DESC);
			BSController::getInstance()->setAttribute($file, $agents);
		}
		$this['display_infos'] = $file->getSerialized();
	}

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
