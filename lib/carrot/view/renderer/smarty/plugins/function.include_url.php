<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * 外部コンテンツをインクルード
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
function smarty_function_include_url ($params, &$smarty) {
	$params = new BSArray($params);

	if (BSString::isBlank($params['src'])) {
		$url = BSURL::create($params, 'carrot');
	} else {
		$url = BSURL::create($params['src']);
	}
	if (!$url) {
		return null;
	}

	if (!$url['host']->isForeign(BSController::getInstance()->getHost())) {
		$url->setUserAgent($smarty->getUserAgent());
	}
	return $url->fetch();
}

/* vim:set tabstop=4: */
