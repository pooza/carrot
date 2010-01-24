<?php
/**
 * @package org.carrot-framework
 * @subpackage file
 */

ini_set('auto_detect_line_endings', true);

/**
 * ファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSFile extends BSDirectoryEntry implements BSRenderer {
	private $mode;
	private $lines;
	private $size;
	private $handle;
	private $error;
	const LINE_SEPARATOR = "\n";
	const COMPRESSED_SUFFIX = '.gz';

	/**
	 * @access public
	 * @param string $path パス
	 */
	public function __construct ($path) {
		$this->setPath($path);
	}

	/**
	 * @access public
	 */
	public function __destruct () {
		if ($this->isOpened()) {
			$this->close();
		}
	}

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		return BSMIMEType::getType($this->getSuffix());
	}

	/**
	 * ファイルの内容から、メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function analyzeType () {
		return BSMIMEType::analyzeType($this);
	}

	/**
	 * 規定のサフィックスを返す
	 *
	 * @access public
	 * @return string 規定サフィックス
	 */
	public function getDefaultSuffix () {
		$types = new BSArray(BSMIMEType::getInstance());
		$suffixes = $types->getFlipped();
		return '.' . $suffixes[$this->getType()];
	}

	/**
	 * リネーム
	 *
	 * @access public
	 * @param string $name 新しい名前
	 */
	public function rename ($name) {
		if ($this->isOpened()) {
			throw new BSFileException($this . 'は既に開かれています。');
		}

		if ($this->isUploaded()) {
			$path = $this->getDirectory()->getPath() . DIRECTORY_SEPARATOR . basename($name);
			if (!move_uploaded_file($this->getPath(), $path)) {
				throw new BSFileException('アップロードされた%sをリネームできません。', $this);
			}
			$this->setPath($path);
		} else {
			parent::rename($name);
		}
	}

	/**
	 * 移動
	 *
	 * @access public
	 * @param BSDirectory $dir 移動先ディレクトリ
	 */
	public function moveTo (BSDirectory $dir) {
		if ($this->isOpened()) {
			throw new BSFileException($this . 'は既に開かれています。');
		}
		if ($this->isUploaded()) {
			$path = $dir->getPath() . DIRECTORY_SEPARATOR . $this->getName();
			if (!move_uploaded_file($this->getPath(), $path)) {
				throw new BSFileException('アップロードされた%sを移動できません。', $this);
			}
			$this->setPath($path);
		} else {
			parent::moveTo($dir);
		}
	}

	/**
	 * コピー
	 *
	 * @access public
	 * @param BSDirectory $dir コピー先ディレクトリ
	 * @param string $class クラス名
	 * @return BSFile コピーされたファイル
	 */
	public function copyTo (BSDirectory $dir, $class = 'BSFile') {
		$path = $dir->getPath() . DIRECTORY_SEPARATOR . $this->getName();
		if (!copy($this->getPath(), $path)) {
			throw new BSFileException($this . 'をコピーできません。');
		}
		$class = BSClassLoader::getInstance()->getClass($class);
		return new $class($path);
	}

	/**
	 * 削除
	 *
	 * @access public
	 */
	public function delete () {
		if (!$this->isWritable($this->getPath())) {
			throw new BSFileException($this . 'を削除できません。');
		} else if ($this->isOpened()) {
			throw new BSFileException($this . 'は既に開かれています。');
		}
		if (!unlink($this->getPath())) {
			throw new BSFileException($this . 'を削除できません。');
		}
	}

	/**
	 * ストリームを開く
	 *
	 * @access public
	 * @param string $mode モード
	 */
	public function open ($mode = 'r') {
		if (!in_array($mode, array('r', 'a', 'w'))) {
			throw new BSFileException('モード"%s"が正しくありません。', $mode);
		} else if (($mode == 'r') && !$this->isExists()) {
			throw new BSFileException($this . 'が存在しません。');
		} else if ($this->isCompressed()) {
			throw new BSFileException($this . 'はgzip圧縮されています。');
		} else if ($this->isOpened()) {
			throw new BSFileException($this . 'は既に開かれています。');
		}

		if (!$this->handle = fopen($this->getPath(), $mode)) {
			$this->handle = null;
			$this->mode = null;
			throw new BSFileException('%sを%sモードで開くことができません。', $this, $mode);
		}

		$this->mode = $mode;
	}

	/**
	 * ストリームを閉じる
	 *
	 * @access public
	 */
	public function close () {
		if ($this->isOpened()) {
			fclose($this->handle);
		}
		$this->handle = null;
		$this->mode = null;
	}

	/**
	 * ストリームに1行書き込む
	 *
	 * @access public
	 * @param string $str 書き込む内容
	 */
	public function putLine ($str = '', $separator = self::LINE_SEPARATOR) {
		if (!$this->isOpened() || !in_array($this->mode, array('w', 'a'))) {
			throw new BSFileException($this . 'はw又はaモードで開かれていません。');
		}

		flock($this->handle, LOCK_EX);
		fwrite($this->handle, $str . $separator);
		flock($this->handle, LOCK_UN);
		$this->lines = null;
	}

	/**
	 * ストリームから1行読み込む
	 *
	 * @access public
	 * @param integer $length 一度に読み込む最大のサイズ
	 * @return string 読み込んだ内容
	 */
	public function getLine ($length = 4096) {
		if ($this->isOpened()) {
			if ($this->mode != 'r') {
				throw new BSFileException($this . 'はrモードで開かれていません。');
			}
		} else {
			$this->open();
		}

		if ($this->isEof()) {
			return '';
		}
		$line = fgets($this->handle, $length);
		$line = rtrim($line);
		return $line;
	}

	/**
	 * 全ての行を返す
	 *
	 * @access public
	 * @return string[] 読み込んだ内容の配列
	 */
	public function getLines () {
		if (!$this->lines) {
			if ($this->isCompressed()) {
				$this->lines = gzfile($this->getPath());
			} else {
				$this->lines = file($this->getPath());
			}
			foreach ($this->lines as &$line) {
				$line = rtrim($line);
			}
		}
		return $this->lines;
	}

	/**
	 * 全て返す
	 *
	 * @access public
	 * @return string 読み込んだ内容
	 */
	public function getContents () {
		if ($this->isCompressed()) {
			return readgzfile($this->getPath());
		} else {
			return file_get_contents($this->getPath());
		}
	}

	/**
	 * 書き換える
	 *
	 * @access public
	 * @param string $contents 書き込む内容
	 */
	public function setContents ($contents) {
		if ($this->isCompressed()) {
			file_put_contents($this->getPath(), gzencode($contents, 9));
		} else {
			file_put_contents($this->getPath(), $contents);
		}
	}

	/**
	 * gzip圧縮
	 *
	 * @access public
	 */
	public function compress () {
		if ($this->isCompressed()) {
			throw new BSFileException($this . 'をgzip圧縮することはできません。');
		}
		$contents = gzencode($this->getContents(), 9);
		$this->setContents($contents);
		$this->rename($this->getName() . self::COMPRESSED_SUFFIX);
	}

	/**
	 * gzip圧縮されているか？
	 *
	 * @access public
	 * @return boolean gzip圧縮されていたらTrue
	 */
	public function isCompressed () {
		// BSMIMEType等を通る無限ループが発生する為、$this->getType()は使用できない。
		return ($this->getSuffix() == self::COMPRESSED_SUFFIX);
	}

	/**
	 * 開かれているか？
	 *
	 * @access public
	 * @return boolean 開かれていたらtrue
	 */
	public function isOpened () {
		return is_resource($this->handle);
	}

	/**
	 * ポインタがEOFに達しているか？
	 *
	 * @access public
	 * @return boolean EOFに達していたらtrue
	 */
	public function isEof () {
		if (!$this->isReadable()) {
			throw new BSFileException($this . 'を読み込めません。');
		}
		return feof($this->handle);
	}

	/**
	 * ファイルサイズを返す
	 *
	 * @access public
	 * @return integer ファイルサイズ
	 */
	public function getSize () {
		if ($this->size === null) {
			if (!$this->isExists()) {
				throw new BSFileException($this . 'が存在しません。');
			}
			$this->size = filesize($this->getPath());
		}
		return $this->size;
	}

	/**
	 * 書式化されたファイルサイズを文字列で返す
	 *
	 * @access public
	 * @param string $suffix サフィックス、デフォルトはバイトの略で"B"
	 * @return string 書式化されたファイルサイズ
	 */
	public function getBinarySize ($suffix = 'B') {
		return BSNumeric::getBinarySize($this->getSize()) . $suffix;
	}

	/**
	 * アップロードされたファイルか？
	 *
	 * @access public
	 * @return boolean アップロードされたファイルならTrue
	 */
	public function isUploaded () {
		return is_uploaded_file($this->getPath());
	}

	/**
	 * ファイルか？
	 *
	 * @access public
	 * @return boolean ファイルならTrue
	 */
	public function isFile () {
		return true;
	}

	/**
	 * ディレクトリか？
	 *
	 * @access public
	 * @return boolean ディレクトリならTrue
	 */
	public function isDirectory () {
		return false;
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		if (!$this->isReadable()) {
			$this->error = $this . 'が開けません。';
			return false;
		}
		return true;
	}

	/**
	 * エラーメッセージを返す
	 *
	 * @access public
	 * @return string エラーメッセージ
	 */
	public function getError () {
		return $this->error;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('ファイル "%s"', $this->getShortPath());
	}
}

/* vim:set tabstop=4: */
