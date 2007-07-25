<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage smarty
 */

/**
 * 日付を書式化
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: modifier.bs_date_format.php 166 2006-07-22 07:35:31Z pooza $
 */
function smarty_modifier_bs_date_format ($value, $format = 'Y/m/d H:i:s') {
	if (is_array($value)) {
		return $value;
	} else if ($value) {
		try {
			$date = new BSDate($value);
			return $date->format($format);
		} catch (BSDateException $e) {
			return null;
		}
	}
}

/* vim:set tabstop=4 ai: */
?>