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

	if (BSString::isBlank($params['contents'])) {
		$params->removeParameter('contents');
		$url = BSURL::getInstance(null, 'BSCarrotURL');
		foreach ($params as $key => $value) {
			if (mb_ereg('^params_(.*)$', $key, $matches)) {
				$url->setParameter($matches[1], $value);
			} else {
				$url[$key] = $value;
			}
		}
		if (BSString::isBlank($params['module'])) {
			if (BSString::isBlank($params['action'])) {
				$url['action'] = BSController::getInstance()->getAction();
				if (!BSString::isBlank($params['record_id'])) {
					$url['record'] = $params['record_id'];
				}
			} else {
				$url['module'] = BSController::getInstance()->getModule();
			}
		}
	} else {
		$url = BSURL::getInstance($params['contents']);
	}

	if (($useragent = $smarty->getUserAgent()) && $useragent->isMobile()) {
		$url->setParameters($useragent->getAttribute('query'));
	}
	return $url->getContents();
}

/* vim:set tabstop=4: */
