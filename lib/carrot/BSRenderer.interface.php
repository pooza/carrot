<?php
/**
 * @package jp.co.b-shock.carrot
 */

/**
 * レンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSRenderer.interface.php 97 2007-11-17 12:24:53Z pooza $
 */
interface BSRenderer {

	/**
	 * 出力内容を返す
	 *
	 * @access public
	 */
	public function getContents ();

	/**
	 * 出力内容のサイズを返す
	 *
	 * @access public
	 * @return integer サイズ
	 */
	public function getSize ();

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType ();

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate ();

	/**
	 * エラーメッセージを返す
	 *
	 * @access public
	 * @return string エラーメッセージ
	 */
	public function getError ();
}

/* vim:set tabstop=4 ai: */
?>