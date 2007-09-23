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
 * @version $Id$
 */
class BSDirectory extends BSDirectoryEntry implements IteratorAggregate {
	private $suffix = '';
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
		$this->setSuffix();
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
	 * サフィックスを返す
	 *
	 * @access public
	 * @return string サフィックス
	 */
	public function getSuffix () {
		return $this->suffix;
	}

	/**
	 * サフィックスを設定する
	 *
	 * @access public
	 * @param string $suffix 
	 */
	public function setSuffix ($suffix = null) {
		if (!$suffix) {
			$suffix = $this->getDefaultSuffix();
		}
		$this->suffix = preg_replace('/^\**/', '', $suffix);
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
				} else if ($this->getSuffix()) {
					$suffix = sprintf('/%s$/i', str_replace('.', '\\.', $this->getSuffix()));
					if (!preg_match($suffix, $entry)) {
						continue;
					}
				}
				$this->entries[] = basename($entry, $this->getSuffix());
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
	 * 内容を返す - getEntryNamesのエイリアス
	 *
	 * @access public
	 * @return string[] 全エントリー
	 */
	public function getContents () {
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
		} else if (is_file($path .= $this->getSuffix())) {
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
		$pattern = '/' . str_replace('\.', '\\\.', $this->getSuffix()) . '$/';
		$name = preg_replace($pattern, '', $name);
		$file = new $class($this->getPath() . '/' . $name . $this->getSuffix());
		$file->setContents(null);
		$this->entries = array();
		return $file;
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
		if (file_exist($path)) {
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
	 * 規定のサフィックスを返す
	 *
	 * @access public
	 * @return string 規定のサフィックス
	 */
	public function getDefaultSuffix () {
		return '';
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