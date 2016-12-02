<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * CSSキャッシュ関数
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
function smarty_function_css_cache ($params, &$smarty) {
	$params = BSArray::create($params);
	if (BSString::isBlank($params['name'])) {
		$params['name'] = 'carrot';
	}

	$styleset = new BSStyleSet($params['name']);
	return $styleset->createElement()->getContents();
}

/* vim:set tabstop=4: */
