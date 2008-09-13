<?php
/**
 * @package org.carrot-framework
 * @subpackage smarty
 */

/**
 * 電話番号変換修飾子
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
function smarty_modifier_tel2link ($value) {
	if (is_array($value)) {
		return $value;
	} else if ($value instanceof BSArray) {
		return $value->getParameters();
	} else if ($value != '') {
		return preg_replace(
			"/[0-9]{2,4}\-[0-9]{2,4}\-[0-9]{4}/",
			"<a href=\"tel:\\0\">\\0</a>",
			$value
		);
	}
}

/* vim:set tabstop=4 ai: */
?>