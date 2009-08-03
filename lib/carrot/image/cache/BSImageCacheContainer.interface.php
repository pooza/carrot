<?php
/**
 * @package org.carrot-framework
 * @subpackage image.cache
 */

/**
 * 画像キャッシュコンテナ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
interface BSImageCacheContainer extends BSImageContainer {

	/**
	 * 画像の情報を返す
	 *
	 * @access public
	 * @param string $size サイズ名
	 * @param integer $pixel ピクセルサイズ
	 * @param integer $flags フラグのビット列
	 * @return BSArray 画像の情報
	 */
	public function getImageInfo ($size = null, $pixel = null, $flags = null);

	/**
	 * 画像ファイルを設定
	 *
	 * @access public
	 * @param BSImageFile $file 画像ファイル
	 * @param string $size サイズ名
	 */
	public function setImageFile (BSImageFile $file, $size = null);
}

/* vim:set tabstop=4: */
