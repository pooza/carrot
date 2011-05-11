<?php
/**
 * @package org.carrot-framework
 * @subpackage media.convertor
 */

/**
 * MPEG4(H.264)への変換
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMPEG4MediaConvertor extends BSMediaConvertor {

	/**
	 * 変換後ファイルのサフィックス
	 *
	 * @access public
	 * @return string サフィックス
	 */
	public function getSuffix () {
		return '.mp4';
	}

	/**
	 * 変換後のクラス名
	 *
	 * @access public
	 * @return string クラス名
	 */
	public function getClass () {
		return 'BSMPEG4MovieFile';
	}
}

/* vim:set tabstop=4: */
