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
	$element->registerStyleClass($params['style_class']);
	if ($id = $params['container_id']) {
		$element->setID($id);
	}

	switch ($mode = BSString::toLower($params['mode'])) {
		case 'size':
			return $info['pixel_size'];
		case 'pixel_size':
		case 'width':
		case 'height':
		case 'url':
			return $info[$mode];
		case 'lightbox':
			$element = $element->wrap(new BSAnchorElement);
			if (BSString::isBlank($params['group'])) {
				$element->setAttribute('rel', 'lightbox');
			} else {
				$element->setAttribute('rel', 'lightbox[' . $params['group'] . ']');
			}
			$flags = $caches->convertFlags($params['flags_full']);
			$element->setURL(
				$caches->getURL($container, $params['size'], $params['pixel_full'], $flags)
			);
			break;
		case 'thickbox':
			$element = $element->wrap(new BSAnchorElement);
			$element->registerStyleClass('thickbox');
			$element->setAttribute('rel', $params['group']);
			$flags = $caches->convertFlags($params['flags_full']);
			$element->setURL(
				$caches->getURL($container, $params['size'], $params['pixel_full'], $flags)
			);
			break;
	}
	return $element->getContents();
}

/* vim:set tabstop=4: */
