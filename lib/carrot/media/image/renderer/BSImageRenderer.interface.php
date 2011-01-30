<?php
/**
 * @package org.carrot-framework
 * @subpackage media.image.renderer
 */

/**
 * 画像レンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
interface BSImageRenderer extends BSRenderer {

	/**
	 * GD画像リソースを返す
	 *
	 * @access public
	 * @return resource GD画像リソース
	 */
	public function getGDHandle ();

	/**
	 * 幅を返す
	 *
	 * @access public
	 * @return integer 幅
	 */
	public function getWidth ();

	/**
	 * 高さを返す
	 *
	 * @access public
	 * @return integer 高さ
	 */
	public function getHeight ();
}

/* vim:set tabstop=4: */
