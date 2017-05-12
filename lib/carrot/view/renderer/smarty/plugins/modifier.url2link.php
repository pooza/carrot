<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * URL変換修飾子
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
function smarty_modifier_url2link ($value) {
	if (is_array($value)) {
		return $value;
	} else if ($value instanceof BSParameterHolder) {
		return $value->getParameters();
	} else if (!BSString::isBlank($value)) {
		return mb_ereg_replace(
			'https?://[-_.!~*\'()a-zA-Z0-9;/?:@&=+$,%#]+',
			'<a href="\\0" target="_blank">\\0</a>',
			$value
		);
	}
}

