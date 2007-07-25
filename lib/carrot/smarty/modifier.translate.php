<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage smarty
 */

/**
 * 翻訳フィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: modifier.translate.php 301 2007-03-10 16:19:41Z pooza $
 */
function smarty_modifier_translate ($value, $language = null) {
	if (is_array($value)) {
		return $value;
	} else if ($value) {
		return BSString::convertEncoding(
			BSTranslator::getInstance()->translate($value, $language),
			BSString::TEMPLATE_ENCODING,
			BSString::SCRIPT_ENCODING
		);
	}
}

/* vim:set tabstop=4 ai: */
?>