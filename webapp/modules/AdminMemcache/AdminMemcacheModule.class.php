<?php
/**
 * AdminMemcacheモジュール
 *
 * @package jp.co.b-shock.carrot
 * @subpackage AdminMemcache
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class AdminMemcacheModule extends BSModule {

	/**
	 * タイトルを返す
	 *
	 * @access public
	 * @return string タイトル
	 */
	public function getTitle () {
		return 'Memcache管理モジュール';
	}

	/**
	 * メニューでのタイトルを返す
	 *
	 * @access public
	 * @return string タイトル
	 */
	public function getMenuTitle () {
		return 'Memcache';
	}
}

