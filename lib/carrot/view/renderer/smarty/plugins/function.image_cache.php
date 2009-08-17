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
	$mode = BSString::toLower($params['mode']);

	if (!$container = $caches->getContainer($params)) {
		return null;
	} else if (!$info = $container->getImageInfo($params['size'], $params['pixel'], $flags)) {
		return null;
	}

	switch ($mode) {
		case 'size':
			return $info['pixel_size'];
		case 'width':
			return $info['width'];
		case 'height':
			return $info['height'];
	}

	$element = $caches->getImageElement($info);
	if (($class = $params['style_class']) && !$smarty->getUserAgent()->isMobile()) {
		$element->setAttribute('class', $class);
	}

	if ($mode == 'lightbox') {
		$url = $caches->getURL(
			$container,
			$params['size'],
			$params['pixel_full'],
			$caches->convertFlags($params['flags_full'])
		);
		$parent = new BSXMLElement('a');
		$parent->addElement($element);
		$element = $parent;
		$element->setAttribute('rel', 'lightbox');
		$element->setAttribute('href', $url->getContents());
	}

	return $element->getContents();
}

/* vim:set tabstop=4: */
