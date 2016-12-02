<?php
/**
 * BackupDatabaseアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Console
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BackupDatabaseAction extends BSAction {
	private $database;

	private function getDatabase () {
		if (!$this->database) {
			if (!$name = $this->request['d']) {
				$name = 'default';
			}
			$this->database = BSDatabase::getInstance($name);
		}
		return $this->database;
	}

	public function initialize () {
		$this->request->addOption('d');
		$this->request->parse();
		return true;
	}

	public function execute () {
		try {
			$this->getDatabase()->createDumpFile();
		} catch (Exception $e) {
			$this->handleError();
		}
		return BSView::NONE;
	}

	public function handleError () {
		return BSView::NONE;
	}

	public function validate () {
		return !!$this->getDatabase();
	}
}

/* vim:set tabstop=4: */
