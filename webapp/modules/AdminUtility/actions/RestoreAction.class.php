<?php
/**
 * Restoreアクション
 *
 * @package org.carrot-framework
 * @subpackage AdminUtility
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class RestoreAction extends BSAction {

	/**
	 * タイトルを返す
	 *
	 * @access public
	 * @return string タイトル
	 */
	public function getTitle () {
		return 'リストア';
	}

	public function execute () {
		try {
			BSBackupManager::getInstance()->restore(
				new BSFile($this->request['file']['tmp_name'])
			);
			return $this->getDefaultView();
		} catch (BSFileException $e) {
			$message = new BSStringFormat('リストアに失敗しました。 (%s)');
			$message[] = $e->getMessage();
			$this->request->setError('bsutility', $message);
			return $this->handleError();
		}
	}

	public function getDefaultView () {
		$this->request->setAttribute(
			'is_restoreable',
			BSBackupManager::getInstance()->isRestoreable()
		);
		return BSView::INPUT;
	}

	public function handleError () {
		return $this->getDefaultView();
	}

	public function validate () {
		return parent::validate() && BSBackupManager::getInstance()->isRestoreable();
	}
}

/* vim:set tabstop=4: */
