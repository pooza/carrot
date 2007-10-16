<?php
/**
 * DumpDatabaseアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Console
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class DumpDatabaseAction extends BSAction {
	public function execute () {
		BSDatabase::getInstance()->dump();
		BSLog::put(get_class($this) . 'を実行しました。');
		return View::NONE;
	}
}

/* vim:set tabstop=4 ai: */
?>