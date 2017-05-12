<?php
/**
 * @package jp.co.b-shock.carrot
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
		if ($source['duration'] < BS_FFMPEG_CONVERT_JPG_SS) {
			$this->setConfig('ss', 0);
		}
		return parent::execute($source);
	}
}

