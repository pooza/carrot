<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * 絵文字修飾子
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
function smarty_modifier_pictogram ($value) {
	if (is_array($value)) {
		return $value;
	} else if ($value instanceof BSParameterHolder) {
		return $value->getParameters();
	} else if (!BSString::isBlank($value)) {
		foreach (BSString::eregMatchAll('\\[\\[([^\\]]+)\\]\\]', $value) as $matches) {
			$tag = new BSPictogramTag($matches[1]);
			if ($tag->isMatched()) {
				$value = $tag->execute($value);
			}
		}
		return $value;
	}
}

/* vim:set tabstop=4: */
