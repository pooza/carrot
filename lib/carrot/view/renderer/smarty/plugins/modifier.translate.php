<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * 翻訳修飾子
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
function smarty_modifier_translate ($value, $dictionary = null, $language = null) {
	if (is_array($value)) {
		return $value;
	} else if ($value instanceof BSArray) {
		return $value->getParameters();
	} else if ($value != '') {
		try {
			return BSTranslateManager::getInstance()->execute($value, $dictionary, $language);
		} catch (Exception $e) {
			// Smartyプラグインの中なので、例外は即エラー。
			trigger_error($e->getMessage(), E_USER_ERROR);
		}
	}
}

/* vim:set tabstop=4: */
