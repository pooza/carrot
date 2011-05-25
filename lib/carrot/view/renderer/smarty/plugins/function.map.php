<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * GoogleMaps関数
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
function smarty_function_map ($params, &$smarty) {
	$params = new BSArray($params);
	try {
		$service = new BSGoogleMapsService;
		$service->setUserAgent($smarty->getUserAgent());
		if ($params['lat'] && ($params['lng'] || $params['lon'])) {
			if (BSString::isBlank($params['lng'])) {
				$params['lng'] = $params['lon'];
				$params->removeParameter('lon');
			}
			$addr = new BSStringFormat('lat=%s,lng=%s');
			$addr[] = $params['lat'];
			$addr[] = $params['lng'];
			$params['addr'] = $addr->getContents();
		}
		$element = $service->createElement($params['addr'], $params);
	} catch (Exception $e) {
		$element = new BSDivisionElement;
		$span = $element->addElement(new BSSpanElement);
		$span->registerStyleClass('alert');
		$span->setBody('ジオコードが取得できません。');
	}
	return $element->getContents();
}

/* vim:set tabstop=4: */
