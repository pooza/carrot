<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * JavaScriptキャッシュ関数
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
function smarty_function_js_cache ($params, &$smarty) {
	$params = new BSArray($params);
	if (BSString::isBlank($params['name'])) {
		$params['name'] = 'carrot';
	}

	$url = BSURL::getInstance(null, 'carrot');
	$url['module'] = 'Default';
	$url['action'] = 'JavaScript';
	$url->setParameter('jsset', $params['name']);

	$element = new BSScriptElement;
	$element->setAttribute('src', $url->getContents());

	return $element->getContents();
}

/* vim:set tabstop=4: */
