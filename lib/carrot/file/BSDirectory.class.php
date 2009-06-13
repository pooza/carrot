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
	private $suffix;
	private $entries;
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
		parent::setPath(rtrim($path, '/'));
	}

	/**
	 * 規定サフィックスを返す
	 *
	 * @access public
	 * @return string サフィックス
	 */
	public function getDefaultSuffix () {
		return $this->suffix;
	}

	/**
	 * 規定サフィックスを設定
	 *
	 * @access public
	 * @param string $suffix 
	 */
	public function setDefaultSuffix ($suffix) {
		$this->suffix = ltrim($suffix, '*');
		$this->entries = null;
	}

	/**
	 * エントリーの名前を返す
	 *
	 * 拡張子による抽出を行い、かつ拡張子を削除する。
	 *
	 * @access public
	 * @return BSArray 抽出されたエントリー名
	 */
	public function getEntryNames () {
		$names = new BSArray;
		foreach ($this->getAllEntryNames() as $name) {
			if (fnmatch('*' . $this->getDefaultSuffix(), $name)) {
				$names[] = basename($name, $this->getDefaultSuffix());
			}
		}
		return $names;
	}

	/**
	 * 全エントリーの名前を返す
	 *
	 * 拡張子に関わらず全てのエントリーを返す。
	 *
	 * @access public
	 * @return BSArray 全エントリー名
	 */
	public function getAllEntryNames () {
		if (!$this->entries) {
			$this->entries = new BSArray;
			$dir = dir($this->getPath());
			while ($name = $dir->read()) {
				if (!preg_match("/^\.+$/", $name)) {
					$this->entries[] = $name;
				}
			}
			$dir->close();
			if ($this->getSortOrder() == self::SORT_DESC) {
				$this->entries->sort(BSArray::SORT_VALUE_DESC);
			} else {
				$this->entries->sort(BSArray::SORT_VALUE_ASC);
			}
		}
		return $this->entries;
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

		if (BSString::isBlank($class)) {
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
		if (BSString::isBlank($class)) {
			$class = $this->getDefaultEntryClassName();
		}

		$name = basename($name, $this->getDefaultSuffix());
		$path = $this->getPath() . DIRECTORY_SEPARATOR . $name . $this->getDefaultSuffix();

		$class = BSClassLoader::getInstance()->getClassName($class);
		$file = new $class($path);
		$file->setContents(null);
		$this->entries = null;
		return $file;
	}

	/**
	 * 削除
	 *
	 * @access public
	 */
	public function delete () {
		foreach ($this->getAllEntryNames() as $name) {
			$this->getEntry($name)->delete();
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
	 * $param string $class クラス名
	 * @return BSDirectory 作成されたディレクトリ
	 */
	public function createDirectory ($name, $class = 'BSDirectory') {
		$path = $this->getPath() . DIRECTORY_SEPARATOR . $name;
		if (file_exists($path)) {
			if (!is_dir($path)) {
				throw new BSFileException('"%s"と同名のファイルが存在します。', $path);
			}
		} else {
			mkdir($path);
		}
		return new $class($path);
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
		return sprintf('ディレクトリ "%s"', $this->getShortPath());
	}
}

/* vim:set tabstop=4: */
