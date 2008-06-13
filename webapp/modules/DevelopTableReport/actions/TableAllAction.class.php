<?php
/**
 * TableAllアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage DevelopTableReport
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class TableAllAction extends BSAction {
	private $database;

	private function getDatabase () {
		if (!$this->database) {
			$this->database = BSDatabase::getInstance($this->request['database']);
		}
		return $this->database;
	}

	public function execute () {
		$profiles = new BSArray;
		foreach ($this->getDatabase()->getTableNames() as $table) {
			$profile = $this->getDatabase()->getTableProfile($table);
			$profiles[] = array(
				'tablename' => $profile->getName(),
				'attributes' => $profile->getAttributes(),
				'fields' => $profile->getFields(),
				'keys' => $profile->getKeys(),
			);
		}
		$this->request->setAttribute('profiles', $profiles);

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