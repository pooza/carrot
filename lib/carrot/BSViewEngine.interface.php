<?php
/**
 * @package jp.co.b-shock.carrot
 */

/**
 * ビューエンジン - BSView::setEngineの為のインターフェース
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
interface BSViewEngine {

	/**
	 * 出力内容を返す
	 *
	 * @access public
	 */
	public function getContents ();

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