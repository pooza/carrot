<?php
/**
 * CreateDatabaseSchemaアクション
 *
 * @package org.carrot-framework
 * @subpackage Console
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class CreateDatabaseSchemaAction extends BSAction {
	public function initialize () {
		$this->request->addOption('d');
		$this->request->parse();
		return true;
	}

	public function execute () {
		if (!$database = $this->request['d']) {
			$database = 'default';
		}

		BSDatabase::getInstance($database)->createSchemaFile();
		$this->controller->putLog(
			sprintf('%sのスキーマを作成しました。', $this->database),
			get_class($this->database)
		);
		return BSView::NONE;
	}
}

/* vim:set tabstop=4 ai: */
?>