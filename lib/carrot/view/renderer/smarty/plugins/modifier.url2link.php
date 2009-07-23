<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * URL変換修飾子
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
function smarty_modifier_url2link ($value) {
	if (is_array($value)) {
		return $value;
	} else if ($value instanceof BSParameterHolder) {
		return $value->getParameters();
	} else if (!BSString::isBlank($value)) {
		return preg_replace(
			'/https?:\/\/[a-zA-Z0-9_~.,:;\/?&=+$%#!\-]+/',
			'<a href="\\0" target="_blank">\\0</a>',
			$value
		);
	}
}

/* vim:set tabstop=4: */
