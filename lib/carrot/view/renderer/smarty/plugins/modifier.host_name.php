<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * URLからホスト名を抽出する修飾子
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
function smarty_modifier_host_name ($value) {
	if (is_array($value)) {
		return $value;
	} else if ($value instanceof BSParameterHolder) {
		return $value->getParameters();
	} else if (!BSString::isBlank($value)
		&& ($url = BSURL::create($value))
		&& ($url instanceof BSHTTPURL)) {

		return $url['host']->getName();
	}
}
/* vim:set tabstop=4: */
