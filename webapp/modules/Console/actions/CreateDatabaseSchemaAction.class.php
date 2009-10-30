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
		if (BSString::isBlank($name = $this->request['d'])) {
			$name = 'default';
		}
		$db = BSDatabase::getInstance($name);
		$db->createSchemaFile();

		$message = new BSStringFormat('%sのスキーマを作成しました。');
		$message[] = $db;
		BSLogManager::getInstance()->put($message, $db);
		return BSView::NONE;
	}
}

/* vim:set tabstop=4: */
