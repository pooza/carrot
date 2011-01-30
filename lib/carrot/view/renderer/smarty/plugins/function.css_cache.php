<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * CSSキャッシュ関数
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
function smarty_function_css_cache ($params, &$smarty) {
	$params = new BSArray($params);
	if (BSString::isBlank($params['name'])) {
		$params['name'] = 'carrot';
	}

	$styleset = new BSStyleSet($params['name']);
	$element = new BSXHTMLElement('link');
	$element->setEmptyElement(true);
	$element->setAttribute('rel', 'stylesheet');
	$element->setAttribute('type', $styleset->getType());
	$element->setAttribute('charset', 'utf-8');
	$element->setAttribute('href', $styleset->getURL()->getContents());
	return $element->getContents();
}

/* vim:set tabstop=4: */
