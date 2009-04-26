<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * マルチバイト対応truncate修飾子
 *
 * 非推奨。truncate修飾子を使うべき。
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
function smarty_modifier_mb_truncate ($value, $length = 80, $suffix = '...') {
	static $alert;
	if (!$alert) {
		BSLogManager::getInstance()->put('mb_truncate修飾子が呼ばれました。');
		$alert = true;
	}

	require_once('modifier.truncate.php');
	return smarty_modifier_truncate($value, $format);
}

/* vim:set tabstop=4: */
