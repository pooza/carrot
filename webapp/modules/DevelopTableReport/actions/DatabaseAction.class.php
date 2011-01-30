<?php
/**
 * Databaseアクション
 *
 * @package org.carrot-framework
 * @subpackage DevelopTableReport
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class DatabaseAction extends BSAction {
	private $database;

	/**
	 * タイトルを返す
	 *
	 * @access public
	 * @return string タイトル
	 */
	public function getTitle () {
		return 'データベース:' . $this->getDatabase()->getName();
	}

	private function getDatabase () {
		if (!$this->database) {
			$this->database = BSDatabase::getInstance($this->request['database']);
		}
		return $this->database;
	}

	public function execute () {
		$this->request->setAttribute('database', $this->getDatabase());
		return BSView::SUCCESS;
	}

	public function handleError () {
		return $this->controller->getAction('not_found')->forward();
	}

	public function validate () {
		return !!$this->getDatabase();
	}
}

/* vim:set tabstop=4: */
