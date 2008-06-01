<?php
/**
 * Databaseアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage DevelopTableReport
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class DatabaseAction extends BSAction {
	private $database;

	private function getDatabase () {
		if (!$this->database) {
			$this->database = BSDatabase::getInstance($this->request->getParameter('database'));
		}
		return $this->database;
	}

	public function execute () {
		$values = $this->getDatabase()->getInfo()->getParameters();
		$values['tables'] = $this->getDatabase()->getTableNames();
		$this->request->setAttribute('database', $values);
		return BSView::SUCCESS;
	}

	public function handleError () {
		return $this->controller->forwardTo($this->controller->getNotFoundAction());
	}

	public function validate () {
		return ($this->getDatabase() != null);
	}
}

/* vim:set tabstop=4 ai: */
?>