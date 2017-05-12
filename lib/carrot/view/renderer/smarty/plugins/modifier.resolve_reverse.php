<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * resolve_reverse修飾子
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
function smarty_modifier_resolve_reverse ($value) {
	if (is_array($value)) {
		return $value;
	} else if ($value instanceof BSParameterHolder) {
		return $value->getParameters();
	} else if (!BSString::isBlank($value)) {
		return (new BSHost($value))->resolveReverse();
	}
	return $value;
}

