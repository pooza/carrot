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
			if ($db = BSDatabase::getInstance($name)) {
				$zip->register($db->getBackupTarget());
			}
		}
		foreach ($this->config['directories'] as $name) {
			if ($dir = BSFileUtility::getDirectory($name)) {
				$zip->register($dir, null, BSDirectory::WITHOUT_ALL_IGNORE);
			}
		}
		$dir = BSFileUtility::getDirectory('serialized');
		foreach (new BSArray($this->config['serializes']) as $name) {
			foreach (array('.json', '.serialized') as $suffix) {
				if ($entry = $dir->getEntry($name . $suffix)) {
					$zip->register($entry);
				}
			}
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
		if (!$this->isRestoreable()) {
			throw new BSFileException('この環境はリストアできません。');
		}

		$zip = new BSZipArchive;
		$zip->open($file->getPath());
		$dir = BSFileUtility::getDirectory('tmp')->createDirectory(BSUtility::getUniqueID());
		$zip->extractTo($dir);
		$zip->close();

		$this->restoreDatabase($dir);
		$this->restoreDirectories($dir);
		$this->restoreSerializes($dir);
		$dir->delete();
	}

	private function restoreDatabase (BSDirectory $dir) {
		foreach ($this->config['databases'] as $name) {
			if ($file = $dir->getEntry($name . '.sqlite3')) {
				$file->moveTo(BSFileUtility::getDirectory('db'));
			}
		}
	}

	private function restoreDirectories (BSDirectory $dir) {
		foreach ($this->config['directories'] as $name) {
			if (($src = $dir->getEntry($name)) && ($dest = BSFileUtility::getDirectory($name))) {
				$dest->clear();
				foreach ($src as $file) {
					if (!$file->isIgnore()) {
						$file->moveTo($dest);
					}
				}
			}
		}
	}

	private function restoreSerializes (BSDirectory $dir) {
		BSConfigManager::getInstance()->clear();
		BSRequest::getInstance()->getUserAgent()->createImageManager()->clear();
		BSController::getInstance()->getSerializeHandler()->clear();
		BSRenderManager::getInstance()->clear();

		// 念のためにクリアしておく
		if ($server = BSMemcacheManager::getInstance()->getServer()) {
			$server->flush();
		}

		foreach (new BSArray($this->config['serializes']) as $name) {
			foreach (array('.json', '.serialized') as $suffix) {
				if ($file = $dir->getEntry($name . $suffix)) {
					$file->moveTo(BSFileUtility::getDirectory('serialized'));
				}
			}
		}
	}

	/**
	 * リストアに対応した環境か？
	 *
	 * @access public
	 * @return boolean リストアに対応した環境ならTrue
	 */
	public function isRestoreable () {
		foreach ($this->config['databases'] as $name) {
			if (($db = BSDatabase::getInstance($name)) && !($db instanceof BSSQLiteDatabase)) {
				return false;
			}
		}
		return true;
	}
}

/* vim:set tabstop=4: */
