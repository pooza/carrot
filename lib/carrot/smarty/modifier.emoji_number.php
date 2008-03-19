<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage smarty
 */

/**
 * 絵文字修飾子
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
function smarty_modifier_emoji_number ($value) {
	if (is_array($value)) {
		return $value;
	} else if ($value instanceof BSArray) {
		return $value->getParameters();
	} else if (in_array($value, range(0, 9))) {
		switch (BSController::getInstance()->getUserAgent()->getType()) {
			case 'Docomo':
			case 'Au':
				if ($value == '0') {
					return '&#63888;';
				} else {
					return '&#' . (63878 + $value) . ';';
				}
			case 'SoftBank';
				if ($value == '0') {
					return '&#57893;';
				} else {
					return '&#' . (57883 + $value) . ';';
				}
			default:
				return '[' . $value . ']';
		}
	}
}

/* vim:set tabstop=4 ai: */
?>