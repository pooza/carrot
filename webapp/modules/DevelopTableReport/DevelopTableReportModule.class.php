<?php
/**
 * DevelopTableReportモジュール
 *
 * @package org.carrot-framework
 * @subpackage DevelopTableReport
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class DevelopTableReportModule extends BSModule {

	/**
	 * タイトルを返す
	 *
	 * @access public
	 * @return string タイトル
	 */
	public function getTitle () {
		return 'TableReport管理モジュール';
	}

	/**
	 * メニューでのタイトルを返す
	 *
	 * @access public
	 * @return string タイトル
	 */
	public function getMenuTitle () {
		return 'TableReport';
	}
}

/* vim:set tabstop=4: */
