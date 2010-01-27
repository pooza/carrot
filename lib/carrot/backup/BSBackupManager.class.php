<?php
/**
 * @package org.carrot-framework
 * @subpackage backup
 */

/**
 * バックアップマネージャ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSBackupManager {
	private $config;
	static private $instance;

	/**
	 * @access private
	 */
	private function __construct () {
		$this->config = new BSArray;
		$configure = BSConfigManager::getInstance();
		foreach ($configure->compile('backup/application') as $key => $values) {
			$this->config[$key] = new BSArray($values);
			$this->config[$key]->trim();
		}
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSBackupManager インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * @access public
	 */
	public function __clone () {
		throw new BSSingletonException(__CLASS__ . 'はコピーできません。');
	}

	/**
	 * ZIPアーカイブにバックアップを取り、返す
	 *
	 * @access public
	 * @param BSDirectory $dir 出力先ディレクトリ
	 * @return BSFile バックアップファイル
	 */
	public function execute (BSDirectory $dir = null) {
		if (!$dir) {
			$dir = BSFileUtility::getDirectory('backup');
		}

		try {
			$file = $this->createArchive()->getFile();
			$file->rename(sprintf('%s.zip', BSDate::getNow('Y-m-d')));
			$file->moveTo($dir);
			$dir->purge();
		} catch (Exception $e) {
p($e);exit;
			return;
		}

		BSLogManager::getInstance()->put('バックアップを実行しました。', $this);
		return $file;
	}

	private function createArchive () {
		$zip = new BSZipArchive;
		$zip->open();
		foreach ($this->config['databases'] as $name) {
			if (!$db = BSDatabase::getInstance($name)) {
				throw new BSDatabaseException('データベース "%s" が見つかりません。', $name);
			}
			$zip->register($db->getBackupTarget());
		}
		foreach ($this->config['directories'] as $name) {
			if (!$dir = BSFileUtility::getDirectory($name)) {
				throw new BSFileException('ディレクトリ "%s" が見つかりません。', $name);
			}
			$zip->register($dir, null, BSDirectory::WITHOUT_ALL_IGNORE);
		}
		$zip->close();
		return $zip;
	}
}

/* vim:set tabstop=4: */
