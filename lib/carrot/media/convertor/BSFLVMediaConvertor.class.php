<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage media.convertor
 */

/**
 * FLVへの変換
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSFLVMediaConvertor extends BSMediaConvertor {

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

