<?php
/**
 * @package org.carrot-framework
 * @subpackage platform
 */

/**
 * FreeBSDプラットフォーム
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSFreeBSDPlatform extends BSPlatform {

	/**
	 * ファイルをリネーム
	 *
	 * @access public
	 * @param BSFile $file 対象ファイル
	 * @param string $path リネーム後のパス
	 */
	public function renameFile (BSFile $file, $path) {
		if (!@rename($file->getPath(), $path)) {
			throw new BSFileException($this . 'を移動できません。');
		}
	}
}

/* vim:set tabstop=4: */
