<?php
/**
 * @package org.carrot-framework
 * @subpackage media.convertor
 */

/**
 * FLVへの変換
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSFLVMediaConvertor extends BSMediaConvertor {

	/**
	 * 変換して返す
	 *
	 * @access public
	 * @param BSMovieFile $source 変換後ファイル
	 * @return BSMediaFile 変換後ファイル
	 */
	public function execute (BSMediaFile $source) {
		return BSMovieFile::search(parent::execute($source));
	}

	/**
	 * 変換後ファイルのサフィックス
	 *
	 * @access public
	 * @return string サフィックス
	 */
	public function getSuffix () {
		return '.flv';
	}

	/**
	 * 変換後のクラス名
	 *
	 * @access public
	 * @return string クラス名
	 */
	public function getClass () {
		return 'BSMovieFile';
	}
}

/* vim:set tabstop=4: */
