<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage image
 */

/**
 * 画像を扱うレンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSImageRenderer.interface.php 84 2007-11-04 03:51:29Z pooza $
 */
interface BSImageRenderer extends BSRenderer {

	/**
	 * GDイメージリソースを返す
	 *
	 * @access public
	 * @return resource GDイメージリソース
	 */
	public function getImage ();

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

/* vim:set tabstop=4 ai: */
?>