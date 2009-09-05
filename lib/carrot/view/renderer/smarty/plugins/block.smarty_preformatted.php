<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * Smarty整形済みテキスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
function smarty_block_smarty_preformatted ($params, $contents, &$smarty) {
	$params = new BSArray($params);
	if (BSString::isBlank($params['style_class'])) {
		$params['style_class'] = 'smarty_preformatted';
	}

	$contents = preg_replace('/\{[^}]*\}/', '<em>\0</em>', $contents);
	$contents = nl2br($contents);

	$element = new BSXMLElement('span');
	$element->setAttribute('class', $params['style_class']);
	$element->setRawMode(true);
	$element->setBody($contents);

	return $element->getContents();
}

/* vim:set tabstop=4: */