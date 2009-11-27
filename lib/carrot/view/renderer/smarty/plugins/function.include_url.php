<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * 外部コンテンツをインクルード
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
function smarty_function_include_url ($params, &$smarty) {
	$params = new BSArray($params);

	if (BSString::isBlank($params['src'])) {
		$url = BSURL::getInstance($params, 'BSCarrotURL');
	} else {
		$url = BSURL::getInstance($params['src']);
	}
	if (!$url) {
		return null;
	}

	if ($url['host']->getName() == BSController::getInstance()->getHost()->getName()) {
		if (($useragent = $smarty->getUserAgent()) && $useragent->isMobile()) {
			$url->setParameters($useragent->getAttribute('query'));
		}
	}
	return $url->fetch();
}

/* vim:set tabstop=4: */
