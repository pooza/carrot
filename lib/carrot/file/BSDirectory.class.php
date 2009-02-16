<?php
/**
 * @package org.carrot-framework
 * @subpackage file
 */

/**
 * ディレクトリ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSDirectory extends BSDirectoryEntry implements IteratorAggregate {
	private $defaultSuffix = '';
	private $entries = array();
	const SORT_ASC = 'asc';
	const SORT_DESC = 'dsc';

	/**
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
	 * パスを設定
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
	 * 規定サフィックスを設定
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
	public function getEntry ($name, $class = null) {
		// "/"が含まれること許したいので、basename関数は利用出来ない。
		$name = BSString::stripControlCharacters($name);
		$name = str_replace('..' . DIRECTORY_SEPARATOR, '', $name);

		if (!$class) {
			$class = $this->getDefaultEntryClassName();
		}
		$class = BSClassLoader::getInstance()->getClassName($class);

		$path = $this->getPath() . DIRECTORY_SEPARATOR . $name;
		if ($this->hasSubDirectory() && is_dir($path)) {
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
	public function createEntry ($name, $class = null) {
		if (!$class) {
			$class = $this->getDefaultEntryClassName();
		}

		$name = basename($name, $this->getDefaultSuffix());
		$path = $this->getPath() . DIRECTORY_SEPARATOR . $name . $this->getDefaultSuffix();

		$class = BSClassLoader::getInstance()->getClassName($class);
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
		$path = $this->getPath() . DIRECTORY_SEPARATOR . $name;
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
	 * ファイルか？
	 *
	 * @access public
	 * @return boolean ファイルならTrue
	 */
	public function isFile () {
		return false;
	}

	/**
	 * ディレクトリか？
	 *
	 * @access public
	 * @return boolean ディレクトリならTrue
	 */
	public function isDirectory () {
		return true;
	}

	/**
	 * サブディレクトリを持つか？
	 *
	 * @access public
	 * @return boolean サブディレクトリを持つならTrue
	 */
	public function hasSubDirectory () {
		return true;
	}

	/**
	 * エントリーのクラス名を返す
	 *
	 * @access public
	 * @return string エントリーのクラス名
	 */
	public function getDefaultEntryClassName () {
		return 'BSFile';
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
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('ディレクトリ "%s"', $this->getPath());
	}
}

/* vim:set tabstop=4: */
