<?php
/**
 * DatabaseListアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage DevelopTableReport
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class DatabaseListAction extends BSAction {
	public function execute () {
		$this->request->setAttribute('databases', BSDatabase::getDatabases());
		return BSView::SUCCESS;
	}
}

/* vim:set tabstop=4 ai: */
?>