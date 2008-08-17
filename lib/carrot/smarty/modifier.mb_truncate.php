<?php
/**
 * @package org.carrot-framework
 * @subpackage smarty
 */

/**
 * マルチバイト対応truncate修飾子
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
function smarty_modifier_mb_truncate ($value, $length = 80, $suffix = '...') {
	if (is_array($value)) {
		return $value;
	} else if ($value instanceof BSArray) {
		return $value->getParameters();
	} else if ($value != '') {
		return BSString::truncate($value, $length, $suffix);
	}
	return $value;
}

/* vim:set tabstop=4 ai: */
?>