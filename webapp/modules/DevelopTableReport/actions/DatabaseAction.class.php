<?php
/**
 * Databaseアクション
 *
 * @package org.carrot-framework
 * @subpackage DevelopTableReport
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class DatabaseAction extends BSAction {
	private $database;

	private function getDatabase () {
		if (!$this->database) {
			$this->database = BSDatabase::getInstance($this->request['database']);
		}
		return $this->database;
	}

	public function execute () {
		$values = array(
			'name' => $this->getDatabase()->getName(),
			'info' => $this->getDatabase()->getInfo()->getParameters(),
			'tables' => $this->getDatabase()->getTableNames(),
		);
		$this->request->setAttribute('database', $values);
		return BSView::SUCCESS;
	}

	public function handleError () {
		return $this->controller->getNotFoundAction()->forward();
	}

	public function validate () {
		return ($this->getDatabase() != null);
	}
}

/* vim:set tabstop=4 ai: */
?>