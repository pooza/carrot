<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage smarty
 */

/**
 * サニタイズ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
function smarty_modifier_sanitize ($value) {
	if (is_array($value) || preg_match('/&([a-z]+|#[0-9]+);/', $value)) {
		return $value;
	} else if ($value) {
		return BSString::sanitize($value);
	}
}

/* vim:set tabstop=4 ai: */
?>