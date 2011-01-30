<?php
/**
 * Tableアクション
 *
 * @package org.carrot-framework
 * @subpackage DevelopTableReport
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class TableAction extends BSAction {
	private $database;
	private $tableProfile;

	/**
	 * タイトルを返す
	 *
	 * @access public
	 * @return string タイトル
	 */
	public function getTitle () {
		return 'テーブル:' . $this->getTableProfile()->getName();
	}

	private function getDatabase () {
		if (!$this->database) {
			$this->database = BSDatabase::getInstance($this->request['database']);
		}
		return $this->database;
	}

	private function getTableProfile () {
		if (!$this->tableProfile) {
			$this->tableProfile = $this->getDatabase()->getTableProfile($this->request['table']);
		}
		return $this->tableProfile;
	}

	public function execute () {
		$this->request->setAttribute('database', $this->getDatabase());
		$this->request->setAttribute('table', $this->getTableProfile());
		return BSView::SUCCESS;
	}

	public function handleError () {
		return $this->controller->getAction('not_found')->forward();
	}

	public function validate () {
		return !!$this->getTableProfile();
	}
}

/* vim:set tabstop=4: */
