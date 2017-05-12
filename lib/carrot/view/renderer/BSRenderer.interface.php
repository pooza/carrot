<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage view.renderer
 */

/**
 * レンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
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

