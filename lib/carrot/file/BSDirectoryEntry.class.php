<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage file
 */

/**
 * ディレクトリエントリ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
abstract class BSDirectoryEntry {
	use BSBasicObject;
	protected $name;
	protected $path;
	protected $id;
	private $suffix;
	private $basename;
	private $shortPath;
	private $linkTarget;
	protected $directory;

	/**
	 * ユニークなファイルIDを返す
	 *
	 * @access public
	 * @return integer ID
	 */
	public function getID () {
		if (!$this->id) {
			$this->id = BSCrypt::digest([
				$this->getPath(),
				fileinode($this->getPath()),
			]);
		}
		return $this->id;
	}

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
	 * 名前を設定
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
			throw new BSFileException($this . 'が存在しません。');
		} else if (!$this->isWritable($this->getPath())) {
			throw new BSFileException($this . 'をリネームできません。');
		} else if (BSString::isContain('/', $name)) {
			throw new BSFileException($this . 'をリネームできません。');
		}

		$path = $this->getDirectory()->getPath() . '/' . basename($name);
		$this->controller->getPlatform()->renameFile($this, $path);
		$this->setPath($path);
		$this->getDirectory()->clearEntryNames();
	}

	/**
	 * 削除
	 *
	 * @access public
	 * @abstract
	 */
	abstract public function delete ();

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
	 * パスを設定
	 *
	 * @access protected
	 * @param string $path パス
	 */
	protected function setPath ($path) {
		if (!BSUtility::isPathAbsolute($path) || BSString::isContain('../', $path)) {
			$message = new BSStringFormat('パス"%s"が正しくありません。');
			$message[] = $path;
			throw new BSFileException($message);
		}
		$this->path = $path;
		$this->name = null;
		$this->basename = null;
		$this->suffix = null;
	}

	/**
	 * 短いパスを返す
	 *
	 * @access public
	 * @return string 短いパス
	 */
	public function getShortPath () {
		if (!$this->shortPath) {
			$this->shortPath = str_replace(
				BSFileUtility::getPath('root') . '/',
				'',
				$this->getPath()
			);
		}
		return $this->shortPath;
	}

	/**
	 * 移動
	 *
	 * @access public
	 * @param BSDirectory $dir 移動先ディレクトリ
	 */
	public function moveTo (BSDirectory $dir) {
		if (!$this->isExists()) {
			throw new BSFileException($this . 'が存在しません。');
		} else if (!$this->isWritable() || !$dir->isWritable()) {
			throw new BSFileException($this . 'を移動できません。');
		}

		$path = $dir->getPath() . '/' . $this->getName();
		$this->controller->getPlatform()->renameFile($this, $path);
		$this->setPath($path);
	}

	/**
	 * コピー
	 *
	 * @access public
	 * @param BSDirectory $dir コピー先ディレクトリ
	 * @return BSFile コピーされたファイル
	 */
	public function copyTo (BSDirectory $dir) {
		$path = $dir->getPath() . '/' . $this->getName();
		if (!copy($this->getPath(), $path)) {
			throw new BSFileException($this . 'をコピーできません。');
		}
		$class = get_class($this);
		return new $class($path);
	}

	/**
	 * ドットファイル等を削除
	 *
	 * @access public
	 */
	public function clearIgnoreFiles () {
		if ($this->isIgnore() || $this->isDotted()) {
			$this->delete();
		}
	}

	/**
	 * サフィックスを返す
	 *
	 * @access public
	 * @return string サフィックス
	 */
	public function getSuffix () {
		if (!$this->suffix) {
			$this->suffix = BSFileUtility::getSuffix($this->getName());
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
	 * 無視対象か？
	 *
	 * @access public
	 * @return boolean 無視対象ならTrue
	 */
	public function isIgnore () {
		return BSFileUtility::isIgnoreName($this->getName());
	}

	/**
	 * 名前がドットから始まるか？
	 *
	 * @access public
	 * @return boolean ドットから始まるならTrue
	 */
	public function isDotted () {
		return BSFileUtility::isDottedName($this->getName());
	}

	/**
	 * シンボリックリンクか？
	 *
	 * @access public
	 * @return boolean シンボリックリンクならTrue
	 */
	public function isLink () {
		return is_link($this->getPath());
	}

	/**
	 * リンク先を返す
	 *
	 * @access public
	 * @return BSDirectoryEntry リンク先
	 */
	public function getLinkTarget () {
		if ($this->isLink() && !$this->linkTarget) {
			$class = get_class($this);
			$this->linkTarget = new $class(readlink($this->getPath()));
		}
		return $this->linkTarget;
	}

	/**
	 * シンボリックリンクを作成
	 *
	 * @access public
	 * @param BSDirectory $dir 作成先ディレクトリ
	 * @param string $name リンクのファイル名。空欄の場合は、元ファイルと同じ。
	 * @return BSDirectoryEntry リンク先
	 */
	public function createLink (BSDirectory $dir, $name = null) {
		if (BSString::isBlank($name)) {
			$name = $this->getName();
		}

		$path = $dir->getPath() . '/' . $name;
		if (is_link($path)) {
			unlink($path);
		}
		symlink($this->getPath(), $path);
		return $dir->getEntry($name);
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
			throw new BSFileException($this . 'が存在しません。');
		}
		return BSDate::create(filectime($this->getPath()), BSDate::TIMESTAMP);
	}

	/**
	 * 更新日付を返す
	 *
	 * @access public
	 * @return BSDate 更新日付
	 */
	public function getUpdateDate () {
		if (!$this->isExists()) {
			throw new BSFileException($this . 'が存在しません。');
		}
		return BSDate::create(filemtime($this->getPath()), BSDate::TIMESTAMP);
	}

	/**
	 * 存在するか？
	 *
	 * @access public
	 * @return boolean 存在するならtrue
	 */
	public function isExists () {
		return file_exists($this->getPath());
	}

	/**
	 * 存在し、かつ読めるか？
	 *
	 * @access public
	 * @return boolean 読めればtrue
	 */
	public function isReadable () {
		return is_readable($this->getPath());
	}

	/**
	 * 存在し、書き込めるか？
	 *
	 * @access public
	 * @return boolean 書き込めればtrue
	 */
	public function isWritable () {
		return is_writable($this->getPath());
	}

	/**
	 * ファイルモード（パーミッション）を設定
	 *
	 * @access public
	 * @param integer $mode ファイルモード
	 * @param integer $flags フラグのビット列
	 */
	public function setMode ($mode, $flags = null) {
		if (!$this->isWritable() || !chmod($this->getPath(), $mode)) {
			throw new BSFileException($this . 'のファイルモードを変更できません。');
		}
	}
}

/* vim:set tabstop=4: */
