<?php
/**
 * DatabaseListアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage DevelopTableReport
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class DatabaseListAction extends BSAction {

	/**
	 * タイトルを返す
	 *
	 * @access public
	 * @return string タイトル
	 */
	public function getTitle () {
		return 'データベース一覧';
	}

	public function execute () {
		$this->request->setAttribute('databases', BSDatabase::getDatabases());
		return BSView::SUCCESS;
	}
}

