<?php
/**
 * CreateDatabaseDumpアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Console
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class CreateDatabaseDumpAction extends BSAction {
	public function execute () {
		BSDatabase::getInstance()->createDumpFile();
		return View::NONE;
	}
}

/* vim:set tabstop=4 ai: */
?>