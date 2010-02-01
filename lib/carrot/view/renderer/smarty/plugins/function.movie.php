<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * 動画関数
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
function smarty_function_movie ($params, &$smarty) {
	$params = new BSArray($params);
	$mode = BSString::toLower($params['mode']);

	if (!$file = BSMovieUtility::getFile($params)) {
		return null;
	}

	switch ($mode) {
		case 'size':
			return $file['pixel_size'];
		case 'width':
		case 'height':
		case 'height_full':
		case 'seconds':
		case 'duration':
		case 'type':
			return $file[$mode];
		default:
			if (BSString::isBlank($params['href_prefix'])) {
				if ($record = BSController::getInstance()->getModule()->searchRecord($params)) {
					$url = BSFileUtility::getURL('movies');
					$url['path'] .= $record->getTable()->getDirectory()->getName() . '/';
					$params['href_prefix'] = $url['path'];
				}
			}
			return $file->getImageElement($params)->getContents();
	}
}

/* vim:set tabstop=4: */
