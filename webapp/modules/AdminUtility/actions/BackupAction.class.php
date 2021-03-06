<?php
/**
 * Backupアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage AdminUtility
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BackupAction extends BSAction {
	private $manager;

	/**
	 * メモリ上限を返す
	 *
	 * @access public
	 * @return integer メモリ上限(MB)、設定の必要がない場合はNULL
	 */
	public function getMemoryLimit () {
		return 1024;
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
		return 'バックアップ';
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
			if (!$file = $this->getBackupManager()->execute()) {
				throw new BSFileException('バックアップファイルを取得できません。');
			}
			$this->request->setAttribute('renderer', $file);
			$this->request->setAttribute('filename', $file->getName());
			return BSView::SUCCESS;
		} catch (BSException $e) {
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

