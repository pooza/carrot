<?php
/**
 * Backupアクション
 *
 * @package org.carrot-framework
 * @subpackage AdminUtility
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BackupAction extends BSAction {

	/**
	 * タイトルを返す
	 *
	 * @access public
	 * @return string タイトル
	 */
	public function getTitle () {
		return 'バックアップ';
	}

	public function execute () {
		try {
			if (!$file = BSBackupManager::getInstance()->execute()) {
				throw new BSFileException('バックアップファイルを取得できません。');
			}
			$this->request->setAttribute('renderer', $file);
			$this->request->setAttribute('filename', $file->getName());
			return BSView::SUCCESS;
		} catch (BSFileException $e) {
			$message = new BSStringFormat('バックアップに失敗しました。 (%s)');
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
