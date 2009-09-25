<?php
/**
 * BackupDatabaseアクション
 *
 * @package org.carrot-framework
 * @subpackage Console
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BackupDatabaseAction extends BSAction {
	private $database;

	/**
	 * 対象データベースを返す
	 *
	 * @access private
	 * @return BSDatabase 対象データベース
	 */
	private function getDatabase () {
		if (!$this->database) {
			if (!$name = $this->request['d']) {
				$name = 'default';
			}
			try {
				$this->database = BSDatabase::getInstance($name);
			} catch (BSDatabaseException $e) {
				return null;
			}
		}
		return $this->database;
	}

	/**
	 * バックアップ
	 *
	 * @access private
	 * @param BSDirectory 出力先ディレクトリ
	 */
	private function backup (BSDirectory $dir) {
		$suffix = '_' . BSDate::getNow('Y-m-d');
		if (!$file = $this->getDatabase()->createDumpFile($suffix, $dir)) {
			throw new BSDatabaseException($this->getDatabase() . 'がバックアップ出来ません。');
		}

		$file->setMode(0666);
		$file->compress();

		$message = new BSStringFormat('%sをバックアップしました。');
		$message[] = $this->getDatabase();
		$this->controller->putLog($message, $this->getDatabase());
	}

	/**
	 * 古いダンプをパージ
	 *
	 * @access private
	 * @param BSDirectory 対象ディレクトリ
	 */
	private function purge (BSDirectory $dir) {
		$expire = BSDate::getNow()->setAttribute('month', '-1');
		foreach ($dir as $entry) {
			if ($entry->isDirectory()) {
				continue;
			}
			if ($entry->getUpdateDate()->isPast($expire)) {
				$entry->delete();
			}
		}
	}

	public function initialize () {
		$this->request->addOption('d');
		$this->request->parse();
		return true;
	}

	public function execute () {
		try {
			$dir = $this->controller->getDirectory('dump');
			$this->backup($dir);
			$this->purge($dir);
		} catch (Exception $e) {
			$this->handleError();
		}

		$this->controller->putLog('実行しました。', $this);
		return BSView::NONE;
	}

	public function handleError () {
		return BSView::NONE;
	}

	public function validate () {
		return !!$this->getDatabase();
	}
}

/* vim:set tabstop=4: */
