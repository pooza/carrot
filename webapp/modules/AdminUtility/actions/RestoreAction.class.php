<?php
/**
 * Restoreアクション
 *
 * @package org.carrot-framework
 * @subpackage AdminUtility
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class RestoreAction extends BSAction {
	public function execute () {
		try {
			BSBackupManager::getInstance()->restore(
				new BSFile($this->request['file']['tmp_name'])
			);
			return BSView::SUCCESS;
		} catch (BSFileException $e) {
			$message = new BSStringFormat('リストアに失敗しました。 (%s)');
			$message[] = $e->getMessage();
			$this->request->setError('bsutility', $message);
			return $this->handleError();
		}
	}

	public function getDefaultView () {
		return BSView::INPUT;
	}

	public function handleError () {
		return $this->getDefaultView();
	}
}

/* vim:set tabstop=4: */
