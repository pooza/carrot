<?php
/**
 * CookieDisabledアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class CookieDisabledAction extends BSAction {
	public function execute () {
		return BSView::ERROR;
	}
}

