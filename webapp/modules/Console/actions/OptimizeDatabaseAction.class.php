<?php
/**
 * OptimizeDatabaseアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Console
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class OptimizeDatabaseAction extends BSAction {
	public function execute () {
		BSDatabase::getInstance()->optimize();
		return BSView::NONE;
	}
}

/* vim:set tabstop=4 ai: */
?>