<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * キャッシュ画像関数
 *
 * BSImageCacheHandlerのフロントエンド
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
function smarty_function_image_cache ($params, &$smarty) {
	$caches = BSImageCacheHandler::getInstance();
	$params = new BSArray($params);

	if (!$container = $caches->getContainer($params)) {
		return null;
	}

	$flags = $caches->convertFlags($params['flags']);
	if (!$info = $caches->getImageInfo($container, $params['size'], $params['pixel'], $flags)) {
		return null;
	}

	if (BSString::toLower($params['mode']) == 'size') {
		return $info['pixel_size'];
	}

	$element = $caches->getImageElement($info);
	if (($class = $params['style_class']) && !$smarty->getUserAgent()->isMobile()) {
		$element->setAttribute('class', $class);
	}
	return $element->getContents();
}

/* vim:set tabstop=4: */
