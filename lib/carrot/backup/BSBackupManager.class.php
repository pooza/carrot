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
		$this->config = new BSArray(
			BSConfigManager::getInstance()->compile('backup/application')
		);
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
	 * ZIPアーカイブにバックアップを取る
	 *
	 * @access public
	 * @return BSZipArchive バックアップ
	 */
	public function execute () {
		$dir = BSFileUtility::getDirectory('backup');
		$file = $this->createArchive()->getFile();
		$file->rename(sprintf('%s.zip', BSDate::getNow('Y-m-d')));
		$file->moveTo($dir);

		$expire = BSDate::getNow()->setAttribute('month', '-1');
		foreach ($dir as $entry) {
			if ($entry->isDirectory() || $entry->isIgnore() || $entry->isDotted()) {
				continue;
			}
			if ($entry->getUpdateDate()->isPast($expire)) {
				$entry->delete();
			}
		}

		BSLogManager::getInstance()->put('バックアップを実行しました。', $this);
		return $file;
	}

	private function createArchive () {
		$zip = new BSZipArchive;
		$zip->open();
		foreach ((array)$this->config['databases'] as $name) {
			if (!$db = BSDatabase::getInstance($name)) {
				throw new BSDatabaseException('データベース "%s" が見つかりません。', $name);
			}
			$zip->register($db->getBackupTarget());
		}
		foreach ((array)$this->config['directories'] as $name) {
			if (!$dir = BSFileUtility::getDirectory($name)) {
				throw new BSDatabaseException('データベース "%s" が見つかりません。', $name);
			}
			$zip->register($dir, null, BSDirectory::WITHOUT_DOTTED);
		}
		$zip->close();
		return $zip;
	}
}

/* vim:set tabstop=4: */
