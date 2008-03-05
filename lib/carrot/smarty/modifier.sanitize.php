<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage smarty
 */

/**
 * サニタイズ修飾子
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
function smarty_modifier_sanitize ($value) {
	if ($value == '0') {
		return '0';
	} else if (is_array($value)) {
		return $value;
	} else if ($value) {
		return BSString::sanitize($value);
	}
}

/* vim:set tabstop=4 ai: */
?>