<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage smarty
 */

/**
 * 文字コードを標準化
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: modifier.encoding.php 238 2006-12-03 04:54:37Z pooza $
 */
function smarty_modifier_encoding ($value) {
	if (is_array($value)) {
		return $value;
	} else if ($value) {
		return BSString::convertEncoding($value, BSString::TEMPLATE_ENCODING);
	}
}

/* vim:set tabstop=4 ai: */
?>