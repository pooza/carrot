<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * GoogleMaps関数
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
function smarty_function_map ($params, &$smarty) {
	$params = new BSArray($params);
	$service = new BSGoogleMapsService;
	$service->setUserAgent($smarty->getUserAgent());
	$element = $service->getElement($params['addr'], $params);
	return $element->getContents();
}

/* vim:set tabstop=4: */
