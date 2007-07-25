<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage image
 */

/**
 * 画像を扱うビューエンジン
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSImageViewEngine.interface.php 273 2007-02-03 11:59:01Z pooza $
 */
interface BSImageViewEngine extends BSViewEngine {

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