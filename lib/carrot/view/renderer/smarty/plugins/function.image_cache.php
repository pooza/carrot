<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * キャッシュ画像関数
 *
 * BSImageManagerのフロントエンド
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
function smarty_function_image_cache ($params, &$smarty) {
	$params = BSArray::encode($params);
	if (BSString::isBlank($params['size'])) {
		$params['size'] = 'thumbnail';
	}

	$manager = $smarty->getUserAgent()->createImageManager($params['flags']);
	if (($record = $manager->getContainer($params))
		&& ($info = $manager->getImageInfo($record, $params['size'], $params['pixel']))) {

		$element = $manager->createElement($info);
		$element->setAttribute('align', $params['align']);
		$element->setStyles($params['style']);
		$element->registerStyleClass($params['style_class']);
		$element->setID($params['container_id']);

		switch ($mode = BSString::toLower($params['mode'])) {
			case 'size':
				return $info['pixel_size'];
			case 'pixel_size':
			case 'width':
			case 'height':
			case 'url':
				return $info[$mode];
			case 'lightbox':
			case 'lightpop':
			case 'shadowbox':
				$anchor = BSLoader::getInstance()->createObject($mode, 'AnchorElement');
				$element = $element->wrap($anchor);
				$element->setImageGroup($params['group']);
				$element->setCaption($info['alt']);
				$element->setImage(
					$record, $params['size'], $params['pixel_full'], $params['flags_full']
				);
				break;
		}
		return $element->getContents();
	}
}

/* vim:set tabstop=4: */
