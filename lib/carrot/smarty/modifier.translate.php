<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage smarty
 */

/**
 * 翻訳修飾子
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
function smarty_modifier_translate ($value, $dictionary = null, $language = null) {
	if (is_array($value)) {
		return $value;
	} else if ($value instanceof BSArray) {
		return $value->getParameters();
	} else if ($value != '') {
		return BSString::convertEncoding(
			BSTranslator::getInstance()->translate($value, $dictionary, $language),
			BSString::TEMPLATE_ENCODING,
			BSString::SCRIPT_ENCODING
		);
	}
}

/* vim:set tabstop=4 ai: */
?>