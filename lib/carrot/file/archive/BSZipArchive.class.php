<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage file.archive
 */

/**
 * ZIPアーカイブ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSZipArchive extends ZipArchive implements BSRenderer {
	private $file;
	private $temporaryFile;
	private $opened = false;

	/**
	 * @access public
	 */
	public function __destruct () {
		if ($this->opened) {
			$this->close();
		}
	}

	/**
	 * 開く
	 *
	 * @access public
	 * @param mixed $file ファイル、又はそのパス。nullの場合は、一時ファイルを使用。
	 * @param integer $flags フラグのビット列
	 *   self::OVERWRITE
	 *   self::CREATE
	 *   self::EXCL
	 *   self::CHECKCONS
	 * @return mixed 正常終了時はtrue、それ以外はエラーコード。
	 */
	public function open ($path = null, $flags = null) {
		if ($this->opened) {
			throw new BSFileException($this->getFile() . 'が開かれています。');
		}
		$this->setFile($path);
		$this->opened = true;
		return parent::open($this->getFile()->getPath(), self::OVERWRITE);
	}

	/**
	 * 閉じる
	 *
	 * @access public
	 * @return mixed 正常終了時はtrue、それ以外はエラーコード。
	 */
	public function close () {
		if ($this->opened) {
			$this->opened = false;
			return parent::close();
		}
	}

	/**
	 * 展開
	 *
	 * @access public
	 * @param mixed $path 展開先パス、又はディレクトリ
	 * @param mixed $entries 対象エントリー名、又はその配列。
	 * @return boolean 正常終了時はtrue。
	 */
	public function extractTo ($path, $entries = null) {
		if ($path instanceof BSDirectory) {
			$path = $path->getPath() . '/';
		}
		$command = new BSCommandLine('bin/unzip');
		$command->setDirectory(BSFileUtility::getDirectory('unzip'));
		$command->push($this->getFile()->getPath());
		$command->push('-d');
		$command->push($path);
		$command->execute();
		return true;
	}

	/**
	 * エントリーを登録
	 *
	 * @access public
	 * @param BSDirectoryEntry $entry エントリー
	 * @param string $prefix エントリー名のプレフィックス
	 * @param integer $flags フラグのビット列
	 *   BSDirectory::WITHOUT_DOTTED ドットファイルを除く
	 *   BSDirectory::WITHOUT_IGNORE 無視ファイルを除く
	 */
	public function register (BSDirectoryEntry $entry, $prefix = null, $flags = null) {
		if (($flags & BSDirectory::WITHOUT_DOTTED) && $entry->isDotted()) {
			return;
		} else if (($flags & BSDirectory::WITHOUT_IGNORE) && $entry->isIgnore()) {
			return;
		}

		if (BSString::isBlank($prefix)) {
			$path = $entry->getName();
		} else {
			$path = $prefix . '/' . $entry->getName();
		}
		if ($entry instanceof BSDirectory) {
			$this->addEmptyDir($path);
			foreach ($entry as $node) {
				$this->register($node, $path, $flags);
			}
		} else {
			$this->addFile($entry->getPath(), $path);
		}
	}

	/**
	 * ファイルを返す
	 *
	 * @access public
	 * @return BSFile ファイル
	 */
	public function getFile () {
		if (!$this->file) {
			$this->temporaryFile = true;
			$this->file = BSFileUtility::createTemporaryFile('.zip');
		}
		return $this->file;
	}

	/**
	 * ファイルを設定
	 *
	 * @access public
	 * @param mixed $file ファイル
	 */
	public function setFile ($file) {
		if ($this->opened) {
			throw new BSFileException($this->getFile() . 'が開かれています。');
		}
		if (BSString::isBlank($file)) {
			$file = null;
		} else if (!($file instanceof BSFile)) {
			$path = $file;
			if (!BSUtility::isPathAbsolute($path)) {
				$path = BSFileUtility::getPath('tmp') . '/' . $path;
			}
			$this->temporaryFile = false;
			$file = new BSFile($path);
		}
		$this->file = $file;
	}

	/**
	 * 出力内容を返す
	 *
	 * @access public
	 */
	public function getContents () {
		$this->close();
		return $this->getFile()->getContents();
	}

	/**
	 * 出力内容のサイズを返す
	 *
	 * @access public
	 * @return integer サイズ
	 */
	public function getSize () {
		return strlen($this->getContents());
	}

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		return BSMIMEType::getType('zip');
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		return true;
	}

	/**
	 * エラーメッセージを返す
	 *
	 * @access public
	 * @return string エラーメッセージ
	 */
	public function getError () {
		return null;
	}
}

/* vim:set tabstop=4: */
