<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * リンクターゲット修飾子
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
function smarty_modifier_link_target ($value) {
	if (is_array($value)) {
		return $value;
	} else if ($value instanceof BSParameterHolder) {
		return $value->getParameters();
	} else if (!BSString::isBlank($value)
		&& ($url = BSURL::create($value))
		&& ($url instanceof BSHTTPURL)) {

		if ($url->isForeign()) {
			return '_blank';
		}
		return '_self';
	}
}
