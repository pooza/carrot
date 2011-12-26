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

	/**
	 * 変換して返す
	 *
	 * @access public
	 * @param BSMovieFile $source 変換後ファイル
	 * @return BSMediaFile 変換後ファイル
	 */
	public function execute (BSMediaFile $source) {
		$source = BSMovieFile::search($source);
		return parent::execute($source);
	}
}

/* vim:set tabstop=4: */
