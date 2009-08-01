<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * CarrotアプリケーションのURLを貼り付ける関数
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
function smarty_function_carrot_url ($params, &$smarty) {
	$params = new BSArray($params);

	if ($params['contents']) {
		$url = BSURL::getInstance($params['contents']);
	} else {
		$url = BSURL::getInstance(null, 'BSCarrotURL');
		foreach ($params as $key => $value) {
			$url[$key] = $value;
		}
		if (BSString::isBlank($params['module'])) {
			$url['module'] = BSController::getInstance()->getModule();
		}
	}

	if (($useragent = $smarty->getUserAgent()) && $useragent->isMobile()) {
		$url->setParameters($useragent->getAttribute('query'));
	}
	return $url->getContents();
}

/* vim:set tabstop=4: */
