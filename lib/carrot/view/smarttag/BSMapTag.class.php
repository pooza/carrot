<?php
/**
 * @package org.carrot-framework
 * @subpackage view.smarttag
 */

/**
 * Google Mapタグ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMapTag extends BSSmartTag {
	private $geocode;

	/**
	 * タグ名を返す
	 *
	 * @access public
	 * @return string タグ名
	 */
	public function getTagName () {
		return 'map';
	}

	/**
	 * 置換して返す
	 *
	 * @access public
	 * @param string $body 置換対象文字列
	 * @return string 置換された文字列
	 */
	public function execute ($body) {
		try {
			if ($this->getUserAgent()->isMobile()) {
				$element = $this->getMobileAnchorElement();
			} else {
				$element = $this->getAjaxDivisionElement();
			}
			$replace = $element->getContents();
		} catch (Exception $e) {
			$replace = sprintf('[エラー: %s]', $e->getMessage());
		}
		return str_replace($this->getContents(), $replace, $body);
	}

	private function getMobileAnchorElement () {
		$url = BSGoogleMapsService::getURL($this->tag[1], $this->getUserAgent());
		$element = new BSAnchorElement;
		$element->setBody($this->tag[1]);
		$element->setURL($url);
		return $element;
	}

	private function getAjaxDivisionElement () {
		$params = $this->getQueryParameters();
		$geocode = $this->getGeocode();

		$element = new BSDivisionElement;
		$inner = $element->addElement(new BSDivisionElement);
		$script = $element->addElement(new BSScriptElement);

		$inner->setID('map_' . BSCrypt::getSHA1($this->tag[1] . BS_CRYPT_SALT));
		$inner->setStyle('width', $params['width']);
		$inner->setStyle('height', $params['height']);
		$inner->setBody('Loading...');

		$statement = 'actions.onload.push(function(){handleGoogleMaps($(%s), %f, %f);});';
		$statement = new BSStringFormat($statement);
		$statement[] = BSJavaScriptUtility::quote($inner->getID());
		$statement[] = $geocode['lat'];
		$statement[] = $geocode['lng'];
		$script->setBody($statement->getContents());

		return $element;
	}

	private function getGeocode () {
		if (!$this->geocode) {
			$service = new BSGoogleMapsService;
			if (!$this->geocode = $service->getGeocode($this->tag[1])) {
				throw new BSGeocodeException('ジオコードが取得できません。');
			}
		}
		return $this->geocode;
	}
}

/* vim:set tabstop=4: */
