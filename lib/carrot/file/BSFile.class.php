<?php
/**
 * @package org.carrot-framework
 * @subpackage file
 */

// MacOSのテキストファイル（CR改行）対応
ini_set('auto_detect_line_endings', true);

/**
 * ファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSFile extends BSDirectoryEntry implements BSRenderer {
	private $mode;
	private $lines;
	private $size;
	private $handle;
	private $error;
	protected $type;
	const LINE_SEPARATOR = "\n";

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param string $path パス
	 */
	public function __construct ($path) {
		$this->setPath($path);
	}

	/**
	 * デストラクタ
	 *
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
		if (!$this->type) {
			$this->type = BSMediaType::getType($this->getSuffix());
		}
		return $this->type;
	}

	/**
	 * リネーム
	 *
	 * @access public
	 * @param string $name 新しい名前
	 */
	public function rename ($name) {
		if ($this->isOpened()) {
			throw new BSFileException('%sは既に開かれています。', $this);
		}

		if ($this->isUploaded()) {
			$path = $this->getDirectory()->getPath() . DIRECTORY_SEPARATOR . basename($name);
			if (!move_uploaded_file($this->getPath(), $path)) {
				throw new BSFileException('アップロードされた%sをリネーム出来ません。', $this);
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
			throw new BSFileException('%sは既に開かれています。', $this);
		}
		if ($this->isUploaded()) {
			$path = $dir->getPath() . DIRECTORY_SEPARATOR . $this->getName();
			if (!move_uploaded_file($this->getPath(), $path)) {
				throw new BSFileException('アップロードされた%sを移動出来ません。', $this);
			}
			$this->setPath($path);
		} else {
			parent::moveTo($dir);
		}
	}

	/**
	 * 削除
	 *
	 * @access public
	 */
	public function delete () {
		if (!$this->isWritable($this->getPath())) {
			throw new BSFileException('%sを削除出来ません。', $this);
		} else if ($this->isOpened()) {
			throw new BSFileException('%sは既に開かれています。', $this);
		}
		if (!unlink($this->getPath())) {
			throw new BSFileException('%sを削除出来ません。', $this);
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
			throw new BSFileException('%sが存在しません。', $this);
		} else if ($this->isCompressed()) {
			throw new BSFileException('%sはgzip圧縮されています。', $this);
		} else if ($this->isOpened()) {
			throw new BSFileException('%sは既に開かれています。', $this);
		}

		if (!$this->handle = fopen($this->getPath(), $mode)) {
			$this->handle = null;
			$this->mode = null;
			throw new BSFileException('%sを%sモードで開くことが出来ません。', $this, $mode);
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
	public function putLine ($str = '') {
		if (!$this->isOpened() || !in_array($this->mode, array('w', 'a'))) {
			throw new BSFileException('%sはw又はaモードで開かれていません。', $this);
		}

		flock($this->handle, LOCK_EX);
		fputs($this->handle, $str . self::LINE_SEPARATOR);
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
				throw new BSFileException('%sはrモードで開かれていません。', $this);
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
			throw new BSFileException('%sをgzip圧縮することは出来ません。', $this);
		}
		$contents = gzencode($this->getContents(), 9);
		$this->setContents($contents);
		$this->rename($this->getName() . '.gz');
	}

	/**
	 * gzip圧縮されているか？
	 *
	 * @access public
	 * @return boolean gzip圧縮されていたらTrue
	 */
	public function isCompressed () {
		return ($this->getSuffix() == '.gz');
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
			throw new BSFileException('%sを読み込めません。', $this);
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
				throw new BSFileException('%sが存在しません。', $this);
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
	public function getFormattedSize ($suffix = 'B') {
		foreach (array('', 'K', 'M', 'G', 'T', 'P', 'E') as $number => $unit) {
			$unitsize = pow(1024, $number);
			if ($this->getSize() < ($unitsize * 1024 * 2)) {
				return sprintf(
					'%s %s%s',
					number_format(floor($this->getSize() / $unitsize)),
					$unit,
					$suffix
				);
			}
		}
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
	 * 基本情報を文字列で返す
	 *
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('ファイル "%s"', $this->getPath());
	}

}

/* vim:set tabstop=4 ai: */
?>