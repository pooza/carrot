<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * GoogleAnalytics関数
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
function smarty_function_analytics ($params, &$smarty) {
	$params = BSArray::create($params);
	$service = BSGoogleAnalyticsService::getInstance();
	if ($id = $params['id']) {
		$service->setID($id);
	}

	try {
		return $service->getTrackingCode();
	} catch (Exception $e) {
	}
}

