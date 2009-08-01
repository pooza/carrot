<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * キャッシュ画像関数
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
function smarty_function_image_cache ($params, &$smarty) {
	$params = new BSArray($params);
	$flags = BSImageCacheHandler::convertFlags($params['flags']);

	if (BSString::isBlank($params['class'])) {
		$module = BSController::getInstance()->getModule();
		$params['class'] = $module->getRecordClassName();
		if (BSString::isBlank($params['id']) && ($record = $module->getRecord())) {
			$params['id'] = $record->getID();
		}
	}

	if (($table = BSTableHandler::getInstance($params['class']))
		&& ($record = $table->getRecord($params['id']))
		&& ($info = $record->getImageInfo($params['size'], $params['pixel'], $flags))
	) {
		switch (BSString::toLower($params['mode'])) {
			case 'size':
			case 'size_text':
				$string = new BSStringFormat('%d×%d');
				$string[] = $info['width'];
				$string[] = $info['height'];
				return $string->getContents();
			default:
				$element = new BSXMLElement('img');
				$element->setAttribute('src', $info['url']);
				$element->setAttribute('width', $info['width']);
				$element->setAttribute('height', $info['height']);
				if (!$smarty->getUserAgent()->isMobile()) {
					$element->setAttribute('alt', $info['alt']);
					if ($class = $params['style_class']) {
						$element->setAttribute('class', $class);
					}
				}
				return $element->getContents();
		}
	}
}

/* vim:set tabstop=4: */
