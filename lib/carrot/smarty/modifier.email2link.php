<?php
/**
 * @package org.carrot-framework
 * @subpackage smarty
 */

/**
 * メールアドレス変換修飾子
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
function smarty_modifier_email2link ($value) {
	if (is_array($value)) {
		return $value;
	} else if ($value instanceof BSArray) {
		return $value->getParameters();
	} else if ($value != '') {
		return preg_replace(
			'/([0-9a-z_\.\-]+)@(([0-9a-z_\-]+\.)+[a-z]+)/i',
			"<a href=\"mailto:\\0\">\\0</a>",
			$value
		);
	}
}

/* vim:set tabstop=4 ai: */
?>