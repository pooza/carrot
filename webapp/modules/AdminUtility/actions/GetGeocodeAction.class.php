<?php
/**
 * GetGeocodeアクション
 *
 * @package org.carrot-framework
 * @subpackage AdminUtility
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class GetGeocodeAction extends BSAction {
	public function execute () {
		$maps = new BSGoogleMapsService;
		if (!$geocode = $maps->getGeocode($this->request['addr'])) {
			return BSView::ERROR;
		}

		$json = new BSResultJSONRenderer;
		$json->setContents(new BSArray(array(
			'lat' => $geocode['lat'],
			'lng' => $geocode['lng'],
		)));
		$this->request->setAttribute('renderer', $json);
		return BSView::SUCCESS;
	}
}

/* vim:set tabstop=4: */