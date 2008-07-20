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
	public function initialize () {
		$this->request->addOption('d');
		$this->request->parse();
		return true;
	}

	public function execute () {
		if (!$db = $this->request['d']) {
			$db = 'default';
		}
		$db = BSDatabase::getInstance($db);

		$db->createDumpFile();
		$this->controller->putLog(sprintf('%sのダンプを作成しました。', $db), get_class($db));
		return BSView::NONE;
	}
}

/* vim:set tabstop=4 ai: */
?>