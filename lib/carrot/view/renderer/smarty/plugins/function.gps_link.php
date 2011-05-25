<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * GPS対応のリンクを貼り付ける関数
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
function smarty_function_gps_link ($params, &$smarty) {
	$params = new BSArray($params);
	if ($useragent = $smarty->getUserAgent()) {
		if (BSString::isBlank($params['contents'])) {
			$url = BSURL::create($params, 'carrot');
		} else {
			$url = BSURL::create($params['contents']);
		}
		$url->setUserAgent($useragent);
		$element = $useragent->createGPSAnchorElement($url, $params['label']);
		return $element->getContents();
	}
}

/* vim:set tabstop=4: */
