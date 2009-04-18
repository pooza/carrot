<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * 定数関数
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
function smarty_function_const ($params, &$smarty) {
	if (!isset($params['name'])) {
		throw new BSViewException('const関数には、name引数が必要です。');
	}
	return BSConstantHandler::getInstance()->getParameter($params['name']);
}

/* vim:set tabstop=4: */
