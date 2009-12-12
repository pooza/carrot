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
	$flags = $caches->convertFlags($params['flags']);

	if (!$container = $caches->getContainer($params)) {
		return null;
	} else if (!$info = $container->getImageInfo($params['size'], $params['pixel'], $flags)) {
		return null;
	}

	$element = $caches->getImageElement($info);
	$element->setAttribute('class', $params['style_class']);

	switch ($mode = BSString::toLower($params['mode'])) {
		case 'pixel_size':
		case 'size':
			return $info['pixel_size'];
		case 'width':
		case 'height':
			return $info[$mode];
		case 'lightbox':
			$element = $element->wrap(new BSAnchorElement);
			$element->setAttribute('rel', 'lightbox');
			$url = $caches->getURL(
				$container,
				$params['size'],
				$params['pixel_full'],
				$caches->convertFlags($params['flags_full'])
			);
			$element->setURL($url);
			//↓そのまま実行
		default:
			return $element->getContents();
	}
}

/* vim:set tabstop=4: */
