<?php
/**
 * @package org.carrot-framework
 * @subpackage media.convertor
 */

/**
 * JPEGへの変換
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSJPEGMediaConvertor extends BSMediaConvertor {

	/**
	 * 変換後ファイルのサフィックス
	 *
	 * @access public
	 * @return string サフィックス
	 */
	public function getSuffix () {
		return '.jpg';
	}

	/**
	 * 変換後のクラス名
	 *
	 * @access public
	 * @return string クラス名
	 */
	public function getClass () {
		return 'BSImageFile';
	}
}

/* vim:set tabstop=4: */
