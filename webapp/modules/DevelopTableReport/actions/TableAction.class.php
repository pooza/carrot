<?php
/**
 * Tableアクション
 *
 * @package org.carrot-framework
 * @subpackage DevelopTableReport
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class TableAction extends BSAction {
	private $database;
	private $tableProfile;

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
		$this->request->setAttribute('database', $this->getDatabase()->getInfo());

		$values = array(
			'name' => $this->getTableProfile()->getName(),
			'fields' => $this->getTableProfile()->getFields(),
			'constraints' => $this->getTableProfile()->getConstraints(),
		);
		$this->request->setAttribute('table', $values);

		return BSView::SUCCESS;
	}

	public function handleError () {
		return $this->controller->getNotFoundAction()->forward();
	}

	public function validate () {
		return ($this->getTableProfile() != null);
	}
}

/* vim:set tabstop=4 ai: */
?>