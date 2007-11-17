<?php
/**
 * @package jp.co.b-shock.carrot
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
	} else if ($length == 0) {
		return '';
	} else if ($length < mb_strlen($value, BSString::SCRIPT_ENCODING)) {
		return mb_substr($value, 0, $length, BSString::SCRIPT_ENCODING) . $suffix;
	} else {
		return $value;
	}
}
/* vim:set tabstop=4 ai: */
?>