<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * 動画関数
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
function smarty_function_movie ($params, &$smarty) {
	$params = new BSArray($params);
	if (!$file = BSMovieFile::search($params)) {
		return null;
	}

	switch ($mode = BSString::toLower($params['mode'])) {
		case 'size':
			return $file['pixel_size'];
		case 'width':
		case 'height':
		case 'height_full':
		case 'pixel_size':
		case 'seconds':
		case 'duration':
		case 'type':
			return $file[$mode];
		default:
			if (BSString::isBlank($params['href_prefix'])) {
				$finder = new BSRecordFinder($params);
				if ($record = $finder->execute()) {
					$url = BSFileUtility::createURL('movies');
					$url['path'] .= $record->getTable()->getDirectory()->getName() . '/';
					$params['href_prefix'] = $url->getContents();
				}
			}
			return $file->createElement($params, $smarty->getUserAgent())->getContents();
	}
}

/* vim:set tabstop=4: */
