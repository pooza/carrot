<?php
/**
 * @package org.carrot-framework
 * @subpackage backup
 */

/**
 * バックアップマネージャ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
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
		throw new BadFunctionCallException(__CLASS__ . 'はコピーできません。');
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

		$name = new BSStringFormat('%s_%s.zip');
		$name[] = BSController::getInstance()->getHost()->getName();
		$name[] = BSDate::getNow('Y-m-d');

		try {
			$file = $this->createArchive()->getFile();
			$file->rename($name->getContents());
			$file->moveTo($dir);
			$dir->purge();
		} catch (Exception $e) {
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
				$message = new BSStringFormat('データベース "%s" が見つかりません。');
				$message[] = $name;
				throw new BSDatabaseException($message);
			}
			$zip->register($db->getBackupTarget());
		}
		foreach ($this->config['directories'] as $name) {
			if (!$dir = BSFileUtility::getDirectory($name)) {
				$message = new BSStringFormat('ディレクトリ "%s" が見つかりません。');
				$message[] = $name;
				throw new BSFileException($message);
			}
			$zip->register($dir, null, BSDirectory::WITHOUT_ALL_IGNORE);
		}
		$zip->close();
		return $zip;
	}

	/**
	 * ZIPアーカイブファイルをリストア
	 *
	 * @access public
	 * @param BSFile $file アーカイブファイル
	 */
	public function restore (BSFile $file) {
		$zip = new BSZipArchive;
		$zip->open($file->getPath());
		$dir = BSFileUtility::getDirectory('tmp')->createDirectory(BSUtility::getUniqueID());
		$zip->extractTo($dir);

		foreach ($this->config['databases'] as $name) {
			if ($file = $dir->getEntry($name . '.sqlite3')) {
				$file->moveTo(BSFileUtility::getDirectory('db'));
			}
		}
		foreach ($this->config['directories'] as $name) {
			if (($src = $dir->getEntry($name)) && ($dest = BSFileUtility::getDirectory($name))) {
				foreach ($src as $file) {
					if (!$file->isIgnore()) {
						$file->moveTo($dest);
					}
				}
			}
		}

		$zip->close();
		$dir->delete();
		BSConfigManager::getInstance()->clearCache();
	}
}

/* vim:set tabstop=4: */
