<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * GoogleAnalytics関数
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
function smarty_function_analytics ($params, &$smarty) {
	$params = BSArray::encode($params);
	$service = BSGoogleAnalyticsService::getInstance();
	if ($id = $params['id']) {
		$service->setID($id);
	}

	try {
		return $service->getTrackingCode();
	} catch (Exception $e) {
	}
}

/* vim:set tabstop=4: */
