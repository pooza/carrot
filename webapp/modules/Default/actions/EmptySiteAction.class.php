<?php
/**
 * EmptySiteアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class EmptySiteAction extends BSAction {
	public function execute () {
		return BSView::ERROR;
	}
}

