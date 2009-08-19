<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * テンプレート上で、変数のアサインを行う
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
function smarty_compiler_assign ($params, &$compiler) {
	$params = new BSArray($compiler->_parse_attrs($params));

	if (BSString::isBlank($params['var'])) {
		$compiler->_syntax_error('assign: varが未定義です。', E_USER_WARNING);
		return;
	}
	if (!$params->hasParameter('value')) {
		$compiler->_syntax_error('assign: valueが未定義です。', E_USER_WARNING);
		return;
	}

	$var = null;
	foreach (BSString::explode('.', str_replace('.', "'.'", $params['var'])) as $part) {
		if ($part == "''") {
			$var .= '[]';
		} else {
			$var .= '[' . $part . ']';
		}
	}

	return '$this->_tpl_vars' . $var . '=' . $params['value'] . ';';
}

/* vim:set tabstop=4: */
