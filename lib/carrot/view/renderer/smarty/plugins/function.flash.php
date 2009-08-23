<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * Flashムービー関数
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
function smarty_function_flash ($params, &$smarty) {
	$params = new BSArray($params);
	$mode = BSString::toLower($params['mode']);

	if (!$file = BSFlashUtility::getFile($params)) {
		return null;
	}

	switch ($mode) {
		case 'size':
			return $file['pixel_size'];
		case 'width':
			return $file['width'];
		case 'height':
			return $file['height'];
		default:
			if (BSString::isBlank($params['href_prefix'])) {
				if ($record = BSController::getInstance()->getModule()->searchRecord($params)) {
					$dir = $record->getTable()->getDirectory();
					$params['href_prefix'] = BS_FLASH_HREF_PREFIX . $dir->getName() . '/';
				}
			}
			return $file->getImageElement($params)->getContents();
	}
}

/* vim:set tabstop=4: */
