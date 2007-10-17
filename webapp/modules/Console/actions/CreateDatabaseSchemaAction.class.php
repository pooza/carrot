<?php
/**
 * CreateDatabaseSchemaアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Console
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class CreateDatabaseSchemaAction extends BSAction {
	public function execute () {
		BSDatabase::getInstance()->createSchemaFile();
		return View::NONE;
	}
}

/* vim:set tabstop=4 ai: */
?>