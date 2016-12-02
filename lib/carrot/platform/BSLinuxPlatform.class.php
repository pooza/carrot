<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage platform
 */

/**
 * Linuxプラットフォーム
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSLinuxPlatform extends BSPlatform {

	/**
	 * ファイルの内容から、メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function analyzeFile (BSFile $file) {
		return rtrim(exec('file -bi ' . $file->getPath()));
	}
}

/* vim:set tabstop=4: */
