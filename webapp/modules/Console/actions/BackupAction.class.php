<?php
/**
 * Backupアクション
 *
 * @package org.carrot-framework
 * @subpackage Console
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BackupAction extends BSAction {
	public function execute () {
		BSBackupManager::getInstance()->execute();
		return BSView::NONE;
	}
}

/* vim:set tabstop=4: */
