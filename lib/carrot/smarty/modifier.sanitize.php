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
 * @version $Id: modifier.sanitize.php 167 2008-03-06 12:44:43Z pooza $
 */
function smarty_modifier_sanitize ($value) {
	if (is_array($value)) {
		return $value;
	} else if ($value instanceof BSArray) {
		return $value->getParameters();
	} else if ($value != '') {
		return BSString::sanitize($value);
	}
}

/* vim:set tabstop=4 ai: */
?>