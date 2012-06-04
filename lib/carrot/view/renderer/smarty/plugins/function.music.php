<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * 楽曲関数
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
function smarty_function_music ($params, &$smarty) {
	$params = BSArray::create($params);
	if (!$file = BSMusicFile::search($params)) {
		return null;
	}

	switch ($mode = BSString::toLower($params['mode'])) {
		case 'seconds':
		case 'duration':
		case 'type':
			return $file[$mode];
		default:
			if (BSString::isBlank($params['href_prefix'])) {
				$finder = new BSRecordFinder($params);
				if ($record = $finder->execute()) {
					$url = BSFileUtility::createURL('musics');
					$url['path'] .= $record->getTable()->getDirectory()->getName() . '/';
					$params['href_prefix'] = $url->getContents();
				}
			}
			return $file->createElement($params)->getContents();
	}
}

/* vim:set tabstop=4: */
