<?php
/**
 * @package org.carrot-framework
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
	} else if ($value instanceof BSArray) {
		return $value->getParameters();
	} else if ($value != '') {
		return BSString::stripHTMLTags($value);
	}
}

/* vim:set tabstop=4 ai: */
?>