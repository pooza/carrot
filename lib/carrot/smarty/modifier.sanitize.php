<?php
/**
 * @package org.carrot-framework
 * @subpackage smarty
 */

/**
 * サニタイズ修飾子
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
function smarty_modifier_sanitize ($value) {
	if (is_array($value)) {
		return $value;
	} else if ($value instanceof BSArray) {
		return $value->getParameters();
	} else if ($value != '') {
		$value = BSString::unsanitize($value);
		return BSString::sanitize($value);
	}
}

/* vim:set tabstop=4 ai: */
?>