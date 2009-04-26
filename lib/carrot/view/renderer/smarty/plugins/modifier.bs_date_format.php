<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * 日付書式化修飾子
 *
 * 非推奨。date_format修飾子を使うべき。
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
function smarty_modifier_bs_date_format ($value, $format = 'Y/m/d H:i:s') {
	static $alert;
	if (!$alert) {
		BSLogManager::getInstance()->put('bs_date_format修飾子が呼ばれました。');
		$alert = true;
	}

	require_once('modifier.date_format.php');
	return smarty_modifier_date_format($value, $format);
}

/* vim:set tabstop=4: */
