<?php
/**
 * CleaningTemporaryFilesアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Console
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class CleaningTemporaryFilesAction extends BSAction {
	public function execute () {
		$expire = BSDate::getNow()->setAttribute('day', '-1');
		foreach ($this->controller->getDirectory('tmp') as $entry) {
			if ($entry->isDirectory() || $entry->isDoted()) {
				continue;
			}
			if ($entry->getUpdateDate()->isAgo($expire)) {
				try {
					$entry->delete();
				} catch (BSFileException $e) {
					$e->sendAlert();
				}
			}
		}

		BSLog::put(get_class($this) . 'を実行しました。');
		return View::NONE;
	}
}

/* vim:set tabstop=4 ai: */
?>