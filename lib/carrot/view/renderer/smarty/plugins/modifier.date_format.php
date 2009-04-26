<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * 日付書式化修飾子
 *
 * Smarty標準のdate_format修飾子と互換。
 * strftime関数に加え、date関数でも処理する。
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
function smarty_modifier_date_format ($value, $format = 'Y/m/d H:i:s') {
	if (is_array($value)) {
		return $value;
	} else if ($value instanceof BSParameterHolder) {
		return $value->getParameters();
	} else if (!BSString::isBlank($value)) {
		try {
			$date = new BSDate($value);
			return $date->format($format);
		} catch (BSDateException $e) {
			return $value;
		}
	}
}

/* vim:set tabstop=4: */
