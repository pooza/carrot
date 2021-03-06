<?php
/**
 * Restoreアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage AdminUtility
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class RestoreAction extends BSAction {
	private $manager;

	/**
	 * メモリ上限を返す
	 *
	 * @access public
	 * @return integer メモリ上限(MB)、設定の必要がない場合はNULL
	 */
	public function getMemoryLimit () {
		return 256;
	}

	/**
	 * タイムアウト時間を返す
	 *
	 * @access public
	 * @return integer タイムアウト時間(秒)、設定の必要がない場合はNULL
	 */
	public function getTimeLimit () {
		return 300;
	}

	/**
	 * タイトルを返す
	 *
	 * @access public
	 * @return string タイトル
	 */
	public function getTitle () {
		return 'リストア';
	}

	private function getBackupManager () {
		if (!$this->manager) {
			$class = BS_BACKUP_CLASS;
			$this->manager = $class::getInstance();
		}
		return $this->manager;
	}

	public function execute () {
		try {
			$this->getBackupManager()->restore(
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
			$this->getBackupManager()->isRestoreable()
		);
		return BSView::INPUT;
	}

	public function handleError () {
		return $this->getDefaultView();
	}

	public function validate () {
		return parent::validate() && $this->getBackupManager()->isRestoreable();
	}
}

