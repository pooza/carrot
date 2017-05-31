<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage backup
 */

/**
 * バックアップマネージャ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSBackupManager {
	use BSSingleton, BSBasicObject;
	protected $config;
	protected $temporaryDir;

	/**
	 * @access protected
	 */
	protected function __construct () {
		$this->config = new BSArray;
		$this->temporaryDir = BSFileUtility::createTemporaryDirectory();
		$configure = BSConfigManager::getInstance();
		foreach ($configure->compile('backup/application') as $key => $values) {
			$this->config[$key] = new BSArray($values);
			$this->config[$key]->trim();
		}
	}

	/**
	 * @access public
	 */
	public function __destruct () {
		$this->temporaryDir->delete();
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
		$name[] = $this->controller->getHost()->getName();
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

	protected function createArchive () {
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
		foreach ($this->getOptionalEntries() as $entry) {
			$zip->register($entry);
		}
		$zip->close();
		return $zip;
	}

	protected function getOptionalEntries () {
		return new BSArray;
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
		$zip->extractTo($this->temporaryDir);
		$zip->close();

		if (!$this->isValidBackup()) {
			throw new BSFileException('このバックアップからはリストアできません。');
		}

		(new BSImageManager)->clear();
		BSRenderManager::getInstance()->clear();
		foreach (BSSerializeHandler::getClasses() as $class) {
			foreach (BSTableHandler::create($class) as $record) {
				$record->removeSerialized();
			}
		}
		$this->restoreDatabase();
		$this->restoreDirectories();
		$this->restoreOptional();
		BSLogManager::getInstance()->put('リストアを実行しました。', $this);
	}

	protected function isValidBackup () {
		foreach ($this->config['databases'] as $name) {
			if (!$this->temporaryDir->getEntry($name . '.sqlite3')) {
				return false;
			}
		}
		foreach ($this->config['directories'] as $name) {
			if (!$this->temporaryDir->getEntry($name)) {
				return false;
			}
		}
		return true;
	}

	protected function restoreDatabase () {
		foreach ($this->config['databases'] as $name) {
			$file = $this->temporaryDir->getEntry($name . '.sqlite3');
			$file->moveTo(BSFileUtility::getDirectory('db'));
			BSDatabase::getInstance($name, BSDatabase::RECONNECT);
		}
	}

	protected function restoreDirectories () {
		foreach ($this->config['directories'] as $name) {
			$dest = BSFileUtility::getDirectory($name);
			$dest->clear();
			foreach ($this->temporaryDir->getEntry($name) as $file) {
				if (!$file->isIgnore()) {
					$file->moveTo($dest);
				}
			}
		}
	}

	protected function restoreOptional () {
		// 適宜オーバーライド
	}

	/**
	 * リストア可能な環境か？
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

