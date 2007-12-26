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
	if (!is_array($value)) {
		$value = self::convertEncoding($value, 'eucjp-win', BSString::SCRIPT_ENCODING);
		mb_internal_encoding('eucjp-win');
		if ($length < mb_strlen($value)) {
			return mb_substr($value, 0, $length) . $suffix;
		}
		mb_internal_encoding(BSString::SCRIPT_ENCODING);
		$value = self::convertEncoding($value, BSString::SCRIPT_ENCODING, 'eucjp-win');
	}
	return $value;
}
/* vim:set tabstop=4 ai: */
?>