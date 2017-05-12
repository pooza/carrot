<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * JavaScriptキャッシュ関数
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
function smarty_function_js_cache ($params, &$smarty) {
	$params = BSArray::create($params);
	if (BSString::isBlank($params['name'])) {
		$params['name'] = 'carrot';
	}

	$jsset = new BSJavaScriptSet($params['name']);
	return $jsset->createElement()->getContents();
}

