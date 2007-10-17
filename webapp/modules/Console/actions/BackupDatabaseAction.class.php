<?php
/**
 * BackupDatabaseアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Console
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BackupDatabaseAction extends BSAction {
	public function execute () {
		$dir = $this->controller->getDirectory('dump');
		$name = $this->database->getName() . '_' . BSDate::getNow('Y-m-d');
		if (BSDatabase::getInstance()->createDumpFile($name, $dir)) {
			BSLog::put(get_class($this) . 'を実行しました。');
		}
		return View::NONE;
	}
}

/* vim:set tabstop=4 ai: */
?>