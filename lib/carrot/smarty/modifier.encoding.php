<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage smarty
 */

/**
 * 文字コード標準化修飾子
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
function smarty_modifier_encoding ($value) {
	if ($value == '0') {
		return '0';
	} else if (is_array($value)) {
		return $value;
	} else if ($value) {
		return BSString::convertEncoding($value, BSString::TEMPLATE_ENCODING);
	}
}

/* vim:set tabstop=4 ai: */
?>