<?php
/**
 * BackupDatabaseアクション
 *
 * @package org.carrot-framework
 * @subpackage Console
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BackupDatabaseAction extends BSAction {
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

		$dir = $this->controller->getDirectory('dump');
		if ($file = $db->createDumpFile('_' . BSDate::getNow('Y-m-d'), $dir)) {
			$file->setMode(0666);
			$file->compress();
			$this->controller->putLog(sprintf('%sをバックアップしました。', $db), get_class($db));
		}
		return BSView::NONE;
	}
}

/* vim:set tabstop=4 ai: */
?>