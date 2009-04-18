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
	} else if ($value instanceof BSParameterHolder) {
		return $value->getParameters();
	} else if (!BSString::isBlank($value)) {
		preg_match_all('/\[\[([^\]]+)\]\]/', $value, $matchesAll, PREG_SET_ORDER);
		foreach ($matchesAll as $matches) {
			$tag = new BSPictogramTag($matches[1]);
			if ($tag->isMatched()) {
				$value = $tag->execute($value);
			}
		}
		return $value;
	}
}

/* vim:set tabstop=4: */
