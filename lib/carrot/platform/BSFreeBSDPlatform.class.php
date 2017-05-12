<?php
/**
 * @package jp.co.b-shock.carrot
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
	 * @param BSDirectoryEntry $file 対象ファイル
	 * @param string $path リネーム後のパス
	 */
	public function renameFile (BSDirectoryEntry $file, $path) {
		if (!@rename($file->getPath(), $path)) {
			throw new BSFileException($this . 'を移動できません。');
		}
	}
}

