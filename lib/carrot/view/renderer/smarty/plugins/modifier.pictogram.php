<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * 絵文字修飾子
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
function smarty_modifier_pictogram ($value) {
	if (is_array($value)) {
		return $value;
	} else if ($value instanceof BSArray) {
		return $value->getParameters();
	} else if ($value != '') {
		while (preg_match('/\[\[([^\]]+)\]\]/', $value, $matches)) {
			$tag = new BSPictogramTag($matches[1]);
			if ($tag->isMatched()) {
				$value = $tag->execute($value);
			}
		}
		return $value;
	}
}

/* vim:set tabstop=4: */
