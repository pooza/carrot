<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * Smarty整形済みテキスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
function smarty_block_smarty_preformatted ($params, $contents, &$smarty) {
	$params = BSArray::create($params);
	if (BSString::isBlank($params['style_class'])) {
		$params['style_class'] = 'smarty_preformatted';
	}

	$contents = mb_ereg_replace('\\{[^}]*\\}', '<em>\\0</em>', $contents);
	$contents = nl2br($contents);

	$element = new BSSpanElement;
	$element->registerStyleClass($params['style_class']);
	$element->setBody($contents);

	return $element->getContents();
}

