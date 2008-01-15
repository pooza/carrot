<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage smarty
 */

/**
 * HTMLタグ削除修飾子
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
function smarty_modifier_strip_html_tag ($value) {
	if (is_array($value)) {
		return $value;
	} else if ($value) {
		while (preg_match('/<\/?[^>]>/', $value, $matches)) {
			$value = str_replace($matches[0], '', $value);
		} 
		return $value;
	}
}

/* vim:set tabstop=4 ai: */
?>