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
		if ($file = BSDatabase::getInstance()->createDumpFile($name, $dir)) {
			$file->compress();
			BSLog::put(get_class($this) . 'を実行しました。');
		}
		return BSView::NONE;
	}
}

/* vim:set tabstop=4 ai: */
?>