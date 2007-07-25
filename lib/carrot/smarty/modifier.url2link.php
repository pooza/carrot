<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage smarty
 */

/**
 * URL変換フィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: modifier.url2link.php 251 2006-12-11 06:38:40Z pooza $
 */
function smarty_modifier_url2link ($value) {
	if (is_array($value)) {
		return $value;
	} else if ($value) {
		return preg_replace(
			"/https?:\/\/[a-zA-Z0-9_~.,:;\/?&=+$%#!\-]+/",
			"<a href=\"\\0\" target=\"_blank\">\\0</a>",
			$value
		);
	}
}
/* vim:set tabstop=4 ai: */
?>