<?php
/**
 * @package org.carrot-framework
 * @subpackage smarty.plugins
 */

/**
 * 文字コード標準化修飾子
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
function smarty_modifier_encoding ($value) {
	if (is_array($value)) {
		return $value;
	} else if ($value instanceof BSArray) {
		return $value->getParameters();
	} else if ($value != '') {
		return BSString::convertEncoding($value, 'utf-8');
	}
}

/* vim:set tabstop=4 ai: */
?>