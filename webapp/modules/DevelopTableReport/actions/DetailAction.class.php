<?php
/**
 * Detailアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage DevelopTableReport
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class DetailAction extends BSAction {
	private $database;
	private $tableProfile;

	private function getDatabase () {
		if (!$this->database) {
			$this->database = BSDatabase::getInstance($this->request->getParameter('database'));
		}
		return $this->database;
	}

	private function getTableProfile () {
		if (!$this->tableProfile) {
			$this->tableProfile = $this->getDatabase()->getTableProfile(
				$this->request->getParameter('table')
			);
		}
		return $this->tableProfile;
	}

	public function execute () {
		$this->request->setAttribute('database', $this->getDatabase()->getInfo());
		$this->request->setAttribute('tablename', $this->getTableProfile()->getName());
		$this->request->setAttribute('attributes', $this->getTableProfile()->getAttributes());
		$this->request->setAttribute('fields', $this->getTableProfile()->getFields());
		$this->request->setAttribute('keys', $this->getTableProfile()->getKeys());
		return BSView::SUCCESS;
	}

	public function handleError () {
		return $this->controller->forwardTo($this->controller->getNotFoundAction());
	}

	public function validate () {
		return ($this->getTableProfile() != null);
	}
}

/* vim:set tabstop=4 ai: */
?>