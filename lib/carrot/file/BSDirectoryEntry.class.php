<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage file
 */

/**
 * ディレクトリエントリ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 * @abstract
 */
abstract class BSDirectoryEntry {
	protected $name;
	protected $path;
	private $suffix;
	private $basename;
	protected $directory;

	/**
	 * 名前を返す
	 *
	 * @access public
	 * @return string 名前
	 */
	public function getName () {
		if (!$this->name) {
			$this->name = basename($this->getPath());
		}
		return $this->name;
	}

	/**
	 * 名前を設定する
	 *
	 * renameのエイリアス
	 *
	 * @access public
	 * @param string $name 新しい名前
	 * @final
	 */
	final public function setName ($name) {
		return $this->rename($name);
	}

	/**
	 * リネーム
	 *
	 * @access public
	 * @param string $name 新しい名前
	 */
	public function rename ($name) {
		if (!$this->isExists()) {
			throw new BSFileException('%sが存在しません。', $this);
		} else if (!$this->isWritable($this->getPath())) {
			throw new BSFileException('%sをリネーム出来ません。', $this);
		} else if (preg_match('/\//', $name)) {
			throw new BSFileException('%sをリネーム出来ません。', $this);
		}

		$path = $this->getDirectory()->getPath() . '/' . basename($name);
		if (!rename($this->getPath(), $path)) {
			throw new BSFileException('%sをリネーム出来ません。', $this);
		}
		$this->setPath($path);
	}

	/**
	 * パスを返す
	 *
	 * @access public
	 * @return string パス
	 */
	public function getPath () {
		return $this->path;
	}

	/**
	 * パスを設定する
	 *
	 * @access public
	 * @param string $path パス
	 */
	public function setPath ($path) {
		if (!Toolkit::isPathAbsolute($path) || preg_match('/\.\./', $path)) {
			throw new BSFileException('パス"%s"が正しくありません。', $path);
		}
		$this->path = $path;
		$this->name = null;
		$this->basename = null;
		$this->suffix = null;
	}

	/**
	 * 移動
	 *
	 * @access public
	 * @param BSDirectory $dir 移動先ディレクトリ
	 */
	public function moveTo (BSDirectory $dir) {
		if (!$this->isExists()) {
			throw new BSFileException('%sが存在しません。', $this);
		} else if (!$this->isWritable() || !$dir->isWritable()) {
			throw new BSFileException('%sを移動出来ません。', $this);
		}

		$path = $dir->getPath() . '/' . $this->getName();
		if (!rename($this->getPath(), $path)) {
			throw new BSFileException('%sを移動出来ません。', $this);
		}
		$this->setPath($path);
	}

	/**
	 * サフィックスを返す
	 *
	 * @access public
	 * @return string サフィックス
	 */
	public function getSuffix () {
		if (!$this->suffix) {
			$name = explode('.', $this->getName());
			if (1 < count($name)) {
				$this->suffix = '.' . end($name);
			}
		}
		return $this->suffix;
	}

	/**
	 * ベース名を返す
	 *
	 * @access public
	 * @return string ベース名
	 */
	public function getBaseName () {
		if (!$this->basename) {
			$this->basename = basename($this->getPath(), $this->getSuffix());
		}
		return $this->basename;
	}

	/**
	 * 名前がドットから始まるか
	 *
	 * @access public
	 * @return boolean ドットから始まるならTrue
	 */
	public function isDoted () {
		return preg_match("/^\./", $this->getName());
	}

	/**
	 * 親ディレクトリを返す
	 *
	 * @access public
	 * @return BSDirectory ディレクトリ
	 */
	public function getDirectory () {
		if (!$this->directory) {
			$this->directory = new BSDirectory(dirname($this->getPath()));
		}
		return $this->directory;
	}

	/**
	 * 作成日付を返す
	 *
	 * @access public
	 * @return BSDate 作成日付
	 */
	public function getCreateDate () {
		if (!$this->isExists()) {
			throw new BSFileException('%sが存在しません。', $this);
		}

		clearstatcache();
		$date = new BSDate();
		$date->setTimestamp(filectime($this->getPath()));
		return $date;
	}

	/**
	 * 更新日付を返す
	 *
	 * @access public
	 * @return BSDate 更新日付
	 */
	public function getUpdateDate () {
		if (!$this->isExists()) {
			throw new BSFileException('%sが存在しません。', $this);
		}

		clearstatcache();
		$date = new BSDate();
		$date->setTimestamp(filemtime($this->getPath()));
		return $date;
	}

	/**
	 * 存在するか
	 *
	 * @access public
	 * @return boolean 存在するならtrue
	 */
	public function isExists () {
		return file_exists($this->getPath());
	}

	/**
	 * 存在し、かつ読めるか
	 *
	 * @access public
	 * @return boolean 読めればtrue
	 */
	public function isReadable () {
		return is_readable($this->getPath());
	}

	/**
	 * 存在し、書き込めるか
	 *
	 * @access public
	 * @return boolean 書き込めればtrue
	 */
	public function isWritable () {
		return is_writable($this->getPath());
	}

	/**
	 * ファイルモード（パーミッション）を設定する
	 *
	 * @access public
	 * @param integer $mode ファイルモード
	 */
	public function setMode ($mode) {
		if (!$this->isExists() || !chmod($this->getPath(), $mode)) {
			throw new BSFileException('%sのファイルモードの変更に失敗しました。', $this);
		}
	}

	/**
	 * ファイルか
	 *
	 * @access public
	 * @return boolean ファイルならTrue
	 * @abstract
	 */
	abstract public function isFile ();

	/**
	 * ディレクトリか
	 *
	 * @access public
	 * @return boolean ディレクトリならTrue
	 * @abstract
	 */
	abstract public function isDirectory ();
}

/* vim:set tabstop=4 ai: */
?>