<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage file
 */

/**
 * ディレクトリ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSDirectory.class.php 100 2007-11-18 08:26:50Z pooza $
 */
class BSDirectory extends BSDirectoryEntry implements IteratorAggregate {
	private $defaultSuffix = '';
	private $entries = array();
	const SORT_ASC = 'asc';
	const SORT_DESC = 'dsc';
	const DEFAULT_ENTRY_CLASS = 'BSFile';

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param string $path ディレクトリのパス
	 */
	public function __construct ($path) {
		$this->setPath($path);
		if (!is_dir($this->getPath())) {
			throw new BSFileException('%sを開くことが出来ません。', $this);
		}
	}

	/**
	 * パスを設定する
	 *
	 * @access public
	 * @param string $path パス
	 */
	public function setPath ($path) {
		$path = preg_replace('/\/$/', '', $path);
		parent::setPath($path);
	}

	/**
	 * 規定サフィックスを返す
	 *
	 * @access public
	 * @return string サフィックス
	 */
	public function getDefaultSuffix () {
		return $this->defaultSuffix;
	}

	/**
	 * 規定サフィックスを設定する
	 *
	 * @access public
	 * @param string $suffix 
	 */
	public function setDefaultSuffix ($suffix) {
		$suffix = preg_replace('/^\**/', '', $suffix);
		$this->defaultSuffix = $suffix;
		$this->entries = array();
	}

	/**
	 * 全エントリーの名前を返す
	 *
	 * @access public
	 * @return string[] 全エントリー
	 */
	public function getEntryNames () {
		if (!$this->entries) {
			$dir = dir($this->getPath());
			while ($entry = $dir->read()) {
				if (preg_match("/^\.+$/", $entry)) {
					continue;
				} else if (!fnmatch('*' . $this->getDefaultSuffix(), $entry)) {
					continue;
				}
				$this->entries[] = basename($entry, $this->getDefaultSuffix());
			}
			$dir->close();
			if ($this->getSortOrder() == self::SORT_DESC) {
				rsort($this->entries);
			} else {
				sort($this->entries);
			}
		}
		return $this->entries;
	}

	/**
	 * 内容を返す
	 *
	 * getEntryNamesのエイリアス
	 *
	 * @access public
	 * @return string[] 全エントリー
	 * @final
	 */
	final public function getContents () {
		return $this->getEntryNames();
	}

	/**
	 * エントリーを返す
	 *
	 * @access public
	 * @param string $name エントリーの名前
	 * @param string $class エントリーのクラス名
	 * @return BSDirectoryEntry ディレクトリかファイル
	 */
	public function getEntry ($name, $class = self::DEFAULT_ENTRY_CLASS) {
		if (is_dir($path = $this->getPath() . '/' . $name)) {
			return new BSDirectory($path);
		} else if (is_file($path)) {
			return new $class($path);
		} else if (is_file($path .= $this->getDefaultSuffix())) {
			return new $class($path);
		}
	}

	/**
	 * 新しく作ったエントリーを作って返す
	 *
	 * @access public
	 * @param string $name エントリーの名前
	 * $param string $class クラス名
	 * @return BSFile ファイル
	 */
	public function createEntry ($name, $class = self::DEFAULT_ENTRY_CLASS) {
		$name = basename($name, $this->getDefaultSuffix());
		$path = $this->getPath() . '/' . $name . $this->getDefaultSuffix();
		$file = new $class($path);
		$file->setContents(null);
		$this->entries = array();
		return $file;
	}

	/**
	 * 削除
	 *
	 * @access public
	 */
	public function delete () {
		foreach ($this as $entry) {
			$entry->delete();
		}
		if (!rmdir($this->getPath())) {
			throw new BSFileException('%sを削除できませんでした。', $this);
		}
	}

	/**
	 * 新規ディレクトリを作り、返す
	 *
	 * @access public
	 * @param string $name ディレクトリの名前
	 * @return BSDirectory 作成されたディレクトリ
	 */
	public function createDirectory ($name) {
		$path = $this->getPath() . '/' . $name;
		if (file_exists($path)) {
			if (!is_dir($path)) {
				throw new BSFileException('"%s"と同名のファイルが存在します。', $path);
			}
		} else {
			mkdir($path);
		}
		return new BSDirectory($path);
	}

	/**
	 * イテレータを返す
	 *
	 * @access public
	 * @return BSDirectoryIterator イテレータ
	 */
	public function getIterator () {
		return new BSDirectoryIterator($this);
	}

	/**
	 * ファイルか
	 *
	 * @access public
	 * @return boolean ファイルならTrue
	 */
	public function isFile () {
		return false;
	}

	/**
	 * ディレクトリか
	 *
	 * @access public
	 * @return boolean ディレクトリならTrue
	 */
	public function isDirectory () {
		return true;
	}

	/**
	 * ソート順を返す
	 *
	 * @access public
	 * @return string (ソート順 self::SORT_ASC | self::SORT_DESC)
	 */
	public function getSortOrder () {
		return self::SORT_ASC;
	}

	/**
	 * 基本情報を文字列で返す
	 *
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('ディレクトリ "%s"', $this->getPath());
	}
}

/* vim:set tabstop=4 ai: */
?>