<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage view.storage
 */

/**
 * レンダーマネージャ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
interface BSRenderStorage {

	/**
	 * キャッシュを返す
	 *
	 * @access public
	 * @param BSAction $action アクション
	 * @return BSView キャッシュ
	 */
	public function getCache (BSAction $action);

	/**
	 * キャッシュを削除
	 *
	 * @access public
	 * @param BSAction $action アクション
	 */
	public function removeCache (BSAction $action);

	/**
	 * レスポンスをキャッシュする
	 *
	 * @access public
	 * @param BSHTTPResponse $view キャッシュ対象
	 */
	public function cache (BSHTTPResponse $view);

	/**
	 * キャッシュを持っているか？
	 *
	 * @access public
	 * @param BSAction $action アクション
	 * @return boolean キャッシュを持っていたらTrue
	 */
	public function hasCache (BSAction $action);

	/**
	 * 全てのキャッシュをクリア
	 *
	 * @access public
	 */
	public function clear ();
}

