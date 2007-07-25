<?php
/**
 * CleaningTemporaryFilesアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Console
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id: CleaningTemporaryFilesAction.class.php 300 2007-03-10 12:56:23Z pooza $
 */
class CleaningTemporaryFilesAction extends BSAction {
	public function execute () {
		$expire = BSDate::getNow();
		$expire->setAttribute('day', '-1');

		foreach ($this->controller->getDirectory('tmp') as $entry) {
			if ($entry->isDirectory() || $entry->isDoted()) {
				continue;
			}

			try {
				if ($entry->getUpdateDate()->isAgo($expire)) {
					$entry->delete();
				}
			} catch (BSFileException $e) {
				$e->sendNotify();
			}
		}
		return View::NONE;
	}
}

/* vim:set tabstop=4 ai: */
?>